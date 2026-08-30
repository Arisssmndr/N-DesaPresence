<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-outfit text-[#064E3B] flex items-center gap-2">
                <span class="p-2 rounded-xl bg-emerald-100/80 text-[#064E3B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <span>Manajemen Jadwal Piket</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola pembagian tugas piket malam/siaga dan otomatisasi absensi Lepas Piket perangkat desa.</p>
        </div>
        <button wire:click="openCreateModal" class="sadi-btn-primary self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tetapkan Jadwal Piket</span>
        </button>
    </div>

    <!-- Filter & Search Card -->
    <div class="sadi-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Cari Perangkat / Tugas</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau pos..."
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Bulan</label>
                <select wire:model.live="bulan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Tahun</label>
                <select wire:model.live="tahun" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                    @foreach (range(date('Y') - 2, date('Y') + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Status Piket</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                    <option value="semua">Semua Status</option>
                    <option value="terjadwal">Terjadwal</option>
                    <option value="hadir">Selesai / Hadir</option>
                    <option value="lepas_piket">Lepas Piket</option>
                    <option value="batal">Batal</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Tanggal Piket</th>
                        <th class="py-3.5 px-4">Perangkat Ditugaskan</th>
                        <th class="py-3.5 px-4">Jam Pelaksanaan</th>
                        <th class="py-3.5 px-4">Tugas / Keterangan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($pikets as $p)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-bold text-[#064E3B]">
                                <div>{{ $p->tanggal_piket->isoFormat('dddd, D MMMM Y') }}</div>
                                @if($p->tanggal_piket->isToday())
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase">Hari Ini</span>
                                @elseif($p->tanggal_piket->isTomorrow())
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-100 text-blue-800 uppercase">Besok (H-1)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-800">{{ $p->pegawai->nama_lengkap ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 font-mono">{{ $p->pegawai->jabatan->nama_jabatan ?? 'Perangkat' }} (NIPD: {{ $p->pegawai->nipd ?? '-' }})</p>
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-700">
                                {{ substr($p->jam_mulai, 0, 5) }} — {{ substr($p->jam_selesai, 0, 5) }} WIB
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs">
                                <p class="font-medium text-slate-800">{{ $p->keterangan }}</p>
                                @if($p->waktu_absen)
                                    <p class="text-[10px] text-emerald-600 font-mono mt-0.5">Absen: {{ $p->waktu_absen->format('d/m H:i') }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @switch($p->status)
                                    @case('hadir')
                                    @case('lepas_piket')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ✓ Hadir (Lepas Piket)
                                        </span>
                                        @break
                                    @case('terjadwal')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                            Terjadwal
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600 border border-slate-300">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                @if($p->status === 'terjadwal')
                                    <button wire:click="verifikasiHadir({{ $p->id }})"
                                            wire:confirm="Konfirmasi kehadiran piket untuk {{ $p->pegawai->nama_lengkap }}? Presensi hari berikutnya akan otomatis dicatat sebagai Lepas Piket."
                                            class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition" title="Verifikasi Hadir Piket">
                                        Konfirmasi Hadir
                                    </button>
                                @endif
                                <button wire:click="openEditModal({{ $p->id }})" class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition inline-block" title="Ubah Jadwal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="delete({{ $p->id }})"
                                        wire:confirm="Hapus jadwal piket untuk {{ $p->pegawai->nama_lengkap }}?"
                                        class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition inline-block" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Belum ada jadwal piket yang ditetapkan untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $pikets->links() }}
        </div>
    </div>

    <!-- Modal Form Jadwal Piket -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white">
                        {{ $isEdit ? 'Ubah Jadwal Piket' : 'Tetapkan Jadwal Piket Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Perangkat Desa yang Bertugas <span class="text-red-500">*</span></label>
                        <select wire:model="pegawai_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                            <option value="">-- Pilih Perangkat Desa --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? 'Perangkat' }})</option>
                            @endforeach
                        </select>
                        @error('pegawai_id') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Piket <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="tanggal_piket" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                        @error('tanggal_piket') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="jam_mulai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                            @error('jam_mulai') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="jam_selesai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                            @error('jam_selesai') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tugas / Pos Piket <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="keterangan" placeholder="Contoh: Piket Jaga Malam Balai Desa" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                        @error('keterangan') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-800 text-[11px] space-y-1">
                        <p class="font-bold">Informasi Otomatisasi:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-emerald-700">
                            <li>Notifikasi jadwal piket akan otomatis tampil di portal staf minimal H-1 sebelum hari piket.</li>
                            <li>Ketika staf mengisi tanda tangan presensi piket, kehadiran hari berikutnya otomatis berstatus <strong>Lepas Piket</strong>.</li>
                        </ul>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold text-xs hover:bg-[#04392B] transition cursor-pointer">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Tetapkan Piket' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
