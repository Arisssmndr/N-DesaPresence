<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-outfit font-extrabold text-[#064E3B]">Konfigurasi Waktu & Jam Absensi</h1>
            <p class="text-slate-500 text-sm mt-1">Atur batasan waktu buka & tutup portal absensi untuk staf perangkat desa</p>
        </div>
        <div class="flex items-center px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-bold">
            Waktu Server Sekarang: {{ $nowTime }} WIB
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-800 text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Form Pengaturan Jam --}}
    <div class="sadi-card p-6 border-2 border-[#C9A84C]/30 bg-white">
        <h3 class="font-outfit font-bold text-[#064E3B] text-base mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Pengaturan Jendela Waktu Absensi Harian</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Panel Jam Masuk --}}
            <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-200/70 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-outfit font-bold text-[#064E3B] text-sm">Absensi Masuk</h4>
                            <p class="text-xs text-slate-500">Rentang waktu staf dapat melakukan absen masuk</p>
                        </div>
                    </div>
                    @if($isMasukNow)
                        <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-full text-[10px] font-bold uppercase tracking-wider">Sedang Buka</span>
                    @else
                        <span class="px-2.5 py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] font-medium uppercase tracking-wider">Tutup</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Mulai</label>
                        <input type="time" wire:model="jam_masuk_mulai"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:border-[#064E3B]">
                        @error('jam_masuk_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Selesai</label>
                        <input type="time" wire:model="jam_masuk_selesai"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:border-[#064E3B]">
                        @error('jam_masuk_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Panel Jam Pulang --}}
            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-200/70 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center shadow shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-outfit font-bold text-[#064E3B] text-sm">Absensi Pulang</h4>
                            <p class="text-xs text-slate-500">Rentang waktu staf dapat melakukan absen pulang</p>
                        </div>
                    </div>
                    @if($isPulangNow)
                        <span class="px-2.5 py-1 bg-amber-600 text-white rounded-full text-[10px] font-bold uppercase tracking-wider">Sedang Buka</span>
                    @else
                        <span class="px-2.5 py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] font-medium uppercase tracking-wider">Tutup</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Mulai</label>
                        <input type="time" wire:model="jam_pulang_mulai"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:border-[#064E3B]">
                        @error('jam_pulang_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jam Selesai</label>
                        <input type="time" wire:model="jam_pulang_selesai"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:border-[#064E3B]">
                        @error('jam_pulang_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="flex justify-end mt-8">
            <button wire:click="simpan"
                class="btn-sadi-primary px-8 py-3 rounded-xl text-white text-sm font-bold shadow-lg transition duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span wire:loading.remove wire:target="simpan">Simpan Jadwal Absensi</span>
                <span wire:loading wire:target="simpan">Menyimpan...</span>
            </button>
        </div>
    </div>

</div>
