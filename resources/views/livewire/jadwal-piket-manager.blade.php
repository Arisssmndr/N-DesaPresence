<div class="space-y-6">
    <!-- Header Section Sederhana & Jelas -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-outfit text-[#064E3B] flex items-center gap-2">
                <span class="p-2 rounded-xl bg-emerald-100/80 text-[#064E3B] shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <span>Jadwal Piket Balai Desa</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pengaturan jadwal piket malam staf desa, presensi masuk & pulang, serta otomatisasi status Lepas Piket.</p>
        </div>

        <!-- 2 Tombol Aksi Utama Saja -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- 1. Tombol Utama (Sekali Klik untuk Mengatur & Menerapkan Jadwal dengan Masa Aktif) -->
            <button wire:click="openPolaModal"
                    class="btn-sadi-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>Atur Jadwal Piket & Masa Aktif</span>
            </button>

            <!-- 2. Tombol Tambah 1 Jadwal Khusus -->
            <button wire:click="openCreateModal"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-emerald-700 font-bold text-xs border border-emerald-900 shadow-xs hover:border-slate-400 transition cursor-pointer">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah 1 Jadwal</span>
            </button>
        </div>
    </div>

    <!-- Filter & Pencarian Sederhana -->
    <div class="sadi-card p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Cari Nama Petugas</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama perangkat..."
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Pilih Bulan</label>
                <select wire:model.live="bulan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Pilih Tahun</label>
                <select wire:model.live="tahun" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium">
                    @foreach (range(date('Y') - 1, date('Y') + 2) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Status Kehadiran</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium">
                    <option value="semua">Semua Status</option>
                    <option value="terjadwal">Terjadwal (Belum Masuk)</option>
                    <option value="sedang_piket">Sedang Bertugas (Sudah Masuk)</option>
                    <option value="hadir">Selesai / Hadir Lengkap</option>
                </select>
            </div>
        </div>

        <!-- Bar Hapus Massal jika Ada yang Dicentang -->
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
                        <span>Hapus Yang Dicentang</span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Tabel Daftar Jadwal Piket -->
    <div class="sadi-card overflow-hidden" x-data="{ previewTtdSrc: null, previewTtdTitle: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 text-[#064E3B] focus:ring-[#064E3B] cursor-pointer" title="Pilih Semua">
                        </th>
                        <th class="py-3.5 px-4">Tanggal & Hari</th>
                        <th class="py-3.5 px-4">Petugas Piket</th>
                        <th class="py-3.5 px-4">Jam Pelaksanaan</th>
                        <th class="py-3.5 px-4">Absen Masuk & Pulang</th>
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
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-100 text-blue-800 uppercase">Besok</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800">{{ $p->pegawai->nama_lengkap ?? '-' }}</div>
                                <p class="text-[10px] text-slate-400 font-mono">{{ $p->pegawai->jabatan->nama_jabatan ?? 'Perangkat' }} (NIPD: {{ $p->pegawai->nipd ?? '-' }})</p>
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-700">
                                <div>{{ substr($p->jam_mulai, 0, 5) }} — {{ substr($p->jam_selesai, 0, 5) }} WIB</div>
                                <span class="text-[10px] text-slate-400 font-sans block">{{ $p->keterangan }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 space-y-1">
                                <!-- Status Absen Masuk -->
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="font-bold text-slate-500 w-12 shrink-0">Masuk:</span>
                                    @if($p->waktu_absen)
                                        <span class="font-mono text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $p->waktu_absen->format('d/m H:i') }}</span>
                                        @if($p->tanda_tangan)
                                            <button type="button" @click="previewTtdSrc = '{{ $p->tanda_tangan }}'; previewTtdTitle = 'Tanda Tangan Masuk: {{ addslashes($p->pegawai->nama_lengkap ?? '') }}';"
                                                    class="text-[10px] text-emerald-600 hover:text-emerald-800 underline font-bold cursor-pointer">
                                                Lihat TTD
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Belum</span>
                                    @endif
                                </div>
                                <!-- Status Absen Pulang -->
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="font-bold text-slate-500 w-12 shrink-0">Pulang:</span>
                                    @if($p->waktu_pulang)
                                        <span class="font-mono text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $p->waktu_pulang->format('d/m H:i') }}</span>
                                        @if($p->tanda_tangan_pulang)
                                            <button type="button" @click="previewTtdSrc = '{{ $p->tanda_tangan_pulang }}'; previewTtdTitle = 'Tanda Tangan Pulang: {{ addslashes($p->pegawai->nama_lengkap ?? '') }}';"
                                                    class="text-[10px] text-emerald-600 hover:text-emerald-800 underline font-bold cursor-pointer">
                                                Lihat TTD
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Belum</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($p->isSelesaiLengkap() || $p->status === 'hadir' || $p->status === 'lepas_piket')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        ✓ Hadir Lengkap
                                    </span>
                                @elseif($p->isSudahMasuk() || $p->status === 'sedang_piket')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        Sedang Bertugas
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                        Terjadwal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                <!-- Tombol Verifikasi Cepat oleh Admin jika diperlukan -->
                                @if($p->status === 'terjadwal' && !$p->isSudahMasuk())
                                    <button wire:click="verifikasiHadir({{ $p->id }})"
                                            wire:confirm="Verifikasi kehadiran MASUK untuk {{ $p->pegawai->nama_lengkap }}?"
                                            class="px-2 py-1 rounded-lg bg-blue-600 text-white font-bold text-[10px] hover:bg-blue-700 transition cursor-pointer" title="Verifikasi Masuk">
                                        Verif Masuk
                                    </button>
                                @endif

                                @if($p->isSudahMasuk() && !$p->isSudahPulang())
                                    <button wire:click="verifikasiPulang({{ $p->id }})"
                                            wire:confirm="Verifikasi kepulangan/selesai piket untuk {{ $p->pegawai->nama_lengkap }}? Kehadiran Lepas Piket otomatis dicatat."
                                            class="px-2 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px] hover:bg-emerald-700 transition cursor-pointer" title="Verifikasi Selesai Pulang">
                                        Verif Pulang
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
                                <div class="max-w-md mx-auto space-y-2.5">
                                    <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="font-bold text-slate-700 text-sm">Belum Ada Jadwal Piket di Bulan Ini</p>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        Klik tombol hijau <strong>"Atur Jadwal Piket & Masa Aktif"</strong> di atas untuk langsung menerapkan jadwal resmi desa sepanjang 1 tahun penuh atau periode yang Anda inginkan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Menampilkan {{ $pikets->firstItem() ?? 0 }} - {{ $pikets->lastItem() ?? 0 }} dari {{ $pikets->total() }} jadwal
            </div>
            <div>
                {{ $pikets->links() }}
            </div>
        </div>

        <!-- MODAL PREVIEW TTD DIGITAL -->
        <div x-show="previewTtdSrc !== null" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl overflow-hidden border border-slate-200">
                <div class="px-4 py-3 bg-[#064E3B] text-white flex items-center justify-between">
                    <h4 class="font-bold text-xs" x-text="previewTtdTitle"></h4>
                    <button type="button" @click="previewTtdSrc = null" class="text-emerald-200 hover:text-white cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 bg-slate-50 flex items-center justify-center">
                    <img :src="previewTtdSrc" alt="Tanda Tangan Digital" class="max-h-48 rounded-lg bg-white border border-slate-200 p-2 shadow-xs">
                </div>
                <div class="p-3 bg-white border-t border-slate-100 flex justify-end">
                    <button type="button" @click="previewTtdSrc = null" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL UTAMA: ATUR JADWAL PIKET DESA & MASA AKTIF (SUPER MUDAH) -->
    @if ($showPolaModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-auto flex flex-col max-h-[95vh]">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-xl bg-emerald-800/90 text-[#E2C268] border border-[#C9A84C]/40 shadow-xs">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-outfit text-base font-extrabold text-white leading-tight">
                                Atur Jadwal Piket & Masa Aktif
                            </h3>
                            <p class="text-xs text-emerald-200 font-medium mt-0.5">
                                Cukup tentukan rentang waktu, jadwal mingguan akan otomatis diterapkan.
                            </p>
                        </div>
                    </div>
                    <button wire:click="closePolaModal" class="p-1.5 rounded-xl hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="generatePolaJadwalDesa" class="p-6 text-xs flex flex-col justify-between overflow-y-auto space-y-5">
                    <!-- BAGIAN 1: RENTANG WAKTU / MASA AKTIF -->
                    <div class="p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-[#064E3B] uppercase tracking-wider text-xs">
                                1. Pilih Masa Berlaku Jadwal
                            </label>
                            <span class="text-[11px] text-slate-500 font-medium">Berapa lama jadwal ini berlaku?</span>
                        </div>

                        <!-- Pilihan Tombol Cepat -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-0.5 {{ $polaDurasi === '1_tahun' ? 'border-[#064E3B] bg-white text-[#064E3B] font-extrabold shadow-sm' : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-white' }}">
                                <input type="radio" wire:model.live="polaDurasi" value="1_tahun" class="sr-only">
                                <span class="text-xs font-bold">1 Tahun Penuh</span>
                                <span class="text-[10px] text-slate-400">12 Bulan (365 Hari)</span>
                            </label>
                            <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-0.5 {{ $polaDurasi === '6_bulan' ? 'border-[#064E3B] bg-white text-[#064E3B] font-extrabold shadow-sm' : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-white' }}">
                                <input type="radio" wire:model.live="polaDurasi" value="6_bulan" class="sr-only">
                                <span class="text-xs font-bold">6 Bulan</span>
                                <span class="text-[10px] text-slate-400">1 Semester</span>
                            </label>
                            <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-0.5 {{ $polaDurasi === '1_bulan' ? 'border-[#064E3B] bg-white text-[#064E3B] font-extrabold shadow-sm' : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-white' }}">
                                <input type="radio" wire:model.live="polaDurasi" value="1_bulan" class="sr-only">
                                <span class="text-xs font-bold">1 Bulan</span>
                                <span class="text-[10px] text-slate-400">~30 Hari</span>
                            </label>
                            <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center justify-center gap-0.5 {{ $polaDurasi === 'custom' ? 'border-[#064E3B] bg-white text-[#064E3B] font-extrabold shadow-sm' : 'border-slate-200 bg-white/70 text-slate-600 hover:bg-white' }}">
                                <input type="radio" wire:model.live="polaDurasi" value="custom" class="sr-only">
                                <span class="text-xs font-bold">Pilih Tanggal</span>
                                <span class="text-[10px] text-slate-400">Tentukan Sendiri</span>
                            </label>
                        </div>

                        <!-- Info Tanggal Aktif -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block font-bold text-slate-700 text-[11px] mb-1">Mulai Dari Tanggal:</label>
                                <input type="date" wire:model.live="polaTanggalMulai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium">
                                @error('polaTanggalMulai') <span class="text-red-500 text-[10px] block mt-0.5 font-semibold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 text-[11px] mb-1">Sampai Dengan Tanggal:</label>
                                <input type="date" wire:model="polaTanggalSelesai" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] bg-white font-medium" {{ $polaDurasi !== 'custom' ? 'readonly' : '' }}>
                                @error('polaTanggalSelesai') <span class="text-red-500 text-[10px] block mt-0.5 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: DAFTAR NAMA PETUGAS PER HARI (SENIN S/D MINGGU) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-slate-800 uppercase tracking-wider text-xs">
                                2. Petugas Piket Per Hari (Senin — Minggu)
                            </label>
                            <button type="button" wire:click="resetPolaKeDefaultDesa" class="text-xs font-bold text-[#064E3B] hover:underline flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Kembalikan ke Jadwal Resmi</span>
                            </button>
                        </div>

                        @php
                            $daysMeta = [
                                1 => ['nama' => 'Senin', 'badge' => 'bg-emerald-100 text-[#064E3B]'],
                                2 => ['nama' => 'Selasa', 'badge' => 'bg-emerald-100 text-[#064E3B]'],
                                3 => ['nama' => 'Rabu', 'badge' => 'bg-blue-100 text-blue-900'],
                                4 => ['nama' => 'Kamis', 'badge' => 'bg-blue-100 text-blue-900'],
                                5 => ['nama' => 'Jumat', 'badge' => 'bg-blue-100 text-blue-900'],
                                6 => ['nama' => 'Sabtu', 'badge' => 'bg-amber-100 text-amber-900'],
                                0 => ['nama' => 'Minggu', 'badge' => 'bg-amber-100 text-amber-900'],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-2.5">
                            @foreach ($daysMeta as $dayIndex => $meta)
                                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-2.5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="px-2 py-0.5 rounded-md font-extrabold text-[11px] {{ $meta['badge'] }}">
                                                {{ $meta['nama'] }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-bold">
                                                {{ count($polaHariStaf[$dayIndex] ?? []) }} Petugas
                                            </span>
                                        </div>

                                        <!-- Daftar Centang Nama Staf Laki-laki Desa -->
                                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-0.5">
                                            @foreach ($pegawais as $p)
                                                @php
                                                    $isChecked = in_array((string)$p->id, $polaHariStaf[$dayIndex] ?? []);
                                                @endphp
                                                <label class="flex items-center gap-1.5 p-1 rounded-lg transition cursor-pointer text-[11px] {{ $isChecked ? 'bg-emerald-100/70 text-[#064E3B] font-bold' : 'hover:bg-white text-slate-700' }}">
                                                    <input type="checkbox" wire:model.live="polaHariStaf.{{ $dayIndex }}" value="{{ (string) $p->id }}" class="rounded text-[#064E3B] focus:ring-[#064E3B] w-3.5 h-3.5">
                                                    <span class="truncate" title="{{ $p->nama_lengkap }}">
                                                        {{ $p->nama_lengkap }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Banner Konfirmasi Ringkas -->
                    <div class="p-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div>
                            <span>Jadwal akan otomatis dibuat untuk rentang <strong>{{ \Carbon\Carbon::parse($polaTanggalMulai)->isoFormat('D MMMM Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($polaTanggalSelesai)->isoFormat('D MMMM Y') }}</strong> (Jam: 19:00 - 06:00 WIB).</span>
                        </div>
                    </div>

                    <!-- Modal Actions Sederhana -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" wire:click="closePolaModal" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-sadi-primary px-6 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan & Terapkan Jadwal</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL SATUAN: TAMBAH / UBAH 1 JADWAL KHUSUS -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <h3 class="font-outfit text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ $isEdit ? 'Ubah Jadwal Piket' : 'Tambah 1 Jadwal Piket' }}</span>
                    </h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-xs mb-1">
                            Petugas Piket <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="pegawai_id" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] bg-white font-medium">
                            <option value="">-- Pilih Nama Perangkat Desa --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nama_lengkap }} — {{ $p->jabatan->nama_jabatan ?? 'Perangkat' }} (NIPD: {{ $p->nipd ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('pegawai_id') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
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
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Tugas <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="keterangan" placeholder="Contoh: Piket Jaga Malam Balai Desa" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] font-medium">
                        @error('keterangan') <span class="text-red-500 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-sadi-primary px-5 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Jadwal' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
