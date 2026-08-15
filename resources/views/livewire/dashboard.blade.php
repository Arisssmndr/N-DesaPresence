<div wire:poll.10s class="space-y-8">

    <!-- Top Greeting Banner & Live Indicator -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-3xl font-bold text-[#064E3B] tracking-tight">PRESENCE DESA NANGTANG</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Selamat Pagi, <span class="text-[#064E3B] font-bold">{{ auth()->user()->name }}</span> ({{ ucfirst(auth()->user()->role) }})</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Digital Clock Widget (Alpine.js) -->
            <div x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB', 1000)" class="px-4 py-2 rounded-xl bg-white border border-[#C9A84C]/30 shadow-sm text-xs font-mono font-bold text-[#064E3B] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="time">00:00:00 WIB</span>
            </div>

            <!-- Live Status Indicator -->
            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-ping"></span>
                <span>LIVE FEED</span>
            </span>
        </div>
    </div>

    <!-- Pinned Pengumuman Banner (if available) -->
    @foreach ($pengumumans as $p)
        <div class="p-4 rounded-2xl bg-gradient-to-r from-[#064E3B] to-[#1B4D3E] text-white shadow-lg border border-[#C9A84C]/30 flex items-start justify-between gap-4">
            <div class="flex gap-3">
                <span class="p-2.5 rounded-xl bg-[#C9A84C]/20 text-[#C9A84C] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </span>
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#C9A84C] text-[#064E3B]">
                        {{ ucfirst($p->kategori) }}
                    </span>
                    <h4 class="font-outfit text-sm font-bold text-white mt-1">{{ $p->judul }}</h4>
                    <p class="text-xs text-emerald-100/90 mt-0.5 leading-relaxed">{{ $p->isi }}</p>
                </div>
            </div>
            <span class="text-[10px] text-emerald-300 font-mono flex-shrink-0">{{ $p->created_at->diffForHumans() }}</span>
        </div>
    @endforeach

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

        <!-- Belum Masuk -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Masuk</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-500 mt-1">{{ $statistik['belumMasuk'] }}</p>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Pegawai belum melakukan scan</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shadow-inner border border-slate-200 shrink-0">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Izin / Sakit -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Izin / Sakit</p>
                <p class="font-outfit text-3xl font-extrabold text-purple-700 mt-1">{{ $statistik['izinSakit'] }}</p>
                <p class="text-[10px] text-purple-600 font-medium mt-1">Pengajuan disetujui</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center shadow-inner border border-purple-200 shrink-0">
                <svg class="w-7 h-7 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <!-- Dinas Luar (SPT) -->
        <div class="sadi-card p-6 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dinas Luar (SPT)</p>
                <p class="font-outfit text-3xl font-extrabold text-blue-700 mt-1">{{ $statistik['dinasLuar'] }}</p>
                <p class="text-[10px] text-blue-600 font-medium mt-1">Tugas resmi di luar kantor</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shadow-inner border border-blue-200 shrink-0">
                <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>

    </div>

    <!-- Main Workspace Section Grid (Table Live Feed + Matrix & Audit Trail) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Today's Attendance Table Feed (8 Cols) -->
        <div class="lg:col-span-8 sadi-card p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-outfit text-lg font-bold text-[#064E3B] flex items-center gap-2">
                        <span>Absensi Hari Ini</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    </h3>
                    <p class="text-xs text-slate-500">Log kehadiran perangkat desa secara real-time (auto update 10 detik)</p>
                </div>
                <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                    {{ count($listAbsenHariIni) }} Scan Terakhir
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3 px-3">Nama Pegawai</th>
                            <th class="py-3 px-3">Jabatan</th>
                            <th class="py-3 px-3">Jam Masuk</th>
                            <th class="py-3 px-3">Jam Pulang</th>
                            <th class="py-3 px-3 text-right">Status Badge</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
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
                                    @match ($a->status)
                                        'Hadir' => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Hadir</span>,
                                        'Tepat Waktu' => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Hadir</span>,
                                        'Terlambat' => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Hadir</span>,
                                        'Dinas Luar' => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">Dinas Luar</span>,
                                        'Izin' => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">Izin</span>,
                                        'Sakit' => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">Sakit</span>,
                                        default => <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800">Alpa</span>,
                                    @endmatch
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

            <!-- Matriks Presensi Visual Widget -->
            <div class="sadi-card p-6">
                <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-1">Matriks Presensi</h3>
                <p class="text-xs text-slate-500 mb-4">Rekap harian tanggal 1–{{ count($matrixDays) }} bulan ini</p>

                <!-- Grid Visual 1-31 -->
                <div class="grid grid-cols-7 gap-1.5 mb-4">
                    @foreach ($matrixDays as $day)
                        <div class="h-6 rounded-md text-[10px] font-mono font-bold flex items-center justify-center text-white {{ $day == date('j') ? 'bg-amber-500 ring-2 ring-amber-600' : ($day % 7 == 0 || $day % 7 == 6 ? 'bg-slate-300 text-slate-700' : 'bg-emerald-600') }}">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-around text-[10px] font-semibold text-slate-600 border-t border-slate-100 pt-3">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Hadir</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Hari Ini</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span> Libur</span>
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
