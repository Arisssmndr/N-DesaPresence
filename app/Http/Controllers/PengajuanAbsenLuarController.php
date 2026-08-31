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
            ->whereDate('tanggal', $today)
            ->first();

        // Cek apakah sudah ada pengajuan absen luar hari ini
        $pengajuanHariIni = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Cek apakah ada izin/sakit aktif hari ini
        $izinHariIni = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();

        return view('staf.ajukan-absen', compact('user', 'pegawai', 'kehadiranHariIni', 'pengajuanHariIni', 'izinHariIni', 'today'));
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

        $minDate = Carbon::today()->subDays(30)->toDateString();

        $request->validate([
            'tanggal'              => 'required|date|before_or_equal:today|after_or_equal:' . $minDate,
            'jenis'                => 'required|in:dinas_luar_undangan,dinas_luar_pengajuan,dinas_luar_surat_tugas,kegiatan_sosial,dinas_luar',
            'judul'                => 'required|string|max:150',
            'instansi_pengundang'  => 'required_if:jenis,dinas_luar_undangan|nullable|string|max:150',
            'nomor_surat_tugas'    => 'nullable|string|max:100',
            'deskripsi'            => 'required|string|max:2000',
            'foto_lokasi'          => 'required_if:jenis,kegiatan_sosial,dinas_luar_pengajuan|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'file_dokumen'         => 'required_if:jenis,dinas_luar_undangan,dinas_luar_surat_tugas,dinas_luar|nullable|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            // Bounding box wilayah Indonesia: lat -11 s/d 6, lng 95 s/d 141.1
            'latitude'             => 'required|numeric|between:-11,6',
            'longitude'            => 'required|numeric|between:95,141.1',
            'alamat_gps'           => 'nullable|string|max:255',
            'sumber_koordinat'     => 'nullable|in:gps,ip_geolocation,manual',
            'akurasi_gps_meter'    => 'nullable|numeric|min:0',
            'tanda_tangan'         => 'required|string|min:100',
        ], [
            'tanggal.after_or_equal'           => 'Pengajuan absen luar maksimal untuk 30 hari ke belakang.',
            'instansi_pengundang.required_if'  => 'Instansi / Pihak Pengundang wajib diisi untuk Dinas Luar Undangan.',
            'foto_lokasi.required_if'          => 'Foto bukti situasi / lokasi wajib diunggah.',
            'file_dokumen.required_if'         => 'Dokumen resmi / surat tugas / undangan wajib diunggah.',
            'latitude.required'                => 'Titik lokasi GPS wajib diaktifkan sebelum mengajukan.',
            'latitude.between'                 => 'Koordinat lokasi tidak valid (di luar wilayah Indonesia). Matikan VPN, aktifkan GPS presisi tinggi, lalu coba lagi.',
            'longitude.required'               => 'Titik lokasi GPS wajib diaktifkan sebelum mengajukan.',
            'longitude.between'                => 'Koordinat lokasi tidak valid (di luar wilayah Indonesia). Matikan VPN, aktifkan GPS presisi tinggi, lalu coba lagi.',
            'tanda_tangan.required'            => 'Tanda tangan digital wajib diisi.',
        ]);

        // 0. Conflict Guard: Cek apakah ada SPT yang aktif dan diterima pada tanggal yang diajukan
        $sptBentrok = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'diterima')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $request->tanggal)
            ->whereDate('tanggal_selesai', '>=', $request->tanggal)
            ->first();

        if ($sptBentrok) {
            $mulai = \Carbon\Carbon::parse($sptBentrok->tanggal_mulai)->isoFormat('D MMMM Y');
            $selesai = \Carbon\Carbon::parse($sptBentrok->tanggal_selesai)->isoFormat('D MMMM Y');
            return back()->withInput()
                ->with('error', "Gagal: Anda sedang dalam penugasan resmi SPT {$sptBentrok->nomor_spt} ({$sptBentrok->tujuan}) periode {$mulai} s/d {$selesai}. Presensi kehadiran sudah tercatat otomatis.")
                ->with('conflict_modal', [
                    'icon'      => 'warning',
                    'title'     => 'Surat Perintah Tugas Aktif',
                    'badge'     => 'SPT Resmi',
                    'nama'      => $pegawai->nama_lengkap,
                    'tanggal'   => "{$mulai} s/d {$selesai}",
                    'status'    => 'Dinas SPT',
                    'pesan'     => "Anda tercatat sedang melaksanakan tugas kedinasan berdasarkan SPT {$sptBentrok->nomor_spt} ke {$sptBentrok->tujuan}."
                ]);
        }

        // 1. Conflict Guard: Cek apakah pegawai memiliki izin / sakit aktif pada tanggal yang diajukan
        $izinBentrok = \App\Models\IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->whereDate('tanggal_mulai', '<=', $request->tanggal)
            ->whereDate('tanggal_selesai', '>=', $request->tanggal)
            ->first();

        if ($izinBentrok) {
            $mulai = \Carbon\Carbon::parse($izinBentrok->tanggal_mulai)->isoFormat('D MMMM Y');
            $selesai = \Carbon\Carbon::parse($izinBentrok->tanggal_selesai)->isoFormat('D MMMM Y');
            return back()->withInput()
                ->with('error', "Gagal: Anda tercatat memiliki Izin/Sakit aktif ({$izinBentrok->jenis}) periode {$mulai} s/d {$selesai}.")
                ->with('conflict_modal', [
                    'icon'      => 'warning',
                    'title'     => 'Masa Izin / Sakit Aktif',
                    'badge'     => 'Pemberitahuan',
                    'nama'      => $pegawai->nama_lengkap,
                    'tanggal'   => "{$mulai} s/d {$selesai}",
                    'status'    => ucfirst(str_replace('_', ' ', $izinBentrok->jenis)),
                    'pesan'     => "Anda sedang dalam masa izin aktif ({$izinBentrok->label_jenis}) pada tanggal ini."
                ]);
        }

        // 2. Conflict Guard: Cek apakah sudah ada catatan kehadiran langsung di kantor pada tanggal yang diajukan
        $kehadiran = Kehadiran::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($kehadiran && ($kehadiran->jam_masuk || in_array(strtolower($kehadiran->status), ['hadir', 'tepat waktu', 'terlambat', 'dinas luar']))) {
            $tglStr = \Carbon\Carbon::parse($request->tanggal)->isoFormat('dddd, D MMMM Y');
            if ($kehadiran->jam_pulang) {
                $judul = 'Sudah Absen Pulang';
                $statusStr = 'Absen Pulang (' . substr($kehadiran->jam_pulang, 0, 5) . ' WIB)';
                $pesan = "Anda sudah menyelesaikan absensi masuk dan pulang pada {$tglStr}.";
            } elseif ($kehadiran->jam_masuk) {
                $judul = 'Sudah Absen Masuk';
                $statusStr = 'Absen Masuk (' . substr($kehadiran->jam_masuk, 0, 5) . ' WIB)';
                $pesan = "Anda sudah melakukan absensi masuk kantor pada {$tglStr}.";
            } else {
                $judul = 'Sudah Melakukan Presensi';
                $statusStr = 'Hadir Sah di Kantor';
                $pesan = "Data kehadiran Anda sudah sah tercatat pada {$tglStr}.";
            }

            return back()->withInput()
                ->with('error', 'Anda sudah tercatat melakukan absensi kehadiran langsung di kantor pada ' . $tglStr . '.')
                ->with('conflict_modal', [
                    'icon'      => 'warning',
                    'title'     => $judul,
                    'badge'     => 'Pemberitahuan',
                    'nama'      => $pegawai->nama_lengkap,
                    'tanggal'   => $tglStr,
                    'status'    => $statusStr,
                    'pesan'     => $pesan,
                ]);
        }

        // 3. Cek duplikasi pengajuan pada tanggal yang sama
        $existing = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $request->tanggal)
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
            'sumber_koordinat'    => $request->sumber_koordinat ?: 'gps',
            'akurasi_gps_meter'   => $request->filled('akurasi_gps_meter') ? round((float) $request->akurasi_gps_meter) : null,
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

        return redirect()->route('staf.riwayat', ['tab' => 'absen_luar'])
            ->with('success', 'Pengajuan absen luar berhasil dikirim! Silakan tunggu persetujuan Admin Desa.');
    }

    /**
     * Riwayat pengajuan absen luar milik staf yang sedang login.
     */
    public function riwayat()
    {
        return redirect()->route('staf.riwayat', ['tab' => 'absen_luar']);
    }
}
