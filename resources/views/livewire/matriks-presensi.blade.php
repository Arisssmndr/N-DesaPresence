<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Buku Matriks Presensi Bulanan</h1>
            <p class="text-xs text-slate-500 mt-1">Laporan rekapitulasi kehadiran perangkat desa tanggal 1 hingga {{ $daysInMonth }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('spj.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Cetak SPJ PDF Resmi</span>
            </a>
        </div>
    </div>

    <!-- Filters & Color Legend Bar -->
    <div class="sadi-card p-4 flex flex-col md:flex-row items-center justify-between gap-4 bg-white">
        <div class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Periode:</label>
            <select wire:model.live="bulan" class="px-3 py-2 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/40 focus:outline-none focus:ring-2 focus:ring-[#064E3B] text-slate-700 font-bold">
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}">{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>

            <select wire:model.live="tahun" class="px-3 py-2 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/40 focus:outline-none focus:ring-2 focus:ring-[#064E3B] text-slate-700 font-bold">
                @foreach (range(2024, 2030) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-[11px] font-bold text-slate-700">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-600"></span> H: Hadir</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-600"></span> A: Alpa</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-teal-600"></span> I: Izin/Sakit</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-600"></span> D: Dinas Luar</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-200 border border-slate-300"></span> L: Libur</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-50 border border-slate-300"></span> - : Belum Berjalan</span>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-center text-xs border-collapse">
                <thead>
                    <tr class="bg-[#064E3B] text-white font-bold">
                        <th class="py-3 px-3 text-left w-48 border-r border-emerald-800">Nama Perangkat</th>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                $isTodayCol = ($dStr === $todayStr);
                            @endphp
                            <th class="py-2 px-1 w-7 font-mono border-r border-emerald-800/40 text-[11px] {{ $isTodayCol ? 'bg-[#C9A84C] text-[#064E3B] font-black' : '' }}">
                                {{ $d }}
                            </th>
                        @endfor
                        <th class="py-2 px-1.5 bg-emerald-900 border-r border-emerald-800 text-emerald-200 font-bold" title="Total Hadir">H</th>
                        <th class="py-2 px-1.5 bg-emerald-900 border-r border-emerald-800 text-teal-300 font-bold" title="Total Izin/Sakit">I</th>
                        <th class="py-2 px-1.5 bg-emerald-900 border-r border-emerald-800 text-blue-300 font-bold" title="Total Dinas Luar">D</th>
                        <th class="py-2 px-1.5 bg-emerald-900 border-r border-emerald-800 text-red-300 font-bold" title="Total Alpa">A</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-bold font-mono">
                    @forelse ($pegawais as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-3 text-left font-sans font-bold text-slate-800 border-r border-slate-200">
                                <span class="text-slate-800">{{ $p->nama_lengkap }}</span>
                                <p class="text-[10px] text-slate-400 font-normal leading-tight">{{ $p->jabatan->nama_jabatan ?? '-' }}</p>
                            </td>
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $code = $matrix[$p->id][$d] ?? '-';
                                @endphp
                                <td class="py-1 px-0.5 border-r border-slate-100 text-[10px]">
                                    @if ($code === 'H')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-white bg-emerald-600 font-bold mx-auto shadow-2xs">H</span>
                                    @elseif ($code === 'T')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-white bg-amber-500 font-bold mx-auto shadow-2xs">T</span>
                                    @elseif ($code === 'I')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-white bg-teal-600 font-bold mx-auto shadow-2xs">I</span>
                                    @elseif ($code === 'D')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-white bg-blue-600 font-bold mx-auto shadow-2xs">D</span>
                                    @elseif ($code === 'L')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-slate-500 bg-slate-100 border border-slate-200/80 font-semibold mx-auto">L</span>
                                    @elseif ($code === 'A')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-white bg-red-600 font-bold mx-auto shadow-2xs">A</span>
                                    @else
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-slate-300 bg-slate-50 border border-slate-100 font-normal mx-auto">-</span>
                                    @endif
                                </td>
                            @endfor
                            <td class="py-2 px-1 bg-emerald-50/70 text-emerald-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['H'] ?? 0 }}</td>
                            <td class="py-2 px-1 bg-teal-50/70 text-teal-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['I'] ?? 0 }}</td>
                            <td class="py-2 px-1 bg-blue-50/70 text-blue-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['D'] ?? 0 }}</td>
                            <td class="py-2 px-1 bg-red-50/70 text-red-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['A'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 5 }}" class="py-8 text-center text-slate-400 italic">
                                Belum ada data perangkat desa aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
