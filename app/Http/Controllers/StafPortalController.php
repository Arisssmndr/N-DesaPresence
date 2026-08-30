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

        // Jika pengajuan dinas luar hari ini sudah disetujui atau status kehadiran sudah Dinas Luar / Izin / Sakit
        $sudahDinasLuarAtauIzin = ($pengajuanHariIni && $pengajuanHariIni->status === 'disetujui') 
            || ($kehadiranHariIni && in_array(strtolower($kehadiranHariIni->status), ['dinas luar', 'izin', 'sakit']));

        $bisaAbsenMasuk = !$sudahDinasLuarAtauIzin && $isWifiValid && $isWaktuMasuk && (!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk);
        $bisaAbsenPulang = !$sudahDinasLuarAtauIzin && $isWifiValid && $isWaktuPulang && ($kehadiranHariIni && $kehadiranHariIni->jam_masuk && !$kehadiranHariIni->jam_pulang);

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
            ->whereDate('tanggal_piket', '<=', Carbon::today()->addDays(7))
            ->orderBy('tanggal_piket')
            ->get();

        // Cek jika kemarin piket dan hadir -> hari ini Lepas Piket
        $piketKemarin = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal_piket', Carbon::yesterday())
            ->where('status', 'hadir')
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
                    'tanda_tangan_masuk'  => $piketKemarin->tanda_tangan,
                    'sumber_data'         => 'manual_admin',
                    'diverifikasi_oleh'   => $piketKemarin->created_by ?? 1,
                    'keterangan'          => 'Lepas Piket (Tugas Piket Malam tgl ' . $piketKemarin->tanggal_piket->format('d/m/Y') . ')'
                ]);
            } elseif (!$kehadiranHariIni->jam_masuk && !str_contains($kehadiranHariIni->keterangan ?? '', 'Lepas Piket') && $kehadiranHariIni->sumber_data === 'manual_admin') {
                $kehadiranHariIni->update([
                    'status'              => 'Hadir',
                    'jam_masuk'           => $piketKemarin->waktu_absen ? $piketKemarin->waktu_absen->format('H:i:s') : '07:30:00',
                    'tanda_tangan_masuk'  => $piketKemarin->tanda_tangan,
                    'keterangan'          => 'Lepas Piket (Tugas Piket Malam tgl ' . $piketKemarin->tanggal_piket->format('d/m/Y') . ')'
                ]);
            }
        }

        return view('staf.beranda', compact(
            'user',
            'pegawai',
            'kehadiranHariIni',
            'pengajuanHariIni',
            'isWifiValid',
            'clientIp',
            'wifiDiagnosis',
            'isWaktuMasuk',
            'isWaktuPulang',
            'bisaAbsenMasuk',
            'bisaAbsenPulang',
            'jamMasukMulai',
            'jamMasukSelesai',
            'jamPulangMulai',
            'jamPulangSelesai',
            'riwayatTerakhir',
            'pengumumans',
            'notifPengajuan',
            'notifSpts',
            'notifPikets',
            'isLepasPiketHariIni',
            'piketKemarin'
        ));
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
            'tanda_tangan'    => 'required|string',
        ], [
            'tanda_tangan.required' => 'Goreskan tanda tangan digital untuk konfirmasi kehadiran piket.',
        ]);

        $piket = \App\Models\JadwalPiket::where('id', $validated['piket_id'])
            ->where('pegawai_id', $pegawai->id)
            ->firstOrFail();

        $clientIp = $this->signatureService->resolveClientIp($request);

        $piket->update([
            'status'       => 'hadir',
            'tanda_tangan' => $validated['tanda_tangan'],
            'waktu_absen'  => now(),
            'ip_absen'     => $clientIp,
        ]);

        // Otomatis tandai presensi hari berikutnya sebagai "Lepas Piket" dengan tanda tangan bukti
        $besokStr = Carbon::parse($piket->tanggal_piket)->addDay()->toDateString();
        $kehadiranBesok = Kehadiran::firstOrNew([
            'pegawai_id' => $pegawai->id,
            'tanggal'    => $besokStr,
        ]);
        $kehadiranBesok->status             = 'Hadir';
        $kehadiranBesok->jam_masuk          = now()->format('H:i:s');
        $kehadiranBesok->tanda_tangan_masuk = $validated['tanda_tangan'];
        $kehadiranBesok->sumber_data        = 'manual_admin';
        $kehadiranBesok->keterangan         = "Lepas Piket (Tugas Piket Malam tgl " . $piket->tanggal_piket->format('d/m/Y') . ")";
        $kehadiranBesok->save();

        \App\Models\AuditLog::create([
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $user->role ?? 'perangkat',
            'aktivitas' => "Mengisi absensi & tanda tangan piket tanggal " . $piket->tanggal_piket->format('d/m/Y'),
            'modul'     => 'Jadwal Piket',
        ]);

        return redirect()->route('staf.beranda')->with('success', 'Absensi tugas piket berhasil tercatat! Status presensi hari berikutnya otomatis berstatus Lepas Piket.');
    }

    public function halamanAbsen(Request $request, string $jenis)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda');
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
            return redirect()->route('staf.beranda');
        }

        $tab = $request->query('tab', 'presensi'); // presensi | izin | absen_luar

        $riwayats = Kehadiran::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->paginate(15, ['*'], 'presensi_page');

        $riwayatIzin = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->latest()
            ->paginate(15, ['*'], 'izin_page');

        $riwayatAbsenLuar = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->paginate(15, ['*'], 'absen_luar_page');

        return view('staf.riwayat', compact('user', 'pegawai', 'riwayats', 'riwayatIzin', 'riwayatAbsenLuar', 'tab'));
    }

    public function profil()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        return view('staf.profil', compact('user', 'pegawai'));
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
}
