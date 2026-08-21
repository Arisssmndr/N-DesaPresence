<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Konfigurasi Shift Kerja</h1>
            <p class="text-xs text-slate-500 mt-1">Atur jam masuk, jam pulang, dan toleransi keterlambatan presensi</p>
        </div>
        <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Shift Baru</span>
        </button>
    </div>

    <!-- Shifts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($shifts as $s)
            <div class="sadi-card p-6 flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-[#064E3B]">{{ $s->nama_shift }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                        {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Jam Masuk</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ substr($s->jam_masuk, 0, 5) }} WIB</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Jam Pulang</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ substr($s->jam_pulang, 0, 5) }} WIB</p>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600 border-t border-slate-100 pt-3">
                    <span class="font-medium">Toleransi: <strong class="text-amber-700">{{ $s->toleransi_menit }} Menit</strong></span>
                    <span class="font-medium">Pegawai: <strong class="text-emerald-800">{{ $s->pegawais_count }} Orang</strong></span>
                </div>

                <div class="pt-2 flex justify-end">
                    <button wire:click="openEditModal({{ $s->id }})" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 font-bold text-xs hover:bg-amber-100 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Shift</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Form Shift -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white">{{ $isEdit ? 'Edit Shift Kerja' : 'Tambah Shift Baru' }}</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Shift <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nama_shift" placeholder="Contoh: Shift Pagi" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('nama_shift') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Masuk <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="jam_masuk" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Pulang <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="jam_pulang" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Toleransi Keterlambatan (Menit) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="toleransi_menit" min="0" max="120" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('toleransi_menit') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold bg-[#064E3B] text-white hover:bg-[#04392B] transition">Simpan Shift</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
