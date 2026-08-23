<div class="space-y-6">

    {{-- ===== HEADER & CONTEXT BANNER ===== --}}
    <div class="sadi-card p-6 border-l-4 border-l-[#C9A84C] bg-white">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-0.5 rounded-full text-[11px] font-extrabold tracking-wide uppercase bg-[#064E3B] text-[#E2C268] border border-[#C9A84C]">
                        Sekretaris Desa / Admin
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                        <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Data Asli Murni 100% Terlindungi
                    </span>
                </div>
                <h1 class="font-outfit text-2xl lg:text-3xl font-extrabold text-[#064E3B]">
                    Laporan Presensi Disesuaikan (Shadow Layer)
                </h1>
                <p class="text-xs text-slate-600 max-w-3xl leading-relaxed">
                    Halaman ini digunakan khusus untuk kebutuhan administratif dan pencairan dana (SPJ). Penyesuaian status kehadiran hanya berlaku pada laporan bayangan ini dan tidak mengubah data presensi asli yang digunakan Kepala Desa untuk evaluasi kinerja.
                </p>
            </div>

            {{-- Mode Switcher Tabs --}}
            <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200 shrink-0 self-start lg:self-center">
                <button wire:click="setMode('harian')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $mode === 'harian' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-700 hover:text-[#064E3B]' }}">
                    Presensi Harian
                </button>
                <button wire:click="setMode('bulanan')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $mode === 'bulanan' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-700 hover:text-[#064E3B]' }}">
                    Matriks Bulanan
                </button>
                <button wire:click="setMode('pusat_cetak')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $mode === 'pusat_cetak' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-700 hover:text-[#064E3B]' }}">
                    Cetak 4 Laporan PDF
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: MODE HARIAN (TABEL INTERAKTIF & 1-CLICK HADIRKAN)               --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($mode === 'harian')
    <div class="space-y-4">
        {{-- Toolbar Filter & Quick Actions --}}
        <div class="sadi-card p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Tanggal:</label>
                    <input type="date" wire:model.live="tanggalHarian"
                        class="px-3.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-[#064E3B] outline-none shadow-xs cursor-pointer">
                </div>

                @if($isWeekendHarian)
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">Akhir Pekan</span>
                @endif
                @if($hariLiburHariIni)
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">{{ $hariLiburHariIni->nama_hari_libur }}</span>
                @endif

                <div class="relative min-w-[220px]">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama pegawai..."
                        class="w-full pl-8 pr-3 py-2 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-[#064E3B] outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            {{-- Bulk Actions --}}
            <div class="flex items-center gap-2">
                <button wire:click="hadirkanSemuaHariIni"
                    wire:confirm="Yakin ingin menyesuaikan seluruh pegawai aktif menjadi HADIR untuk tanggal ini beserta tanda tangan pinjaman?"
                    class="btn-gold px-4 py-2 rounded-lg text-xs font-extrabold flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Hadirkan Semua Hari Ini</span>
                </button>

                @if($rekapHarian['disesuaikan'] > 0)
                <button wire:click="resetSemuaTanggal"
                    wire:confirm="Hapus seluruh penyesuaian tanggal ini dan kembalikan ke data asli murni?"
                    class="px-3 py-2 rounded-lg text-xs font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-100 transition cursor-pointer">
                    Reset ke Murni
                </button>
                @endif

                <a href="{{ $urlHarian }}" target="_blank"
                    class="btn-sadi-primary px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>

        {{-- Mini Summary Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
            <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Hadir</p>
                <p class="font-outfit text-lg font-extrabold text-[#064E3B]">{{ $rekapHarian['hadir'] }}</p>
            </div>
            <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Terlambat</p>
                <p class="font-outfit text-lg font-extrabold text-slate-800">{{ $rekapHarian['terlambat'] }}</p>
            </div>
            <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Izin</p>
                <p class="font-outfit text-lg font-extrabold text-slate-800">{{ $rekapHarian['izin'] }}</p>
            </div>
            <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Sakit</p>
                <p class="font-outfit text-lg font-extrabold text-slate-800">{{ $rekapHarian['sakit'] }}</p>
            </div>
            <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Dinas Luar</p>
                <p class="font-outfit text-lg font-extrabold text-slate-800">{{ $rekapHarian['dinas'] }}</p>
            </div>
            <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs text-center">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Alpa</p>
                <p class="font-outfit text-lg font-extrabold text-slate-800">{{ $rekapHarian['alpa'] }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-[#C9A84C]/50 shadow-xs text-center col-span-2 sm:col-span-1">
                <p class="text-[10px] text-[#064E3B] font-bold uppercase tracking-wider">Disesuaikan</p>
                <p class="font-outfit text-lg font-extrabold text-[#064E3B]">{{ $rekapHarian['disesuaikan'] }} Pegawai</p>
            </div>
        </div>

        {{-- Table Daily --}}
        <div class="sadi-card overflow-hidden bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-[#064E3B] text-white">
                        <tr>
                            <th class="py-2.5 px-2.5 text-center w-10 font-extrabold text-[#E2C268] border-r border-emerald-800">NO</th>
                            <th class="py-2.5 px-3 font-extrabold text-white border-r border-emerald-800 w-[22%]">NAMA PERANGKAT / NIPD</th>
                            <th class="py-2.5 px-3 font-extrabold text-white border-r border-emerald-800 w-[15%]">JABATAN</th>
                            <th class="py-2.5 px-2.5 text-center font-extrabold text-white border-r border-emerald-800 w-[10%]">STATUS ASLI</th>
                            <th class="py-2.5 px-2.5 text-center font-extrabold text-[#E2C268] border-r border-emerald-800 w-[11%]">STATUS LAPORAN</th>
                            <th class="py-2.5 px-2.5 text-center font-extrabold text-white border-r border-emerald-800 w-[12%]">JAM KERJA</th>
                            <th class="py-2.5 px-3 text-center font-extrabold text-white border-r border-emerald-800 w-[13%]">TANDA TANGAN</th>
                            <th class="py-2.5 px-3 text-center font-extrabold text-[#E2C268] w-[17%]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($harianList as $idx => $item)
                        <tr class="hover:bg-slate-50/80 transition {{ $item['is_adjusted'] ? 'bg-amber-50/20' : '' }}">
                            <td class="py-2.5 px-2.5 text-center font-bold text-slate-500 border-r border-slate-100">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3 border-r border-slate-100">
                                <div class="font-bold text-slate-900 truncate">{{ $item['pegawai']->nama_lengkap }}</div>
                                <div class="text-[10.5px] text-slate-500 truncate">NIPD: {{ $item['pegawai']->nipd ?: '-' }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 border-r border-slate-100">
                                <span class="truncate block">{{ $item['pegawai']->jabatan->nama_jabatan ?? '—' }}</span>
                            </td>

                            {{-- Status Asli --}}
                            <td class="py-2.5 px-2.5 text-center border-r border-slate-100">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold border inline-block
                                    @if(in_array($item['status_asli'], ['Hadir', 'Tepat Waktu']))
                                        bg-emerald-50 text-emerald-800 border-emerald-300
                                    @elseif($item['status_asli'] === 'Terlambat')
                                        bg-amber-50 text-amber-800 border-amber-300
                                    @elseif(in_array($item['status_asli'], ['Izin', 'Sakit']))
                                        bg-teal-50 text-teal-800 border-teal-300
                                    @elseif($item['status_asli'] === 'Dinas Luar')
                                        bg-blue-50 text-blue-800 border-blue-300
                                    @elseif($item['status_asli'] === 'Libur')
                                        bg-slate-100 text-slate-600 border-slate-300
                                    @else
                                        bg-rose-50 text-rose-700 border-rose-300
                                    @endif">
                                    {{ $item['status_asli'] }}
                                </span>
                            </td>

                            {{-- Status Disesuaikan --}}
                            <td class="py-2.5 px-2.5 text-center border-r border-slate-100">
                                <div class="inline-flex items-center gap-1 justify-center">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold border inline-block
                                        @if(in_array($item['status_aktif'], ['Hadir', 'Tepat Waktu']))
                                            bg-emerald-50 text-[#064E3B] border-emerald-300
                                        @elseif(in_array($item['status_aktif'], ['Izin', 'Sakit', 'Dinas Luar']))
                                            bg-slate-100 text-slate-800 border-slate-300
                                        @elseif($item['status_aktif'] === 'Libur')
                                            bg-slate-50 text-slate-500 border-slate-200
                                        @else
                                            bg-slate-100 text-slate-800 border-slate-300
                                        @endif">
                                        {{ $item['status_aktif'] }}
                                    </span>
                                    @if($item['is_adjusted'])
                                        <span class="text-[9px] font-extrabold bg-[#C9A84C] text-[#064E3B] px-1 py-0.5 rounded leading-none" title="Disesuaikan Admin">
                                            ADJ
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Jam Kerja --}}
                            <td class="py-2.5 px-2.5 text-center font-mono text-[11px] text-slate-800 border-r border-slate-100">
                                @if($item['jam_masuk'] !== '-')
                                    <span class="font-bold">{{ $item['jam_masuk'] }}</span> – <span class="font-bold">{{ $item['jam_pulang'] }}</span>
                                    <div class="text-[9.5px] text-slate-400 font-sans">({{ $item['durasi'] }})</div>
                                @else
                                    <span class="text-slate-400 font-sans">-</span>
                                @endif
                            </td>

                            {{-- Tanda Tangan --}}
                            <td class="py-2.5 px-3 text-center border-r border-slate-100">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($item['ttd_src'])
                                        <img src="{{ $item['ttd_src'] }}" class="h-5 max-w-[50px] object-contain border border-slate-200 rounded p-0.5 bg-white shadow-2xs">
                                        <span class="text-[10px] text-slate-700 font-medium text-left truncate">
                                            {{ $item['ttd_label'] }}
                                        </span>
                                    @else
                                        <span class="text-[10.5px] text-slate-400 italic">Belum Ada</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-2.5 px-3 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if(!in_array($item['status_aktif'], ['Hadir', 'Tepat Waktu']))
                                    <button wire:click="hadirkanPegawaiCepat({{ $item['pegawai']->id }}, '{{ $tanggalHarian }}')"
                                        title="Ubah langsung jadi Hadir + Pinjam TTD"
                                        class="h-7 px-2.5 inline-flex items-center gap-1 rounded-md text-[11px] font-bold bg-[#064E3B] text-white hover:bg-[#04392B] border border-[#064E3B] transition shadow-2xs whitespace-nowrap cursor-pointer">
                                        <svg class="w-3 h-3 text-[#E2C268] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Hadirkan</span>
                                    </button>
                                    @endif

                                    <button wire:click="bukaEdit({{ $item['pegawai']->id }}, '{{ $tanggalHarian }}')"
                                        title="Edit detail presensi pegawai ini"
                                        class="h-7 px-2.5 inline-flex items-center gap-1 rounded-md text-[11px] font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 transition shadow-2xs whitespace-nowrap cursor-pointer">
                                        <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    @if($item['is_adjusted'])
                                    <button wire:click="resetKeDataAsli({{ $item['pegawai']->id }}, '{{ $tanggalHarian }}')"
                                        title="Kembalikan ke data asli murni"
                                        class="h-7 w-7 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">Tidak ada data pegawai yang sesuai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: MODE MATRIKS BULANAN (GRID TANGGAL 1-31)                        --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($mode === 'bulanan')
    <div class="space-y-4">
        {{-- Filter Bulan & Tahun --}}
        <div class="sadi-card p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider block mb-1">Bulan:</label>
                    <select wire:model.live="bulanBulanan" class="px-3.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-[#064E3B] outline-none cursor-pointer">
                        @foreach ($listBulan as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider block mb-1">Tahun:</label>
                    <select wire:model.live="tahunBulanan" class="px-3.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-[#064E3B] outline-none cursor-pointer">
                        @foreach ($tahunOptions as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ $urlBulanan }}" target="_blank"
                    class="btn-sadi-primary px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Cetak PDF Matriks Bulanan</span>
                </a>
            </div>
        </div>

        {{-- Matriks Table --}}
        <div class="sadi-card overflow-hidden bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-center text-xs border-collapse">
                    <thead class="bg-[#064E3B] text-white">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-2 font-extrabold text-[#E2C268] w-8 border-r border-emerald-800">No</th>
                            <th rowspan="2" class="py-2.5 px-3 font-extrabold text-white text-left min-w-[160px] border-r border-emerald-800">Nama Perangkat</th>
                            <th colspan="{{ $daysInMonth }}" class="py-1 text-center font-extrabold text-white border-r border-emerald-800">
                                Tanggal Presensi (1 – {{ $daysInMonth }} {{ $namaBulan }} {{ $tahunBulanan }})
                            </th>
                            <th colspan="5" class="py-1 text-center font-extrabold text-[#E2C268] bg-[#04392B] border-r border-emerald-800">Rekap</th>
                            <th rowspan="2" class="py-2.5 px-2 font-extrabold text-[#E2C268] w-12">%</th>
                        </tr>
                        <tr>
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $dt = \Carbon\Carbon::createFromDate($tahunBulanan, $bulanBulanan, $d);
                                    $isWk = $dt->isWeekend();
                                @endphp
                                <th class="p-1 text-[11px] font-bold border-r border-emerald-800/40 {{ $isWk ? 'bg-slate-800 text-slate-300' : '' }}">{{ $d }}</th>
                            @endfor
                            <th class="p-1 text-[10px] font-extrabold bg-[#04392B] text-white border-r border-emerald-800">H</th>
                            <th class="p-1 text-[10px] font-extrabold bg-[#04392B] text-white border-r border-emerald-800">T</th>
                            <th class="p-1 text-[10px] font-extrabold bg-[#04392B] text-white border-r border-emerald-800">I</th>
                            <th class="p-1 text-[10px] font-extrabold bg-[#04392B] text-white border-r border-emerald-800">D</th>
                            <th class="p-1 text-[10px] font-extrabold bg-[#04392B] text-white border-r border-emerald-800">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($pegawais as $idx => $p)
                        @php
                            $sum = $summaryBulanan[$p->id] ?? ['H' => 0, 'T' => 0, 'I' => 0, 'D' => 0, 'A' => 0, 'L' => 0, 'persen' => 0, 'adjusted_count' => 0];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2 px-1 text-slate-500 font-bold text-[11px] border-r border-slate-100">{{ $idx + 1 }}</td>
                            <td class="py-2 px-3 text-left font-bold text-slate-900 border-r border-slate-100">
                                {{ $p->nama_lengkap }}
                                @if($sum['adjusted_count'] > 0)
                                    <span class="inline-flex px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-[#C9A84C] text-[#064E3B] ml-1">
                                        {{ $sum['adjusted_count'] }} adj
                                    </span>
                                @endif
                            </td>

                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cell = $matrixBulanan[$p->id][$d] ?? ['code' => '-', 'is_adjusted' => false, 'date_str' => ''];
                                    $code = $cell['code'];
                                    $isAdj = $cell['is_adjusted'];
                                    $dStr = $cell['date_str'];
                                @endphp
                                <td wire:click="bukaEdit({{ $p->id }}, '{{ $dStr }}')"
                                    title="Klik untuk edit tgl {{ $dStr }}"
                                    class="p-1 text-[11px] font-bold cursor-pointer hover:bg-slate-200 transition border-r border-slate-100
                                        {{ $isAdj ? 'bg-amber-100 text-amber-950 font-black' : '' }}
                                        @if($code === 'H') text-[#064E3B] font-extrabold
                                        @elseif($code === 'T') text-slate-800
                                        @elseif($code === 'I') text-slate-700
                                        @elseif($code === 'D') text-slate-800
                                        @elseif($code === 'A') text-slate-900 font-bold
                                        @elseif($code === 'L') text-slate-400 bg-slate-50
                                        @else text-slate-300 @endif">
                                    {{ $code }}
                                </td>
                            @endfor

                            <td class="py-2 px-1 font-bold text-slate-900 bg-slate-50 border-r border-slate-100">{{ $sum['H'] }}</td>
                            <td class="py-2 px-1 font-bold text-slate-700 bg-slate-50 border-r border-slate-100">{{ $sum['T'] }}</td>
                            <td class="py-2 px-1 font-bold text-slate-700 bg-slate-50 border-r border-slate-100">{{ $sum['I'] }}</td>
                            <td class="py-2 px-1 font-bold text-slate-700 bg-slate-50 border-r border-slate-100">{{ $sum['D'] }}</td>
                            <td class="py-2 px-1 font-bold text-slate-900 bg-slate-50 border-r border-slate-100">{{ $sum['A'] }}</td>
                            <td class="py-2 px-1 font-extrabold text-[#064E3B] bg-slate-100">{{ $sum['persen'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: PUSAT CETAK 4 DOKUMEN PDF (STANDAR RESMI)                       --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($mode === 'pusat_cetak')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- 1. Laporan Harian --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 border border-slate-200 bg-white hover:shadow-md transition">
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#064E3B] text-[#E2C268] border border-[#C9A84C] flex items-center justify-center font-bold">1</div>
                    <div>
                        <h3 class="font-outfit text-base font-bold text-slate-900">Laporan Presensi Harian (Disesuaikan)</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Daftar hadir harian lengkap dengan kolom tanda tangan sah. Format A4 Portrait.</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Tanggal</label>
                    <input type="date" wire:model.live="tanggalHarian" class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 cursor-pointer">
                </div>
            </div>
            <a href="{{ $urlHarian }}" target="_blank"
                class="btn-sadi-primary w-full py-2.5 rounded-lg text-xs font-bold flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Cetak PDF Harian</span>
            </a>
        </div>

        {{-- 2. Laporan Bulanan --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 border border-slate-200 bg-white hover:shadow-md transition">
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#064E3B] text-[#E2C268] border border-[#C9A84C] flex items-center justify-center font-bold">2</div>
                    <div>
                        <h3 class="font-outfit text-base font-bold text-slate-900">Laporan Matriks Bulanan (Disesuaikan)</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Matriks tanggal 1-31 dan persentase kehadiran bulanan. Format A4 Landscape.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Bulan</label>
                        <select wire:model.live="bulanBulanan" class="w-full px-2.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 cursor-pointer">
                            @foreach ($listBulan as $num => $nama)
                                <option value="{{ $num }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tahun</label>
                        <select wire:model.live="tahunBulanan" class="w-full px-2.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 cursor-pointer">
                            @foreach ($tahunOptions as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <a href="{{ $urlBulanan }}" target="_blank"
                class="btn-sadi-primary w-full py-2.5 rounded-lg text-xs font-bold flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Cetak PDF Bulanan</span>
            </a>
        </div>

        {{-- 3. Laporan Tahunan --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 border border-slate-200 bg-white hover:shadow-md transition">
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#064E3B] text-[#E2C268] border border-[#C9A84C] flex items-center justify-center font-bold">3</div>
                    <div>
                        <h3 class="font-outfit text-base font-bold text-slate-900">Laporan Akumulasi Tahunan (Disesuaikan)</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Rekapitulasi 12 bulan (Jan–Des) tingkat kehadiran tahunan. Format A4 Landscape.</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Tahun</label>
                    <select wire:model.live="tahunTahunan" class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 cursor-pointer">
                        @foreach ($tahunOptions as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <a href="{{ $urlTahunan }}" target="_blank"
                class="btn-sadi-primary w-full py-2.5 rounded-lg text-xs font-bold flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Cetak PDF Tahunan</span>
            </a>
        </div>

        {{-- 4. Laporan Rentang Bebas --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 border border-slate-200 bg-white hover:shadow-md transition">
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#064E3B] text-[#E2C268] border border-[#C9A84C] flex items-center justify-center font-bold">4</div>
                    <div>
                        <h3 class="font-outfit text-base font-bold text-slate-900">Laporan Rentang Fleksibel (Disesuaikan)</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Cetak presensi bebas tanggal mulai s/d tanggal selesai (kegiatan khusus/SPJ).</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" wire:model.live="tanggalMulai" class="w-full px-2.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Selesai</label>
                        <input type="date" wire:model.live="tanggalSelesai" class="w-full px-2.5 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 cursor-pointer">
                    </div>
                </div>
            </div>
            <a href="{{ $urlRentang }}" target="_blank"
                class="btn-gold w-full py-2.5 rounded-lg text-xs font-extrabold flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Cetak PDF Rentang Tanggal</span>
            </a>
        </div>

    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL EDIT INDIVIDUAL PRESENSI DISESUAIKAN                             --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="sadi-card w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-150 border-2 border-[#C9A84C] bg-white">
            
            {{-- Modal Header --}}
            <div class="bg-[#064E3B] p-4 text-white flex items-center justify-between border-b border-[#C9A84C]">
                <div>
                    <h3 class="font-outfit text-base font-bold text-[#E2C268]">Edit Penyesuaian Presensi</h3>
                    <p class="text-xs text-slate-300 mt-0.5">{{ $editNamaPegawai }} ({{ \Carbon\Carbon::parse($editTanggal)->translatedFormat('d F Y') }})</p>
                </div>
                <button wire:click="$set('showEditModal', false)" class="text-slate-300 hover:text-white text-lg font-bold p-1 cursor-pointer">✕</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto bg-white">
                
                {{-- Status Comparison --}}
                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block">Status Asli Murni</span>
                        <span class="text-xs font-bold text-slate-800 mt-0.5 inline-block">{{ $editStatusAsli }}</span>
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-800 font-extrabold uppercase block mb-1">Status Disesuaikan</label>
                        <select wire:model.live="editStatusDisesuaikan" class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-[#064E3B] outline-none cursor-pointer">
                            <option value="Hadir">Hadir</option>
                            <option value="Tepat Waktu">Tepat Waktu</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Dinas Luar">Dinas Luar</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa</option>
                            <option value="Libur">Libur</option>
                        </select>
                    </div>
                </div>

                {{-- Jam Masuk & Pulang --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam Masuk</label>
                        <input type="time" wire:model="editJamMasuk" class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam Pulang</label>
                        <input type="time" wire:model="editJamPulang" class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B] outline-none">
                    </div>
                </div>

                {{-- Tanda Tangan Section --}}
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span>Tanda Tangan Bukti Hadir:</span>
                        </label>
                        <button type="button" wire:click="cariUlangTandaTangan"
                            class="text-[11px] font-bold text-[#064E3B] hover:underline flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Cari Arsip TTD (H-1 s/d H-7)</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-3 p-2 bg-white rounded-lg border border-slate-200">
                        @if($editTandaTangan)
                            <img src="{{ str_starts_with($editTandaTangan, 'data:') ? $editTandaTangan : \Illuminate\Support\Facades\Storage::url($editTandaTangan) }}"
                                class="h-12 max-w-[120px] object-contain border border-slate-200 rounded p-1 bg-white">
                            <div class="text-xs space-y-0.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $editSumberTtd === 'asli' ? 'Tanda Tangan Asli' : ($editTanggalSumberTtd ? 'Dipinjam dari ' . \Carbon\Carbon::parse($editTanggalSumberTtd)->translatedFormat('d M Y') : 'TTD Arsip') }}
                                </span>
                                <p class="text-[11px] text-slate-500">Tanda tangan siap digunakan pada cetak laporan.</p>
                            </div>
                        @else
                            <div class="text-center w-full py-2 text-xs text-slate-400 italic">
                                Belum ada tanda tangan. Klik "Cari Arsip TTD" di atas untuk mencari tanda tangan riil pegawai ini.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Keperluan (Opsional)</label>
                    <input type="text" wire:model="editKeterangan" placeholder="Contoh: Penyesuaian SPJ Dana Desa Tahap 2"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#064E3B] outline-none">
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="bg-slate-50 p-4 flex items-center justify-end gap-2.5 border-t border-slate-200">
                <button wire:click="$set('showEditModal', false)"
                    class="px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button wire:click="simpanEdit"
                    class="btn-sadi-primary px-5 py-2 text-xs font-bold rounded-lg shadow cursor-pointer">
                    Simpan Penyesuaian
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
