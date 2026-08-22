<div class="space-y-5">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="font-outfit text-xl font-bold text-[#064E3B] tracking-tight">Pengumuman & Informasi Desa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola informasi resmi yang tampil pada portal staf & dashboard presensi</p>
        </div>
        <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-bold text-xs shadow-md transition cursor-pointer self-start sm:self-auto">
            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Pengumuman Baru</span>
        </button>
    </div>

    <!-- Data Cards Compact List -->
    <div class="space-y-3">
        @forelse ($pengumumans as $p)
            @php
                $kategoriStyle = match($p->kategori) {
                    'penting' => 'bg-red-50 text-red-700 border-red-200',
                    'rapat' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'kegiatan' => 'bg-amber-50 text-amber-800 border-amber-200',
                    default => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                };
            @endphp
            <div class="sadi-card p-4 sm:p-5 bg-white border {{ $p->is_pinned ? 'border-[#C9A84C] ring-1 ring-[#C9A84C]/30 shadow-md' : 'border-slate-200 shadow-sm' }} rounded-2xl transition hover:border-[#064E3B]">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    
                    {{-- Content --}}
                    <div class="space-y-1.5 flex-1 min-w-0">
                        {{-- Meta Baris Atas --}}
                        <div class="flex flex-wrap items-center gap-2 text-[10px]">
                            @if ($p->is_pinned)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold bg-[#FAF6F0] text-[#064E3B] border border-[#C9A84C]">
                                    <svg class="w-3 h-3 text-[#C9A84C]" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                    <span>PINNED</span>
                                </span>
                            @endif

                            <span class="px-2 py-0.5 rounded-md font-bold uppercase tracking-wider border {{ $kategoriStyle }}">
                                {{ ucfirst($p->kategori) }}
                            </span>

                            <span class="text-slate-400 font-medium">
                                {{ $p->created_at->isoFormat('D MMM Y, HH:mm') }} WIB
                            </span>

                            @if ($p->berlaku_hingga)
                                <span class="text-amber-800 font-semibold bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                    s/d: {{ \Carbon\Carbon::parse($p->berlaku_hingga)->isoFormat('D MMM Y') }}
                                </span>
                            @endif
                        </div>

                        {{-- Judul & Pesan --}}
                        <h3 class="font-outfit text-base font-bold text-[#064E3B]">
                            {{ $p->judul }}
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                            {{ $p->isi }}
                        </p>
                    </div>

                    {{-- Actions Ringkas --}}
                    <div class="flex items-center gap-1.5 self-end md:self-center shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100 w-full md:w-auto justify-end">
                        <button wire:click="togglePin({{ $p->id }})" title="{{ $p->is_pinned ? 'Lepas Pin' : 'Sematkan di Atas' }}"
                                class="p-2 rounded-lg {{ $p->is_pinned ? 'bg-[#FAF6F0] text-[#064E3B] border border-[#C9A84C]' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 {{ $p->is_pinned ? 'text-[#C9A84C]' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            <span class="hidden sm:inline">{{ $p->is_pinned ? 'Unpin' : 'Pin' }}</span>
                        </button>
                        <button wire:click="edit({{ $p->id }})" class="p-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span class="hidden sm:inline">Edit</span>
                        </button>
                        <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus pengumuman ini?" class="p-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </div>

                </div>
            </div>
        @empty
            <div class="sadi-card p-8 text-center bg-white shadow-sm rounded-2xl space-y-1.5 border border-slate-200">
                <div class="w-10 h-10 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h4 class="font-outfit font-bold text-[#064E3B] text-sm">Belum Ada Pengumuman</h4>
                <p class="text-xs text-slate-500">Klik tombol "Buat Pengumuman Baru" di atas untuk menerbitkan informasi.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-8">
                <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]">
                    <h3 class="font-outfit text-sm font-bold text-white">{{ $pengumumanId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-5 space-y-3.5 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Pengumuman <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="judul" placeholder="Contoh: Rapat Mingguan Evaluasi APBDes" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B] outline-none">
                        @error('judul') <span class="text-[11px] text-red-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                        <select wire:model="kategori" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B] outline-none">
                            <option value="informasi">Informasi Umum</option>
                            <option value="rapat">Undangan Rapat</option>
                            <option value="kegiatan">Kegiatan Desa</option>
                            <option value="penting">Penting / Darurat</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Pesan <span class="text-red-500">*</span></label>
                        <textarea wire:model="isi" rows="3" placeholder="Tuliskan pesan atau arahan..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B] outline-none resize-none"></textarea>
                        @error('isi') <span class="text-[11px] text-red-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 items-center">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Berlaku Hingga</label>
                            <input type="date" wire:model="berlaku_hingga" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] outline-none">
                        </div>

                        <div class="pt-3.5">
                            <label class="flex items-center gap-1.5 cursor-pointer p-2 rounded-xl bg-slate-50 border border-slate-200">
                                <input type="checkbox" wire:model="is_pinned" class="w-3.5 h-3.5 text-[#064E3B] rounded border-[#C9A84C]">
                                <span class="font-bold text-slate-700 text-[10px]">Pin di Dashboard</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold btn-sadi-primary text-white shadow transition cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
