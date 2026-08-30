<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Services\AbsensiSignatureService;
use Carbon\Carbon;

class PortalAbsensiController extends Controller
{
    public function __construct(private AbsensiSignatureService $absensiService) {}

    /**
     * Tampilkan halaman portal absensi mobile.
     * Catatan: Portal ini hanya bisa diakses dari jaringan WiFi Desa (sama seperti portal staf).
     */
    public function index(Request $request)
    {
        $clientIp    = $this->absensiService->resolveClientIp($request);
        $isWifiValid = $this->absensiService->validasiIpWifi($clientIp);

        $pegawais = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get();

        $today = Carbon::today()->toDateString();

        return view('portal.absensi', compact('pegawais', 'clientIp', 'today', 'isWifiValid'));
    }

    /**
     * Status absensi hari ini untuk pegawai tertentu (AJAX).
     */
    public function statusHariIni(Request $request)
    {
        $request->validate(['pegawai_id' => 'required|exists:pegawais,id']);

        $today    = Carbon::today()->toDateString();
        $kehadiran = Kehadiran::where('pegawai_id', $request->pegawai_id)
            ->where('tanggal', $today)
            ->with('pegawai.jabatan')
            ->first();

        return response()->json([
            'sudah_masuk'  => $kehadiran && $kehadiran->jam_masuk,
            'sudah_pulang' => $kehadiran && $kehadiran->jam_pulang,
            'jam_masuk'    => $kehadiran?->jam_masuk ? substr($kehadiran->jam_masuk, 0, 5) : null,
            'jam_pulang'   => $kehadiran?->jam_pulang ? substr($kehadiran->jam_pulang, 0, 5) : null,
            'status'       => $kehadiran?->status,
        ]);
    }

    /**
     * Status koneksi WiFi klien saat ini — digunakan JS polling portal.
     * Sangat ringan & rate limited (30/menit).
     */
    public function wifiStatus(Request $request)
    {
        $clientIp  = $this->absensiService->resolveClientIp($request);
        $diagnosis = $this->absensiService->getWifiDiagnosis($clientIp);

        return response()->json([
            'valid'           => $diagnosis['is_valid'],
            'client_ip'       => $clientIp,
            'matched_network' => $diagnosis['matched_network'],
            'message'         => $diagnosis['is_valid']
                ? 'Terhubung ke ' . ($diagnosis['matched_network'] ?? 'WiFi Kantor Desa Nangtang')
                : ($diagnosis['rejection_reason'] ?? 'Tidak terhubung ke WiFi Kantor Desa Nangtang.'),
        ]);
    }

    /**
     * Proses absen masuk dengan tanda tangan.
     * WAJIB terhubung ke WiFi Desa — divalidasi ketat di sisi backend.
     */
    public function absenMasuk(Request $request)
    {
        $request->validate([
            'pegawai_id'   => 'required|exists:pegawais,id',
            'tanda_tangan' => 'required|string|min:100',
        ]);

        // ─── Validasi Jaringan WiFi Desa (anti-spoofing) ──────────────────────
        $clientIp = $this->absensiService->resolveClientIp($request);

        if (!$this->absensiService->validasiIpWifi($clientIp)) {
            $msg = 'Akses ditolak: Presensi langsung hanya dapat dilakukan saat terhubung ke WiFi Kantor Desa Nangtang (IP Anda: ' . $clientIp . ').';
            
            $this->absensiService->catatWifiAccessLog(
                clientIp: $clientIp,
                jenisAksi: 'absen_masuk',
                hasil: 'ditolak',
                pegawaiId: (int) $request->pegawai_id,
                alasanDitolak: $msg,
                userAgent: $request->userAgent()
            );

            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg, 'ip' => $clientIp], 403);
            }
            return back()->with('error', $msg);
        }
        // ─────────────────────────────────────────────────────────────────────

        $pegawai = Pegawai::with('jabatan')->findOrFail($request->pegawai_id);
        $hasil   = $this->absensiService->prosesAbsenMasuk($pegawai, $request->tanda_tangan, $clientIp);

        if ($request->expectsJson()) {
            return response()->json($hasil, $hasil['status'] === 'berhasil' ? 200 : 422);
        }

        return back()->with($hasil['status'] === 'berhasil' ? 'success' : 'error', $hasil['message']);
    }

    /**
     * Proses absen pulang dengan tanda tangan.
     * WAJIB terhubung ke WiFi Desa — divalidasi ketat di sisi backend.
     */
    public function absenPulang(Request $request)
    {
        $request->validate([
            'pegawai_id'   => 'required|exists:pegawais,id',
            'tanda_tangan' => 'required|string|min:100',
        ]);

        // ─── Validasi Jaringan WiFi Desa (anti-spoofing) ──────────────────────
        $clientIp = $this->absensiService->resolveClientIp($request);

        if (!$this->absensiService->validasiIpWifi($clientIp)) {
            $msg = 'Akses ditolak: Presensi langsung hanya dapat dilakukan saat terhubung ke WiFi Kantor Desa Nangtang (IP Anda: ' . $clientIp . ').';

            $this->absensiService->catatWifiAccessLog(
                clientIp: $clientIp,
                jenisAksi: 'absen_pulang',
                hasil: 'ditolak',
                pegawaiId: (int) $request->pegawai_id,
                alasanDitolak: $msg,
                userAgent: $request->userAgent()
            );

            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg, 'ip' => $clientIp], 403);
            }
            return back()->with('error', $msg);
        }
        // ─────────────────────────────────────────────────────────────────────

        $pegawai = Pegawai::with('jabatan')->findOrFail($request->pegawai_id);
        $hasil   = $this->absensiService->prosesAbsenPulang($pegawai, $request->tanda_tangan, $clientIp);

        if ($request->expectsJson()) {
            return response()->json($hasil, $hasil['status'] === 'berhasil' ? 200 : 422);
        }

        return back()->with($hasil['status'] === 'berhasil' ? 'success' : 'error', $hasil['message']);
    }
}

