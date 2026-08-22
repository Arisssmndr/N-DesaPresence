<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanAbsenLuar;
use App\Models\Kehadiran;
use Carbon\Carbon;

class PengajuanAbsenLuarController extends Controller
{
    /**
     * Tampilkan form pengajuan absen luar.
     */
    public function form()
    {
        $user    = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')
                ->with('error', 'Data kepegawaian Anda tidak ditemukan.');
        }

        $today = Carbon::today()->toDateString();

        // Cek apakah sudah ada catatan kehadiran langsung di kantor hari ini
        $kehadiranHariIni = Kehadiran::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        // Cek apakah sudah ada pengajuan absen luar hari ini
        $pengajuanHariIni = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        return view('staf.ajukan-absen', compact('user', 'pegawai', 'kehadiranHariIni', 'pengajuanHariIni', 'today'));
    }

    /**
     * Simpan pengajuan absen luar baru.
     */
    public function store(Request $request)
    {
        $user    = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return back()->with('error', 'Data kepegawaian Anda tidak ditemukan.');
        }

        $request->validate([
            'tanggal'              => 'required|date|before_or_equal:today',
            'jenis'                => 'required|in:dinas_luar_undangan,dinas_luar_pengajuan,dinas_luar_surat_tugas,kegiatan_sosial,dinas_luar',
            'judul'                => 'required|string|max:150',
            'instansi_pengundang'  => 'required_if:jenis,dinas_luar_undangan|nullable|string|max:150',
            'nomor_surat_tugas'    => 'nullable|string|max:100',
            'deskripsi'            => 'required|string|max:2000',
            'foto_lokasi'          => 'required_if:jenis,kegiatan_sosial,dinas_luar_pengajuan|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'file_dokumen'         => 'required_if:jenis,dinas_luar_undangan,dinas_luar_surat_tugas,dinas_luar|nullable|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            // Bounding box wilayah Indonesia: lat -11 s/d 6, lng 95 s/d 141
            'latitude'             => 'required|numeric|between:-11,6',
            'longitude'            => 'required|numeric|between:95,141',
            'alamat_gps'           => 'nullable|string|max:255',
            'tanda_tangan'         => 'required|string|min:100',
        ], [
            'instansi_pengundang.required_if'  => 'Instansi / Pihak Pengundang wajib diisi untuk Dinas Luar Undangan.',
            'foto_lokasi.required_if'          => 'Foto bukti situasi / lokasi wajib diunggah.',
            'file_dokumen.required_if'         => 'Dokumen resmi / surat tugas / undangan wajib diunggah.',
            'latitude.required'                => 'Titik lokasi GPS wajib diaktifkan sebelum mengajukan.',
            'latitude.between'                 => 'Koordinat GPS tidak valid (di luar wilayah Indonesia). Matikan VPN, aktifkan GPS presisi tinggi, lalu coba lagi.',
            'longitude.required'               => 'Titik lokasi GPS wajib diaktifkan sebelum mengajukan.',
            'longitude.between'                => 'Koordinat GPS tidak valid (di luar wilayah Indonesia). Matikan VPN, aktifkan GPS presisi tinggi, lalu coba lagi.',
            'tanda_tangan.required'            => 'Tanda tangan digital wajib diisi.',
        ]);

        // 1. Cek apakah sudah ada catatan kehadiran langsung di kantor pada tanggal yang diajukan
        $kehadiran = Kehadiran::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($kehadiran && ($kehadiran->jam_masuk || in_array(strtolower($kehadiran->status), ['hadir', 'terlambat', 'dinas luar']))) {
            $info = $kehadiran->jam_masuk 
                ? 'Absen Masuk pukul ' . substr($kehadiran->jam_masuk, 0, 5) . ' WIB' 
                : 'Status: ' . $kehadiran->status;
            return back()->with('error', 'Anda sudah tercatat melakukan absensi kehadiran langsung di kantor pada tanggal ' . Carbon::parse($request->tanggal)->isoFormat('D MMMM Y') . ' (' . $info . '). Pengajuan absen luar hanya dapat dilakukan jika belum ada riwayat kehadiran langsung pada tanggal tersebut.');
        }

        // 2. Cek duplikasi pengajuan pada tanggal yang sama
        $existing = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pengajuan absen luar untuk tanggal ' . Carbon::parse($request->tanggal)->isoFormat('D MMMM Y') . ' (Status: ' . $existing->label_status . '). Satu pengajuan per hari.');
        }

        $data = [
            'pegawai_id'          => $pegawai->id,
            'user_id'             => $user->id,
            'tanggal'             => $request->tanggal,
            'jenis'               => $request->jenis,
            'judul'               => $request->judul,
            'instansi_pengundang' => $request->instansi_pengundang ?: null,
            'nomor_surat_tugas'   => $request->nomor_surat_tugas ?: null,
            'deskripsi'           => $request->deskripsi,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'alamat_gps'          => $request->alamat_gps,
            'tanda_tangan'        => $request->tanda_tangan,
            'status'              => 'menunggu',
        ];

        // Upload foto lokasi (kegiatan_sosial & dinas_luar_pengajuan atau bukti foto lainnya)
        if ($request->hasFile('foto_lokasi')) {
            $data['foto_lokasi'] = $request->file('foto_lokasi')
                ->store('pengajuan-absen/foto', 'public');
        }

        // Upload file dokumen/surat (undangan / SPT / dokumen pendukung)
        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen'] = $request->file('file_dokumen')
                ->store('pengajuan-absen/dokumen', 'public');
        }

        // Simpan tanda tangan ke disk jika base64
        $signatureService = app(\App\Services\AbsensiSignatureService::class);
        $data['tanda_tangan'] = $signatureService->simpanTandaTanganKeDisk($request->tanda_tangan, 'pengajuan_' . $pegawai->id);

        PengajuanAbsenLuar::create($data);

        return redirect()->route('staf.riwayat.pengajuan')
            ->with('success', 'Pengajuan absen luar berhasil dikirim! Silakan tunggu persetujuan Admin Desa.');
    }

    /**
     * Riwayat pengajuan absen luar milik staf yang sedang login.
     */
    public function riwayat()
    {
        $user    = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda');
        }

        $pengajuans = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('staf.riwayat-pengajuan', compact('user', 'pegawai', 'pengajuans'));
    }
}
