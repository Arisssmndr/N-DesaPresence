<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kehadiran;
use App\Models\KonfigurasiAbsensi;
use App\Models\Pengumuman;
use App\Services\AbsensiSignatureService;
use Carbon\Carbon;

class StafPortalController extends Controller
{
    public function __construct(private AbsensiSignatureService $signatureService) {}

    public function beranda(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return view('staf.tanpa-pegawai', compact('user'));
        }

        $today = Carbon::today()->toDateString();
        $kehadiranHariIni = Kehadiran::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Cek WiFi Desa
        $clientIp = $this->signatureService->resolveClientIp($request);
        $wifiDiagnosis = $this->signatureService->getWifiDiagnosis($clientIp);
        $isWifiValid = $wifiDiagnosis['is_valid'];

        // Cek Waktu Absensi dari cache batch
        $jadwal = KonfigurasiAbsensi::getJadwal();
        $nowTime = Carbon::now()->format('H:i');
        $jamMasukMulai = $jadwal['jam_masuk_mulai'];
        $jamMasukSelesai = $jadwal['jam_masuk_selesai'];
        $jamPulangMulai = $jadwal['jam_pulang_mulai'];
        $jamPulangSelesai = $jadwal['jam_pulang_selesai'];

        $isWaktuMasuk = ($nowTime >= $jamMasukMulai && $nowTime <= $jamMasukSelesai);
        $isWaktuPulang = ($nowTime >= $jamPulangMulai && $nowTime <= $jamPulangSelesai);

