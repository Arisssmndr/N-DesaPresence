<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IzinSakit;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StafIzinController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun belum terhubung dengan data pegawai.');
        }

        $riwayats = IzinSakit::where('pegawai_id', $pegawai->id)
            ->latest()
            ->paginate(10);

        return view('staf.izin', compact('user', 'pegawai', 'riwayats'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('staf.beranda')->with('error', 'Akun belum terhubung dengan data pegawai.');
        }

        $minDate = Carbon::today()->subDays(60)->toDateString();

        $validated = $request->validate([
            'kategori' => 'required|in:izin,sakit',
            'jenis_detail' => 'nullable|string|max:50',
            'tanggal_mulai' => 'required|date|after_or_equal:' . $minDate,
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:500',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Opsional, maks 5MB
        ], [
            'kategori.required' => 'Pilih kategori pengajuan (Izin atau Sakit).',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai maksimal 60 hari ke belakang.',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'keterangan.required' => 'Berikan alasan / keterangan pengajuan.',
            'file_lampiran.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);

        // 0. Conflict Guard: Cek apakah ada Surat Perintah Tugas (SPT) aktif pada rentang tanggal pengajuan
        $sptBentrok = \App\Models\SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('respons_staf', 'diterima')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $validated['tanggal_selesai'])
            ->whereDate('tanggal_selesai', '>=', $validated['tanggal_mulai'])
            ->first();

        if ($sptBentrok) {
            $mulai = \Carbon\Carbon::parse($sptBentrok->tanggal_mulai)->isoFormat('D MMMM Y');
            $selesai = \Carbon\Carbon::parse($sptBentrok->tanggal_selesai)->isoFormat('D MMMM Y');
            return back()->withInput()
                ->with('error', "Gagal: Anda memiliki Surat Perintah Tugas aktif (SPT {$sptBentrok->nomor_spt} - {$sptBentrok->tujuan}) periode {$mulai} s/d {$selesai}.")
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

        // 1. Conflict Guard: Cek apakah sudah ada catatan kehadiran langsung fisik di kantor
        $kehadiranBentrok = \App\Models\Kehadiran::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', '>=', $validated['tanggal_mulai'])
            ->whereDate('tanggal', '<=', $validated['tanggal_selesai'])
            ->where(function ($q) {
                $q->whereNotNull('jam_masuk')
                  ->orWhereIn('sumber_data', ['web_signature', 'fingerprint']);
            })
            ->first();

        if ($kehadiranBentrok) {
            $tglStr = \Carbon\Carbon::parse($kehadiranBentrok->tanggal)->isoFormat('dddd, D MMMM Y');
            if ($kehadiranBentrok->jam_pulang) {
                $judul = 'Sudah Absen Pulang';
                $statusStr = 'Absen Pulang (' . substr($kehadiranBentrok->jam_pulang, 0, 5) . ' WIB)';
                $pesan = "Anda sudah menyelesaikan absensi masuk dan pulang pada {$tglStr}.";
            } elseif ($kehadiranBentrok->jam_masuk) {
                $judul = 'Sudah Absen Masuk';
                $statusStr = 'Absen Masuk (' . substr($kehadiranBentrok->jam_masuk, 0, 5) . ' WIB)';
                $pesan = "Anda sudah melakukan absensi masuk kantor pada {$tglStr}.";
            } else {
                $judul = 'Sudah Melakukan Presensi';
                $statusStr = 'Hadir Sah di Kantor';
                $pesan = "Data kehadiran Anda sudah sah tercatat pada {$tglStr}.";
            }

            return back()->withInput()
                ->with('error', "Gagal: Anda sudah tercatat melakukan presensi di kantor pada {$tglStr}.")
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

        // 2. Conflict Guard: Cek apakah ada pengajuan absen luar yang aktif / disetujui pada rentang tanggal
        $absenLuarBentrok = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->whereDate('tanggal', '>=', $validated['tanggal_mulai'])
            ->whereDate('tanggal', '<=', $validated['tanggal_selesai'])
            ->first();

        if ($absenLuarBentrok) {
            $tglStr = \Carbon\Carbon::parse($absenLuarBentrok->tanggal)->isoFormat('dddd, D MMMM Y');
            return back()->withInput()
                ->with('error', "Gagal: Anda memiliki pengajuan absen luar ({$absenLuarBentrok->label_jenis}) pada tanggal {$tglStr}.")
                ->with('conflict_modal', [
                    'icon'      => 'warning',
                    'title'     => 'Sudah Ada Absen Luar',
                    'badge'     => 'Pemberitahuan',
                    'nama'      => $pegawai->nama_lengkap,
                    'tanggal'   => $tglStr,
                    'status'    => $absenLuarBentrok->label_jenis ?? 'Dinas Luar',
                    'pesan'     => "Anda sudah memiliki permohonan absen luar tercatat pada {$tglStr}."
                ]);
        }

        // 3. Cek tumpang tindih pengajuan izin yang masih aktif
        $adaIzinBentrok = IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->where(function ($q) use ($validated) {
                $q->where(function($sub) use ($validated) {
                    $sub->whereDate('tanggal_mulai', '>=', $validated['tanggal_mulai'])
                        ->whereDate('tanggal_mulai', '<=', $validated['tanggal_selesai']);
                })->orWhere(function($sub) use ($validated) {
                    $sub->whereDate('tanggal_selesai', '>=', $validated['tanggal_mulai'])
                        ->whereDate('tanggal_selesai', '<=', $validated['tanggal_selesai']);
                })->orWhere(function ($sub) use ($validated) {
                    $sub->whereDate('tanggal_mulai', '<=', $validated['tanggal_mulai'])
                        ->whereDate('tanggal_selesai', '>=', $validated['tanggal_selesai']);
                });
            })->exists();

        if ($adaIzinBentrok) {
            return back()->withInput()->with('error', 'Anda sudah memiliki pengajuan izin/sakit yang aktif pada rentang tanggal tersebut.');
        }

        $lampiranPath = null;
        if ($request->hasFile('file_lampiran')) {
            $lampiranPath = $request->file('file_lampiran')->store('izin-lampiran', 'public');
        }

        $start = Carbon::parse($validated['tanggal_mulai']);
        $end = Carbon::parse($validated['tanggal_selesai']);
        $jumlahHari = $start->diffInDays($end) + 1;

        // Map kategori ke jenis IzinSakit model
        $jenis = ($validated['kategori'] === 'sakit')
            ? ($lampiranPath ? 'sakit_dengan_surat' : 'sakit_tanpa_surat')
            : ($validated['jenis_detail'] ?? 'izin_pribadi');

        $izin = IzinSakit::create([
            'pegawai_id' => $pegawai->id,
            'jenis' => $jenis,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $jumlahHari,
            'keterangan' => $validated['keterangan'],
            'file_lampiran' => $lampiranPath,
            'status' => 'menunggu',
            'diproses_oleh' => null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role ?? 'perangkat',
            'aktivitas' => "Pengajuan {$validated['kategori']} oleh {$pegawai->nama_lengkap} ({$jumlahHari} hari: {$validated['tanggal_mulai']} s/d {$validated['tanggal_selesai']})",
            'modul' => 'Izin & Sakit',
        ]);

        return redirect()->route('staf.izin')->with('success', 'Pengajuan ' . ucfirst($validated['kategori']) . ' berhasil dikirim. Menunggu persetujuan dari Admin / Kepala Desa.');
    }
}
