@extends('staf.layout', ['title' => 'Profil & Pusat Akses Staf — ' . ($pegawai->nama_lengkap ?? $user->name)])

@section('content')
<div class="space-y-4 pb-10" x-data="{ showPasswordModal: false, showFotoModal: false, showTtdModal: false }">

    {{-- Flash Notification --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl shadow-sm animate-fade-in">
        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-emerald-900 text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl shadow-sm animate-fade-in">
        <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <p class="text-rose-900 text-xs font-bold">{{ session('error') }}</p>
    </div>
    @endif

    @if ($errors->any())
    <div class="p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl shadow-sm space-y-1">
        <div class="flex items-center gap-2 text-rose-800 font-bold text-xs">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Perhatian:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] text-rose-700 font-semibold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 1. HERO PROFILE CARD (MODERN APP HUB STYLE)                            -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-6 bg-gradient-to-b from-white via-white to-emerald-50/40 rounded-3xl border border-[#C9A84C]/40 text-center relative overflow-hidden shadow-lg space-y-4">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-[#C9A84C]/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-emerald-700/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Avatar Wrapper with Quick Edit/Delete Actions -->
        <div class="relative inline-block mx-auto">
            @if($user->foto_profil || ($pegawai && $pegawai->foto_profil))
                <div class="w-24 h-24 rounded-3xl p-1 bg-gradient-to-tr from-[#064E3B] to-[#C9A84C] shadow-lg">
                    <img src="{{ asset('storage/' . ($user->foto_profil ?? $pegawai->foto_profil)) }}"
                         alt="{{ $pegawai->nama_lengkap ?? $user->name }}"
                         class="w-full h-full rounded-[22px] object-cover bg-white">
                </div>
            @else
                <div class="w-24 h-24 rounded-3xl p-1 bg-gradient-to-tr from-[#064E3B] to-[#C9A84C] shadow-lg">
                    <div class="w-full h-full rounded-[22px] bg-slate-100 flex items-center justify-center">
                        <span class="font-outfit font-extrabold text-2xl text-[#064E3B]">
                            {{ strtoupper(substr($pegawai->nama_lengkap ?? $user->name, 0, 2)) }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Badge Role -->
            <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#064E3B] text-[#E2C268] border-2 border-white shadow-sm uppercase tracking-wider">
                Staf Desa
            </span>
        </div>

        <!-- Nama & Jabatan -->
        <div class="space-y-1">
            <h2 class="font-outfit font-extrabold text-slate-900 text-lg sm:text-xl">
                {{ $pegawai->nama_lengkap ?? $user->name }}
            </h2>
            <p class="text-xs text-[#064E3B] font-bold uppercase tracking-wider">
                {{ $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}
            </p>
            <div class="flex items-center justify-center gap-2 pt-1">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[11px] font-bold border border-slate-200">
                    <span class="text-slate-400">@</span>{{ $user->username }}
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-900 text-[10.5px] font-bold border border-emerald-300">
                    ✓ Terverifikasi
                </span>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-slate-100">
            <a href="{{ route('staf.profil.edit') }}"
               class="py-2.5 px-3 rounded-2xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span>Edit Profil</span>
            </a>

            <button type="button" @click="showPasswordModal = true"
                    class="py-2.5 px-3 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Ganti Password</span>
            </button>
        </div>

        @if($user->foto_profil || ($pegawai && $pegawai->foto_profil))
        <div class="pt-1">
            <form action="{{ route('staf.profil.hapus-foto') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 underline cursor-pointer">
                    Hapus Foto Profil Saat Ini
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2. STATISTIK KEDISIPLINAN & KINERJA STAF (PERFORMANCE MATRIX)          -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-4 sm:p-5 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <h3 class="font-outfit font-extrabold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Ringkasan Kehadiran & Rekam Jejak</span>
            </h3>
            <span class="text-[10px] text-slate-400 font-mono font-bold">Akumulasi Resmi</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-200/80 text-center">
                <span class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider block">Total Hadir</span>
                <span class="font-outfit font-extrabold text-lg text-[#064E3B] mt-0.5 block">{{ $totalHadir }}</span>
                <span class="text-[9.5px] text-emerald-600 font-semibold">Hari Kerja</span>
            </div>

            <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200/80 text-center">
                <span class="text-[10px] text-amber-800 font-bold uppercase tracking-wider block">Izin / Sakit</span>
                <span class="font-outfit font-extrabold text-lg text-amber-700 mt-0.5 block">{{ $totalIzin + $totalSakit }}</span>
                <span class="text-[9.5px] text-amber-600 font-semibold">Hari Izin</span>
            </div>

            <div class="p-3 bg-blue-50/70 rounded-2xl border border-blue-200/80 text-center">
                <span class="text-[10px] text-blue-800 font-bold uppercase tracking-wider block">Dinas Luar</span>
                <span class="font-outfit font-extrabold text-lg text-blue-700 mt-0.5 block">{{ $totalAbsenLuar }}</span>
                <span class="text-[9.5px] text-blue-600 font-semibold">Pengajuan</span>
            </div>

            <div class="p-3 bg-teal-50/70 rounded-2xl border border-teal-200/80 text-center">
                <span class="text-[10px] text-teal-800 font-bold uppercase tracking-wider block">Tugas SPT</span>
                <span class="font-outfit font-extrabold text-lg text-teal-800 mt-0.5 block">{{ $totalSpt }}</span>
                <span class="text-[9.5px] text-teal-600 font-semibold">Surat Diterima</span>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 3. PUSAT GALERI TANDA TANGAN DIGITAL RESMI SAYA                       -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-4 sm:p-5 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#064E3B] text-[#E2C268] flex items-center justify-center font-bold shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <h3 class="font-outfit font-extrabold text-slate-800 text-xs uppercase tracking-wider">
                    Spesimen Tanda Tangan Resmi Saya
                </h3>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                E-Sign Resmi
            </span>
        </div>

        @if($spesimenTtd)
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-2.5">
                <div class="p-2 bg-white rounded-xl border border-dashed border-slate-300 inline-block shadow-2xs">
                    <img src="{{ $spesimenTtd }}" alt="Spesimen Tanda Tangan Staf" class="h-20 max-w-full object-contain mx-auto">
                </div>
                <p class="text-[10.5px] text-slate-500 font-medium">
                    Tanda tangan ini adalah spesimen resmi Anda yang digunakan untuk presensi mandiri, SPT, dan administrasi laporan kedinasan.
                </p>
                <button type="button" @click="showTtdModal = true; initSignaturePad();"
                        class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#064E3B] font-extrabold text-xs border border-emerald-200 transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                    <svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Perbarui Spesimen Tanda Tangan</span>
                </button>
            </div>
        @else
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-2.5">
                <div class="text-slate-400 space-y-0.5">
                    <p class="text-xs font-bold text-slate-600">Belum Ada Spesimen Tanda Tangan</p>
                    <p class="text-[11px] text-slate-400">Buat spesimen tanda tangan digital resmi Anda agar tersimpan dalam sistem desa.</p>
                </div>
                <button type="button" @click="showTtdModal = true; initSignaturePad();"
                        class="w-full py-2.5 px-3 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer shadow-sm active:scale-98">
                    <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Buat Tanda Tangan Resmi Sekarang</span>
                </button>
            </div>
        @endif
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 4. PUSAT AKSES SEMUA ARSIP & MENU PINTAS                               -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-4 sm:p-5 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-3">
        <h3 class="font-outfit font-extrabold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-100 pb-2">
            <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            <span>Pusat Akses Dokumen & Layanan Staf</span>
        </h3>

        <div class="grid grid-cols-1 gap-2">
            <!-- Link Riwayat Presensi Lengkap -->
            <a href="{{ route('staf.riwayat', ['tab' => 'presensi']) }}"
               class="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50 border border-slate-200/80 hover:border-emerald-200 transition flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-[#064E3B] flex items-center justify-center font-bold shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-outfit font-bold text-xs text-slate-800 group-hover:text-[#064E3B]">Buku Riwayat Presensi & Jam Kerja</h4>
                        <p class="text-[10.5px] text-slate-500">Lihat seluruh riwayat masuk/pulang, durasi, dan tanda tangan digital</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#064E3B] group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>

            <!-- Link Riwayat SPT -->
            <a href="{{ route('staf.spt.riwayat') }}"
               class="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50 border border-slate-200/80 hover:border-emerald-200 transition flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-outfit font-bold text-xs text-slate-800 group-hover:text-[#064E3B]">Arsip Surat Perintah Tugas (SPT)</h4>
                        <p class="text-[10.5px] text-slate-500">Daftar penugasan dinas resmi, berkas softfile, & status penerimaan</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#064E3B] group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>

            <!-- Link Riwayat Pengajuan Absen Luar -->
            <a href="{{ route('staf.riwayat', ['tab' => 'absen_luar']) }}"
               class="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50 border border-slate-200/80 hover:border-emerald-200 transition flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-outfit font-bold text-xs text-slate-800 group-hover:text-[#064E3B]">Riwayat Pengajuan Dinas Luar</h4>
                        <p class="text-[10.5px] text-slate-500">Arsip absensi luar kantor beserta koordinat GPS dan lampiran surat</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#064E3B] group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>

            <!-- Link Riwayat Izin & Sakit -->
            <a href="{{ route('staf.riwayat', ['tab' => 'izin']) }}"
               class="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50 border border-slate-200/80 hover:border-emerald-200 transition flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center font-bold shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-outfit font-bold text-xs text-slate-800 group-hover:text-[#064E3B]">Riwayat Izin & Surat Dokter</h4>
                        <p class="text-[10.5px] text-slate-500">Rekam permohonan izin cuti, sakit, dan lampiran surat dokter</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#064E3B] group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 5. DATA DETAIL IDENTITAS RESMI KEPEGAWAIAN                             -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if($pegawai)
    <div class="sadi-card p-5 bg-white space-y-3.5 shadow-sm rounded-3xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs uppercase tracking-wider">
                Identitas Resmi Kepegawaian Desa
            </h4>
            <span class="text-[10px] text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full font-bold">Terdaftar</span>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                <span class="text-slate-500 font-medium">Nomor SK / NIPD</span>
                <span class="font-mono font-bold text-slate-800">{{ $pegawai->nipd ?? '—' }}</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                <span class="text-slate-500 font-medium">NIK (KTP)</span>
                <span class="font-mono font-bold text-slate-800">{{ $pegawai->nik ?? '—' }}</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                <span class="text-slate-500 font-medium">Tempat, Tgl Lahir</span>
                <span class="font-bold text-slate-800">{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                <span class="text-slate-500 font-medium">No. WhatsApp / HP</span>
                <span class="font-mono font-bold text-slate-800">{{ $pegawai->no_hp ?? '—' }}</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-50">
                <span class="text-slate-500 font-medium">Jabatan Kedinasan</span>
                <span class="font-bold text-[#064E3B]">{{ $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</span>
            </div>
            <div class="flex justify-between items-start py-1">
                <span class="text-slate-500 font-medium shrink-0">Alamat Domisili</span>
                <span class="font-medium text-slate-800 text-right max-w-[200px]">{{ $pegawai->alamat ?? 'Desa Nangtang' }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 6. INFORMASI SISTEM & PENGEMBANG (LUXURY WATERMARK & SPEC CARD)         -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <a href="{{ route('tentang.aplikasi') }}" 
       class="block p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-[#022017] via-[#064E3B] to-[#043327] border-2 border-[#C9A84C]/60 shadow-lg hover:shadow-2xl hover:border-[#C9A84C] transition-all duration-300 group relative overflow-hidden text-white">
        
        <!-- Decorative Glow -->
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#C9A84C]/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="flex items-center justify-between gap-3 relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#C9A84C] to-[#99731C] text-[#021811] flex items-center justify-center font-bold shadow-md shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9.5px] font-bold uppercase tracking-widest text-[#F3E5AB]">SADI v2.0 Enterprise</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-[#C9A84C]/30 text-[#FFF0BD] font-mono font-bold">INFO</span>
                    </div>
                    <h4 class="font-outfit font-extrabold text-sm text-white group-hover:text-[#FFF0BD] transition-colors mt-0.5">
                        Tentang Sistem & Pengembang
                    </h4>
                    <p class="text-[10.5px] text-emerald-200/90 font-mono">
                        Lead Architect: <strong>Aris Munandar</strong> (LP3I × Desa Nangtang)
                    </p>
                </div>
            </div>

            <div class="w-8 h-8 rounded-xl bg-white/10 group-hover:bg-[#C9A84C] group-hover:text-[#021811] text-white flex items-center justify-center transition-all duration-200 shrink-0">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
    </a>

    <!-- Logout Button -->
    <form action="{{ route('staf.logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full py-3 px-4 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-extrabold border border-rose-200 transition flex items-center justify-center gap-2 cursor-pointer shadow-xs active:scale-98">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Keluar dari Akun Saya</span>
        </button>
    </form>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL GANTI PASSWORD CEPAT                                              -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div x-show="showPasswordModal"
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="showPasswordModal = false">
        <div @click.away="showPasswordModal = false"
             class="bg-white rounded-3xl max-w-sm w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-6 flex flex-col">
            
            <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40">
                <h3 class="font-outfit text-sm font-bold text-white">Ganti Kata Sandi Login</h3>
                <button type="button" @click="showPasswordModal = false" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('staf.profil.update-password') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none text-xs">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none text-xs">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Ulangi Kata Sandi Baru <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="6" placeholder="Ketik ulang kata sandi baru..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none text-xs">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showPasswordModal = false" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold hover:bg-[#04392B] transition cursor-pointer shadow-md">
                        Simpan Sandi Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL SPESIMEN TANDA TANGAN DIGITAL RESMI                               -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div x-show="showTtdModal"
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="showTtdModal = false">
        <div @click.away="showTtdModal = false"
             class="bg-white rounded-3xl max-w-sm w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-6 flex flex-col">
            
            <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40">
                <h3 class="font-outfit text-sm font-bold text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Spesimen Tanda Tangan Resmi</span>
                </h3>
                <button type="button" @click="showTtdModal = false" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('staf.profil.update-ttd') }}" method="POST" id="formTtdProfil" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')

                <input type="hidden" name="tanda_tangan" id="inputTtdProfil">

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Goreskan Tanda Tangan Anda</label>
                        <button type="button" onclick="clearSignatureProfil()" class="text-[10.5px] font-bold text-rose-600 hover:text-rose-700 underline cursor-pointer">
                            Hapus & Ulangi
                        </button>
                    </div>
                    <div class="w-full bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300 p-1">
                        <canvas id="signaturePadProfil" class="w-full h-44 bg-white rounded-xl touch-none cursor-crosshair"></canvas>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Bubuhkan goresan tanda tangan asli Anda di dalam kotak di atas.</p>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showTtdModal = false" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="submitSignatureProfil()" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold hover:bg-[#04392B] transition cursor-pointer shadow-md flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Spesimen TTD</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
let padInstance = null;

function initSignaturePad() {
    setTimeout(() => {
        const canvas = document.getElementById('signaturePadProfil');
        if (!canvas) return;
        
        // Resize canvas to internal pixel ratio
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);

        if (!padInstance) {
            padInstance = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: '#064E3B'
            });
        } else {
            padInstance.clear();
        }
    }, 200);
}

function clearSignatureProfil() {
    if (padInstance) {
        padInstance.clear();
    }
}

function submitSignatureProfil() {
    if (!padInstance || padInstance.isEmpty()) {
        Swal.fire({
            icon: 'warning',
            title: 'Tanda Tangan Kosong',
            text: 'Silakan goreskan tanda tangan Anda pada canvas terlebih dahulu.',
            confirmButtonColor: '#064E3B'
        });
        return;
    }

    const dataUrl = padInstance.toDataURL('image/png');
    document.getElementById('inputTtdProfil').value = dataUrl;
    document.getElementById('formTtdProfil').submit();
}
</script>
@endsection