        // Cek pengajuan absen luar hari ini
        $pengajuanHariIni = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Cek apakah pegawai memiliki izin / sakit aktif pada hari ini
        $izinAktifHariIni = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        // ─── Cek Surat Perintah Tugas (SPT) ─────────────────────────────────
        // 1. SPT yang menunggu respons staf (Terima / Tolak)
        $sptMenunggu = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'menunggu')
            ->where('status', '!=', 'ditolak')
            ->latest()
            ->get();

        // 2. SPT yang aktif hari ini (sudah diterima staf & disetujui)
        $sptAktifHariIni = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'diterima')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        // Jika ada SPT aktif hari ini, pastikan data kehadiran tercatat & kunci presensi mandiri
        if ($sptAktifHariIni) {
            if (!$kehadiranHariIni) {
                $kehadiranHariIni = Kehadiran::create([
                    'pegawai_id'          => $pegawai->id,
                    'tanggal'             => $today,
                    'status'              => 'Hadir',
                    'jam_masuk'           => '07:30:00',
                    'jam_pulang'          => '15:30:00',
                    'tanda_tangan_masuk'  => $sptAktifHariIni->tanda_tangan_staf,
                    'tanda_tangan_pulang' => $sptAktifHariIni->tanda_tangan_staf,
                    'sumber_data'         => 'manual_admin',
                    'diverifikasi_oleh'   => $sptAktifHariIni->disetujui_oleh ?? 1,
                    'keterangan'          => "Dinas Luar SPT: {$sptAktifHariIni->nomor_spt} ({$sptAktifHariIni->tujuan})"
                ]);
            }
        }

        $sudahAbsenMasukHariIni = (bool) ($kehadiranHariIni && $kehadiranHariIni->jam_masuk);
        $sudahAbsenPulangHariIni = (bool) ($kehadiranHariIni && $kehadiranHariIni->jam_pulang);

        // Jika pengajuan dinas luar hari ini sudah disetujui atau status kehadiran sudah Dinas Luar / Izin / Sakit / SPT Aktif
        $sudahDinasLuarAtauIzin = ($pengajuanHariIni && $pengajuanHariIni->status === 'disetujui') 
            || ($izinAktifHariIni !== null)
            || ($sptAktifHariIni !== null)
            || ($kehadiranHariIni && in_array(strtolower($kehadiranHariIni->status), ['dinas luar', 'izin', 'sakit']) && !$sudahAbsenMasukHariIni);

        $bisaAbsenMasuk = !$sudahDinasLuarAtauIzin && $isWifiValid && $isWaktuMasuk && !$sudahAbsenMasukHariIni;
        $bisaAbsenPulang = !$sudahDinasLuarAtauIzin && $isWifiValid && $isWaktuPulang && ($sudahAbsenMasukHariIni && !$sudahAbsenPulangHariIni);

        // Conflict Flags untuk Menu Absen Luar & Izin/Sakit
        $bisaAjukanAbsenLuar = true;
        $alasanKunciAbsenLuar = null;
        if ($sptAktifHariIni) {
            $bisaAjukanAbsenLuar = false;
            $alasanKunciAbsenLuar = "Terkunci: Anda sedang bertugas dalam Surat Perintah Tugas (SPT {$sptAktifHariIni->nomor_spt} - {$sptAktifHariIni->tujuan}). Presensi dinas otomatis tercatat Hadir.";
        } elseif ($sudahAbsenMasukHariIni) {
            $bisaAjukanAbsenLuar = false;
            $alasanKunciAbsenLuar = 'Terkunci: Anda sudah melakukan presensi langsung di kantor desa hari ini.';
        } elseif ($izinAktifHariIni) {
            $bisaAjukanAbsenLuar = false;
            $alasanKunciAbsenLuar = 'Terkunci: Anda tercatat memiliki izin/sakit aktif (' . ucfirst(str_replace('_', ' ', $izinAktifHariIni->jenis)) . ') hari ini.';
        }

        $bisaAjukanIzin = true;
        $alasanKunciIzin = null;
        if ($sptAktifHariIni) {
            $bisaAjukanIzin = false;
            $alasanKunciIzin = "Terkunci: Anda memiliki Surat Perintah Tugas (SPT {$sptAktifHariIni->nomor_spt}) yang aktif hari ini.";
        } elseif ($sudahAbsenMasukHariIni) {
            $bisaAjukanIzin = false;
            $alasanKunciIzin = 'Terkunci: Anda sudah tercatat hadir langsung di kantor hari ini.';
        } elseif ($izinAktifHariIni) {
            $bisaAjukanIzin = false;
            $alasanKunciIzin = 'Terkunci: Anda sudah memiliki permohonan izin/sakit aktif periode ini.';
        }

        // 5 riwayat kehadiran terakhir
        $riwayatTerakhir = Kehadiran::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->take(5)
            ->get();

        // Pengumuman aktif untuk perangkat / staf desa (disesuaikan dengan kategori target penerima)
        $pengumumans = Pengumuman::with('pembuat')
            ->where(function ($query) use ($today) {
                $query->where('is_pinned', true)
                      ->orWhereNull('berlaku_hingga')
                      ->orWhereDate('berlaku_hingga', '>=', $today);
            })
            ->where(function ($query) use ($pegawai) {
                $query->whereNull('target_penerima')
                      ->orWhere('target_penerima', 'semua')
                      ->orWhere('target_penerima', $pegawai->kategori_pegawai);
            })
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        // Cek apakah ada notifikasi persetujuan/penolakan pengajuan absen luar hari ini / kemarin
        $notifPengajuan = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->whereNotNull('diproses_pada')
            ->whereDate('diproses_pada', '>=', Carbon::yesterday())
            ->latest('diproses_pada')
            ->first();

        // Ambil Surat Perintah Tugas (SPT) resmi untuk pegawai yang bersangkutan
        $notifSpts = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where(function ($query) {
                $query->whereDate('tanggal_selesai', '>=', Carbon::today()->subDays(14))
                      ->orWhereDate('created_at', '>=', Carbon::today()->subDays(14));
            })
            ->latest()
            ->get();

        // ─── JADWAL PIKET DESA (H-1, Hari Ini, & Lepas Piket) ─────────────────
        $notifPikets = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal_piket', '>=', Carbon::yesterday())
            ->whereDate('tanggal_piket', '<=', Carbon::tomorrow())
            ->orderBy('tanggal_piket')
            ->get();

        // ─── SELURUH JADWAL PIKET SAYA (UNTUK MODAL KALENDER) ─────────────────
        $semuaJadwalPiket = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id)
            ->orderBy('tanggal_piket')
            ->get()
            ->map(function ($p) {
                return [
                    'id'              => $p->id,
                    'tanggal_piket'   => $p->tanggal_piket->toDateString(),
                    'jam_mulai'       => substr($p->jam_mulai, 0, 5),
                    'jam_selesai'     => substr($p->jam_selesai, 0, 5),
                    'keterangan'      => $p->keterangan,
                    'status'          => $p->status,
                    'is_sudah_masuk'  => $p->isSudahMasuk(),
                    'is_sudah_pulang' => $p->isSudahPulang(),
                    'is_selesai'      => $p->isSelesaiLengkap(),
                    'waktu_absen'     => $p->waktu_absen ? $p->waktu_absen->format('H:i') : null,
                    'waktu_pulang'    => $p->waktu_pulang ? $p->waktu_pulang->format('H:i') : null,
                ];
            });

        // Cek jika kemarin piket dan selesai hadir/pulang -> hari ini Lepas Piket
        $piketKemarin = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal_piket', Carbon::yesterday())
            ->where(function ($q) {
                $q->where('status', 'hadir')
                  ->orWhere('status', 'lepas_piket')
                  ->orWhereNotNull('waktu_pulang');
            })
            ->first();

        $isLepasPiketHariIni = false;
        if ($piketKemarin) {
            $isLepasPiketHariIni = true;
            // Otomatis pastikan kehadiran hari ini berstatus Lepas Piket HANYA jika belum ada absen fisik
            if (!$kehadiranHariIni) {
                $kehadiranHariIni = Kehadiran::create([
                    'pegawai_id'          => $pegawai->id,
                    'tanggal'             => $today,
                    'status'              => 'Hadir',
                    'jam_masuk'           => $piketKemarin->waktu_absen ? $piketKemarin->waktu_absen->format('H:i:s') : '07:30:00',
                    'jam_pulang'          => $piketKemarin->waktu_pulang ? $piketKemarin->waktu_pulang->format('H:i:s') : null,
                    'tanda_tangan_masuk'  => $piketKemarin->tanda_tangan,
                    'tanda_tangan_pulang' => $piketKemarin->tanda_tangan_pulang,
                    'sumber_data'         => 'manual_admin',
                    'diverifikasi_oleh'   => $piketKemarin->created_by ?? 1,
                    'keterangan'          => 'Lepas Piket (Tugas Piket Malam tgl ' . $piketKemarin->tanggal_piket->format('d/m/Y') . ')'
                ]);
            } elseif (!$kehadiranHariIni->jam_masuk && !str_contains($kehadiranHariIni->keterangan ?? '', 'Lepas Piket') && $kehadiranHariIni->sumber_data === 'manual_admin') {
                $kehadiranHariIni->update([
                    'status'              => 'Hadir',
                    'jam_masuk'           => $piketKemarin->waktu_absen ? $piketKemarin->waktu_absen->format('H:i:s') : '07:30:00',
                    'jam_pulang'          => $piketKemarin->waktu_pulang ? $piketKemarin->waktu_pulang->format('H:i:s') : null,
                    'tanda_tangan_masuk'  => $piketKemarin->tanda_tangan,
                    'tanda_tangan_pulang' => $piketKemarin->tanda_tangan_pulang,
                    'keterangan'          => 'Lepas Piket (Tugas Piket Malam tgl ' . $piketKemarin->tanggal_piket->format('d/m/Y') . ')'
                ]);
            }
        }

        return view('staf.beranda', compact(
            'user',
            'pegawai',
            'kehadiranHariIni',
            'pengajuanHariIni',
            'izinAktifHariIni',
            'sptMenunggu',
            'sptAktifHariIni',
            'isWifiValid',
            'clientIp',
            'wifiDiagnosis',
            'isWaktuMasuk',
            'isWaktuPulang',
            'bisaAbsenMasuk',
            'bisaAbsenPulang',
            'bisaAjukanAbsenLuar',
            'alasanKunciAbsenLuar',
            'bisaAjukanIzin',
            'alasanKunciIzin',
            'jamMasukMulai',
            'jamMasukSelesai',
            'jamPulangMulai',
            'jamPulangSelesai',
            'riwayatTerakhir',
            'pengumumans',
            'notifPengajuan',
            'notifSpts',
            'notifPikets',
            'semuaJadwalPiket',
            'isLepasPiketHariIni',
            'piketKemarin'
        ));
    }

    public function terimaSpt(Request $request, int $id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun tidak terhubung dengan pegawai.');
        }

        $validated = $request->validate([
            'tanda_tangan' => 'required|string|min:50',
        ], [
            'tanda_tangan.required' => 'Goreskan tanda tangan digital untuk konfirmasi penerimaan SPT.',
            'tanda_tangan.min'      => 'Goreskan tanda tangan digital dengan jelas.',
        ]);

        $spt = \App\Models\SuratPerintahTugas::where('id', $id)
            ->where('pegawai_id', $pegawai->id)
            ->firstOrFail();

        \Illuminate\Support\Facades\DB::transaction(function () use ($spt, $validated, $user, $pegawai) {
            $spt->update([
                'status'              => 'disetujui',
                'respons_staf'        => 'diterima',
                'waktu_respons_staf'  => now(),
                'tanda_tangan_staf'   => $validated['tanda_tangan'],
                'tanggal_persetujuan' => $spt->tanggal_persetujuan ?? now(),
                'disetujui_oleh'      => $spt->disetujui_oleh ?? 1,
            ]);

            // Terapkan kehadiran otomatis untuk seluruh rentang tanggal SPT
            $spt->terapkanKehadiran($validated['tanda_tangan'], $user->id);

            \App\Models\AuditLog::create([
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'role'      => $user->role ?? 'perangkat',
                'aktivitas' => "Menerima penugasan SPT {$spt->nomor_spt} (Tujuan: {$spt->tujuan}, Periode: {$spt->tanggal_mulai->format('d/m/Y')} - {$spt->tanggal_selesai->format('d/m/Y')})",
                'modul'     => 'Surat Perintah Tugas',
            ]);
        });

        return redirect()->route('staf.beranda')->with('success', "Penugasan SPT {$spt->nomor_spt} berhasil diterima! Presensi dinas luar telah otomatis tercatat Hadir.");
    }

    public function tolakSpt(Request $request, int $id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun tidak terhubung dengan pegawai.');
        }

        $validated = $request->validate([
            'alasan_tolak' => 'required|string|min:5|max:500',
        ], [
            'alasan_tolak.required' => 'Wajib mengisi alasan penolakan SPT.',
            'alasan_tolak.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $spt = \App\Models\SuratPerintahTugas::where('id', $id)
            ->where('pegawai_id', $pegawai->id)
            ->firstOrFail();

        \Illuminate\Support\Facades\DB::transaction(function () use ($spt, $validated, $user, $pegawai) {
            $spt->update([
                'status'             => 'ditolak',
                'respons_staf'       => 'ditolak',
                'waktu_respons_staf' => now(),
                'alasan_tolak_staf'  => $validated['alasan_tolak'],
            ]);

            // Batalkan catatan kehadiran SPT jika pernah ada
            $spt->batalkanKehadiran();

            \App\Models\AuditLog::create([
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'role'      => $user->role ?? 'perangkat',
                'aktivitas' => "Menolak penugasan SPT {$spt->nomor_spt} (Alasan: {$validated['alasan_tolak']})",
                'modul'     => 'Surat Perintah Tugas',
            ]);
        });

        return redirect()->route('staf.beranda')->with('info', "Penolakan SPT {$spt->nomor_spt} telah dikirimkan ke Admin Desa.");
    }

    public function absenPiket(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun tidak terhubung dengan pegawai.');
        }

        $validated = $request->validate([
            'piket_id'        => 'required|exists:jadwal_pikets,id',
            'tipe'            => 'nullable|in:masuk,pulang',
            'tanda_tangan'    => 'required|string',
        ], [
            'tanda_tangan.required' => 'Goreskan tanda tangan digital untuk konfirmasi kehadiran piket.',
        ]);

        $piket = \App\Models\JadwalPiket::where('id', $validated['piket_id'])
            ->where('pegawai_id', $pegawai->id)
            ->firstOrFail();

        $tipe = $validated['tipe'] ?? ($piket->isSudahMasuk() ? 'pulang' : 'masuk');
        $clientIp = $this->signatureService->resolveClientIp($request);

        if ($tipe === 'masuk') {
            if (!$piket->isWaktuMasukTiba()) {
                $jamBuka = substr($piket->jam_mulai, 0, 5);
                return redirect()->route('staf.beranda')->with('error', "Waktu absen hadir/masuk piket belum dimulai. Absen dibuka mulai pukul {$jamBuka} WIB.");
            }
            if ($piket->isSudahMasuk()) {
                $jamMasuk = $piket->waktu_absen ? $piket->waktu_absen->format('H:i') : '-';
                return redirect()->route('staf.beranda')->with('info', "Anda sudah melakukan absen masuk piket pada pukul {$jamMasuk} WIB.");
            }

            $piket->update([
                'status'       => 'sedang_piket',
                'tanda_tangan' => $validated['tanda_tangan'],
                'waktu_absen'  => now(),
                'ip_absen'     => $clientIp,
            ]);

            \App\Models\AuditLog::create([
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'role'      => $user->role ?? 'perangkat',
                'aktivitas' => "Mengisi absensi MASUK piket tanggal " . $piket->tanggal_piket->format('d/m/Y'),
                'modul'     => 'Jadwal Piket',
            ]);

            return redirect()->route('staf.beranda')->with('success', 'Absen MASUK piket berhasil dicatat! Selamat bertugas. Jangan lupa mengisi absen pulang saat jam piket selesai.');
        } else { // PULANG
            if (!$piket->isSudahMasuk()) {
                return redirect()->route('staf.beranda')->with('error', 'Harap lakukan absen masuk piket terlebih dahulu sebelum melakukan absen pulang.');
            }
            if (!$piket->isWaktuPulangTiba()) {
                $jamSelesai = substr($piket->jam_selesai, 0, 5);
                return redirect()->route('staf.beranda')->with('error', "Waktu piket belum selesai. Absen pulang baru dapat dilakukan mulai pukul {$jamSelesai} WIB.");
            }
            if ($piket->isSudahPulang()) {
                $jamPulang = $piket->waktu_pulang ? $piket->waktu_pulang->format('H:i') : '-';
                return redirect()->route('staf.beranda')->with('info', "Anda sudah melakukan absen pulang piket pada pukul {$jamPulang} WIB.");
            }

            $piket->update([
                'status'               => 'hadir',
                'tanda_tangan_pulang'  => $validated['tanda_tangan'],
                'waktu_pulang'         => now(),
                'ip_pulang'            => $clientIp,
            ]);

            // Otomatis tandai presensi hari berikutnya sebagai "Lepas Piket (Hadir)" dengan tanda tangan bukti lengkap
            $tglLepasPiket = $piket->waktu_selesai_datetime->toDateString();
            $kehadiranLepasPiket = Kehadiran::firstOrNew([
                'pegawai_id' => $pegawai->id,
                'tanggal'    => $tglLepasPiket,
            ]);
            $kehadiranLepasPiket->status              = 'Hadir';
            $kehadiranLepasPiket->jam_masuk           = $piket->waktu_absen ? $piket->waktu_absen->format('H:i:s') : '07:30:00';
            $kehadiranLepasPiket->jam_pulang          = now()->format('H:i:s');
            $kehadiranLepasPiket->tanda_tangan_masuk  = $piket->tanda_tangan;
            $kehadiranLepasPiket->tanda_tangan_pulang = $validated['tanda_tangan'];
            $kehadiranLepasPiket->sumber_data         = 'manual_admin';
            $kehadiranLepasPiket->keterangan          = "Lepas Piket (Tugas Piket Malam tgl " . $piket->tanggal_piket->format('d/m/Y') . ")";
            $kehadiranLepasPiket->save();

            \App\Models\AuditLog::create([
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'role'      => $user->role ?? 'perangkat',
                'aktivitas' => "Mengisi absensi PULANG piket tanggal " . $piket->tanggal_piket->format('d/m/Y') . " (Selesai piket)",
                'modul'     => 'Jadwal Piket',
            ]);

            return redirect()->route('staf.beranda')->with('success', 'Absen PULANG piket berhasil dicatat! Tugas piket selesai, status kehadiran Anda otomatis Hadir / Lepas Piket.');
        }
    }

    public function halamanAbsen(Request $request, string $jenis)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda');
        }

        $today = Carbon::today()->toDateString();

        // Cek jika sedang ada SPT aktif hari ini
        $sptAktifHariIni = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'diterima')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        if ($sptAktifHariIni) {
            return redirect()->route('staf.beranda')->with('error', "Akses Ditolak: Anda tercatat sedang bertugas dalam Surat Perintah Tugas (SPT {$sptAktifHariIni->nomor_spt} - {$sptAktifHariIni->tujuan}). Presensi dinas luar telah otomatis tercatat Hadir.");
        }

        // Cek jika sedang ada izin aktif hari ini
        $izinAktifHariIni = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        if ($izinAktifHariIni) {
            return redirect()->route('staf.beranda')->with('error', 'Akses Ditolak: Anda tercatat memiliki izin/sakit aktif (' . ucfirst(str_replace('_', ' ', $izinAktifHariIni->jenis)) . ') hari ini.');
        }

        $clientIp = $this->signatureService->resolveClientIp($request);
        $wifiDiagnosis = $this->signatureService->getWifiDiagnosis($clientIp);

        if (!$wifiDiagnosis['is_valid']) {
            return redirect()->route('staf.beranda')->with('error', 'Akses Ditolak: Anda sedang menggunakan koneksi di luar WiFi Kantor Desa (IP: ' . $clientIp . '). Hubungkan ke WiFi Kantor Desa atau ajukan Absen Luar jika sedang dinas luar.');
        }

        $jadwal = KonfigurasiAbsensi::getJadwal();
        $nowTime = Carbon::now()->format('H:i');
        $jamMasukMulai = $jadwal['jam_masuk_mulai'];
        $jamMasukSelesai = $jadwal['jam_masuk_selesai'];
        $jamPulangMulai = $jadwal['jam_pulang_mulai'];
        $jamPulangSelesai = $jadwal['jam_pulang_selesai'];

        if ($jenis === 'masuk' && ($nowTime < $jamMasukMulai || $nowTime > $jamMasukSelesai)) {
            return redirect()->route('staf.beranda')->with('error', "Belum/sudah lewat waktu absen masuk ({$jamMasukMulai} - {$jamMasukSelesai}).");
        }

        if ($jenis === 'pulang' && ($nowTime < $jamPulangMulai || $nowTime > $jamPulangSelesai)) {
            return redirect()->route('staf.beranda')->with('error', "Belum/sudah lewat waktu absen pulang ({$jamPulangMulai} - {$jamPulangSelesai}).");
        }

        return view('staf.absen', compact('user', 'pegawai', 'jenis', 'clientIp', 'wifiDiagnosis'));
    }

    public function wifiStatus(Request $request)
    {
        $clientIp = $this->signatureService->resolveClientIp($request);
        $wifiDiagnosis = $this->signatureService->getWifiDiagnosis($clientIp);

        return response()->json([
            'valid'           => $wifiDiagnosis['is_valid'],
            'client_ip'       => $clientIp,
            'matched_network' => $wifiDiagnosis['matched_network'],
            'diagnosis'       => $wifiDiagnosis,
        ]);
    }

    public function submitAbsen(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,pulang',
            'tanda_tangan' => 'required|string|min:100',
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return response()->json(['status' => 'error', 'message' => 'Data profil pegawai tidak ditemukan.'], 422);
        }

        $today = Carbon::today()->toDateString();

        // Cek jika sedang ada SPT aktif hari ini
        $sptAktifHariIni = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'diterima')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        if ($sptAktifHariIni) {
            return response()->json([
                'status'  => 'error',
                'message' => "Akses ditolak: Anda tercatat sedang bertugas dalam Surat Perintah Tugas (SPT {$sptAktifHariIni->nomor_spt} - {$sptAktifHariIni->tujuan}). Presensi dinas luar otomatis tercatat Hadir."
            ], 403);
        }

        // Cek jika sedang ada izin aktif hari ini
        $izinAktifHariIni = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        if ($izinAktifHariIni) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak: Anda tercatat memiliki izin/sakit aktif (' . ucfirst(str_replace('_', ' ', $izinAktifHariIni->jenis)) . ') pada hari ini.'
            ], 403);
        }

        $clientIp = $this->signatureService->resolveClientIp($request);
        $wifiDiagnosis = $this->signatureService->getWifiDiagnosis($clientIp);

        // Validasi WiFi
        if (!$wifiDiagnosis['is_valid']) {
            $this->signatureService->catatWifiAccessLog(
                clientIp: $clientIp,
                jenisAksi: $request->jenis === 'masuk' ? 'absen_masuk' : 'absen_pulang',
                hasil: 'ditolak',
                pegawaiId: $pegawai->id,
                alasanDitolak: $wifiDiagnosis['rejection_reason'] ?? 'IP tidak terdaftar di WiFi Kantor Desa',
                matchedWifi: null,
                userAgent: $request->userAgent()
            );

            return response()->json([
                'status'    => 'error',
                'ip'        => $clientIp,
                'message'   => 'Akses ditolak: Presensi langsung hanya dapat dilakukan saat terhubung ke WiFi Kantor Desa Nangtang (IP Anda: ' . $clientIp . '). Jika sedang bertugas dinas luar, silakan gunakan fitur Pengajuan Absen Luar.',
                'diagnosis' => $wifiDiagnosis,
            ], 403);
        }

        if ($request->jenis === 'masuk') {
            $hasil = $this->signatureService->prosesAbsenMasuk($pegawai, $request->tanda_tangan, $clientIp);
        } else {
            $hasil = $this->signatureService->prosesAbsenPulang($pegawai, $request->tanda_tangan, $clientIp);
        }

        return response()->json($hasil, $hasil['status'] === 'berhasil' ? 200 : 422);
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun tidak terhubung dengan data pegawai.');
        }

        $tab = $request->query('tab', 'presensi'); // presensi | izin | absen_luar | spt

        $riwayats = Kehadiran::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->paginate(15, ['*'], 'presensi_page');

        $riwayatIzin = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->latest()
            ->paginate(15, ['*'], 'izin_page');

        $riwayatAbsenLuar = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->paginate(15, ['*'], 'absen_luar_page');

        $riwayatSpt = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->with(['pembuat'])
            ->latest()
            ->paginate(15, ['*'], 'spt_page');

        $countPresensi = Kehadiran::where('pegawai_id', $pegawai->id)->count();
        $countIzin = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)->count();
        $countAbsenLuar = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)->count();
        $countSpt = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)->count();

        return view('staf.riwayat', compact(
            'user',
            'pegawai',
            'riwayats',
            'riwayatIzin',
            'riwayatAbsenLuar',
            'riwayatSpt',
            'tab',
            'countPresensi',
            'countIzin',
            'countAbsenLuar',
            'countSpt'
        ));
    }

    public function profil()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun tidak terhubung dengan data pegawai.');
        }

        // Statistik kedisiplinan dan absensi staf
        $totalHadir = Kehadiran::where('pegawai_id', $pegawai->id)
            ->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat'])
            ->count();

        $totalIzin = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->where('jenis', 'not like', '%sakit%')
            ->count();

        $totalSakit = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->where('jenis', 'like', '%sakit%')
            ->count();

        $totalAbsenLuar = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->count();

        $totalSpt = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'diterima')
            ->count();

        // Cari tanda tangan digital terbaru dari staf
        $spesimenTtd = $pegawai->tanda_tangan
            ?? Kehadiran::where('pegawai_id', $pegawai->id)
            ->whereNotNull('tanda_tangan_masuk')
            ->latest('tanggal')
            ->value('tanda_tangan_masuk')
            ?? \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->whereNotNull('tanda_tangan_staf')
            ->latest()
            ->value('tanda_tangan_staf')
            ?? \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereNotNull('tanda_tangan')
            ->latest()
            ->value('tanda_tangan');

        return view('staf.profil', compact(
            'user',
            'pegawai',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAbsenLuar',
            'totalSpt',
            'spesimenTtd'
        ));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $request->validate([
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'nama_lengkap' => 'required|string|max:100',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'no_hp' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:255',
        ]);

        $userData = [
            'username' => strtolower($request->username),
            'name' => $request->nama_lengkap,
        ];

        $pegawaiData = [
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
        ];

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('foto-profil', 'public');
            $userData['foto_profil'] = $path;
            $pegawaiData['foto_profil'] = $path;
        }

        // Update User
        $user->update($userData);

        // Update Pegawai
        if ($pegawai) {
            $pegawai->update($pegawaiData);
        }

        return redirect()->route('staf.profil')->with('success', 'Profil dan foto berhasil diperbarui.');
    }

    public function riwayatSpt(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun belum terhubung dengan data pegawai.');
        }

        $filterStatus = $request->query('status', 'semua');

        $query = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->with(['pembuat'])
            ->latest();

        if ($filterStatus === 'menunggu') {
            $query->where('respons_staf', 'menunggu')->where('status', '!=', 'ditolak');
        } elseif ($filterStatus === 'diterima') {
            $query->where('respons_staf', 'diterima');
        } elseif ($filterStatus === 'ditolak') {
            $query->where(function ($q) {
                $q->where('respons_staf', 'ditolak')
                  ->orWhere('status', 'ditolak');
            });
        }

        $spts = $query->paginate(10)->withQueryString();

        $countSemua = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)->count();
        $countMenunggu = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)->where('respons_staf', 'menunggu')->where('status', '!=', 'ditolak')->count();
        $countDiterima = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)->where('respons_staf', 'diterima')->count();
        $countDitolak = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)->where(function ($q) {
            $q->where('respons_staf', 'ditolak')->orWhere('status', 'ditolak');
        })->count();

        return view('staf.spt-riwayat', compact(
            'user',
            'pegawai',
            'spts',
            'filterStatus',
            'countSemua',
            'countMenunggu',
            'countDiterima',
            'countDitolak'
        ));
    }
}
