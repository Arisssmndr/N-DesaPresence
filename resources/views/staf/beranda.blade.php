@extends('staf.layout', ['title' => 'Beranda Presensi — ' . $pegawai->nama_lengkap])

@section('content')
<div class="space-y-5 pb-6">

    <!-- Profile Header Card -->
    <div class="sadi-card p-5 text-white border-0 shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #064E3B 0%, #04392B 100%) !important;">
        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[#C9A84C]/10 rounded-full blur-xl"></div>

        <div class="flex items-center gap-4 relative z-10">
            @if($user->foto_profil || ($pegawai && $pegawai->foto_profil))
                <img src="{{ asset('storage/' . ($user->foto_profil ?? $pegawai->foto_profil)) }}" alt="{{ $pegawai->nama_lengkap }}"
                    class="w-14 h-14 rounded-full object-cover border-2 border-[#C9A84C] shrink-0 shadow-md">
            @else
                <div class="w-14 h-14 rounded-full bg-slate-200 border-2 border-[#C9A84C] flex items-center justify-center overflow-hidden shrink-0 shadow-md">
                    <svg class="w-10 h-10 text-slate-400 translate-y-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-[10px] text-[#C9A84C] uppercase font-bold tracking-widest leading-tight">Perangkat Desa</p>
                <h2 class="font-outfit font-bold text-lg text-white truncate">{{ $pegawai->nama_lengkap }}</h2>
                <p class="text-xs text-emerald-200/80 truncate">{{ $pegawai->jabatan->nama_jabatan ?? 'Staf Perangkat Desa' }}</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-emerald-800/60 grid grid-cols-2 gap-2 text-xs relative z-10">
            <div>
                <span class="text-emerald-300/60 text-[10px] uppercase font-semibold">NIPD</span>
                <p class="font-mono text-emerald-100 font-semibold">{{ $pegawai->nipd ?? '—' }}</p>
            </div>
            <div>
                <span class="text-emerald-300/60 text-[10px] uppercase font-semibold">Shift Kerja</span>
                <p class="text-emerald-100 font-semibold">{{ $pegawai->shiftKerja->nama_shift ?? 'Reguler Kantor' }}</p>
            </div>
        </div>
    </div>

    <!-- Papan Pengumuman & Informasi Desa (Slider Modern dengan Panah Samping & Batas Akhir) -->
    @if(isset($pengumumans) && $pengumumans->count() > 0)
    <div class="space-y-2" x-data="{ currentSlide: 0, total: {{ $pengumumans->count() }} }">
        <div class="flex items-center justify-between px-1">
            <h3 class="font-outfit font-bold text-[#064E3B] text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Pengumuman & Arahan Desa</span>
            </h3>
            
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-mono">
                Info <span x-text="currentSlide + 1">1</span> dari {{ $pengumumans->count() }}
            </span>
        </div>

        {{-- Slider Wrapper with Side Arrow Buttons --}}
        <div class="relative group">
            
            {{-- Left Arrow Button --}}
            @if($pengumumans->count() > 1)
                <button type="button"
                        @click="if (currentSlide > 0) currentSlide--"
                        :disabled="currentSlide === 0"
                        :class="currentSlide === 0 ? 'opacity-30 cursor-not-allowed text-slate-400 bg-white/70' : 'opacity-90 hover:opacity-100 hover:scale-105 text-[#064E3B] bg-white shadow-md cursor-pointer border-[#C9A84C]/60'"
                        class="absolute -left-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full border flex items-center justify-center transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
            @endif

            {{-- Cards Container --}}
            <div class="overflow-hidden rounded-2xl">
                @foreach($pengumumans as $idx => $p)
                    @php
                        $badgeBg = match($p->kategori) {
                            'penting' => 'bg-red-500 text-white',
                            'rapat' => 'bg-amber-500 text-white',
                            'kegiatan' => 'bg-blue-500 text-white',
                            default => 'bg-[#064E3B] text-[#E2C268]',
                        };
                        $cardBorder = $p->is_pinned ? 'border-2 border-[#C9A84C]' : 'border border-slate-200';
                    @endphp
                    <div x-show="currentSlide === {{ $idx }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="sadi-card p-4 {{ $pengumumans->count() > 1 ? 'px-6' : 'px-4' }} bg-white {{ $cardBorder }} shadow-md relative overflow-hidden rounded-2xl"
                         style="{{ $idx > 0 ? 'display: none;' : '' }}">

                        @if($p->is_pinned)
                            <div class="absolute top-0 right-0 bg-[#C9A84C] text-[#064E3B] text-[9px] font-extrabold px-3 py-0.5 rounded-bl-xl uppercase tracking-wider flex items-center gap-1 shadow-sm">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                <span>Disematkan</span>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#064E3B] to-[#04392B] text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                @if($p->kategori === 'rapat')
                                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                @elseif($p->kategori === 'penting')
                                    <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @elseif($p->kategori === 'kegiatan')
                                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 pr-6">
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider {{ $badgeBg }}">
                                        {{ ucfirst($p->kategori) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3 text-slate-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $p->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB
                                    </span>
                                </div>

                                <h4 class="font-outfit font-bold text-sm text-[#064E3B] leading-snug">{{ $p->judul }}</h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed whitespace-pre-line">{{ $p->isi }}</p>

                                @if($p->berlaku_hingga)
                                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                                        <span class="text-slate-400 font-medium">Batas Waktu:</span>
                                        <span class="font-bold text-amber-900 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            Sampai {{ \Carbon\Carbon::parse($p->berlaku_hingga)->isoFormat('D MMMM Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Right Arrow Button --}}
            @if($pengumumans->count() > 1)
                <button type="button"
                        @click="if (currentSlide < total - 1) currentSlide++"
                        :disabled="currentSlide === total - 1"
                        :class="currentSlide === total - 1 ? 'opacity-30 cursor-not-allowed text-slate-400 bg-white/70' : 'opacity-90 hover:opacity-100 hover:scale-105 text-[#064E3B] bg-white shadow-md cursor-pointer border-[#C9A84C]/60'"
                        class="absolute -right-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full border flex items-center justify-center transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif

        </div>

        {{-- Dots Indicators if > 1 --}}
        @if($pengumumans->count() > 1)
            <div class="flex items-center justify-center gap-1.5 pt-1">
                @foreach($pengumumans as $idx => $p)
                    <button type="button" @click="currentSlide = {{ $idx }}"
                            :class="currentSlide === {{ $idx }} ? 'w-5 bg-[#064E3B]' : 'w-1.5 bg-slate-300'"
                            class="h-1.5 rounded-full transition-all duration-200 cursor-pointer">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    <!-- ═══ BANNER STATUS HARI INI: LEPAS PIKET ═══ -->
    @if(isset($isLepasPiketHariIni) && $isLepasPiketHariIni)
    <div class="sadi-card p-4 bg-linear-to-r from-emerald-900 via-[#064E3B] to-emerald-800 text-white border-2 border-[#E2C268] shadow-lg rounded-2xl flex items-start gap-3.5 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-[#E2C268]/15 rounded-full blur-lg pointer-events-none"></div>
        <div class="w-11 h-11 rounded-2xl bg-[#E2C268] text-[#064E3B] flex items-center justify-center font-bold text-xl shadow-md shrink-0">
            🌙
        </div>
        <div class="flex-1 min-w-0">
            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                STATUS HARI INI: LEPAS PIKET
            </span>
            <h4 class="font-outfit font-extrabold text-sm text-white mt-1">
                Istirahat Lepas Tugas Piket Malam
            </h4>
            <p class="text-[11px] text-emerald-200 mt-0.5 leading-relaxed">
                Presensi kehadiran Anda hari ini telah <strong>otomatis dicatat sebagai Hadir / Lepas Piket</strong> setelah melaksanakan tugas piket.
            </p>
        </div>
    </div>
    @endif

    <!-- ═══ NOTIFIKASI JADWAL PIKET DESA (H-1 & HARI INI) ═══ -->
    @if(isset($notifPikets) && $notifPikets->count() > 0)
    <div class="space-y-3" x-data="{ activePiketModal: null }">
        @foreach($notifPikets as $piket)
            @php
                $isToday = $piket->tanggal_piket->isToday();
                $isTomorrow = $piket->tanggal_piket->isTomorrow();
                $isSudahAbsen = ($piket->status === 'hadir' || $piket->status === 'lepas_piket');
            @endphp
            <div class="sadi-card p-4 {{ $isToday ? 'bg-linear-to-br from-[#064E3B] to-[#04392B] text-white border-2 border-[#C9A84C]' : 'bg-slate-900 text-white border border-slate-700' }} shadow-lg relative overflow-hidden rounded-2xl">
                <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-[#C9A84C]/15 rounded-full blur-lg pointer-events-none"></div>

                <div class="flex items-start gap-3.5 relative z-10">
                    <div class="w-10 h-10 rounded-2xl {{ $isToday ? 'bg-[#C9A84C] text-[#064E3B]' : 'bg-slate-800 text-amber-400' }} flex items-center justify-center shrink-0 shadow-md font-bold text-lg">
                        🛡️
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $isToday ? 'bg-[#E2C268] text-[#064E3B]' : 'bg-blue-900 text-blue-200 border border-blue-700' }}">
                                {{ $isToday ? 'JADWAL PIKET HARI INI' : ($isTomorrow ? 'JADWAL PIKET BESOK (H-1)' : 'JADWAL PIKET DESA') }}
                            </span>
                            @if($isSudahAbsen)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500 text-white shadow-xs">✓ Hadir</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-400 text-slate-900 animate-pulse">Wajib Hadir</span>
                            @endif
                        </div>

                        <h4 class="font-outfit font-extrabold text-sm text-white mt-1 leading-snug">
                            {{ $piket->keterangan }}
                        </h4>
                        
                        <p class="text-xs text-emerald-100 font-semibold mt-0.5">
                            Waktu: <span class="text-[#E2C268] font-mono font-bold">{{ \Carbon\Carbon::parse($piket->tanggal_piket)->isoFormat('dddd, D MMMM Y') }} ({{ substr($piket->jam_mulai, 0, 5) }} - {{ substr($piket->jam_selesai, 0, 5) }} WIB)</span>
                        </p>

                        <div class="mt-3 pt-2.5 border-t border-emerald-800/80 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="text-[10px] text-emerald-300">
                                <span>Kompensasi:</span>
                                <strong class="text-white block font-sans">Otomatis Lepas Piket hari berikutnya</strong>
                            </div>

                            @if(!$isSudahAbsen)
                            <button type="button" @click="activePiketModal = {{ $piket->id }}; setTimeout(() => { initPiketPad({{ $piket->id }}) }, 100);"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#C9A84C] text-[#064E3B] font-outfit text-[11px] font-extrabold shadow hover:bg-[#E2C268] transition cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Tanda Tangan Absen Piket</span>
                            </button>
                            @else
                            <span class="text-[11px] text-emerald-300 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Telah Absen Piket ({{ $piket->waktu_absen?->format('H:i') }} WIB)
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- MODAL TANDA TANGAN DIGITAL ABSEN PIKET -->
                <div x-show="activePiketModal === {{ $piket->id }}"
                     x-transition.opacity
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                     style="display: none;"
                     @keydown.escape.window="activePiketModal = null">
                    <div @click.away="activePiketModal = null"
                         class="bg-white text-slate-800 rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-6 flex flex-col max-h-[92vh]">
                        
                        <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#E2C268] animate-pulse"></span>
                                <h3 class="font-outfit text-sm font-bold text-white">Tanda Tangan Presensi Piket</h3>
                            </div>
                            <button type="button" @click="activePiketModal = null" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form id="form-piket-{{ $piket->id }}" action="{{ route('staf.piket.absen') }}" method="POST" onsubmit="return submitPiketForm(event, {{ $piket->id }})" class="p-4 space-y-3 text-xs">
                            @csrf
                            <input type="hidden" name="piket_id" value="{{ $piket->id }}">
                            <input type="hidden" name="tanda_tangan" id="input-ttd-{{ $piket->id }}">

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                                <p class="font-bold text-slate-800">{{ $piket->keterangan }}</p>
                                <p class="text-[11px] text-slate-600">Pelaksanaan: {{ \Carbon\Carbon::parse($piket->tanggal_piket)->isoFormat('dddd, D MMMM Y') }} ({{ substr($piket->jam_mulai, 0, 5) }} - {{ substr($piket->jam_selesai, 0, 5) }} WIB)</p>
                                <p class="text-[10px] text-emerald-700 font-semibold leading-relaxed">
                                    * Tanda tangan digital ini mengonfirmasi kehadiran Anda pada tugas piket malam dan secara otomatis mencatatkan kehadiran Anda sebagai <strong>Lepas Piket</strong> di hari berikutnya.
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Bubuhkan Tanda Tangan Anda</label>
                                    <button type="button" onclick="clearPiketPad({{ $piket->id }})" class="text-[11px] font-bold text-red-600 hover:underline cursor-pointer">Hapus / Ulangi</button>
                                </div>
                                <div id="wrapper-piket-{{ $piket->id }}" class="border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 overflow-hidden relative">
                                    <canvas id="canvas-piket-{{ $piket->id }}" class="w-full h-40 cursor-crosshair touch-none"></canvas>
                                </div>
                            </div>

                            <div class="pt-2 flex justify-end gap-2">
                                <button type="button" @click="activePiketModal = null" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold hover:bg-[#04392B] transition cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Simpan Presensi Piket</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Notifikasi Surat Perintah Tugas (SPT) Resmi dari Kepala Desa / Pemerintah Desa -->
    @if(isset($notifSpts) && $notifSpts->count() > 0)
    <div class="space-y-3" x-data="{ activeSptModal: null }">
        @foreach($notifSpts as $spt)
        <div class="sadi-card p-4 bg-gradient-to-br from-[#064E3B] to-[#04392B] text-white border-2 border-[#C9A84C] shadow-lg relative overflow-hidden rounded-2xl">
            <!-- Background accent -->
            <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-[#C9A84C]/15 rounded-full blur-lg pointer-events-none"></div>

            <div class="flex items-start gap-3.5 relative z-10">
                <div class="w-10 h-10 rounded-2xl bg-[#C9A84C] text-[#064E3B] flex items-center justify-center shrink-0 shadow-md font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                            Surat Perintah Tugas (SPT)
                        </span>
                        <span class="text-[10px] text-emerald-200/80 font-mono font-semibold">
                            {{ $spt->created_at ? $spt->created_at->diffForHumans() : '' }}
                        </span>
                    </div>

                    <h4 class="font-outfit font-extrabold text-sm text-white mt-1 leading-snug">
                        {{ $spt->nomor_spt }}
                    </h4>
                    
                    <p class="text-xs text-emerald-100 font-semibold mt-0.5">
                        Tujuan: <span class="text-[#E2C268] font-bold">{{ $spt->tujuan }}</span>
                    </p>
                    
                    <p class="text-[11px] text-emerald-200/90 mt-1 leading-relaxed">
                        Keperluan: {{ $spt->keperluan }}
                    </p>

                    <div class="mt-3 pt-2.5 border-t border-emerald-800/80 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="text-[10px] text-emerald-300">
                            <span>Pelaksanaan:</span>
                            <strong class="text-white block font-sans">
                                {{ \Carbon\Carbon::parse($spt->tanggal_mulai)->isoFormat('D MMM Y') }}
                                @if($spt->tanggal_mulai != $spt->tanggal_selesai)
                                    s/d {{ \Carbon\Carbon::parse($spt->tanggal_selesai)->isoFormat('D MMM Y') }}
                                @endif
                            </strong>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="activeSptModal = {{ $spt->id }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#C9A84C] text-[#064E3B] font-outfit text-[11px] font-extrabold shadow hover:bg-[#E2C268] transition cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Berkas / Softfile</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL VIEWER SOFTFILE SURAT PERINTAH TUGAS (SPT) -->
            <div x-show="activeSptModal === {{ $spt->id }}"
                 x-transition.opacity
                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                 style="display: none;"
                 @keydown.escape.window="activeSptModal = null">
                <div @click.away="activeSptModal = null"
                     class="bg-white text-slate-800 rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-6 flex flex-col max-h-[92vh]">
                    
                    <!-- Header Modal -->
                    <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#E2C268] animate-pulse"></span>
                            <div>
                                <h3 class="font-outfit text-sm font-bold text-white">Softfile Surat Perintah Tugas</h3>
                                <p class="text-[10px] text-emerald-200 font-mono">{{ $spt->nomor_spt }}</p>
                            </div>
                        </div>
                        <button type="button" @click="activeSptModal = null" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Body Modal (Softfile Display) -->
                    <div class="p-4 overflow-y-auto space-y-3.5 text-xs">
                        
                        <!-- Ringkasan Info Penugasan -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-2 gap-2 text-[11px]">
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[9px]">Tujuan Kedinasan</span>
                                <p class="font-bold text-[#064E3B] mt-0.5">{{ $spt->tujuan }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[9px]">Waktu Pelaksanaan</span>
                                <p class="font-bold text-slate-800 mt-0.5">
                                    {{ \Carbon\Carbon::parse($spt->tanggal_mulai)->isoFormat('D MMM Y') }}
                                    @if($spt->tanggal_mulai != $spt->tanggal_selesai)
                                        s/d {{ \Carbon\Carbon::parse($spt->tanggal_selesai)->isoFormat('D MMM Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-span-2 pt-1 border-t border-slate-200/60">
                                <span class="text-slate-400 font-bold uppercase text-[9px]">Agenda / Keperluan</span>
                                <p class="text-slate-700 mt-0.5">{{ $spt->keperluan }}</p>
                            </div>
                        </div>

                        <!-- Soft Copy File Preview -->
                        @if($spt->file_undangan)
                            @php
                                $ext = strtolower(pathinfo($spt->file_undangan, PATHINFO_EXTENSION));
                                $fileUrl = asset('storage/' . $spt->file_undangan);
                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                            @endphp

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-700 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Softfile Surat ({{ strtoupper($ext) }})</span>
                                    </span>
                                    <a href="{{ $fileUrl }}" target="_blank" download class="text-[11px] font-extrabold text-[#064E3B] underline hover:text-emerald-900">
                                        Unduh File Asli
                                    </a>
                                </div>

                                @if($isImg)
                                    <div class="rounded-xl overflow-hidden border-2 border-slate-200 bg-slate-100 p-1 text-center shadow-inner">
                                        <img src="{{ $fileUrl }}" alt="Softfile SPT {{ $spt->nomor_spt }}" class="max-h-96 w-auto mx-auto rounded-lg object-contain">
                                    </div>
                                @else
                                    <div class="rounded-xl overflow-hidden border-2 border-slate-200 bg-slate-100 shadow-inner">
                                        <iframe src="{{ $fileUrl }}" class="w-full h-80 border-0 rounded-lg"></iframe>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-6 bg-amber-50 rounded-xl border border-amber-200 text-center space-y-2">
                                <svg class="w-8 h-8 text-amber-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="font-bold text-amber-900 text-xs">Softfile berkas belum diunggah oleh admin</p>
                                <p class="text-[11px] text-amber-800">Dokumen fisik asli SPT tersimpan dan dapat diambil di Kantor Desa Nangtang.</p>
                            </div>
                        @endif

                    </div>

                    <!-- Footer Modal -->
                    <div class="px-5 py-3 bg-slate-100 border-t border-slate-200 flex items-center justify-between shrink-0">
                        <span class="text-[10px] text-slate-500 font-medium">Status: <strong class="text-emerald-700 font-bold">Sah & Tercatat di Presensi</strong></span>
                        <div class="flex items-center gap-2">
                            @if($spt->file_undangan)
                            <a href="{{ asset('storage/' . $spt->file_undangan) }}" target="_blank"
                               class="px-3.5 py-1.5 rounded-xl bg-[#064E3B] text-white text-xs font-bold shadow hover:bg-[#04392B] transition inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Buka Layar Penuh</span>
                            </a>
                            @endif
                            <button type="button" @click="activeSptModal = null" class="px-3.5 py-1.5 rounded-xl bg-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-300 transition cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Notifikasi Approval Pengajuan Absen Luar -->
    @if(isset($notifPengajuan) && $notifPengajuan)
    <div class="sadi-card p-4 border-2 shadow-md flex items-start gap-3.5
        {{ $notifPengajuan->status === 'disetujui' ? 'bg-emerald-50 border-emerald-300' : 'bg-red-50 border-red-300' }}">
        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-sm
            {{ $notifPengajuan->status === 'disetujui' ? 'bg-[#064E3B]' : 'bg-red-600' }}">
            @if($notifPengajuan->status === 'disetujui')
                <svg class="w-5 h-5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <h4 class="font-outfit font-extrabold text-xs {{ $notifPengajuan->status === 'disetujui' ? 'text-[#064E3B]' : 'text-red-900' }}">
                    Pengajuan Absen Luar: {{ $notifPengajuan->status === 'disetujui' ? 'DISETUJUI ADMIN' : 'DITOLAK ADMIN' }}
                </h4>
                <span class="text-[9px] font-bold text-slate-400 shrink-0">{{ $notifPengajuan->diproses_pada ? $notifPengajuan->diproses_pada->diffForHumans() : '' }}</span>
            </div>
            <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $notifPengajuan->judul }}</p>
            @if($notifPengajuan->catatan_admin)
                <p class="text-[11px] text-slate-600 mt-1 italic">"{{ $notifPengajuan->catatan_admin }}"</p>
            @endif
            <div class="mt-2 flex items-center gap-2">
                <a href="{{ route('staf.riwayat.pengajuan') }}" class="text-[11px] font-extrabold {{ $notifPengajuan->status === 'disetujui' ? 'text-[#064E3B] underline' : 'text-red-700 underline' }}">
                    Lihat Bukti & Riwayat Pengajuan &rarr;
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Status Jaringan WiFi Banner -->
    <div class="p-4 rounded-2xl {{ $isWifiValid ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border-2 border-red-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl {{ $isWifiValid ? 'bg-emerald-600 text-white' : 'bg-red-500 text-white' }} flex items-center justify-center shrink-0 shadow-sm">
                @if($isWifiValid)
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M8.464 8.464a5 5 0 000 7.072M5.636 5.636a9 9 0 000 12.728"/></svg>
                @endif
            </div>
            <div>
                <p class="text-xs font-extrabold {{ $isWifiValid ? 'text-emerald-900' : 'text-red-900' }} flex items-center gap-1.5">
                    @if($isWifiValid)
                        <svg class="w-3.5 h-3.5 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Terhubung Jaringan WiFi Kantor Desa</span>
                    @else
                        <svg class="w-3.5 h-3.5 text-red-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tidak Terhubung WiFi Kantor Desa</span>
                    @endif
                </p>
                <p class="text-[11px] {{ $isWifiValid ? 'text-emerald-700' : 'text-red-700' }} mt-0.5 leading-snug">
                    @if($isWifiValid)
                        Koneksi valid dan terverifikasi untuk tanda tangan presensi digital langsung di kantor.
                    @else
                        Jika Anda sedang <strong>Dinas Luar</strong> atau <strong>Tugas Lapangan</strong>, silakan ajukan melalui menu <strong>Absen Luar</strong>.
                    @endif
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-2 self-end sm:self-center">
            @if(!$isWifiValid && (!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk))
                <a href="{{ route('staf.ajukan.form') }}" class="px-3.5 py-2 rounded-xl bg-[#064E3B] text-white font-extrabold text-xs shadow hover:bg-[#04392B] transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Ajukan Absen Luar</span>
                </a>
            @endif
            <span class="text-[10px] font-mono font-bold px-2 py-1 rounded bg-white/80 border border-slate-200 {{ $isWifiValid ? 'text-emerald-800' : 'text-red-800' }} shrink-0">
                {{ $clientIp }}
            </span>
        </div>
    </div>

    <!-- Status Presensi Hari Ini Card -->
    <div class="sadi-card p-5 bg-white space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-outfit font-bold text-[#064E3B] text-sm">Status Presensi Hari Ini</h3>
            <span class="text-[11px] font-semibold text-slate-500">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Jam Masuk</span>
                <p class="font-mono text-base font-extrabold text-[#064E3B] mt-0.5">
                    {{ $kehadiranHariIni?->jam_masuk ? substr($kehadiranHariIni->jam_masuk, 0, 5) . ' WIB' : 'Belum Ada' }}
                </p>
                @if($kehadiranHariIni?->jam_masuk)
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9px] font-bold">Tercatat</span>
                @endif
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Jam Pulang</span>
                <p class="font-mono text-base font-extrabold text-blue-700 mt-0.5">
                    {{ $kehadiranHariIni?->jam_pulang ? substr($kehadiranHariIni->jam_pulang, 0, 5) . ' WIB' : 'Belum Ada' }}
                </p>
                @if($kehadiranHariIni?->jam_pulang)
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[9px] font-bold">Tercatat</span>
                @endif
            </div>
        </div>

        @if($kehadiranHariIni && $kehadiranHariIni->status)
        <div class="p-3 rounded-xl bg-emerald-50 text-center text-xs font-bold text-emerald-800">
            Status Kehadiran: {{ $kehadiranHariIni->status }}
        </div>
        @endif
    </div>

    <!-- Tombol Aksi Absensi (Berdasarkan Jam & WiFi) -->
    <div class="space-y-3">
        {{-- Info jika sedang menunggu persetujuan absen luar --}}
        @if(isset($pengajuanHariIni) && $pengajuanHariIni && $pengajuanHariIni->status === 'menunggu')
            <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-center space-y-1.5 shadow-sm">
                <div class="flex items-center justify-center gap-1.5 text-xs font-extrabold text-amber-900">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pengajuan Absen Luar Sedang Menunggu Persetujuan Admin</span>
                </div>
                <p class="text-[11px] text-amber-800">
                    Anda telah mengajukan <strong>{{ $pengajuanHariIni->judul }}</strong> ({{ $pengajuanHariIni->label_jenis }}). Status presensi akan otomatis terupdate begitu disetujui.
                </p>
                <div class="pt-1">
                    <a href="{{ route('staf.riwayat.pengajuan') }}" class="text-[11px] font-bold text-amber-900 underline">Lihat Detail Pengajuan &rarr;</a>
                </div>
            </div>
        @endif

        {{-- Info jika berstatus Dinas Luar / Izin / Sakit resmi tanpa scan kantor --}}
        @if($kehadiranHariIni && in_array(strtolower($kehadiranHariIni->status), ['dinas luar', 'izin', 'sakit']) && !$kehadiranHariIni->jam_masuk)
            <div class="p-4 rounded-2xl bg-indigo-50 border-2 border-indigo-200 text-center space-y-1.5 shadow-sm">
                <div class="flex items-center justify-center gap-1.5 text-xs font-extrabold text-indigo-900">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Status Presensi Hari Ini: {{ $kehadiranHariIni->status }}</span>
                </div>
                <p class="text-[11px] text-indigo-800">
                    {{ $kehadiranHariIni->keterangan ?? 'Anda tercatat sedang bertugas dinas luar / izin resmi hari ini.' }}
                </p>
            </div>
        @endif

        {{-- Tombol Absen Masuk --}}
        @if((!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk) && !($kehadiranHariIni && in_array(strtolower($kehadiranHariIni->status), ['dinas luar', 'izin', 'sakit'])))
            @if($bisaAbsenMasuk)
                <a href="{{ route('staf.absen.form', 'masuk') }}"
                    class="w-full py-4 rounded-2xl btn-gold text-center text-base font-bold shadow-xl active:scale-98 transition duration-150 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Buka Absen Masuk Sekarang</span>
                </a>
            @else
                <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-center space-y-1">
                    <p class="text-xs font-bold text-slate-600 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Absen Masuk Belum Tersedia</span>
                    </p>
                    <p class="text-[11px] text-slate-500">
                        @if(!$isWifiValid)
                            Sambungkan ke WiFi kantor desa terlebih dahulu.
                        @elseif(!$isWaktuMasuk)
                            Jadwal absen masuk: <strong class="text-slate-700">{{ $jamMasukMulai }} – {{ $jamMasukSelesai }} WIB</strong>.
                        @endif
                    </p>
                </div>
            @endif
        @endif

        {{-- Tombol Absen Pulang --}}
        @if($kehadiranHariIni && $kehadiranHariIni->jam_masuk && !$kehadiranHariIni->jam_pulang)
            @if($bisaAbsenPulang)
                <a href="{{ route('staf.absen.form', 'pulang') }}"
                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-800 text-white text-center text-base font-bold shadow-xl active:scale-98 transition duration-150 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Buka Absen Pulang Sekarang</span>
                </a>
            @else
                <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-center space-y-1">
                    <p class="text-xs font-bold text-slate-600 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Absen Pulang Belum Tersedia</span>
                    </p>
                    <p class="text-[11px] text-slate-500">
                        @if(!$isWifiValid)
                            Sambungkan ke WiFi kantor desa terlebih dahulu.
                        @elseif(!$isWaktuPulang)
                            Jadwal absen pulang: <strong class="text-slate-700">{{ $jamPulangMulai }} – {{ $jamPulangSelesai }} WIB</strong>.
                        @endif
                    </p>
                </div>
            @endif
        @endif

        @if($kehadiranHariIni && $kehadiranHariIni->jam_masuk && $kehadiranHariIni->jam_pulang)
            <div class="p-4 rounded-2xl bg-emerald-100/70 border border-emerald-200 text-center text-xs font-bold text-emerald-900 flex items-center justify-center gap-2">
                <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Anda telah menyelesaikan seluruh presensi masuk & pulang hari ini.</span>
            </div>
        @endif
    </div>

    <!-- Riwayat Singkat -->
    <div class="sadi-card p-5 bg-white space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h4 class="font-outfit font-bold text-[#064E3B] text-xs uppercase tracking-wider">Riwayat 5 Hari Terakhir</h4>
            <a href="{{ route('staf.riwayat') }}" class="text-[11px] font-bold text-[#C9A84C] hover:underline">Lihat Semua →</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($riwayatTerakhir as $r)
            <div class="py-2.5 flex items-center justify-between text-xs">
                <div>
                    <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</p>
                    <p class="text-[10px] text-slate-400">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '—' }} - {{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '—' }}</p>
                </div>
                <span class="px-2 py-1 rounded-full text-[10px] font-bold
                    {{ $r->status === 'Dinas Luar' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $r->status }}
                </span>
            </div>
            @empty
            <p class="text-xs text-slate-400 text-center py-3">Belum ada riwayat presensi.</p>
            @endforelse
        </div>
    </div>

    <!-- Tombol Pengajuan Absen Luar -->
    @php
        $pengajuanAktif = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('tanggal', \Carbon\Carbon::today()->toDateString())
            ->first();
    @endphp

    <div class="sadi-card p-5 bg-white space-y-3 border border-slate-200 shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 border-2 border-amber-300 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-outfit font-bold text-slate-800 text-sm">Sedang Bertugas di Luar?</p>
                <p class="text-[11px] text-slate-500">Ajukan kehadiran Dinas Luar atau Kegiatan Sosial untuk diverifikasi Admin</p>
            </div>
        </div>

        @if($pengajuanAktif)
            {{-- Ada pengajuan hari ini --}}
            <div class="p-3 rounded-xl {{ $pengajuanAktif->status === 'menunggu' ? 'bg-amber-50 border border-amber-200' : ($pengajuanAktif->status === 'disetujui' ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200') }} text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold {{ $pengajuanAktif->status === 'menunggu' ? 'text-amber-900' : ($pengajuanAktif->status === 'disetujui' ? 'text-emerald-900' : 'text-red-900') }}">
                        {{ $pengajuanAktif->judul }}
                    </span>
                    <span class="font-extrabold px-2 py-0.5 rounded-full border text-[10px] {{ $pengajuanAktif->badge_class }}">
                        {{ $pengajuanAktif->label_status }}
                    </span>
                </div>
                <p class="text-slate-500">{{ $pengajuanAktif->label_jenis }}</p>
            </div>
            <a href="{{ route('staf.riwayat.pengajuan') }}"
               class="w-full py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs flex items-center justify-center gap-2 hover:bg-slate-50 transition">
                Lihat Semua Riwayat Pengajuan →
            </a>
        @else
            <a href="{{ route('staf.ajukan.form') }}"
               class="w-full py-3.5 rounded-xl border-2 border-amber-400 bg-amber-50 hover:bg-amber-100 text-amber-900 font-extrabold text-sm flex items-center justify-center gap-2.5 transition cursor-pointer shadow-sm">
                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajukan Absen Luar Sekarang
            </a>
        @endif
    </div>

    <!-- Tombol Pengajuan Izin / Sakit Quick Card -->
    <div class="sadi-card p-5 bg-white space-y-3 border border-slate-200 shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 border-2 border-emerald-300 flex items-center justify-center shrink-0 text-[#064E3B]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-outfit font-bold text-slate-800 text-sm">Berhalangan Hadir / Sakit?</p>
                <p class="text-[11px] text-slate-500">Ajukan permohonan Izin atau Sakit secara digital ke Kepala Desa</p>
            </div>
        </div>

        <a href="{{ route('staf.izin') }}"
           class="w-full py-3.5 rounded-xl border-2 border-emerald-600 bg-emerald-50 hover:bg-emerald-100 text-[#064E3B] font-extrabold text-sm flex items-center justify-center gap-2.5 transition cursor-pointer shadow-sm">
            <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Ajukan Izin / Sakit Sekarang</span>
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const piketPads = {};

    function initPiketPad(piketId) {
        const canvas = document.getElementById('canvas-piket-' + piketId);
        const wrapper = document.getElementById('wrapper-piket-' + piketId);
        if (!canvas || !wrapper) return;

        canvas.width = wrapper.offsetWidth || 350;
        canvas.height = 160;

        if (!piketPads[piketId]) {
            piketPads[piketId] = new SignaturePad(canvas, {
                minWidth: 1.5,
                maxWidth: 3.5,
                penColor: '#064E3B',
                backgroundColor: 'rgba(0,0,0,0)'
            });
        }
    }

    function clearPiketPad(piketId) {
        if (piketPads[piketId]) {
            piketPads[piketId].clear();
        }
    }

    function submitPiketForm(e, piketId) {
        const pad = piketPads[piketId];
        if (!pad || pad.isEmpty()) {
            e.preventDefault();
            alert('⚠️ Harap bubuhkan tanda tangan digital Anda terlebih dahulu sebelum menyimpan absensi piket.');
            return false;
        }

        const inputTtd = document.getElementById('input-ttd-' + piketId);
        if (inputTtd) {
            inputTtd.value = pad.toDataURL('image/png');
        }
        return true;
    }
</script>
@endsection

