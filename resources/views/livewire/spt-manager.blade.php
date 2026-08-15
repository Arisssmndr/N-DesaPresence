<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Surat Perintah Tugas (SPT) Digital</h1>
            <p class="text-xs text-slate-500 mt-1">Pengajuan & Persetujuan Tugas Kedinasan Luar Kantor Perangkat Desa</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#064E3B] to-[#1B4D3E] text-white font-bold text-xs tracking-wide shadow-lg hover:shadow-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Pengajuan SPT</span>
        </button>
    </div>

    <!-- Data Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">No. SPT</th>
                        <th class="py-3.5 px-4">Perangkat Ditugaskan</th>
                        <th class="py-3.5 px-4">Tanggal Dinas</th>
                        <th class="py-3.5 px-4">Tujuan & Keperluan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($spts as $s)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#064E3B]">
                                {{ $s->nomor_spt }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $s->pegawai->nama_lengkap ?? '-' }}
                                <p class="text-[10px] text-slate-400 font-normal">{{ $s->pegawai->jabatan->nama_jabatan ?? '' }}</p>
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-mono text-[11px]">
                                {{ $s->tanggal_mulai->format('d/m/Y') }} — {{ $s->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <p class="font-bold text-slate-800 truncate">{{ $s->tujuan }}</p>
                                <p class="text-[11px] text-slate-500 line-clamp-1">{{ $s->keperluan }}</p>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @match ($s->status)
                                    'disetujui' => <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Disetujui Kades</span>,
                                    'diajukan' => <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Menunggu Approval</span>,
                                    'ditolak' => <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Ditolak</span>,
                                    default => <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">Draft</span>,
                                @endmatch
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                @if ($s->status === 'diajukan' && (auth()->user()->isKades() || auth()->user()->isAdmin()))
                                    <button wire:click="approve({{ $s->id }})" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition">
                                        Setujui
                                    </button>
                                    <button wire:click="reject({{ $s->id }})" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 font-bold text-[11px] hover:bg-red-100 transition">
                                        Tolak
                                    </button>
                                @endif
                                @if ($s->file_undangan)
                                    <a href="{{ Storage::url($s->file_undangan) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition inline-block" title="Lihat Lampiran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Belum ada Surat Perintah Tugas (SPT) yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $spts->links() }}
        </div>
    </div>

    <!-- Modal Form Pengajuan SPT -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white">Buat Surat Perintah Tugas (SPT) Baru</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="createSpt" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Perangkat Desa yang Ditugaskan <span class="text-red-500">*</span></label>
                        <select wire:model="pegawai_id" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            <option value="">-- Pilih Perangkat Desa --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('pegawai_id') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_mulai" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            @error('tanggal_mulai') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_selesai" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            @error('tanggal_selesai') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Lokasi / Tujuan Dinas <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="tujuan" placeholder="Contoh: Kantor DPMD Kabupaten Tasikmalaya" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('tujuan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keperluan / Agenda Tugas <span class="text-red-500">*</span></label>
                        <textarea wire:model="keperluan" rows="3" placeholder="Jelaskan agenda tugas kedinasan luar kantor..." class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]"></textarea>
                        @error('keperluan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Upload File Surat Undangan / Lampiran</label>
                        <input type="file" wire:model="file_undangan" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white">
                        @error('file_undangan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold bg-[#064E3B] text-white hover:bg-[#04392B] transition">Simpan SPT</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
