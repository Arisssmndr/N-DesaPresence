<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanAbsenLuar;
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

        // Cek apakah sudah ada pengajuan hari ini
        $today = Carbon::today()->toDateString();
        $pengajuanHariIni = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $today)
            ->first();

        return view('staf.ajukan-absen', compact('user', 'pegawai', 'pengajuanHariIni', 'today'));
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
            'tanggal'       => 'required|date|before_or_equal:today',
            'jenis'         => 'required|in:kegiatan_sosial,dinas_luar',
            'judul'         => 'required|string|max:150',
            'deskripsi'     => 'required|string|max:2000',
            'foto_lokasi'   => 'required_if:jenis,kegiatan_sosial|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'file_dokumen'  => 'required_if:jenis,dinas_luar|nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'alamat_gps'    => 'nullable|string|max:255',
            'tanda_tangan'  => 'required|string|min:100',
        ], [
            'foto_lokasi.required_if'  => 'Foto lokasi wajib diunggah untuk pengajuan Kegiatan Sosial.',
            'file_dokumen.required_if' => 'Dokumen/surat wajib diunggah untuk pengajuan Dinas Luar Resmi.',
            'latitude.required'        => 'Titik lokasi GPS wajib diaktifkan sebelum mengajukan.',
            'longitude.required'       => 'Titik lokasi GPS wajib diaktifkan sebelum mengajukan.',
            'tanda_tangan.required'    => 'Tanda tangan digital wajib diisi.',
        ]);

        // Cek duplikasi pengajuan pada tanggal yang sama
        $existing = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pengajuan absen luar untuk tanggal ' . Carbon::parse($request->tanggal)->isoFormat('D MMMM Y') . '. Satu pengajuan per hari.');
        }

        $data = [
            'pegawai_id'   => $pegawai->id,
            'user_id'      => $user->id,
            'tanggal'      => $request->tanggal,
            'jenis'        => $request->jenis,
            'judul'        => $request->judul,
            'deskripsi'    => $request->deskripsi,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'alamat_gps'   => $request->alamat_gps,
            'tanda_tangan' => $request->tanda_tangan,
            'status'       => 'menunggu',
        ];

        // Upload foto lokasi (kegiatan_sosial)
        if ($request->hasFile('foto_lokasi')) {
            $data['foto_lokasi'] = $request->file('foto_lokasi')
                ->store('pengajuan-absen/foto', 'public');
        }

        // Upload file dokumen/surat (dinas_luar)
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
