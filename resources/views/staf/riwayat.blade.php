@extends('staf.layout', ['title' => 'Riwayat Presensi — ' . $pegawai->nama_lengkap])

@section('content')
<div class="space-y-4 pb-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-outfit font-extrabold text-[#064E3B] text-lg">Buku Riwayat Presensi</h2>
            <p class="text-xs text-slate-500">Catatan kehadiran dan bukti tanda tangan harian Anda</p>
        </div>
        <span class="text-xs font-bold text-slate-400 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
            Total: {{ $riwayats->total() }}
        </span>
    </div>

    <!-- Riwayat List -->
    <div class="space-y-3">
        @forelse($riwayats as $r)
        <div class="sadi-card p-4 bg-white space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div>
                    <p class="font-bold text-slate-800 text-sm">{{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                    <p class="text-[10px] text-slate-400">Durasi: {{ $r->durasi_kerja_menit ? floor($r->durasi_kerja_menit / 60) . 'j ' . ($r->durasi_kerja_menit % 60) . 'm' : '—' }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    {{ $r->status }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Jam Masuk</span>
                    <p class="font-mono font-bold text-[#064E3B] text-sm mt-0.5">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '—' }}</p>
                    @if($r->tanda_tangan_masuk_src)
                        <img src="{{ $r->tanda_tangan_masuk_src }}" alt="TTD Masuk" class="h-8 mx-auto mt-1 border rounded bg-white">
                    @endif
                </div>

                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Jam Pulang</span>
                    <p class="font-mono font-bold text-blue-700 text-sm mt-0.5">{{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '—' }}</p>
                    @if($r->tanda_tangan_pulang_src)
                        <img src="{{ $r->tanda_tangan_pulang_src }}" alt="TTD Pulang" class="h-8 mx-auto mt-1 border rounded bg-white">
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2">
            <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-xs">Belum ada riwayat kehadiran yang tercatat.</p>
        </div>
        @endforelse
    </div>

    @if($riwayats->hasPages())
    <div class="pt-2">
        {{ $riwayats->links() }}
    </div>
    @endif

</div>
@endsection
