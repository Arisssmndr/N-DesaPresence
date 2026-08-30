@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between gap-2 pt-2">
        {{-- Tombol Halaman Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-bold text-slate-400 bg-slate-100/80 rounded-xl border border-slate-200 cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-extrabold text-[#064E3B] bg-white hover:bg-emerald-50 rounded-xl border border-[#C9A84C]/50 shadow-sm transition active:scale-95">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Info Halaman Saat Ini --}}
        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white/90 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-700 shadow-sm">
            <span class="text-[#064E3B]">{{ $paginator->currentPage() }}</span>
            <span class="text-slate-400">/</span>
            <span class="text-slate-500">{{ $paginator->lastPage() }}</span>
        </div>

        {{-- Tombol Halaman Selanjutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-extrabold text-[#064E3B] bg-white hover:bg-emerald-50 rounded-xl border border-[#C9A84C]/50 shadow-sm transition active:scale-95">
                <span>Berikutnya</span>
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-bold text-slate-400 bg-slate-100/80 rounded-xl border border-slate-200 cursor-not-allowed select-none">
                <span>Berikutnya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif
    </nav>
@endif
