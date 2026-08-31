<div class="space-y-6">

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 1. HEADER & FILTER KONTROL                                             -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Standar Disiplin Aparatur</span>
                </span>
                <span class="text-xs text-slate-400 font-medium">PP No. 94 / Permendagri</span>
            </div>
            <h1 class="font-outfit text-2xl font-extrabold text-[#064E3B] tracking-tight mt-1">
                Analitik Kedisiplinan & Kinerja
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Evaluasi tingkat kehadiran, kepatuhan jam kerja, dan akuntabilitas aparatur Desa Nangtang
            </p>
        </div>

        <!-- Filter Controls & Cetak Button (Single Row Balanced Toolbar) -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <!-- Filter Bulan -->
            <div class="flex items-center bg-white border border-slate-200 rounded-xl px-3 h-9.5 shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-400 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1.5">Bulan:</span>
                <select wire:model.live="selectedMonth" class="text-xs font-bold text-slate-800 bg-transparent border-0 focus:ring-0 cursor-pointer pr-3 py-0">
                    <option value="">Setahun Penuh</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="flex items-center bg-white border border-slate-200 rounded-xl px-3 h-9.5 shadow-xs">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1.5">Tahun:</span>
                <select wire:model.live="selectedYear" class="text-xs font-bold text-slate-800 bg-transparent border-0 focus:ring-0 cursor-pointer pr-3 py-0">
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                </select>
            </div>

            <!-- Tombol Unduh / Cetak PDF Resmi -->
            <a href="{{ route('analitik.pdf', ['bulan' => $selectedMonth, 'tahun' => $selectedYear]) }}" target="_blank"
               class="btn-sadi-primary inline-flex items-center gap-1.5 px-3.5 h-9.5 rounded-xl text-white text-xs font-bold shadow-sm hover:shadow-md transition cursor-pointer shrink-0">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Cetak Rekap PDF</span>
            </a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2. KPI SUMMARY METRIC CARDS                                            -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Card 1: Indeks Kedisiplinan (IKK) -->
        <div class="sadi-card p-4.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-slate-600 text-xs font-bold mb-1">
                <span>Indeks Kedisiplinan</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#064E3B] text-[#E2C268]">
                    {{ $skorIKK >= 90 ? 'Grade A' : ($skorIKK >= 80 ? 'Grade B' : ($skorIKK >= 70 ? 'Grade C' : 'Grade D')) }}
                </span>
            </div>
            <p class="font-outfit text-3xl font-extrabold text-[#064E3B] tracking-tight mt-1">{{ $skorIKK }}%</p>
            <p class="text-[11px] text-slate-500 mt-1 font-medium truncate">
                {{ $skorIKK >= 90 ? 'Kepatuhan Sangat Baik' : ($skorIKK >= 80 ? 'Kepatuhan Baik' : ($skorIKK >= 70 ? 'Kepatuhan Cukup' : 'Perlu Pembinaan')) }}
            </p>
        </div>

        <!-- Card 2: Ketepatan Waktu -->
        <div class="sadi-card p-4.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                <span>Ketepatan Waktu</span>
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="font-outfit text-3xl font-extrabold text-slate-800 tracking-tight mt-1">{{ $persenTepatWaktu }}%</p>
            <p class="text-[11px] text-slate-500 mt-1 font-medium truncate">
                {{ $totalTepatWaktu }} dari {{ $totalHadir }} kehadiran
            </p>
        </div>

        <!-- Card 3: Rata-rata Jam Kerja -->
        <div class="sadi-card p-4.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                <span>Rata Jam Kerja</span>
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <p class="font-outfit text-3xl font-extrabold text-slate-800 tracking-tight mt-1">{{ $avgJamKerja }} <span class="text-xs font-semibold text-slate-400">Jam</span></p>
            <p class="text-[11px] text-slate-500 mt-1 font-medium truncate">
                Target: ≥ 7.5 Jam / Hari
            </p>
        </div>

        <!-- Card 4: Total Keterlambatan -->
        <div class="sadi-card p-4.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                <span>Keterlambatan</span>
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="font-outfit text-3xl font-extrabold text-amber-700 tracking-tight mt-1">{{ $totalMenitTerlambat }} <span class="text-xs font-semibold text-slate-400">Mnt</span></p>
            <p class="text-[11px] text-slate-500 mt-1 font-medium truncate">
                {{ $totalTerlambat }} kali keterlambatan
            </p>
        </div>

        <!-- Card 5: Kepatuhan Piket Pelayanan -->
        <div class="sadi-card p-4.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-slate-500 text-xs font-bold mb-1">
                <span>Piket Pelayanan</span>
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <p class="font-outfit text-3xl font-extrabold text-slate-800 tracking-tight mt-1">{{ $piketRate }}%</p>
            <p class="text-[11px] text-slate-500 mt-1 font-medium truncate">
                {{ $totalPiketHadir }} / {{ $totalPiketJadwal }} sesi terlaksana
            </p>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 3. VISUALISASI TREN BULANAN & DISTRIBUSI WAKTU                          -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kiri: Grafik Batang Tren Bulanan (2 Kolom) -->
        <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs lg:col-span-2 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-outfit text-sm font-extrabold text-slate-900">Tren Kehadiran Bulanan (Tahun {{ $selectedYear }})</h3>
                    <p class="text-[11px] text-slate-500">Rasio perbandingan kehadiran per bulan</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-bold">
                    <span class="inline-flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-[#064E3B]"></span> Hadir</span>
                    <span class="inline-flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span> Izin</span>
                    <span class="inline-flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Alpa</span>
                </div>
            </div>

            <!-- Visual Bar Chart -->
            <div class="grid grid-cols-12 gap-1.5 h-40 items-end pt-3 pb-2 border-b border-slate-100">
                @foreach ($monthlyStats as $stat)
                    @php
                        $maxHadir = max(1, collect($monthlyStats)->max('hadir'));
                        $heightHadir = min(110, max(8, ($stat['hadir'] / $maxHadir) * 105));
                        $isCurrentMonth = ($stat['bulan_num'] == (int)($selectedMonth ?: date('n')));
                    @endphp
                    <div class="flex flex-col items-center gap-1 group relative cursor-pointer"
                         wire:click="setMonth('{{ $stat['bulan_num'] }}')">
                        <!-- Tooltip Hover -->
                        <div class="absolute -top-10 bg-slate-900 text-white text-[9px] py-1 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap shadow-lg z-10">
                            {{ $stat['bulan'] }}: {{ $stat['hadir'] }} Hadir ({{ $stat['persen'] }}%)
                        </div>

                        <!-- Bar -->
                        <div class="w-full flex items-end justify-center">
                            <div class="w-full rounded-t-md transition {{ $isCurrentMonth ? 'bg-[#C9A84C]' : 'bg-[#064E3B] hover:bg-emerald-700' }}"
                                 style="height: {{ $heightHadir }}px"></div>
                        </div>

                        <span class="text-[10px] font-mono font-bold {{ $isCurrentMonth ? 'text-[#064E3B] font-extrabold' : 'text-slate-500' }}">
                            {{ $stat['bulan'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                <span>Klik bulan pada grafik untuk menyaring data analitik</span>
                <span class="font-bold text-[#064E3B]">{{ $monthName }} {{ $selectedYear }}</span>
            </div>
        </div>

        <!-- Kanan: Distribusi Pola Jam Presensi (1 Kolom) -->
        <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs space-y-3">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-outfit text-sm font-extrabold text-slate-900">Distribusi Jam Masuk</h3>
                <p class="text-[11px] text-slate-500">Pola waktu kedatangan aparatur di kantor desa</p>
            </div>

            @php
                $totalMasukSample = max(1, $earlyCount + $onTimeCount + $graceCount + $lateCount);
                $pEarly = round(($earlyCount / $totalMasukSample) * 100);
                $pOnTime = round(($onTimeCount / $totalMasukSample) * 100);
                $pGrace = round(($graceCount / $totalMasukSample) * 100);
                $pLate = round(($lateCount / $totalMasukSample) * 100);
            @endphp

            <div class="space-y-3 text-xs">
                <!-- Bar 1: Awal -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <span>Awal (&lt; 07:30)</span>
                        </span>
                        <span class="font-mono font-bold text-slate-800">{{ $earlyCount }}x ({{ $pEarly }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $pEarly }}%"></div>
                    </div>
                </div>

                <!-- Bar 2: Tepat Waktu -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#064E3B]"></span>
                            <span>Tepat Waktu (07:30 – 08:00)</span>
                        </span>
                        <span class="font-mono font-bold text-slate-800">{{ $onTimeCount }}x ({{ $pOnTime }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#064E3B] h-full rounded-full" style="width: {{ $pOnTime }}%"></div>
                    </div>
                </div>

                <!-- Bar 3: Toleransi -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Toleransi (08:01 – 08:15)</span>
                        </span>
                        <span class="font-mono font-bold text-slate-800">{{ $graceCount }}x ({{ $pGrace }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pGrace }}%"></div>
                    </div>
                </div>

                <!-- Bar 4: Terlambat -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            <span>Terlambat (&gt; 08:15)</span>
                        </span>
                        <span class="font-mono font-bold text-rose-700">{{ $lateCount }}x ({{ $pLate }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-rose-600 h-full rounded-full" style="width: {{ $pLate }}%"></div>
                    </div>
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600">
                <span class="font-bold text-slate-700">Jam Masuk Standar:</span> 08:00 WIB (Toleransi 15 menit).
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 4. APRESIASI DISIPLIN & PERINGATAN KETERLAMBATAN                        -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Box 1: Aparatur Disiplin Tertinggi -->
        <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-outfit text-sm font-extrabold text-slate-900">Aparatur Disiplin Tertinggi</h3>
                        <p class="text-[11px] text-slate-500">Skor kehadiran terbaik tanpa alpa (Periode {{ $monthName }})</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                    Teladan
                </span>
            </div>

            <div class="space-y-2">
                @forelse($topPerformers as $idx => $tp)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-[#064E3B] text-[#C9A84C] font-mono font-extrabold text-xs flex items-center justify-center">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <p class="font-bold text-slate-900 text-xs">{{ $tp['pegawai']->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500">{{ $tp['pegawai']->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                {{ $tp['skor'] }}% ({{ $tp['total_kehadiran'] }}x Hadir)
                            </span>
                            <p class="text-[9.5px] text-slate-400 mt-0.5">0 Menit Telat</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-3 text-center">Belum ada data presensi pada periode ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Box 2: Catatan Keterlambatan & Alpa -->
        <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-outfit text-sm font-extrabold text-slate-900">Catatan Keterlambatan & Alpa</h3>
                        <p class="text-[11px] text-slate-500">Aparatur yang membutuhkan tindak lanjut kedisiplinan</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                    Evaluasi
                </span>
            </div>

            <div class="space-y-2">
                @forelse(array_slice($warningList, 0, 3) as $w)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-900 text-xs">{{ $w['pegawai']->nama_lengkap }}</p>
                            <p class="text-[10px] text-slate-500">{{ $w['pegawai']->jabatan->nama_jabatan ?? 'Perangkat' }}</p>
                        </div>

                        <div class="text-right space-y-0.5">
                            @if($w['alpa'] > 0)
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 inline-block">
                                    {{ $w['alpa'] }}x Alpa
                                </span>
                            @endif
                            @if($w['menit_terlambat'] > 0)
                                <p class="text-[10px] font-bold text-amber-700">
                                    +{{ $w['menit_terlambat'] }} Mnt Telat
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-emerald-800 bg-emerald-50/60 rounded-xl border border-emerald-200 text-xs">
                        <svg class="w-5 h-5 text-emerald-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-bold">Kepatuhan Tertib</p>
                        <p class="text-[10px] text-emerald-600 mt-0.5">Tidak ada catatan alpa atau keterlambatan berulang pada periode ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 5. MATRIKS EVALUASI KINERJA SELURUH APARATUR                           -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card bg-white border border-slate-200/90 rounded-2xl shadow-xs space-y-3 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-outfit text-sm font-extrabold text-[#064E3B]">Matriks Kinerja Aparatur Desa</h3>
                <p class="text-[11px] text-slate-500">Evaluasi kehadiran 14 perangkat desa (Periode: {{ $monthName }} {{ $selectedYear }})</p>
            </div>

            <!-- Filter Tabs Grade & Search (Hanya memfilter baris tabel di bawah) -->
            <div class="flex flex-wrap items-center gap-2">
                <input type="text" wire:model.live.debounce.300ms="searchPegawai" placeholder="Cari nama perangkat..."
                       class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:ring-1 focus:ring-[#064E3B] focus:border-[#064E3B] bg-slate-50 text-slate-800">

                <div class="flex items-center p-0.5 bg-slate-100 rounded-xl text-[10px] font-bold">
                    <button type="button" wire:click="$set('filterKategori', 'all')"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer {{ $filterKategori === 'all' ? 'bg-[#064E3B] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('filterKategori', 'grade_a')"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer {{ $filterKategori === 'grade_a' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Grade A
                    </button>
                    <button type="button" wire:click="$set('filterKategori', 'grade_b')"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer {{ $filterKategori === 'grade_b' ? 'bg-teal-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Grade B
                    </button>
                    <button type="button" wire:click="$set('filterKategori', 'grade_c')"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer {{ $filterKategori === 'grade_c' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Grade C
                    </button>
                    <button type="button" wire:click="$set('filterKategori', 'grade_d')"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer {{ $filterKategori === 'grade_d' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Grade D
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Lengkap 14 Perangkat (Rapi, Padat & Terstruktur) -->
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#064E3B] text-white font-extrabold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3 text-white w-10 text-center">#</th>
                        <th class="py-2.5 px-3 text-white">Nama Perangkat & Jabatan</th>
                        <th class="py-2.5 px-2.5 text-center text-white w-24">Tepat Waktu</th>
                        <th class="py-2.5 px-2.5 text-center text-[#E2C268] w-24">Terlambat</th>
                        <th class="py-2.5 px-2.5 text-center text-white w-20">Dinas Luar</th>
                        <th class="py-2.5 px-2.5 text-center text-white w-20">Izin/Sakit</th>
                        <th class="py-2.5 px-2.5 text-center text-rose-300 w-16">Alpa</th>
                        <th class="py-2.5 px-2.5 text-center text-white w-20">Jam/Hr</th>
                        <th class="py-2.5 px-3 text-center text-white w-20">Skor</th>
                        <th class="py-2.5 px-3 text-center text-[#E2C268] w-24">Predikat</th>
                        <th class="py-2.5 px-3 text-slate-200">Status Kedisiplinan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium bg-white text-slate-700">
                    @forelse ($employeeMatrix as $idx => $row)
                        <tr class="hover:bg-slate-50/80 transition {{ $idx % 2 == 1 ? 'bg-slate-50/40' : '' }}">
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-400 text-center">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-extrabold text-[#064E3B] text-[11px] shrink-0">
                                        {{ substr($row['pegawai']->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 leading-tight">{{ $row['pegawai']->nama_lengkap }}</p>
                                        <p class="text-[10px] text-slate-400 leading-tight mt-0.5">{{ $row['pegawai']->jabatan->nama_jabatan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-bold text-emerald-800">
                                {{ $row['hadir_tepat'] }}x
                            </td>
                            <td class="py-2.5 px-2.5 text-center">
                                @if($row['hadir_terlambat'] > 0)
                                    <span class="inline-flex flex-col items-center leading-tight">
                                        <span class="font-extrabold text-amber-700">{{ $row['hadir_terlambat'] }}x</span>
                                        <span class="text-[9px] text-amber-600 font-mono">({{ $row['menit_terlambat'] }} mnt)</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 font-mono">0</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-bold text-teal-800">
                                {{ $row['dinas_luar'] > 0 ? $row['dinas_luar'] . 'x' : '—' }}
                            </td>
                            <td class="py-2.5 px-2.5 text-center text-slate-600">
                                {{ $row['izin_sakit'] > 0 ? $row['izin_sakit'] . ' Hari' : '—' }}
                            </td>
                            <td class="py-2.5 px-2.5 text-center">
                                @if($row['alpa'] > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                        {{ $row['alpa'] }}x
                                    </span>
                                @else
                                    <span class="text-slate-400 font-mono">0</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-mono font-bold text-slate-700">
                                {{ $row['avg_jam_kerja'] }} Jam
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="flex flex-col items-center gap-0.5">
                                    <span class="font-outfit font-extrabold text-xs text-slate-900">{{ $row['skor'] }}%</span>
                                    <div class="w-12 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $row['skor'] >= 90 ? 'bg-emerald-600' : ($row['skor'] >= 80 ? 'bg-teal-600' : ($row['skor'] >= 70 ? 'bg-amber-500' : 'bg-rose-500')) }}"
                                             style="width: {{ $row['skor'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $row['predikat_class'] }}">
                                    {{ $row['predikat'] }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-[11px] text-slate-600">
                                {{ $row['rekomendasi'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400 italic">
                                Tidak ada data pegawai yang sesuai filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>


