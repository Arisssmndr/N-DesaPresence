<div wire:poll.10s class="space-y-8">

    <!-- Top Greeting Banner & Live Indicator -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @php
                $hour = (int) now()->format('H');
                $greeting = match(true) {
                    $hour >= 5 && $hour < 11  => 'Selamat Pagi',
                    $hour >= 11 && $hour < 15 => 'Selamat Siang',
                    $hour >= 15 && $hour < 18 => 'Selamat Sore',
                    default                   => 'Selamat Malam',
                };
            @endphp
            <h1 class="font-outfit text-3xl font-bold text-[#064E3B] tracking-tight">N-DESAPRESENCE DESA NANGTANG</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">{{ $greeting }}, <span class="text-[#064E3B] font-bold">{{ auth()->user()->name }}</span> ({{ ucfirst(auth()->user()->role) }})</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Digital Clock Widget (Alpine.js) -->
            <div x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB', 1000)" class="px-4 py-2 rounded-xl bg-white border border-slate-200 shadow-xs text-xs font-mono font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="time">00:00:00 WIB</span>
            </div>
        </div>
    </div>

    <!-- Top KPI Stats Cards Grid (Matching reference design 60-30-10) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Hadir Hari Ini -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hadir Hari Ini</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-800 mt-1">{{ $statistik['hadir'] }}</p>
                <p class="text-[10px] text-emerald-700 font-semibold mt-1">{{ $statistik['persenHadir'] }}% dari total {{ $statistik['totalPegawai'] }} pegawai</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-inner border border-emerald-200 shrink-0">
                <svg class="w-7 h-7 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        <!-- Izin -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Izin</p>
                <p class="font-outfit text-3xl font-extrabold text-amber-700 mt-1">{{ $statistik['izin'] }}</p>
                <p class="text-[10px] text-amber-600 font-medium mt-1">Disetujui hari ini</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shadow-inner border border-amber-200 shrink-0">
                <svg class="w-7 h-7 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <!-- Sakit -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sakit</p>
                <p class="font-outfit text-3xl font-extrabold text-purple-700 mt-1">{{ $statistik['sakit'] }}</p>
                <p class="text-[10px] text-purple-600 font-medium mt-1">Disetujui hari ini</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center shadow-inner border border-purple-200 shrink-0">
                <svg class="w-7 h-7 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>

        <!-- Belum Scan / Alpa -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Masuk</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-500 mt-1">{{ $statistik['belumMasuk'] }}</p>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Pegawai belum presensi</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shadow-inner border border-slate-200 shrink-0">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

    </div>

    <!-- Main Workspace Section Grid (Table Live Feed + Matrix & Audit Trail) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Today's Attendance Table Feed (8 Cols) -->
        <div class="lg:col-span-8 sadi-card p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-outfit text-lg font-bold text-[#064E3B]">
                        Absensi Hari Ini
                    </h3>
                    <p class="text-xs text-slate-500">Log kehadiran perangkat desa hari ini</p>
                </div>
                <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                    {{ count($listAbsenHariIni) }} Scan Terakhir
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-[#064E3B] text-white">
                        <tr>
                            <th class="py-3 px-3 font-extrabold text-white">Nama Pegawai</th>
                            <th class="py-3 px-3 font-extrabold text-white">Jabatan</th>
                            <th class="py-3 px-3 font-extrabold text-white">Jam Masuk</th>
                            <th class="py-3 px-3 font-extrabold text-white">Jam Pulang</th>
                            <th class="py-3 px-3 text-right font-extrabold text-[#E2C268]">Status Badge</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium bg-white">
                        @forelse ($listAbsenHariIni as $a)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-[#064E3B] text-[#C9A84C] font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($a->pegawai->nama_lengkap ?? 'P', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-800">{{ $a->pegawai->nama_lengkap ?? 'Pegawai' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-slate-600">
                                    {{ $a->pegawai->jabatan->nama_jabatan ?? '-' }}
                                </td>
                                <td class="py-3 px-3 font-mono font-semibold text-slate-700">
                                    {{ $a->jam_masuk ? substr($a->jam_masuk, 0, 5) . ' WIB' : '—' }}
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-500">
                                    {{ $a->jam_pulang ? substr($a->jam_pulang, 0, 5) . ' WIB' : '—' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    @switch($a->status)
                                        @case('Hadir')
                                        @case('Tepat Waktu')
                                        @case('Terlambat')
                                        @case('Dinas Luar')
                                            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Hadir</span>
                                            @break
                                        @case('Izin')
                                            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">Izin</span>
                                            @break
                                        @case('Sakit')
                                            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-300">Sakit</span>
                                            @break
                                        @default
                                            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-300">Alpa</span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 italic">
                                    Belum ada transaksi scan presensi hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Matriks Presensi Visual Mini & Audit Logs (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Matriks Presensi & Kalender Visual Widget -->
            <div x-data="{ selectedDate: null, selectedInfo: null, selectedType: null }" class="sadi-card p-6 border-2 border-[#C9A84C]/20 bg-white">
                <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-slate-100">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-[#064E3B] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Kalender</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 font-medium capitalize">{{ $namaBulanTahun }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($kalenderBulan !== (int)date('m') || $kalenderTahun !== (int)date('Y'))
                        <button wire:click="resetToToday" @click="selectedDate = null" title="Kembali ke Hari Ini"
                            class="px-2.5 py-1 rounded-lg bg-emerald-50 text-[#064E3B] hover:bg-emerald-100 text-[10px] font-bold border border-emerald-200 shadow-2xs transition">
                            Hari Ini
                        </button>
                        @endif

                        <!-- Arrow Navigation Group (Side-by-Side UX) -->
                        <div class="inline-flex items-center bg-slate-100 rounded-lg p-0.5 border border-slate-200/80 shadow-2xs">
                            <button wire:click="prevMonth" @click="selectedDate = null" title="Bulan Sebelumnya"
                                class="w-7 h-6 rounded-md hover:bg-white text-slate-600 hover:text-[#064E3B] hover:shadow-xs flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="w-px h-3.5 bg-slate-200 mx-0.5"></div>
                            <button wire:click="nextMonth" @click="selectedDate = null" title="Bulan Berikutnya"
                                class="w-7 h-6 rounded-md hover:bg-white text-slate-600 hover:text-[#064E3B] hover:shadow-xs flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Header Hari Senin-Minggu -->
                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 uppercase mb-2">
                    <span>Sen</span>
                    <span>Sel</span>
                    <span>Rab</span>
                    <span>Kam</span>
                    <span>Jum</span>
                    <span class="text-amber-600">Sab</span>
                    <span class="text-red-500">Min</span>
                </div>

                <!-- Grid Visual Tanggal -->
                <div class="grid grid-cols-7 gap-1.5 mb-3">
                    @foreach ($calendarGrid as $c)
                        @if ($c === null)
                            <div class="h-8 rounded-lg bg-transparent"></div>
                        @else
                            @php
                                $cellClass = 'bg-emerald-700 text-white hover:bg-emerald-800';
                                if ($c['isToday']) {
                                    $cellClass = 'bg-amber-500 text-white font-black ring-2 ring-amber-600 shadow-md scale-105 z-10';
                                } elseif ($c['liburInfo']) {
                                    $cellClass = 'bg-red-50 text-red-600 border border-red-200 font-bold hover:bg-red-100';
                                } elseif ($c['isWeekend']) {
                                    $cellClass = 'bg-slate-100 text-slate-400 font-medium hover:bg-slate-200';
                                } elseif ($c['peringatanInfo']) {
                                    $cellClass = 'bg-emerald-50 text-[#064E3B] border border-emerald-300 font-bold hover:bg-emerald-100';
                                }

                                $infoText = $c['keterangan'] ?? ($c['isWeekend'] ? 'Hari Libur Akhir Pekan (Sabtu/Minggu)' : ($c['isToday'] ? 'Hari Ini — Jam Kerja Aktif' : 'Hari Kerja Aktif Kantor Desa'));
                                $typeKey = $c['liburInfo'] ? 'libur' : ($c['peringatanInfo'] ? 'peringatan' : ($c['isToday'] ? 'today' : ($c['isWeekend'] ? 'weekend' : 'kerja')));
                            @endphp
                            <div @click="selectedDate = '{{ $c['day'] }} {{ $namaBulanTahun }}'; selectedInfo = '{{ addslashes($infoText) }}'; selectedType = '{{ $typeKey }}'"
                                class="h-8 rounded-lg text-xs font-mono font-bold flex flex-col items-center justify-center transition cursor-pointer relative group hover:scale-105 active:scale-95 select-none {{ $cellClass }}"
                                :class="selectedDate === '{{ $c['day'] }} {{ $namaBulanTahun }}' ? 'ring-2 ring-emerald-900 ring-offset-1 z-20' : ''">
                                <span>{{ $c['day'] }}</span>

                                {{-- Titik Indikator Khusus --}}
                                @if ($c['liburInfo'])
                                    <span class="w-1 h-1 rounded-full bg-red-500 -mt-0.5"></span>
                                @elseif ($c['peringatanInfo'])
                                    <span class="w-1 h-1 rounded-full bg-[#C9A84C] -mt-0.5"></span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Legenda Status -->
                <div class="flex items-center justify-between text-[10px] font-semibold text-slate-600 border-t border-slate-100 pt-2.5 pb-1">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-700"></span> Kerja</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Hari Ini</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-100 border border-red-300"></span> Libur</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-100 border border-emerald-300"></span> Hari Besar</span>
                </div>

                <!-- Panel Keterangan Interaktif Saat Tanggal Diklik -->
                <div x-show="selectedDate" x-transition.opacity.duration.200ms x-cloak
                    class="mt-2.5 p-3 rounded-xl border transition-all text-xs"
                    :class="{
                        'bg-red-50 border-red-200 text-red-900': selectedType === 'libur',
                        'bg-emerald-50 border-emerald-300 text-emerald-950': selectedType === 'peringatan',
                        'bg-amber-50 border-amber-300 text-amber-950': selectedType === 'today',
                        'bg-slate-50 border-slate-200 text-slate-800': selectedType === 'weekend',
                        'bg-emerald-50/40 border-emerald-200 text-emerald-900': selectedType === 'kerja'
                    }">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-[11px] font-mono px-2 py-0.5 rounded-md bg-white/80 shadow-2xs" x-text="selectedDate"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
                                :class="{
                                    'bg-red-200/70 text-red-800': selectedType === 'libur',
                                    'bg-emerald-200/70 text-emerald-900': selectedType === 'peringatan',
                                    'bg-amber-200/70 text-amber-900': selectedType === 'today',
                                    'bg-slate-200 text-slate-700': selectedType === 'weekend',
                                    'bg-emerald-200/60 text-emerald-800': selectedType === 'kerja'
                                }"
                                x-text="selectedType === 'libur' ? 'Libur Resmi' : (selectedType === 'peringatan' ? 'Peringatan Nasional' : (selectedType === 'today' ? 'Hari Ini' : (selectedType === 'weekend' ? 'Akhir Pekan' : 'Hari Kerja')))">
                            </span>
                        </div>
                        <button @click="selectedDate = null" class="text-slate-400 hover:text-slate-700 p-0.5 rounded transition" title="Tutup">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs font-semibold leading-snug" x-text="selectedInfo"></p>
                </div>
            </div>

            <!-- Audit Trail Widget -->
            <div class="sadi-card p-6">
                <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-1">Audit Trail</h3>
                <p class="text-xs text-slate-500 mb-4">Aktivitas sistem terbaru</p>

                <div class="space-y-3 text-xs">
                    @forelse ($auditLogs as $log)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 mt-1.5 flex-shrink-0"></span>
                            <div class="flex-1 overflow-hidden">
                                <p class="font-bold text-slate-800 truncate">{{ $log->user_name }}</p>
                                <p class="text-[11px] text-slate-600 leading-snug">{{ $log->aktivitas }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 font-mono">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada catatan aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
