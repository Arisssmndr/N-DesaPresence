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
            ->where('tanggal', $today)
            ->first();

        // Cek WiFi Desa
        $clientIp = $this->signatureService->resolveClientIp($request);
        $isWifiValid = $this->signatureService->validasiIpWifi($clientIp);

        // Cek Waktu Absensi dari cache batch
        $jadwal = KonfigurasiAbsensi::getJadwal();
        $nowTime = Carbon::now()->format('H:i');
        $jamMasukMulai = $jadwal['jam_masuk_mulai'];
        $jamMasukSelesai = $jadwal['jam_masuk_selesai'];
        $jamPulangMulai = $jadwal['jam_pulang_mulai'];
        $jamPulangSelesai = $jadwal['jam_pulang_selesai'];

        $isWaktuMasuk = ($nowTime >= $jamMasukMulai && $nowTime <= $jamMasukSelesai);
        $isWaktuPulang = ($nowTime >= $jamPulangMulai && $nowTime <= $jamPulangSelesai);

        $bisaAbsenMasuk = $isWifiValid && $isWaktuMasuk && (!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk);
        $bisaAbsenPulang = $isWifiValid && $isWaktuPulang && ($kehadiranHariIni && $kehadiranHariIni->jam_masuk && !$kehadiranHariIni->jam_pulang);

        // 5 riwayat kehadiran terakhir
        $riwayatTerakhir = Kehadiran::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->take(5)
            ->get();

        // Pengumuman aktif untuk perangkat / staf desa
        $pengumumans = Pengumuman::with('pembuat')
            ->where(function ($query) use ($today) {
                $query->where('is_pinned', true)
                      ->orWhereNull('berlaku_hingga')
                      ->orWhereDate('berlaku_hingga', '>=', $today);
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

        return view('staf.beranda', compact(
            'user',
            'pegawai',
            'kehadiranHariIni',
            'isWifiValid',
            'clientIp',
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
            'notifPengajuan'
        ));
    }

    public function halamanAbsen(Request $request, string $jenis)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda');
        }

        $clientIp = $this->signatureService->resolveClientIp($request);
        $isWifiValid = $this->signatureService->validasiIpWifi($clientIp);

        if (!$isWifiValid) {
            return redirect()->route('staf.beranda')->with('error', 'Absensi hanya bisa dilakukan jika terhubung ke WiFi Desa Nangtang.');
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

        return view('staf.absen', compact('user', 'pegawai', 'jenis', 'clientIp'));
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

        // Validasi WiFi
        if (!$this->signatureService->validasiIpWifi($clientIp)) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Anda harus terhubung ke jaringan WiFi Desa Nangtang.'], 403);
        }

        if ($request->jenis === 'masuk') {
            $hasil = $this->signatureService->prosesAbsenMasuk($pegawai, $request->tanda_tangan, $clientIp);
        } else {
            $hasil = $this->signatureService->prosesAbsenPulang($pegawai, $request->tanda_tangan, $clientIp);
        }

        return response()->json($hasil, $hasil['status'] === 'berhasil' ? 200 : 422);
    }

    public function riwayat()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda');
        }

        $riwayats = Kehadiran::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->paginate(15);

        return view('staf.riwayat', compact('user', 'pegawai', 'riwayats'));
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
