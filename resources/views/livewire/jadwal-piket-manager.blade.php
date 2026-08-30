<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-outfit text-[#064E3B] flex items-center gap-2">
                <span class="p-2 rounded-xl bg-emerald-100/80 text-[#064E3B] shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <span>Manajemen Jadwal Piket</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola pembagian tugas piket malam/siaga dan otomatisasi absensi Lepas Piket perangkat desa (Khusus Staf Laki-laki).</p>
        </div>

        <!-- Action Buttons Header -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Button Bulk Generator (1 Minggu, 1 Bulan, 6 Bulan, 1 Tahun) -->
            <button wire:click="openGeneratorModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold border border-emerald-500/50 shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer"
                    title="Generate rotasi piket otomatis untuk 1 minggu, 1 bulan, 6 bulan, atau 1 tahun">
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Generate Rotasi Otomatis</span>
            </button>

            <!-- Button Tetapkan Satuan -->
            <button wire:click="openCreateModal"
                    class="btn-sadi-primary group inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer">
                <span class="p-0.5 rounded-md bg-emerald-500/20 text-[#E2C268]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </span>
                <span class="font-outfit font-extrabold tracking-wide">Tetapkan Jadwal Satuan</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Card with Dynamic Reset -->
    <div class="sadi-card p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Cari Perangkat / Tugas</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau pos..."
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Bulan</label>
                <select wire:model.live="bulan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Tahun</label>
                <select wire:model.live="tahun" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white">
                    @foreach (range(date('Y') - 2, date('Y') + 2) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Status Piket</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white">
                    <option value="semua">Semua Status</option>
                    <option value="terjadwal">Terjadwal</option>
                    <option value="hadir">Selesai / Hadir</option>
                    <option value="lepas_piket">Lepas Piket</option>
                    <option value="batal">Batal</option>
                </select>
            </div>
            <!-- Dynamic Reset / Hapus Dropdown -->
            <div class="relative" x-data="{ openResetMenu: false }" @click.outside="openResetMenu = false">
                <label class="block font-bold text-slate-600 mb-1">Aksi Reset / Hapus</label>
                <button type="button" @click="openResetMenu = !openResetMenu"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 font-bold transition flex items-center justify-between cursor-pointer shadow-xs">
                    <span class="inline-flex items-center gap-1.5 truncate">
                        <svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus / Reset</span>
                    </span>
                    <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="openResetMenu" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-1.5 w-64 rounded-xl bg-white shadow-xl border border-slate-200 py-1.5 z-40 text-xs">

                    <div class="px-3 py-1.5 border-b border-slate-100 font-bold text-slate-700 text-[11px] bg-slate-50 flex items-center justify-between">
                        <span>Hapus Jadwal Bulan {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }}</span>
                        <span class="text-[10px] text-slate-400 font-normal">{{ $tahun }}</span>
                    </div>

                    <button type="button" wire:click="hapusJadwalPeriode('minggu_1')" @click="openResetMenu = false"
                            wire:confirm="Hapus seluruh jadwal piket Minggu ke-1 (1 - 7 {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }})?"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 text-slate-700 flex items-center justify-between transition cursor-pointer">
                        <span class="font-medium">Minggu 1</span>
                        <span class="text-[10px] text-slate-400 font-mono">Tgl 1 - 7</span>
                    </button>

                    <button type="button" wire:click="hapusJadwalPeriode('minggu_2')" @click="openResetMenu = false"
                            wire:confirm="Hapus seluruh jadwal piket Minggu ke-2 (8 - 14 {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }})?"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 text-slate-700 flex items-center justify-between transition cursor-pointer">
                        <span class="font-medium">Minggu 2</span>
                        <span class="text-[10px] text-slate-400 font-mono">Tgl 8 - 14</span>
                    </button>

                    <button type="button" wire:click="hapusJadwalPeriode('minggu_3')" @click="openResetMenu = false"
                            wire:confirm="Hapus seluruh jadwal piket Minggu ke-3 (15 - 21 {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }})?"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 text-slate-700 flex items-center justify-between transition cursor-pointer">
                        <span class="font-medium">Minggu 3</span>
                        <span class="text-[10px] text-slate-400 font-mono">Tgl 15 - 21</span>
                    </button>

                    <button type="button" wire:click="hapusJadwalPeriode('minggu_4')" @click="openResetMenu = false"
                            wire:confirm="Hapus seluruh jadwal piket Minggu ke-4 (22 - 28 {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }})?"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 text-slate-700 flex items-center justify-between transition cursor-pointer">
                        <span class="font-medium">Minggu 4</span>
                        <span class="text-[10px] text-slate-400 font-mono">Tgl 22 - 28</span>
                    </button>

                    @php
                        $daysInThisMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
                    @endphp
                    @if ($daysInThisMonth >= 29)
                        <button type="button" wire:click="hapusJadwalPeriode('minggu_5')" @click="openResetMenu = false"
                                wire:confirm="Hapus seluruh jadwal piket Minggu ke-5 (29 - {{ $daysInThisMonth }} {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }})?"
                                class="w-full text-left px-3 py-2 hover:bg-slate-50 text-slate-700 flex items-center justify-between transition cursor-pointer">
                            <span class="font-medium">Minggu 5 / Sisa Bulan</span>
                            <span class="text-[10px] text-slate-400 font-mono">Tgl 29 - {{ $daysInThisMonth }}</span>
                        </button>
                    @endif

                    <div class="border-t border-slate-100 my-1"></div>

                    <button type="button" wire:click="hapusJadwalPeriode('semua_bulan')" @click="openResetMenu = false"
                            wire:confirm="PERINGATAN: Kosongkan SEMUA jadwal piket pada bulan {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }}?"
                            class="w-full text-left px-3 py-2 hover:bg-red-50 text-red-600 font-bold flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Kosongkan Seluruh Bulan Ini</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        @if (count($selectedPiketIds) > 0)
            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-amber-900">{{ count($selectedPiketIds) }} jadwal piket terpilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="deleteSelected"
                            wire:confirm="Hapus {{ count($selectedPiketIds) }} jadwal piket terpilih?"
                            class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition cursor-pointer flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus Terpilih</span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 text-[#064E3B] focus:ring-[#064E3B] cursor-pointer">
                        </th>
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
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" wire:model.live="selectedPiketIds" value="{{ $p->id }}" class="rounded border-slate-300 text-[#064E3B] focus:ring-[#064E3B] cursor-pointer">
                            </td>
                            <td class="py-3 px-4 font-bold text-[#064E3B]">
                                <div>{{ $p->tanggal_piket->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
                                @if($p->tanggal_piket->isToday())
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase">Hari Ini</span>
                                @elseif($p->tanggal_piket->isTomorrow())
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-100 text-blue-800 uppercase">Besok (H-1)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5">
                                    <p class="font-bold text-slate-800">{{ $p->pegawai->nama_lengkap ?? '-' }}</p>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200" title="Laki-laki">
                                        ♂ L
                                    </span>
                                </div>
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
                                <button wire:click="openEditModal({{ $p->id }})" class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition inline-block cursor-pointer" title="Ubah Jadwal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="delete({{ $p->id }})"
                                        wire:confirm="Hapus jadwal piket untuk {{ $p->pegawai->nama_lengkap }} tanggal {{ $p->tanggal_piket->locale('id')->isoFormat('D MMMM Y') }}?"
                                        class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition inline-block cursor-pointer" title="Hapus Jadwal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="font-bold text-slate-600">Belum Ada Jadwal Piket</p>
                                    <p class="text-[11px] text-slate-400">Gunakan tombol <strong>"Generate Rotasi Otomatis"</strong> untuk membuat jadwal 1 minggu, 1 bulan, 6 bulan, atau 1 tahun penuh dengan mudah.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
            <div>
                @if($pikets->total() > 0)
                    <button wire:click="hapusJadwalPeriode('semua_bulan')"
                            wire:confirm="PERINGATAN: Kosongkan semua jadwal piket pada bulan {{ \Carbon\Carbon::create(null, $bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }}?"
                            class="text-[11px] font-bold text-red-600 hover:text-red-700 hover:underline cursor-pointer">
                        Kosongkan Jadwal Bulan Ini
                    </button>
                @endif
            </div>
            <div>
                {{ $pikets->links() }}
            </div>
        </div> --}}
    </div>

    <!-- MODAL 1: TETAPKAN / UBAH JADWAL SATUAN -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ $isEdit ? 'Ubah Jadwal Piket' : 'Tetapkan Jadwal Piket Satuan' }}</span>
                    </h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-xs">
                                Perangkat Desa yang Bertugas <span class="text-red-500">*</span>
                            </label>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-[#064E3B] border border-emerald-300 shadow-xs">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Khusus Staf Laki-laki
                            </span>
                        </div>
                        <select wire:model="pegawai_id" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] bg-white font-medium">
                            <option value="">-- Pilih Perangkat Desa (Laki-laki) --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nama_lengkap }} — {{ $p->jabatan->nama_jabatan ?? 'Perangkat' }} (NIPD: {{ $p->nipd ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('pegawai_id') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        @if($pegawais->isEmpty())
                            <p class="text-[11px] text-amber-600 mt-1 italic">⚠️ Tidak ada staf laki-laki aktif yang tersedia untuk jadwal piket.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Piket <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="tanggal_piket" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] font-medium">
                        @error('tanggal_piket') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="jam_mulai" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] font-medium">
                            @error('jam_mulai') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="jam_selesai" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] font-medium">
                            @error('jam_selesai') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tugas / Pos Piket <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="keterangan" placeholder="Contoh: Piket Jaga Malam Balai Desa" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] font-medium">
                        @error('keterangan') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-200/80 text-emerald-900 text-[11px] space-y-1.5 shadow-xs">
                        <div class="flex items-center gap-1.5 font-bold text-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Ketentuan & Otomatisasi Jadwal Piket:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-emerald-800/90 pl-1 text-[11px]">
                            <li>Jadwal piket malam dikhususkan bagi <strong>staf berjenis kelamin laki-laki</strong>.</li>
                            <li>Notifikasi jadwal piket akan otomatis muncul di portal staf terkait sejak H-1 hingga hari piket.</li>
                            <li>Setelah staf mengisi tanda tangan digital presensi piket, kehadiran hari berikutnya otomatis berstatus <strong>Lepas Piket (Hadir)</strong>.</li>
                        </ul>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-sadi-primary px-5 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $isEdit ? 'Simpan Perubahan' : 'Tetapkan Jadwal Piket' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: BULK GENERATOR PENJADWALAN PIKET OTOMATIS -->
    @if ($showGeneratorModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-auto flex flex-col">
                <!-- Modal Header -->
                <div class="px-6 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-xl bg-emerald-800/90 text-[#E2C268] border border-[#C9A84C]/40 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-outfit text-base font-bold text-white leading-tight">
                                Generator Penjadwalan Piket Otomatis
                            </h3>
                            <p class="text-xs text-[#E2C268] font-medium mt-0.5 leading-tight">
                                Rotasi adil (round-robin) pembagian tugas piket malam staf laki-laki per minggu, bulan, semester, atau tahunan.
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeGeneratorModal" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="generateJadwalBulk" class="p-5 text-xs flex flex-col justify-between">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- LEFT COLUMN (Span 7) -->
                        <div class="md:col-span-7 space-y-3">
                            <!-- 1. Preset Durasi -->
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
                                    1. Pilih Durasi Periode <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-4 gap-2">
                                    <!-- 1 Minggu -->
                                    <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition flex flex-col items-center justify-center gap-1 {{ $generatorDurasi === '1_minggu' ? 'border-[#064E3B] bg-emerald-50 text-[#064E3B] font-extrabold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="generatorDurasi" value="1_minggu" class="sr-only">
                                        <svg class="w-4 h-4 {{ $generatorDurasi === '1_minggu' ? 'text-[#064E3B]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[11px] leading-tight">1 Minggu</span>
                                        <span class="text-[9px] text-slate-400 font-normal">7 Hari</span>
                                    </label>

                                    <!-- 1 Bulan -->
                                    <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition flex flex-col items-center justify-center gap-1 {{ $generatorDurasi === '1_bulan' ? 'border-[#064E3B] bg-emerald-50 text-[#064E3B] font-extrabold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="generatorDurasi" value="1_bulan" class="sr-only">
                                        <svg class="w-4 h-4 {{ $generatorDurasi === '1_bulan' ? 'text-[#064E3B]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        <span class="text-[11px] leading-tight">1 Bulan</span>
                                        <span class="text-[9px] text-slate-400 font-normal">~30 Hari</span>
                                    </label>

                                    <!-- 6 Bulan -->
                                    <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition flex flex-col items-center justify-center gap-1 {{ $generatorDurasi === '6_bulan' ? 'border-[#064E3B] bg-emerald-50 text-[#064E3B] font-extrabold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="generatorDurasi" value="6_bulan" class="sr-only">
                                        <svg class="w-4 h-4 {{ $generatorDurasi === '6_bulan' ? 'text-[#064E3B]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-[11px] leading-tight">6 Bulan</span>
                                        <span class="text-[9px] text-slate-400 font-normal">1 Semester</span>
                                    </label>

                                    <!-- 1 Tahun -->
                                    <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition flex flex-col items-center justify-center gap-1 {{ $generatorDurasi === '1_tahun' ? 'border-[#064E3B] bg-emerald-50 text-[#064E3B] font-extrabold shadow-xs' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                                        <input type="radio" wire:model.live="generatorDurasi" value="1_tahun" class="sr-only">
                                        <svg class="w-4 h-4 {{ $generatorDurasi === '1_tahun' ? 'text-[#064E3B]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                        <span class="text-[11px] leading-tight">1 Tahun</span>
                                        <span class="text-[9px] text-slate-400 font-normal">12 Bulan</span>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. Rentang Tanggal & Hari -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                        <input type="date" wire:model.live="generatorTanggalMulai" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium">
                                        @error('generatorTanggalMulai') <span class="text-red-500 text-[10px] block mt-0.5 font-semibold">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                                        <input type="date" wire:model="generatorTanggalSelesai" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium" {{ $generatorDurasi !== 'custom' ? 'readonly' : '' }}>
                                        @error('generatorTanggalSelesai') <span class="text-red-500 text-[10px] block mt-0.5 font-semibold">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Hari Pelaksanaan</label>
                                    <div class="grid grid-cols-3 gap-1.5 text-[11px]">
                                        <label class="flex items-center gap-1.5 p-1.5 rounded-lg bg-white border border-slate-200 cursor-pointer">
                                            <input type="radio" wire:model="generatorTipeHari" value="setiap_hari" class="text-[#064E3B] focus:ring-[#064E3B]">
                                            <span>Setiap Hari</span>
                                        </label>
                                        <label class="flex items-center gap-1.5 p-1.5 rounded-lg bg-white border border-slate-200 cursor-pointer">
                                            <input type="radio" wire:model="generatorTipeHari" value="hari_kerja" class="text-[#064E3B] focus:ring-[#064E3B]">
                                            <span>Senin - Jumat</span>
                                        </label>
                                        <label class="flex items-center gap-1.5 p-1.5 rounded-lg bg-white border border-slate-200 cursor-pointer">
                                            <input type="radio" wire:model="generatorTipeHari" value="akhir_pekan" class="text-[#064E3B] focus:ring-[#064E3B]">
                                            <span>Sabtu - Minggu</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Jam Tugas & Konflik -->
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                                    <input type="time" wire:model="generatorJamMulai" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                                    <input type="time" wire:model="generatorJamSelesai" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Jika Sudah Ada</label>
                                    <select wire:model="generatorOpsiKonflik" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white">
                                        <option value="skip">Lewati (Skip)</option>
                                        <option value="replace">Timpa (Replace)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Tugas / Pos Piket</label>
                                <input type="text" wire:model="generatorKeterangan" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B]">
                            </div>
                        </div>

                        <!-- RIGHT COLUMN (Span 5) -->
                        <div class="md:col-span-5 flex flex-col justify-between space-y-3">
                            <!-- Daftar Staf Laki-laki -->
                            <div class="flex-1 flex flex-col">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px]">
                                        2. Staf Peserta Rotasi ({{ count($selectedStafIds) }} Terpilih) <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" wire:click="toggleSelectAllStaf" class="text-[10px] font-bold text-[#064E3B] hover:underline cursor-pointer">
                                        {{ count($selectedStafIds) === count($pegawais) ? 'Batal Semua' : 'Pilih Semua' }}
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-1.5 p-2.5 bg-slate-50 rounded-xl border border-slate-200 max-h-56 overflow-y-auto">
                                    @foreach ($pegawais as $p)
                                        <label class="flex items-center gap-2 p-1.5 rounded-lg bg-white border border-slate-200 hover:border-emerald-300 transition cursor-pointer">
                                            <input type="checkbox" wire:model.live="selectedStafIds" value="{{ $p->id }}" class="rounded text-[#064E3B] focus:ring-[#064E3B]">
                                            <div class="overflow-hidden leading-tight">
                                                <p class="font-bold text-slate-800 text-[11px] truncate">{{ $p->nama_lengkap }}</p>
                                                <p class="text-[9px] text-slate-400 truncate">{{ $p->jabatan->nama_jabatan ?? 'Perangkat' }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedStafIds') <span class="text-red-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Pratinjau Box -->
                            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 text-[11px] space-y-1">
                                <p class="font-bold flex items-center gap-1.5 text-emerald-900">
                                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Pratinjau Rotasi:</span>
                                </p>
                                <p class="text-[10px] text-emerald-800 leading-relaxed">
                                    Jadwal akan di-generate mulai <strong>{{ \Carbon\Carbon::parse($generatorTanggalMulai)->isoFormat('D MMM Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($generatorTanggalSelesai)->isoFormat('D MMM Y') }}</strong> bergantian ke <strong>{{ count($selectedStafIds) }} staf laki-laki</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" wire:click="closeGeneratorModal" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-sadi-primary px-5 py-2 rounded-xl text-white font-bold text-xs shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Mulai Generate Jadwal Piket</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
