@extends('staf.layout', ['title' => 'Profil Saya — ' . ($pegawai->nama_lengkap ?? $user->name)])

@section('content')
<div class="space-y-5 pb-6">

    {{-- Flash Notification --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl shadow-sm animate-fade-in">
        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-emerald-900 text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Avatar Card -->
    <div class="sadi-card p-6 bg-white text-center space-y-3 relative overflow-hidden shadow-md">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-50 rounded-full blur-2xl pointer-events-none"></div>

        @if($user->foto_profil || ($pegawai && $pegawai->foto_profil))
            <img src="{{ asset('storage/' . ($user->foto_profil ?? $pegawai->foto_profil)) }}" alt="{{ $pegawai->nama_lengkap ?? $user->name }}"
                class="w-24 h-24 rounded-3xl object-contain mx-auto shadow-md"
                style="width: 96px; height: 96px; min-width: 96px; min-height: 96px;">
        @else
            <div class="w-24 h-24 rounded-3xl bg-slate-100 flex items-center justify-center overflow-hidden mx-auto shadow-md"
                 style="width: 96px; height: 96px; min-width: 96px; min-height: 96px;">
                <svg class="w-14 h-14 text-slate-400 translate-y-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
            </div>
        @endif

        <div>
            <h3 class="font-outfit font-extrabold text-slate-800 text-lg">{{ $pegawai->nama_lengkap ?? $user->name }}</h3>
            <p class="text-xs text-[#C9A84C] font-bold uppercase tracking-wider mt-0.5">{{ $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</p>
        </div>

        <div class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-[#064E3B] text-xs font-mono font-bold shadow-sm">
            <span>@ {{ $user->username }}</span>
        </div>

        <div class="pt-2">
            <a href="{{ route('staf.profil.edit') }}"
                class="btn-gold px-6 py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 mx-auto cursor-pointer shadow-md">
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Profil Saya</span>
            </a>
        </div>
    </div>

    <!-- Data Detail Pegawai (Read-Only View) -->
    @if($pegawai)
    <div class="sadi-card p-5 bg-white space-y-4 shadow-md">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs uppercase tracking-wider">
                Identitas Resmi Kepegawaian
            </h4>
            <span class="text-[10px] text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full font-bold">Terverifikasi</span>
        </div>

        <div class="space-y-3 text-xs">
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

    <!-- Logout Action -->
    <form action="{{ route('staf.logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full py-3 px-4 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-extrabold border border-rose-200 transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Keluar dari Akun Ini</span>
        </button>
    </form>

</div>
@endsection
