<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Manajemen Izin & Sakit Digital</h1>
            <p class="text-xs text-slate-500 mt-1">Pengajuan izin pribadi, kedinasan, sakit, dan cuti perangkat desa</p>
        </div>
        <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Ajukan Izin / Sakit</span>
        </button>
    </div>

    <!-- Data Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#064E3B] text-white">
                    <tr>
                        <th class="py-3 px-4 font-extrabold text-white">Pegawai</th>
                        <th class="py-3 px-4 font-extrabold text-white">Jenis Izin</th>
                        <th class="py-3 px-4 font-extrabold text-white">Tanggal & Durasi</th>
                        <th class="py-3 px-4 font-extrabold text-white">Keterangan</th>
                        <th class="py-3 px-4 text-center font-extrabold text-[#E2C268]">Status</th>
                        <th class="py-3 px-4 text-right font-extrabold text-[#E2C268]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium bg-white">
                    @forelse ($izins as $i)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $i->pegawai->nama_lengkap ?? '-' }}
                                <p class="text-[10px] text-slate-400 font-normal">{{ $i->pegawai->jabatan->nama_jabatan ?? '' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @if(str_contains($i->jenis, 'sakit'))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        {{ ucfirst(str_replace('_', ' ', $i->jenis)) }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                        {{ ucfirst(str_replace('_', ' ', $i->jenis)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-mono text-[11px]">
                                {{ $i->tanggal_mulai->format('d/m/Y') }} — {{ $i->tanggal_selesai->format('d/m/Y') }}
                                <p class="text-[10px] text-teal-700 font-bold font-sans">({{ $i->jumlah_hari }} Hari)</p>
                            </td>
                            <td class="py-3 px-4 max-w-xs text-slate-600">
                                {{ $i->keterangan }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @switch($i->status)
                                    @case('disetujui')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Disetujui</span>
                                        @break
                                    @case('menunggu')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">Menunggu</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-300">Ditolak</span>
                                @endswitch
                            </td>
                            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                @if ($i->status === 'menunggu' && (auth()->user()->isAdmin() || auth()->user()->isKades()))
                                    <button wire:click="approve({{ $i->id }})" class="px-3 py-1.5 rounded-lg bg-[#064E3B] text-white font-bold text-[11px] hover:bg-[#04392B] border border-[#064E3B] transition shadow-xs cursor-pointer inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Setujui</span>
                                    </button>
                                    <button wire:click="konfirmasiTolak({{ $i->id }})" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-bold text-[11px] hover:bg-rose-700 border border-rose-700 transition shadow-xs cursor-pointer inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Tolak</span>
                                    </button>
                                @endif
                                @if ($i->file_lampiran)
                                    <a href="{{ Storage::url($i->file_lampiran) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition inline-block align-middle cursor-pointer" title="Lihat Surat Dokter / Lampiran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Belum ada riwayat pengajuan izin/sakit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 bg-white">
            {{ $izins->links() }}
        </div>
    </div>

    <!-- Modal Form Izin -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white">Form Pengajuan Izin / Sakit Digital</h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="createIzin" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Perangkat Desa <span class="text-red-500">*</span></label>
                        <select wire:model="pegawai_id" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            <option value="">-- Pilih Perangkat Desa --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('pegawai_id') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Izin <span class="text-red-500">*</span></label>
                        <select wire:model="jenis" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            <option value="izin_pribadi">Izin Keperluan Pribadi</option>
                            <option value="izin_kedinasan">Izin Keperluan Kedinasan</option>
                            <option value="sakit_dengan_surat">Sakit (dengan Surat Dokter)</option>
                            <option value="sakit_tanpa_surat">Sakit (Tanpa Surat Dokter)</option>
                            <option value="cuti_tahunan">Cuti Tahunan Perangkat</option>
                            <option value="duka_cita">Duka Cita Keluarga Inti</option>
                            <option value="melahirkan">Melahirkan</option>
                        </select>
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
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Alasan <span class="text-red-500">*</span></label>
                        <textarea wire:model="keterangan" rows="3" placeholder="Jelaskan keperluan izin / kondisi kesehatan..." class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]"></textarea>
                        @error('keterangan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Surat Dokter / Lampiran (Opsional)</label>
                        <input type="file" wire:model="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white">
                        @error('file_lampiran') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">Batal</button>
                        <button type="submit" class="btn-sadi-primary px-6 py-2 rounded-xl text-xs font-bold text-white transition cursor-pointer">Simpan Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Reject Izin -->
    @if ($showRejectModal && $selectedIzin)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl overflow-hidden border-2 border-rose-300 p-6 space-y-4">
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center mx-auto text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-outfit font-extrabold text-slate-900 text-base">Tolak Pengajuan Izin/Sakit</h3>
                    <p class="text-xs text-slate-600">Pengajuan dari <strong>{{ $selectedIzin->pegawai->nama_lengkap ?? 'Perangkat' }}</strong> akan ditolak.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea wire:model="catatanPenolakan" rows="3" placeholder="Tuliskan alasan penolakan izin ini..."
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 outline-none resize-none"></textarea>
                    @error('catatanPenolakan')
                    <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2.5">
                    <button wire:click="tutupRejectModal" class="flex-1 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-xs cursor-pointer">Batal</button>
                    <button wire:click="reject" class="flex-[2] py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold transition text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
