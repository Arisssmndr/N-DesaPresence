<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Buku Matriks Presensi Bulanan</h1>
            <p class="text-xs text-slate-500 mt-1">Laporan rekapitulasi kehadiran perangkat desa tanggal 1 hingga 31</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('spj.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Cetak SPJ PDF Resmi</span>
            </a>
        </div>
    </div>

    <!-- Filters & Color Legend Bar -->
    <div class="sadi-card p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <select wire:model.live="bulan" class="px-3 py-2 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-700 font-bold">
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}">{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>

            <select wire:model.live="tahun" class="px-3 py-2 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-700 font-bold">
                @foreach (range(2024, 2030) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-[11px] font-bold text-slate-700">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-600"></span> H: Hadir</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-600"></span> A: Alpa</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-purple-600"></span> I: Izin/Sakit</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-600"></span> D: Dinas Luar</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-300"></span> L: Libur</span>
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
                            <th class="py-2 px-1 w-7 font-mono border-r border-emerald-800/40 text-[11px]">{{ $d }}</th>
                        @endfor
                        <th class="py-2 px-1 bg-emerald-900 border-r border-emerald-800 text-emerald-200">H</th>
                        <th class="py-2 px-1 bg-emerald-900 border-r border-emerald-800 text-purple-300">I</th>
                        <th class="py-2 px-1 bg-emerald-900 border-r border-emerald-800 text-blue-300">D</th>
                        <th class="py-2 px-1 bg-emerald-900 border-r border-emerald-800 text-red-300">A</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-bold font-mono">
                    @foreach ($pegawais as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-3 text-left font-sans font-bold text-slate-800 border-r border-slate-200">
                                {{ $p->nama_lengkap }}
                                <p class="text-[10px] text-slate-400 font-normal">{{ $p->jabatan->nama_jabatan ?? '' }}</p>
                            </td>
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php $code = $matrix[$p->id][$d] ?? 'A'; @endphp
                                <td class="py-1 px-0.5 border-r border-slate-100 text-[10px]">
                                    <span class="w-6 h-6 rounded flex items-center justify-center text-white mx-auto
                                        @if ($code === 'H') bg-emerald-600
                                        @elseif ($code === 'T') bg-amber-500
                                        @elseif ($code === 'I') bg-purple-600
                                        @elseif ($code === 'D') bg-blue-600
                                        @elseif ($code === 'L') bg-slate-200 text-slate-600 font-normal
                                        @else bg-red-600 @endif">
                                        {{ $code }}
                                    </span>
                                </td>
                            @endfor
                            <td class="py-2 px-1 bg-emerald-50 text-emerald-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['H'] ?? 0 }}</td>
                            <td class="py-2 px-1 bg-purple-50 text-purple-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['I'] ?? 0 }}</td>
                            <td class="py-2 px-1 bg-blue-50 text-blue-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['D'] ?? 0 }}</td>
                            <td class="py-2 px-1 bg-red-50 text-red-800 border-r border-slate-200 font-extrabold">{{ $summary[$p->id]['A'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
