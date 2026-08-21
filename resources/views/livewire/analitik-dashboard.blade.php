<div class="space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Analitik Kedisiplinan & Tren Presensi</h1>
        <p class="text-xs text-slate-500 mt-1">Laporan statistik kinerja kehadiran dan ranking kedisiplinan perangkat desa</p>
    </div>

    <!-- Monthly Bar Chart Simulation -->
    <div class="sadi-card p-6">
        <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-4">Tren Kehadiran Per Bulan Tahun {{ date('Y') }}</h3>

        <div class="grid grid-cols-12 gap-2 h-44 items-end pt-6 border-b border-slate-200">
            @foreach ($monthlyStats as $stat)
                <div class="flex flex-col items-center gap-1 group relative">
                    <div class="w-full bg-emerald-600 rounded-t-md hover:bg-emerald-700 transition" style="height: {{ max(8, min(120, $stat['hadir'] * 12)) }}px"></div>
                    <span class="text-[10px] font-mono font-bold text-slate-600">{{ $stat['bulan'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Rankings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Top 5 Paling Disiplin -->
        <div class="sadi-card p-6">
            <h3 class="font-outfit text-base font-bold text-emerald-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span>Peringkat 5 Perangkat Paling Disiplin</span>
            </h3>

            <div class="space-y-3">
                @foreach ($topDisciplined as $idx => $p)
                    <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#064E3B] text-[#C9A84C] font-bold text-xs flex items-center justify-center">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <p class="font-bold text-slate-800 text-xs">{{ $p->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500">{{ $p->jabatan->nama_jabatan ?? '' }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-200 text-emerald-900">
                            {{ $p->hadir_count }}x Hadir
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
