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

        // Cek tumpang tindih pengajuan izin yang masih aktif
        $adaIzinBentrok = IzinSakit::where('pegawai_id', $pegawai->id)
            ->where('status', '!=', 'ditolak')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('tanggal_mulai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                  ->orWhereBetween('tanggal_selesai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                  ->orWhere(function ($sub) use ($validated) {
                      $sub->where('tanggal_mulai', '<=', $validated['tanggal_mulai'])
                          ->where('tanggal_selesai', '>=', $validated['tanggal_selesai']);
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
