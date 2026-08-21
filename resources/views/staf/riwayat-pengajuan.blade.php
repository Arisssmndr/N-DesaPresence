@extends('staf.layout', ['title' => 'Riwayat Pengajuan — Portal Staf Desa Nangtang'])

@section('content')
<div class="space-y-5 pb-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('staf.beranda') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#064E3B] bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm transition hover:bg-slate-50">
            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Beranda
        </a>
        <a href="{{ route('staf.ajukan.form') }}"
           class="btn-gold text-xs px-4 py-2 rounded-xl font-bold flex items-center gap-1.5 shadow-md">
            <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Baru
        </a>
    </div>

    {{-- Flash Success --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl shadow-sm animate-fade-in">
        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-emerald-900 text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Statistik Mini --}}
    @php
        $totalMenunggu  = $pengajuans->total() > 0 ? $pengajuans->getCollection()->where('status', 'menunggu')->count() : 0;
        $allPengajuans  = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id);
        $jmlMenunggu    = (clone $allPengajuans)->where('status', 'menunggu')->count();
        $jmlDisetujui   = (clone $allPengajuans)->where('status', 'disetujui')->count();
        $jmlDitolak     = (clone $allPengajuans)->where('status', 'ditolak')->count();
    @endphp
    <div class="grid grid-cols-3 gap-3">
        <div class="sadi-card p-3 text-center bg-amber-50 border border-amber-200">
            <p class="text-xl font-outfit font-extrabold text-amber-800">{{ $jmlMenunggu }}</p>
            <p class="text-[10px] font-bold text-amber-700 mt-0.5">Menunggu</p>
        </div>
        <div class="sadi-card p-3 text-center bg-emerald-50 border border-emerald-200">
            <p class="text-xl font-outfit font-extrabold text-emerald-800">{{ $jmlDisetujui }}</p>
            <p class="text-[10px] font-bold text-emerald-700 mt-0.5">Disetujui</p>
        </div>
        <div class="sadi-card p-3 text-center bg-red-50 border border-red-200">
            <p class="text-xl font-outfit font-extrabold text-red-800">{{ $jmlDitolak }}</p>
            <p class="text-[10px] font-bold text-red-700 mt-0.5">Ditolak</p>
        </div>
    </div>

    {{-- Daftar Pengajuan --}}
    @if($pengajuans->isEmpty())
    <div class="sadi-card p-10 bg-white text-center shadow-md">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <p class="text-slate-500 text-sm font-bold">Belum ada riwayat pengajuan</p>
        <p class="text-slate-400 text-xs mt-1">Pengajuan absen luar Anda akan tampil di sini.</p>
        <a href="{{ route('staf.ajukan.form') }}" class="inline-flex items-center gap-2 mt-4 btn-sadi-primary text-white text-xs font-bold px-5 py-2.5 rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Pengajuan Pertama
        </a>
    </div>
    @else
    <div class="space-y-3">
        @foreach($pengajuans as $p)
        <div class="sadi-card p-4 bg-white shadow-md space-y-3">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border
                            {{ $p->jenis === 'dinas_luar' ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-pink-50 text-pink-800 border-pink-200' }}">
                            {{ $p->label_jenis }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">{{ $p->tanggal->isoFormat('D MMMM Y') }}</span>
                        @if($p->latitude && $p->longitude)
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                            📍 GPS: {{ $p->latitude }}, {{ $p->longitude }}
                        </span>
                        @endif
                    </div>
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $p->judul }}</p>
                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $p->deskripsi }}</p>
                </div>
                <span class="shrink-0 text-[10px] font-extrabold px-2.5 py-1 rounded-full border {{ $p->badge_class }}">
                    {{ $p->label_status }}
                </span>
            </div>

            {{-- Bukti --}}
            @if($p->foto_lokasi)
            <div class="rounded-xl overflow-hidden border border-slate-200 max-h-32">
                <img src="{{ asset('storage/' . $p->foto_lokasi) }}" class="w-full max-h-32 object-cover" alt="Foto Bukti">
            </div>
            @elseif($p->file_dokumen)
            <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-xs text-slate-600 font-semibold truncate">{{ basename($p->file_dokumen) }}</span>
                <a href="{{ asset('storage/' . $p->file_dokumen) }}" target="_blank" class="text-xs font-bold text-[#064E3B] shrink-0 underline">Lihat</a>
            </div>
            @endif

            {{-- Catatan Admin --}}
            @if($p->catatan_admin)
            <div class="p-3 rounded-xl border text-xs space-y-0.5
                {{ $p->status === 'ditolak' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' }}">
                <p class="font-bold">{{ $p->status === 'ditolak' ? 'Alasan Penolakan:' : 'Catatan Admin:' }}</p>
                <p>{{ $p->catatan_admin }}</p>
            </div>
            @endif

            {{-- Waktu Diproses --}}
            @if($p->diproses_pada)
            <p class="text-[10px] text-slate-400">Diproses: {{ $p->diproses_pada->isoFormat('D MMM Y, HH:mm') }}</p>
            @else
            <p class="text-[10px] text-amber-600 font-semibold">Menunggu verifikasi Admin Desa...</p>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($pengajuans->hasPages())
    <div class="py-2">{{ $pengajuans->links() }}</div>
    @endif
    @endif

</div>
@endsection
