<?php

namespace App\Services;

use App\Models\Kehadiran;
use App\Models\KonfigurasiWifi;
use App\Models\WifiAccessLog;
use App\Models\Pegawai;
use App\Models\SuratPerintahTugas;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AbsensiSignatureService
{
    /**
     * Resolves the secure client IP address.
     * Uses standard Laravel trusted proxy resolution.
     */
    public function resolveClientIp(Request $request): string
    {
        return $request->ip() ?: '127.0.0.1';
    }

    /**
     * Mengambil daftar WiFi aktif dari cache (TTL: 5 menit / 300 detik).
     */
    public function getDaftarWifiAktif()
    {
        return Cache::remember('konfigurasi_wifi_aktif', 300, function () {
            return KonfigurasiWifi::aktif()->get();
        });
    }

    /**
     * Invalidate cache konfigurasi WiFi ketika ada perubahan di admin.
     */
    public function invalidateWifiCache(): void
    {
        Cache::forget('konfigurasi_wifi_aktif');
    }

    /**
     * Catat log akses & verifikasi WiFi ke tabel audit wifi_access_logs.
     */
    public function catatWifiAccessLog(
        string $clientIp,
        string $jenisAksi,
        string $hasil,
        ?int $pegawaiId = null,
        ?string $alasanDitolak = null,
        ?string $matchedWifi = null,
        ?string $userAgent = null
    ): void {
        try {
            WifiAccessLog::create([
                'client_ip'      => $clientIp,
                'pegawai_id'     => $pegawaiId,
                'jenis_aksi'     => $jenisAksi,
                'hasil'          => $hasil,
                'alasan_ditolak' => $alasanDitolak,
                'matched_wifi'   => $matchedWifi,
                'user_agent'     => $userAgent ? Str::limit($userAgent, 500) : null,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Menyimpan signature base64 menjadi file PNG di storage publik untuk menghemat ukuran DB.
     */
    public function simpanTandaTanganKeDisk(string $signatureBase64, string $prefix = 'sign'): string
    {
        if (!str_starts_with($signatureBase64, 'data:image')) {
            return $signatureBase64;
        }

        try {
            $imageParts = explode(';base64,', $signatureBase64);
            if (count($imageParts) === 2) {
                $imageBase64 = base64_decode($imageParts[1]);
                $filename = 'signatures/' . $prefix . '_' . date('Ymd_His') . '_' . Str::random(8) . '.png';
                Storage::disk('public')->put($filename, $imageBase64);
                return $filename;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $signatureBase64;
    }

    /**
     * Validasi apakah IP client termasuk dalam whitelist WiFi desa yang aktif.
     */
    public function validasiIpWifi(string $clientIp): bool
    {
        $daftarWifi = $this->getDaftarWifiAktif();

        foreach ($daftarWifi as $wifi) {
            $ipConfig = trim($wifi->ip_address);

            // Cek CIDR (contoh: 192.168.1.0/24)
            if (str_contains($ipConfig, '/')) {
                if ($this->ipInCidr($clientIp, $ipConfig)) {
                    return true;
                }
            } elseif (str_contains($ipConfig, '*')) {
                // Wildcard (contoh: 192.168.1.*)
                $pattern = '/^' . str_replace('\*', '\d+', preg_quote($ipConfig, '/')) . '$/';
                if (preg_match($pattern, $clientIp)) {
                    return true;
                }
            } else {
                // Exact match
                if ($clientIp === $ipConfig) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Diagnosis lengkap status koneksi WiFi klien.
     */
    public function getWifiDiagnosis(string $clientIp): array
    {
        $daftarWifi = $this->getDaftarWifiAktif();
        $matchedWifi = null;

        foreach ($daftarWifi as $wifi) {
            $ipConfig = trim($wifi->ip_address);

            // Cek CIDR (contoh: 192.168.1.0/24)
            if (str_contains($ipConfig, '/')) {
                if ($this->ipInCidr($clientIp, $ipConfig)) {
                    $matchedWifi = $wifi;
                    break;
                }
            } elseif (str_contains($ipConfig, '*')) {
                // Wildcard (contoh: 192.168.1.*)
                $pattern = '/^' . str_replace('\*', '\d+', preg_quote($ipConfig, '/')) . '$/';
                if (preg_match($pattern, $clientIp)) {
                    $matchedWifi = $wifi;
                    break;
                }
            } else {
                // Exact match
                if ($clientIp === $ipConfig) {
                    $matchedWifi = $wifi;
                    break;
                }
            }
        }

        $isValid = !is_null($matchedWifi);
        $wifiNames = $daftarWifi->pluck('nama_jaringan')->filter()->values()->toArray();

        return [
            'is_valid'          => $isValid,
            'client_ip'         => $clientIp,
            'matched_network'   => $matchedWifi ? $matchedWifi->nama_jaringan : null,
            'active_networks'   => $wifiNames,
            'rejection_reason'  => $isValid ? null : ($daftarWifi->isEmpty()
                ? 'Belum ada konfigurasi WiFi kantor desa yang aktif di sistem.'
                : 'Alamat IP (' . $clientIp . ') tidak terhubung ke jaringan WiFi Kantor Desa Nangtang.'),
        ];
    }


    /**
     * Proses absen masuk dengan tanda tangan (dengan database transaction & row lock).
     */
    public function prosesAbsenMasuk(Pegawai $pegawai, string $signatureBase64, string $ipAddress): array
    {
        $tanggal = Carbon::today()->toDateString();
        $jamMasuk = Carbon::now()->format('H:i:s');

        return DB::transaction(function () use ($pegawai, $signatureBase64, $ipAddress, $tanggal, $jamMasuk) {
            // Lock record hari ini jika ada untuk mencegah double tap / race condition
            $existing = Kehadiran::where('pegawai_id', $pegawai->id)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->jam_masuk) {
                return [
                    'status'  => 'sudah_absen',
                    'message' => 'Anda sudah melakukan absen masuk hari ini pukul ' . substr($existing->jam_masuk, 0, 5),
                    'data'    => $existing,
                ];
            }

            // Simpan gambar tanda tangan ke storage
            $signaturePath = $this->simpanTandaTanganKeDisk($signatureBase64, 'masuk_' . $pegawai->id);

            // Cek status: apakah sedang Dinas Luar (ada SPT aktif)
            $adaSPT = SuratPerintahTugas::where('pegawai_id', $pegawai->id)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_mulai', '<=', $tanggal)
                ->whereDate('tanggal_selesai', '>=', $tanggal)
                ->exists();

            $statusKehadiran = 'Hadir';
            $terlambatMenit = 0;

            if ($adaSPT) {
                $statusKehadiran = 'Dinas Luar';
            } else {
                $shift = $pegawai->shift ?? \App\Models\ShiftKerja::where('is_active', true)->first();
                if ($shift && $shift->jam_masuk) {
                    $jadwalMasuk = Carbon::createFromTimeString($shift->jam_masuk);
                    $toleransi = (int) ($shift->toleransi_menit ?? 0);
                    $batasTepatWaktu = $jadwalMasuk->copy()->addMinutes($toleransi);
                    $waktuMasuk = Carbon::createFromTimeString($jamMasuk);

                    if ($waktuMasuk->gt($batasTepatWaktu)) {
                        $statusKehadiran = 'Terlambat';
                        $terlambatMenit = (int) $jadwalMasuk->diffInMinutes($waktuMasuk);
                    } else {
                        $statusKehadiran = 'Tepat Waktu';
                    }
                }
            }

            $kehadiran = Kehadiran::updateOrCreate(
                ['pegawai_id' => $pegawai->id, 'tanggal' => $tanggal],
                [
                    'jam_masuk'           => $jamMasuk,
                    'terlambat_menit'     => $terlambatMenit,
                    'status'              => $statusKehadiran,
                    'sumber_data'         => 'web_signature',
                    'tanda_tangan_masuk'  => $signaturePath,
                    'ip_absensi_masuk'    => $ipAddress,
                ]
            );

            $ketTerlambat = $terlambatMenit > 0 ? " (Terlambat {$terlambatMenit} menit)" : "";

            AuditLog::create([
                'user_name' => $pegawai->nama_lengkap,
                'role'      => $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa',
                'aktivitas' => "Absen masuk via tanda tangan web ({$statusKehadiran}{$ketTerlambat}) pukul {$jamMasuk} dari IP {$ipAddress}",
                'modul'     => 'Absensi Tanda Tangan',
                'ip_address'=> $ipAddress,
            ]);

            $this->catatWifiAccessLog(
                clientIp: $ipAddress,
                jenisAksi: 'absen_masuk',
                hasil: 'diizinkan',
                pegawaiId: $pegawai->id,
                matchedWifi: 'WiFi Terverifikasi'
            );

            return [
                'status'  => 'berhasil',
                'jenis'   => 'masuk',
                'message' => "Absen masuk berhasil dicatat pukul " . substr($jamMasuk, 0, 5),
                'data'    => $kehadiran,
            ];
        });
    }

    /**
     * Proses absen pulang dengan tanda tangan (dengan database transaction & row lock).
     */
    public function prosesAbsenPulang(Pegawai $pegawai, string $signatureBase64, string $ipAddress): array
    {
        $tanggal = Carbon::today()->toDateString();
        $jamPulang = Carbon::now()->format('H:i:s');

        return DB::transaction(function () use ($pegawai, $signatureBase64, $ipAddress, $tanggal, $jamPulang) {
            $kehadiran = Kehadiran::where('pegawai_id', $pegawai->id)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->first();

            if (!$kehadiran || !$kehadiran->jam_masuk) {
                return [
                    'status'  => 'belum_masuk',
                    'message' => 'Anda belum melakukan absen masuk hari ini. Silakan absen masuk terlebih dahulu.',
                ];
            }

            if ($kehadiran->jam_pulang) {
                return [
                    'status'  => 'sudah_absen',
                    'message' => 'Anda sudah melakukan absen pulang hari ini pukul ' . substr($kehadiran->jam_pulang, 0, 5),
                    'data'    => $kehadiran,
                ];
            }

            $signaturePath = $this->simpanTandaTanganKeDisk($signatureBase64, 'pulang_' . $pegawai->id);
            $durasiMenit = Carbon::parse($kehadiran->jam_masuk)->diffInMinutes(Carbon::now());

            $kehadiran->update([
                'jam_pulang'           => $jamPulang,
                'durasi_kerja_menit'   => $durasiMenit,
                'tanda_tangan_pulang'  => $signaturePath,
                'ip_absensi_pulang'    => $ipAddress,
            ]);

            AuditLog::create([
                'user_name' => $pegawai->nama_lengkap,
                'role'      => $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa',
                'aktivitas' => "Absen pulang via tanda tangan web pukul {$jamPulang}, durasi {$durasiMenit} menit dari IP {$ipAddress}",
                'modul'     => 'Absensi Tanda Tangan',
                'ip_address'=> $ipAddress,
            ]);

            $this->catatWifiAccessLog(
                clientIp: $ipAddress,
                jenisAksi: 'absen_pulang',
                hasil: 'diizinkan',
                pegawaiId: $pegawai->id,
                matchedWifi: 'WiFi Terverifikasi'
            );

            return [
                'status'  => 'berhasil',
                'jenis'   => 'pulang',
                'message' => "Absen pulang berhasil dicatat pukul " . substr($jamPulang, 0, 5),
                'data'    => $kehadiran->fresh(),
            ];
        });
    }

    /**
     * Cek apakah IP termasuk dalam range CIDR.
     * Contoh: 192.168.1.50 dalam 192.168.1.0/24 → true
     */
    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $bits] = explode('/', $cidr);
        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = $bits > 0 ? ~((1 << (32 - (int)$bits)) - 1) : 0;
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
