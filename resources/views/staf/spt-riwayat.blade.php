@extends('staf.layout')

@section('title', 'Riwayat Surat Perintah Tugas (SPT) — Presence Desa')

@section('content')
<div class="space-y-4 pb-12" x-data="{ activeSptTerimaModal: null, activeSptTolakModal: null }">

    <!-- Top Header Navigation -->
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('staf.beranda') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition shadow-2xs">
            <svg class="w-4 h-4 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
        <span class="text-[11px] font-bold text-slate-500 font-mono">
            {{ $pegawai->nama_lengkap }}
        </span>
    </div>

    <!-- Header Card -->
    <div class="sadi-card p-5 bg-gradient-to-br from-[#064E3B] via-[#085a44] to-[#04392B] text-white rounded-3xl shadow-lg border border-[#C9A84C]/40 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-[#C9A84C]/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex items-start gap-3.5 relative z-10">
            <div class="w-11 h-11 rounded-2xl bg-[#E2C268] text-[#064E3B] flex items-center justify-center font-extrabold shadow-md shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                    Administrasi Kedinasan
                </span>
                <h1 class="font-outfit font-extrabold text-base sm:text-lg text-white mt-1">
                    Riwayat Surat Perintah Tugas (SPT)
                </h1>
                <p class="text-xs text-emerald-100/90 mt-0.5 leading-relaxed">
                    Daftar resmi seluruh penugasan kedinasan luar kantor yang diterbitkan oleh Pemerintah Desa Nangtang untuk Anda.
                </p>
            </div>
        </div>

        <!-- Mini Stats Summary -->
        <div class="grid grid-cols-4 gap-2 mt-4 pt-3.5 border-t border-emerald-700/60 text-center">
            <a href="{{ route('staf.spt.riwayat', ['status' => 'semua']) }}"
               class="p-2 rounded-2xl {{ $filterStatus === 'semua' ? 'bg-[#C9A84C] text-[#064E3B] font-extrabold shadow' : 'bg-white/10 text-emerald-100 hover:bg-white/15' }} transition">
                <span class="text-[10px] block opacity-80">Total</span>
                <span class="text-sm font-extrabold">{{ $countSemua }}</span>
            </a>
            <a href="{{ route('staf.spt.riwayat', ['status' => 'menunggu']) }}"
               class="p-2 rounded-2xl {{ $filterStatus === 'menunggu' ? 'bg-amber-400 text-slate-900 font-extrabold shadow' : 'bg-white/10 text-emerald-100 hover:bg-white/15' }} transition relative">
                @if($countMenunggu > 0)
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-amber-400 rounded-full animate-ping"></span>
                @endif
                <span class="text-[10px] block opacity-80">Menunggu</span>
                <span class="text-sm font-extrabold text-amber-300">{{ $countMenunggu }}</span>
            </a>
            <a href="{{ route('staf.spt.riwayat', ['status' => 'diterima']) }}"
               class="p-2 rounded-2xl {{ $filterStatus === 'diterima' ? 'bg-emerald-400 text-[#064E3B] font-extrabold shadow' : 'bg-white/10 text-emerald-100 hover:bg-white/15' }} transition">
                <span class="text-[10px] block opacity-80">Diterima</span>
                <span class="text-sm font-extrabold text-emerald-300">{{ $countDiterima }}</span>
            </a>
            <a href="{{ route('staf.spt.riwayat', ['status' => 'ditolak']) }}"
               class="p-2 rounded-2xl {{ $filterStatus === 'ditolak' ? 'bg-rose-500 text-white font-extrabold shadow' : 'bg-white/10 text-emerald-100 hover:bg-white/15' }} transition">
                <span class="text-[10px] block opacity-80">Ditolak</span>
                <span class="text-sm font-extrabold text-rose-300">{{ $countDitolak }}</span>
            </a>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-900 font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-900 font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Daftar List Surat Perintah Tugas -->
    <div class="space-y-3.5">
        @forelse($spts as $spt)
            <div class="sadi-card p-4 sm:p-5 bg-white border {{ $spt->respons_staf === 'menunggu' ? 'border-amber-400 bg-amber-50/20 shadow-md ring-2 ring-amber-300/40' : ($spt->respons_staf === 'diterima' ? 'border-emerald-200 shadow-sm' : 'border-rose-200 bg-rose-50/15 shadow-sm') }} rounded-3xl space-y-3 relative overflow-hidden transition">
                
                <!-- Status Badge & Header -->
                <div class="flex items-start justify-between gap-2.5">
                    <div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($spt->respons_staf === 'diterima')
                                <span class="px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    ✓ Diterima oleh Anda
                                </span>
                            @elseif($spt->respons_staf === 'ditolak' || $spt->status === 'ditolak')
                                <span class="px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                    ✕ Ditolak oleh Anda
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold bg-amber-400 text-slate-900 shadow-2xs animate-pulse">
                                    ⏳ Menunggu Konfirmasi Anda
                                </span>
                            @endif

                            @if($spt->nomor_spt)
                                <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                    No: {{ $spt->nomor_spt }}
                                </span>
                            @endif
                        </div>

                        <h3 class="font-outfit font-extrabold text-sm sm:text-base text-slate-900 mt-1.5 leading-snug">
                            {{ $spt->tujuan }}
                        </h3>
                    </div>

                    <span class="text-[10px] font-mono text-slate-400 shrink-0">
                        {{ $spt->created_at->format('d/m/Y') }}
                    </span>
                </div>

                <!-- Detail Box -->
                <div class="p-3 bg-slate-50/90 rounded-2xl border border-slate-200/80 text-xs space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Periode Dinas:</span>
                            <p class="font-mono font-bold text-slate-900 mt-0.5">
                                {{ $spt->tanggal_mulai->format('d/m/Y') }} — {{ $spt->tanggal_selesai->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Diterbitkan Oleh:</span>
                            <p class="font-bold text-slate-800 mt-0.5">
                                {{ $spt->pembuat->name ?? 'Pemerintah Desa Nangtang' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Keperluan / Agenda:</span>
                        <p class="text-slate-700 text-xs mt-0.5 leading-relaxed">
                            {{ $spt->keperluan }}
                        </p>
                    </div>

                    @if($spt->file_undangan)
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-[11px] text-slate-600 font-medium">Berkas SPT / Undangan:</span>
                            <a href="{{ asset('storage/' . $spt->file_undangan) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[11px] font-extrabold text-blue-600 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Berkas Lampiran</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Jejak Catatan Status / Tanda Tangan -->
                @if($spt->respons_staf === 'diterima')
                    <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-2xl text-xs space-y-1.5">
                        <div class="flex items-center justify-between text-[11px] text-emerald-900 font-bold">
                            <span>Status Presensi: Otomatis Hadir</span>
                            @if($spt->waktu_respons_staf)
                                <span class="font-normal font-mono text-[10px] text-emerald-700">Diterima {{ $spt->waktu_respons_staf->format('d/m/Y H:i') }} WIB</span>
                            @endif
                        </div>
                        @if($spt->tanda_tangan_staf)
                            <div class="flex items-center gap-3 pt-1">
                                <span class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider">Tanda Tangan Anda:</span>
                                <img src="{{ $spt->tanda_tangan_staf }}" alt="TTD Staf" class="h-10 border border-emerald-300 rounded-lg bg-white p-1">
                            </div>
                        @endif
                    </div>
                @elseif($spt->respons_staf === 'ditolak' || $spt->status === 'ditolak')
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1">
                        <span class="text-[10.5px] font-bold text-rose-800 uppercase tracking-wider block">Alasan Penolakan Anda:</span>
                        <p class="text-xs text-rose-950 font-semibold italic bg-white p-2 rounded-xl border border-rose-200">
                            "{{ $spt->alasan_tolak_staf ?? $spt->catatan_penolakan ?? 'Berbeda tupoksi / tugas lain.' }}"
                        </p>
                    </div>
                @else
                    <!-- Action Buttons: TERIMA / TOLAK -->
                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button type="button" @click="activeSptTolakModal = {{ $spt->id }}"
                                class="px-3.5 py-2 rounded-xl bg-white border border-rose-300 text-rose-700 font-extrabold text-xs hover:bg-rose-50 transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Tolak Tugas</span>
                        </button>

                        <button type="button" @click="activeSptTerimaModal = {{ $spt->id }}; setTimeout(() => { initSptPad({{ $spt->id }}) }, 100);"
                                class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-extrabold text-xs hover:bg-[#04392B] transition cursor-pointer flex items-center gap-1.5 shadow-md">
                            <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Terima & Tanda Tangani</span>
                        </button>
                    </div>
                @endif

                <!-- MODAL TANDA TANGAN PENERIMAAN SPT -->
                <div x-show="activeSptTerimaModal === {{ $spt->id }}"
                     x-transition.opacity
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                     style="display: none;"
                     @keydown.escape.window="activeSptTerimaModal = null">
                    <div @click.away="activeSptTerimaModal = null"
                         class="bg-white text-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 my-6 flex flex-col max-h-[92vh]">
                        
                        <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                            <div>
                                <h3 class="font-outfit text-sm font-bold text-white">Konfirmasi Penerimaan SPT</h3>
                                <p class="text-[10px] text-emerald-200 font-mono">{{ $spt->nomor_spt ?? 'Penugasan Kedinasan' }}</p>
                            </div>
                            <button type="button" @click="activeSptTerimaModal = null" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form id="form-spt-terima-{{ $spt->id }}" action="{{ route('staf.spt.terima', $spt->id) }}" method="POST" onsubmit="return submitSptForm(event, {{ $spt->id }})" class="p-4 space-y-3 text-xs">
                            @csrf
                            <input type="hidden" name="tanda_tangan" id="input-ttd-spt-{{ $spt->id }}">

                            <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200 space-y-1">
                                <p class="font-bold text-emerald-950">{{ $spt->tujuan }}</p>
                                <p class="text-[11px] text-emerald-800">
                                    Periode: <strong class="font-mono">{{ $spt->tanggal_mulai->format('d/m/Y') }} — {{ $spt->tanggal_selesai->format('d/m/Y') }}</strong>
                                </p>
                                <p class="text-[10.5px] text-emerald-700 italic">
                                    * Dengan menandatangani SPT ini, status kehadiran Anda pada rentang tanggal tersebut akan otomatis tercatat sebagai Hadir (Dinas Luar SPT).
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Tanda Tangan Digital Anda <span class="text-rose-500">*</span></label>
                                    <button type="button" onclick="clearSptPad({{ $spt->id }})" class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer">Hapus / Ulangi</button>
                                </div>
                                <div id="wrapper-spt-{{ $spt->id }}" class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 overflow-hidden relative">
                                    <canvas id="canvas-spt-{{ $spt->id }}" class="w-full h-36 cursor-crosshair touch-none"></canvas>
                                </div>
                            </div>

                            <div class="pt-1 flex justify-end gap-2">
                                <button type="button" @click="activeSptTerimaModal = null" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold hover:bg-[#04392B] transition cursor-pointer flex items-center gap-1.5 shadow-md">
                                    <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Konfirmasi & Terima</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL TOLAK PENUGASAN SPT -->
                <div x-show="activeSptTolakModal === {{ $spt->id }}"
                     x-transition.opacity
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                     style="display: none;"
                     @keydown.escape.window="activeSptTolakModal = null">
                    <div @click.away="activeSptTolakModal = null"
                         class="bg-white text-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 my-6 flex flex-col max-h-[92vh]">
                        
                        <div class="px-5 py-3.5 bg-rose-700 text-white flex items-center justify-between border-b border-rose-800 shrink-0">
                            <div>
                                <h3 class="font-outfit text-sm font-bold text-white">Tolak Penugasan SPT</h3>
                                <p class="text-[10px] text-rose-200 font-mono">{{ $spt->nomor_spt ?? 'Penugasan Kedinasan' }}</p>
                            </div>
                            <button type="button" @click="activeSptTolakModal = null" class="p-1 rounded-lg hover:bg-rose-800 text-rose-200 hover:text-white transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('staf.spt.tolak', $spt->id) }}" method="POST" class="p-4 space-y-3 text-xs">
                            @csrf

                            <div class="p-3 bg-rose-50 rounded-2xl border border-rose-200 text-rose-900 space-y-1">
                                <p class="font-bold">{{ $spt->tujuan }}</p>
                                <p class="text-[11px] text-rose-700 leading-relaxed">
                                    Mohon berikan alasan penolakan yang jelas (contoh: berbeda tupoksi jabatan, bentrok agenda desa lain, dsb.) untuk diketahui oleh Kepala Desa / Admin.
                                </p>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Alasan Penolakan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="alasan_tolak" rows="3" required minlength="5" placeholder="Tuliskan alasan penolakan penugasan ini..."
                                          class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                            </div>

                            <div class="pt-1 flex justify-end gap-2">
                                <button type="button" @click="activeSptTolakModal = null" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 transition cursor-pointer flex items-center gap-1.5 shadow-md">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Kirim Penolakan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="p-10 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 space-y-2">
                <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-bold text-slate-600 text-sm">Tidak ada riwayat Surat Perintah Tugas</p>
                <p class="text-xs text-slate-400">Belum ada penugasan kedinasan dengan status ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($spts->hasPages())
        <div class="pt-2">
            {{ $spts->links() }}
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    const sptPads = {};

    function initSptPad(sptId) {
        const canvas = document.getElementById('canvas-spt-' + sptId);
        const wrapper = document.getElementById('wrapper-spt-' + sptId);
        if (!canvas || !wrapper) return;

        canvas.width = wrapper.offsetWidth || 350;
        canvas.height = 140;

        if (!sptPads[sptId]) {
            sptPads[sptId] = new SignaturePad(canvas, {
                minWidth: 1.5,
                maxWidth: 3.5,
                penColor: '#064E3B',
                backgroundColor: 'rgba(0,0,0,0)'
            });
        }
    }

    function clearSptPad(sptId) {
        if (sptPads[sptId]) {
            sptPads[sptId].clear();
        }
    }

    function submitSptForm(e, sptId) {
        const pad = sptPads[sptId];
        if (!pad || pad.isEmpty()) {
            e.preventDefault();
            alert('Harap bubuhkan tanda tangan digital Anda terlebih dahulu sebelum mengonfirmasi penerimaan SPT.');
            return false;
        }

        const inputTtd = document.getElementById('input-ttd-spt-' + sptId);
        if (inputTtd) {
            inputTtd.value = pad.toDataURL('image/png');
        }
        return true;
    }
</script>
@endsection
