<div wire:poll.5s class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false">
    {{-- ══════════ TOMBOL LONCENG DENGAN BADGE ANGKA ══════════ --}}
    <button @click="open = !open" type="button"
            class="p-2.5 rounded-full bg-white border border-[#C9A84C]/40 text-slate-700 hover:text-[#064E3B] hover:border-[#064E3B] transition relative shadow-sm cursor-pointer flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Badge Jumlah Notifikasi Mirip WhatsApp / E-Commerce --}}
        @if($totalCount > 0)
            <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1.5 bg-red-600 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center border-2 border-white shadow-md leading-none animate-pulse">
                {{ $totalCount > 99 ? '99+' : $totalCount }}
            </span>
        @endif
    </button>

    {{-- ══════════ PANEL DROPDOWN NOTIFIKASI ELEGAN SESUAI TEMA ══════════ --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute right-0 mt-3 w-84 sm:w-96 rounded-3xl shadow-2xl overflow-hidden z-50 divide-y divide-slate-100"
         style="display: none; background-color: #FFFFFF; border: 2px solid #C9A84C; box-shadow: 0 20px 40px -15px rgba(6, 78, 59, 0.25);">

        {{-- Header Panel --}}
        <div class="p-4.5 flex items-center justify-between" style="background: linear-gradient(135deg, #064E3B 0%, #04392B 100%); border-bottom: 2px solid #C9A84C;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-inner" style="background-color: #04392B; border: 1.5px solid #C9A84C;">
                    <svg class="w-5 h-5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <h3 class="font-outfit font-extrabold text-sm text-white tracking-wide">Pusat Notifikasi Presensi</h3>
                    <p class="text-[11px] font-semibold text-emerald-200/90 mt-0.5">
                        {{ $totalCount > 0 ? $totalCount . ' permohonan butuh verifikasi' : 'Tidak ada permohonan tertunda' }}
                    </p>
                </div>
            </div>
            @if($totalCount > 0)
                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-red-600 text-white shadow">
                    {{ $totalCount }} Baru
                </span>
            @endif
        </div>

        {{-- Mini Summary Categories (Tab-like summary) --}}
        <div class="grid grid-cols-3 p-2.5 text-center text-xs gap-1.5" style="background-color: #FAF6F0; border-bottom: 1px solid rgba(201, 168, 76, 0.25);">
            <a href="{{ route('pengajuan-absen.index') }}" wire:navigate class="p-2 rounded-xl transition group bg-white border border-slate-200/80 hover:border-[#064E3B] shadow-sm">
                <p class="font-outfit font-extrabold text-sm {{ $totalPengajuanLuar > 0 ? 'text-amber-800' : 'text-slate-700' }}">{{ $totalPengajuanLuar }}</p>
                <p class="text-[10px] font-bold text-slate-500 group-hover:text-[#064E3B]">Absen Luar</p>
            </a>
            <a href="{{ route('izin.index') }}" wire:navigate class="p-2 rounded-xl transition group bg-white border border-slate-200/80 hover:border-[#064E3B] shadow-sm">
                <p class="font-outfit font-extrabold text-sm {{ $totalIzinSakit > 0 ? 'text-teal-800' : 'text-slate-700' }}">{{ $totalIzinSakit }}</p>
                <p class="text-[10px] font-bold text-slate-500 group-hover:text-[#064E3B]">Izin / Sakit</p>
            </a>
            <a href="{{ route('spt.index') }}" wire:navigate class="p-2 rounded-xl transition group bg-white border border-slate-200/80 hover:border-[#064E3B] shadow-sm">
                <p class="font-outfit font-extrabold text-sm {{ $totalSpt > 0 ? 'text-blue-800' : 'text-slate-700' }}">{{ $totalSpt }}</p>
                <p class="text-[10px] font-bold text-slate-500 group-hover:text-[#064E3B]">SPT Dinas</p>
            </a>
        </div>

        {{-- Daftar Notifikasi List --}}
        <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-100 bg-white">

            @if($totalCount === 0)
                <div class="p-8 text-center space-y-3 bg-white">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto" style="background-color: #ECFDF5; border: 2px solid #A7F3D0;">
                        <svg class="w-7 h-7 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-outfit font-extrabold text-sm text-[#064E3B]">Semua Bersih & Terverifikasi</p>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">
                            Tidak ada pengajuan absen luar, surat izin, atau SPT dinas yang tertunda saat ini.
                        </p>
                    </div>
                </div>
            @endif

            {{-- 1. Pengajuan Absen Luar Items --}}
            @foreach($pengajuanLuars as $p)
                <a href="{{ route('pengajuan-absen.index') }}" wire:navigate
                   class="p-3.5 hover:bg-amber-50/80 transition flex items-start gap-3 block group border-l-4 border-amber-600 bg-white">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 border border-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 4h.01M9 12h.01M9 16h.01M13 12h4m-4 4h2"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300">
                                {{ $p->label_jenis }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">{{ $p->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-bold text-xs text-slate-900 truncate group-hover:text-[#064E3B]">
                            {{ $p->pegawai->nama_lengkap ?? 'Perangkat Desa' }}
                        </p>
                        <p class="text-[11px] text-slate-600 line-clamp-1 mt-0.5">
                            {{ $p->judul }}
                        </p>
                        <div class="flex items-center gap-2 mt-1.5 text-[10px]">
                            <span class="text-amber-800 font-bold">⚠️ Butuh Tindakan Admin</span>
                            @if($p->latitude)
                                <span class="text-emerald-700 font-semibold">📍 GPS Aktif</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- 2. Pengajuan Izin & Sakit Items --}}
            @foreach($izinSakits as $i)
                @php
                    $isSakit = str_contains($i->jenis, 'sakit');
                @endphp
                <a href="{{ route('izin.index') }}" wire:navigate
                   class="p-3.5 {{ $isSakit ? 'hover:bg-rose-50/80 border-rose-500' : 'hover:bg-teal-50/80 border-teal-600' }} transition flex items-start gap-3 block group border-l-4 bg-white">
                    <div class="w-9 h-9 rounded-xl {{ $isSakit ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-teal-100 text-teal-800 border-teal-300' }} border flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded {{ $isSakit ? 'bg-rose-100 text-rose-900 border-rose-300' : 'bg-teal-100 text-teal-900 border-teal-300' }} border">
                                Permohonan {{ ucfirst(str_replace('_', ' ', $i->jenis)) }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">{{ $i->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-bold text-xs text-slate-900 truncate group-hover:text-[#064E3B]">
                            {{ $i->pegawai->nama_lengkap ?? 'Perangkat Desa' }}
                        </p>
                        <p class="text-[11px] text-slate-600 line-clamp-1 mt-0.5">
                            {{ $i->keterangan }}
                        </p>
                        <div class="mt-1 text-[10px] {{ $isSakit ? 'text-rose-800' : 'text-teal-800' }} font-bold">
                            📅 {{ $i->tanggal_mulai->format('d/m/Y') }} — {{ $i->tanggal_selesai->format('d/m/Y') }} ({{ $i->jumlah_hari }} Hari)
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- 3. SPT Kedinasan Items --}}
            @foreach($spts as $s)
                <a href="{{ route('spt.index') }}" wire:navigate
                   class="p-3.5 hover:bg-blue-50/80 transition flex items-start gap-3 block group border-l-4 border-blue-600 bg-white">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 border border-blue-300 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded bg-blue-100 text-blue-900 border border-blue-300">
                                SPT Dinas Luar
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">{{ $s->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-bold text-xs text-slate-900 truncate group-hover:text-[#064E3B]">
                            {{ $s->pegawai->nama_lengkap ?? 'Perangkat Desa' }}
                        </p>
                        <p class="text-[11px] text-slate-600 line-clamp-1 mt-0.5">
                            Tujuan: {{ $s->tujuan }} — {{ $s->keperluan }}
                        </p>
                    </div>
                </a>
            @endforeach

        </div>

        {{-- Footer Panel --}}
        <div class="p-3 text-center border-t border-slate-200 flex items-center justify-between px-4" style="background-color: #FAF6F0;">
            <a href="{{ route('pengajuan-absen.index') }}" wire:navigate class="text-xs font-extrabold text-[#064E3B] hover:underline flex items-center gap-1">
                <span>Kelola Semua Absen Luar</span>
                <span class="text-[#C9A84C] font-black">&rarr;</span>
            </a>
            <button type="button" @click="open = false" class="text-xs font-bold text-slate-600 hover:text-slate-900 px-2.5 py-1 rounded-lg hover:bg-slate-200/60 transition cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>
