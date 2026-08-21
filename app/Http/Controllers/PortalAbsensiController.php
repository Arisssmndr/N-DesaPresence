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
     */
    public function index(Request $request)
    {
        $pegawais = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get();

        $clientIp = $this->getClientIp($request);
        $today = Carbon::today()->toDateString();

        return view('portal.absensi', compact('pegawais', 'clientIp', 'today'));
    }

    /**
     * Status absensi hari ini untuk pegawai tertentu (AJAX).
     */
    public function statusHariIni(Request $request)
    {
        $request->validate(['pegawai_id' => 'required|exists:pegawais,id']);

        $today = Carbon::today()->toDateString();
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
     * Proses absen masuk dengan tanda tangan.
     */
    public function absenMasuk(Request $request)
    {
        $request->validate([
            'pegawai_id'    => 'required|exists:pegawais,id',
            'tanda_tangan'  => 'required|string|min:100', // base64 SVG/PNG minimal
        ]);

        $pegawai   = Pegawai::with('jabatan')->findOrFail($request->pegawai_id);
        $clientIp  = $this->getClientIp($request);
        $hasil     = $this->absensiService->prosesAbsenMasuk($pegawai, $request->tanda_tangan, $clientIp);

        if ($request->expectsJson()) {
            return response()->json($hasil, $hasil['status'] === 'berhasil' ? 200 : 422);
        }

        return back()->with($hasil['status'] === 'berhasil' ? 'success' : 'error', $hasil['message']);
    }

    /**
     * Proses absen pulang dengan tanda tangan.
     */
    public function absenPulang(Request $request)
    {
        $request->validate([
            'pegawai_id'    => 'required|exists:pegawais,id',
            'tanda_tangan'  => 'required|string|min:100',
        ]);

        $pegawai   = Pegawai::with('jabatan')->findOrFail($request->pegawai_id);
        $clientIp  = $this->getClientIp($request);
        $hasil     = $this->absensiService->prosesAbsenPulang($pegawai, $request->tanda_tangan, $clientIp);

        if ($request->expectsJson()) {
            return response()->json($hasil, $hasil['status'] === 'berhasil' ? 200 : 422);
        }

        return back()->with($hasil['status'] === 'berhasil' ? 'success' : 'error', $hasil['message']);
    }

    /**
     * Ambil IP client yang sesungguhnya (mendukung proxy/hosting).
     */
    private function getClientIp(Request $request): string
    {
        if ($request->hasHeader('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            return trim($ips[0]);
        }

        return $request->ip();
    }
}
