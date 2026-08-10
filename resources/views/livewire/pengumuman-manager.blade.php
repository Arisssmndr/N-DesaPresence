<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Pengumuman & Informasi Desa</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola pengumuman resmi internal yang tampil pada dashboard perangkat desa</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#064E3B] to-[#1B4D3E] text-white font-bold text-xs tracking-wide shadow-lg hover:shadow-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Pengumuman Baru</span>
        </button>
    </div>

    <!-- Data Cards / Table -->
    <div class="space-y-4">
        @forelse ($pengumumans as $p)
            <div class="sadi-card p-6 border-l-4 {{ $p->is_pinned ? 'border-[#C9A84C]' : 'border-[#064E3B]' }} flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 max-w-3xl">
                    <div class="flex items-center gap-2">
                        @if ($p->is_pinned)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                <span>PINNED DASHBOARD</span>
                            </span>
                        @endif
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                            {{ ucfirst($p->kategori) }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Dibuat: {{ $p->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <h3 class="font-outfit text-lg font-bold text-[#064E3B]">{{ $p->judul }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $p->isi }}</p>
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <button wire:click="delete({{ $p->id }})" wire:confirm="Apakah Anda yakin ingin menghapus pengumuman ini?" class="p-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 transition text-xs font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="sadi-card p-12 text-center text-slate-400 italic">
                Belum ada pengumuman desa yang dibuat.
            </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white">Buat Pengumuman Baru</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="judul" placeholder="Contoh: Rapat Mingguan Evaluasi APBDes" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('judul') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                        <select wire:model="kategori" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            <option value="informasi">Informasi Umum</option>
                            <option value="rapat">Undangan Rapat</option>
                            <option value="kegiatan">Kegiatan Desa</option>
                            <option value="penting">Penting / Darurat</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                        <textarea wire:model="isi" rows="4" placeholder="Tuliskan pesan atau pengumuman secara rinci..." class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]"></textarea>
                        @error('isi') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-center">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Berlaku Hingga</label>
                            <input type="date" wire:model="berlaku_hingga" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        </div>

                        <div class="pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_pinned" class="w-4 h-4 text-[#064E3B] rounded border-[#C9A84C] focus:ring-[#C9A84C]">
                                <span class="font-bold text-slate-700">Pin di Atas Dashboard</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold bg-[#064E3B] text-white hover:bg-[#04392B] transition">Simpan Pengumuman</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
