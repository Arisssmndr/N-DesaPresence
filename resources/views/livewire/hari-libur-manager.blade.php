<div class="space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight flex items-center gap-2">
                <span>Hari Libur & Cuti Bersama</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#064E3B] font-bold border border-emerald-300">Otomatis API</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar hari libur yang dikecualikan dari kalkulasi Alpa dan presensi harian</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button wire:click="sinkronkanApi" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold text-xs tracking-wide shadow-md transition disabled:opacity-50">
                <svg wire:loading.remove wire:target="sinkronkanApi" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <svg wire:loading wire:target="sinkronkanApi" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="sinkronkanApi">Sinkronkan Otomatis (API)</span>
                <span wire:loading wire:target="sinkronkanApi">Menyinkronkan API...</span>
            </button>

            <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Hari Libur Manual</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="sadi-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
        <div class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Tahun:</label>
            <select wire:model.live="filterTahun"
                class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-[#064E3B] focus:ring-2 focus:ring-[#064E3B] focus:outline-none">
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
            </select>
        </div>

        <div class="relative w-full sm:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama hari libur..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#064E3B]">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <!-- Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Nama Hari Libur</th>
                        <th class="py-3.5 px-4">Kategori / Sumber</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($hariLiburs as $h)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-700">
                                    {{ $h->tanggal->format('d M Y') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $h->nama_hari_libur }}
                            </td>
                            <td class="py-3 px-4">
                                @if($h->jenis === 'nasional')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        🇮🇩 Libur Nasional
                                    </span>
                                @elseif($h->jenis === 'cuti_bersama')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        📅 Cuti Bersama
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        🏡 Libur Lokal Desa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button wire:click="delete({{ $h->id }})" wire:confirm="Apakah Anda yakin ingin menghapus hari libur ini?" class="p-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400 space-y-3">
                                <p class="text-sm">Belum ada hari libur yang tercatat untuk tahun {{ $filterTahun }}.</p>
                                <button wire:click="sinkronkanApi"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow transition">
                                    <span>🇮🇩 Sinkronkan Otomatis dari Kalender Nasional RI</span>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white">Tambah Hari Libur Manual</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Libur <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="tanggal" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('tanggal') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Hari Libur <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nama_hari_libur" placeholder="Contoh: Hari Raya Idul Fitri" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('nama_hari_libur') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Hari Libur</label>
                        <select wire:model="jenis" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            <option value="nasional">Libur Nasional (SKB 3 Menteri)</option>
                            <option value="cuti_bersama">Cuti Bersama</option>
                            <option value="lokal">Libur Lokal Desa</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold bg-[#064E3B] text-white hover:bg-[#04392B] transition">Simpan Libur</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
