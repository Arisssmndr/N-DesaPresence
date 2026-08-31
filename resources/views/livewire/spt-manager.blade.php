<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Surat Perintah Tugas (SPT) Digital</h1>
            <p class="text-xs text-slate-500 mt-1">Pengajuan & Persetujuan Tugas Kedinasan Luar Kantor Perangkat Desa</p>
        </div>
        <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Pengajuan SPT</span>
        </button>
    </div>

    <!-- Data Table & Softfile Viewer -->
    <div class="sadi-card overflow-hidden" x-data="{ viewModal: false, activeFile: '', activeExt: '', activeTitle: '', activeNomor: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#064E3B] text-white">
                    <tr>
                        <th class="py-3 px-4 font-extrabold text-[#E2C268]">No. SPT</th>
                        <th class="py-3 px-4 font-extrabold text-white">Perangkat Ditugaskan</th>
                        <th class="py-3 px-4 font-extrabold text-white">Tanggal Dinas</th>
                        <th class="py-3 px-4 font-extrabold text-white">Tujuan & Keperluan</th>
                        <th class="py-3 px-4 text-center font-extrabold text-[#E2C268]">Status</th>
                        <th class="py-3 px-4 text-right font-extrabold text-[#E2C268]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium bg-white">
                    @forelse ($spts as $s)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono text-xs font-bold {{ $s->nomor_spt ? 'text-[#064E3B]' : 'text-slate-400' }}">
                                {{ $s->nomor_spt ?? '—' }}
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
                                <div class="flex flex-col items-center gap-1">
                                    @if ($s->respons_staf === 'diterima' || $s->status === 'disetujui')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Diterima Staf
                                        </span>
                                        @if($s->waktu_respons_staf)
                                            <span class="text-[9px] text-slate-400 font-mono">{{ $s->waktu_respons_staf->format('d/m H:i') }}</span>
                                        @endif
                                    @elseif ($s->respons_staf === 'ditolak' || $s->status === 'ditolak')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                            Ditolak Staf
                                        </span>
                                        @if ($s->alasan_tolak_staf || $s->catatan_penolakan)
                                            <span class="text-[9px] text-rose-600 max-w-[140px] truncate" title="{{ $s->alasan_tolak_staf ?? $s->catatan_penolakan }}">
                                                "{{ $s->alasan_tolak_staf ?? $s->catatan_penolakan }}"
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                            Menunggu Staf
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                @if ($s->file_undangan)
                                    @php
                                        $fileUrl = asset('storage/' . $s->file_undangan);
                                        $ext = strtolower(pathinfo($s->file_undangan, PATHINFO_EXTENSION));
                                    @endphp
                                    <button type="button"
                                            @click="activeFile = '{{ $fileUrl }}'; activeExt = '{{ $ext }}'; activeTitle = '{{ addslashes($s->tujuan) }}'; activeNomor = '{{ $s->nomor_spt }}'; viewModal = true;"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[11px] border border-blue-200 transition cursor-pointer" title="Lihat Berkas SPT">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Softfile</span>
                                    </button>
                                @endif

                                <button type="button" wire:click="bukaDetailModal({{ $s->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-[#064E3B] hover:bg-emerald-100 font-bold text-[11px] border border-emerald-200 transition cursor-pointer" title="Lihat Rincian & Status Respons Staf">
                                    <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Detail</span>
                                </button>

                                <button type="button" wire:click="deleteSpt({{ $s->id }})"
                                        wire:confirm="Apakah Anda yakin ingin membatalkan dan menghapus penugasan SPT ini?"
                                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-[11px] border border-rose-200 transition cursor-pointer" title="Hapus / Batalkan SPT">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Belum ada data Surat Perintah Tugas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 bg-white">
            {{ $spts->links() }}
        </div>

        <!-- MODAL VIEWER SOFTFILE (ADMIN) -->
        <div x-show="viewModal"
             x-transition.opacity
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="viewModal = false">
            <div @click.away="viewModal = false"
                 class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-6 flex flex-col max-h-[90vh]">
                
                <!-- Header Modal -->
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                    <div>
                        <h3 class="font-outfit text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#E2C268]"></span>
                            <span>Softfile Surat Perintah Tugas</span>
                        </h3>
                        <p class="text-[11px] text-emerald-200 font-mono mt-0.5" x-text="activeNomor + ' — ' + activeTitle"></p>
                    </div>
                    <button type="button" @click="viewModal = false" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Body Modal (Preview Image / PDF) -->
                <div class="p-4 overflow-y-auto max-h-[65vh] bg-slate-50 flex items-center justify-center">
                    <template x-if="['jpg', 'jpeg', 'png', 'webp'].includes(activeExt)">
                        <div class="text-center p-2">
                            <img :src="activeFile" :alt="activeNomor" class="max-h-[58vh] w-auto mx-auto rounded-xl border border-slate-200 shadow-md object-contain">
                        </div>
                    </template>
                    <template x-if="activeExt === 'pdf'">
                        <iframe :src="activeFile" class="w-full h-[58vh] border-0 rounded-xl shadow-inner bg-white"></iframe>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-3.5 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                    <span class="text-xs text-slate-500 font-medium font-mono" x-text="'Format: ' + activeExt.toUpperCase()"></span>
                    <div class="flex items-center gap-2">
                        <a :href="activeFile" target="_blank"
                           class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow hover:bg-blue-700 transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka Layar Penuh</span>
                        </a>
                        <a :href="activeFile" download
                           class="px-4 py-2 rounded-xl bg-[#064E3B] text-white text-xs font-bold shadow hover:bg-[#04392B] transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh File</span>
                        </a>
                        <button type="button" @click="viewModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
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

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-bold text-slate-700 uppercase tracking-wider">Nomor Surat Tugas</label>
                            <span class="text-[10px] text-slate-400 font-semibold">(Opsional)</span>
                        </div>
                        <input type="text" wire:model="nomor_spt" placeholder="Contoh: 800/012/Desa/2026"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        @error('nomor_spt') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
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
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Berkas SPT / Surat Undangan Resmi <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="file_undangan" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-0.5">Wajib unggah berkas surat tugas / undangan format PDF, JPG, PNG (Maks. 5 MB)</p>
                        @error('file_undangan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">Batal</button>
                        <button type="submit" class="btn-sadi-primary px-6 py-2 rounded-xl text-xs font-bold text-white transition cursor-pointer">Terbitkan & Kirim ke Staf</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL DETAIL & JEJAK RESPONS STAF (ADMIN) -->
    @if ($showDetailModal && $detailSpt)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-6 flex flex-col">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-white">Rincian Surat Perintah Tugas (SPT)</h3>
                        <p class="text-[11px] text-emerald-200 font-mono">{{ $detailSpt->nomor_spt ?? 'Tanpa Nomor Surat' }}</p>
                    </div>
                    <button wire:click="tutupDetailModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-xs">
                    <!-- Status Respons Badge -->
                    <div class="p-3.5 rounded-2xl border {{ $detailSpt->respons_staf === 'diterima' ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : ($detailSpt->respons_staf === 'ditolak' ? 'bg-rose-50 border-rose-200 text-rose-950' : 'bg-amber-50 border-amber-200 text-amber-950') }}">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-bold text-xs uppercase tracking-wider">Status Konfirmasi Staf:</span>
                            @if($detailSpt->respons_staf === 'diterima')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-600 text-white shadow-2xs">Diterima Staf</span>
                            @elseif($detailSpt->respons_staf === 'ditolak')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-600 text-white shadow-2xs">Ditolak Staf</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white shadow-2xs animate-pulse">Menunggu Konfirmasi Staf</span>
                            @endif
                        </div>

                        @if($detailSpt->respons_staf === 'diterima')
                            <p class="text-[11px] text-emerald-800">
                                Dikonfirmasi dan ditandatangani pada: <strong>{{ $detailSpt->waktu_respons_staf ? $detailSpt->waktu_respons_staf->isoFormat('dddd, D MMMM Y - HH:mm') . ' WIB' : '-' }}</strong>. Status presensi pegawai otomatis tercatat <strong>Hadir</strong>.
                            </p>
                        @elseif($detailSpt->respons_staf === 'ditolak')
                            <div class="mt-2 pt-2 border-t border-rose-200">
                                <span class="text-[10.5px] font-bold text-rose-800 uppercase tracking-wider block">Alasan Penolakan dari Staf:</span>
                                <p class="text-xs text-rose-900 font-semibold mt-0.5 italic bg-white p-2.5 rounded-xl border border-rose-200">
                                    "{{ $detailSpt->alasan_tolak_staf ?? $detailSpt->catatan_penolakan ?? 'Tidak ada catatan tambahan.' }}"
                                </p>
                            </div>
                        @else
                            <p class="text-[11px] text-amber-800">
                                Penugasan telah dikirim ke portal perangkat desa yang bersangkutan dan sedang menunggu tanda tangan / konfirmasi dari staf.
                            </p>
                        @endif
                    </div>

                    <!-- Informasi Pokok SPT -->
                    <div class="grid grid-cols-2 gap-3 text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Perangkat Ditugaskan:</span>
                            <p class="font-bold text-slate-900 text-xs mt-0.5">{{ $detailSpt->pegawai->nama_lengkap ?? '-' }}</p>
                            <p class="text-[10px] text-slate-500">{{ $detailSpt->pegawai->jabatan->nama_jabatan ?? '' }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Periode Tugas:</span>
                            <p class="font-mono font-bold text-slate-900 text-xs mt-0.5">
                                {{ $detailSpt->tanggal_mulai->format('d/m/Y') }} s/d {{ $detailSpt->tanggal_selesai->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-slate-200">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Lokasi / Tujuan:</span>
                            <p class="font-bold text-slate-900 text-xs mt-0.5">{{ $detailSpt->tujuan }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Keperluan / Agenda:</span>
                            <p class="text-slate-700 text-xs mt-0.5 leading-relaxed">{{ $detailSpt->keperluan }}</p>
                        </div>
                    </div>

                    <!-- Bukti Tanda Tangan Staf (Jika Diterima) -->
                    @if($detailSpt->tanda_tangan_staf)
                        <div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block mb-1">Tanda Tangan Digital Staf:</span>
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center">
                                <img src="{{ $detailSpt->tanda_tangan_staf }}" alt="Tanda Tangan Staf" class="max-h-24 object-contain">
                            </div>
                        </div>
                    @endif

                    <!-- Softfile Berkas Surat Tugas -->
                    @if($detailSpt->file_undangan)
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-600 font-medium">Berkas Surat Tugas / Lampiran:</span>
                            <a href="{{ asset('storage/' . $detailSpt->file_undangan) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 transition border border-blue-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Buka / Unduh Softfile</span>
                            </a>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="button" wire:click="tutupDetailModal" class="px-5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
