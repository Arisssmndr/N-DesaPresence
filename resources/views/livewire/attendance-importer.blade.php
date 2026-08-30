<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-extrabold text-[#064E3B]">Log Absensi Digital</h1>
            <p class="text-xs text-slate-500 mt-1">Riwayat seluruh presensi dan bukti tanda tangan digital staf Desa Nangtang</p>
        </div>
        <a href="{{ route('staf.login') }}" target="_blank"
           class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-md transition">
            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span>Buka Portal Absensi</span>
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="sadi-card p-4.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <p class="text-2xl font-outfit font-extrabold text-[#064E3B]">{{ number_format($stats['total']) }}</p>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Total Presensi</p>
        </div>
        <div class="sadi-card p-4.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <p class="text-2xl font-outfit font-extrabold text-slate-800">{{ number_format($stats['web_signature']) }}</p>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Tanda Tangan Digital</p>
        </div>
        <div class="sadi-card p-4.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <p class="text-2xl font-outfit font-extrabold text-slate-800">{{ number_format($stats['manual_admin']) }}</p>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Penugasan / Override</p>
        </div>
        <div class="sadi-card p-4.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <p class="text-2xl font-outfit font-extrabold text-[#064E3B]">{{ number_format($stats['hari_ini']) }}</p>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Hari Ini</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Tanggal</label>
                <input type="date" wire:model.live="filterTanggal"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Pegawai</label>
                <input type="text" wire:model.live.debounce.400ms="filterPegawai" placeholder="Cari nama pegawai..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Sumber Data</label>
                <select wire:model.live="filterSumber"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                    <option value="">Semua Sumber Data</option>
                    <option value="web_signature">Web TTD Digital</option>
                    <option value="pengajuan_luar">Pengajuan Dinas Luar</option>
                    <option value="manual_admin">Penugasan / Override Admin</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="resetFilter" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Log Absensi --}}
    <div class="sadi-card overflow-hidden bg-white border border-slate-200/80 rounded-2xl shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#064E3B] text-white border-b border-[#064E3B]">
                    <tr>
                        <th class="px-4 py-3 text-left font-extrabold uppercase tracking-wider text-[11px]">Tanggal</th>
                        <th class="px-4 py-3 text-left font-extrabold uppercase tracking-wider text-[11px]">Pegawai</th>
                        <th class="px-4 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Masuk</th>
                        <th class="px-4 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Tanda Tangan Masuk</th>
                        <th class="px-4 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Pulang</th>
                        <th class="px-4 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Tanda Tangan Pulang</th>
                        <th class="px-4 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Status</th>
                        <th class="px-4 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Sumber</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kehadirans as $k)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-medium">{{ \Carbon\Carbon::parse($k->tanggal)->isoFormat('dddd') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-900 leading-tight">{{ $k->pegawai->nama_lengkap }}</p>
                            <p class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $k->pegawai->jabatan->nama_jabatan ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($k->jam_masuk)
                                <span class="font-mono text-xs font-bold text-slate-900">{{ substr($k->jam_masuk, 0, 5) }}</span>
                                @if($k->ip_absensi_masuk)
                                    <p class="text-[9px] text-slate-400 font-mono">{{ $k->ip_absensi_masuk }}</p>
                                @endif
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->tanda_tangan_masuk_src)
                                <div class="inline-flex flex-col items-center">
                                    <img src="{{ $k->tanda_tangan_masuk_src }}" alt="TTD Masuk"
                                        class="h-9 max-w-[85px] mx-auto rounded border border-slate-200 bg-white p-0.5 object-contain shadow-2xs cursor-pointer hover:scale-125 transition-transform"
                                        title="Tanda tangan masuk digital">
                                    @if(str_contains($k->keterangan ?? '', 'Lepas Piket'))
                                        <span class="text-[9px] text-slate-500 font-semibold mt-0.5">TTD Piket</span>
                                    @elseif($k->sumber_data === 'pengajuan_luar')
                                        <span class="text-[9px] text-slate-500 font-semibold mt-0.5">TTD Dinas Luar</span>
                                    @endif
                                </div>
                            @elseif(in_array($k->status, ['Izin', 'Sakit']))
                                <span class="text-slate-400 text-xs">—</span>
                            @elseif(in_array($k->status, ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar']))
                                <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block">
                                    Tanpa TTD
                                </span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($k->jam_pulang)
                                <span class="font-mono text-xs font-bold text-slate-900">{{ substr($k->jam_pulang, 0, 5) }}</span>
                                @if($k->ip_absensi_pulang)
                                    <p class="text-[9px] text-slate-400 font-mono">{{ $k->ip_absensi_pulang }}</p>
                                @endif
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($k->tanda_tangan_pulang_src)
                                <img src="{{ $k->tanda_tangan_pulang_src }}" alt="TTD Pulang"
                                    class="h-9 max-w-[85px] mx-auto rounded border border-slate-200 bg-white p-0.5 object-contain shadow-2xs cursor-pointer hover:scale-125 transition-transform"
                                    title="Tanda tangan pulang digital">
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @php
                                $statusStyle = match($k->status) {
                                    'Hadir', 'Tepat Waktu' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                                    'Terlambat'            => 'bg-amber-50 text-amber-800 border-amber-200/80',
                                    'Izin', 'Sakit'        => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Dinas Luar'           => 'bg-blue-50 text-blue-800 border-blue-200/80',
                                    'Alpa'                 => 'bg-rose-50 text-rose-800 border-rose-200/80',
                                    'Libur'                => 'bg-slate-50 text-slate-600 border-slate-200',
                                    default                => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="inline-block text-[11px] font-bold px-2.5 py-0.5 rounded-md border {{ $statusStyle }}">
                                {{ $k->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @php
                                $sumberText = match($k->sumber_data) {
                                    'web_signature' => 'Web TTD',
                                    'manual_admin'  => 'Admin Override',
                                    'pengajuan_luar'=> 'Pengajuan Luar',
                                    'fingerprint'   => 'Hardware PIN',
                                    'import_file'   => 'Import Data',
                                    default         => $k->sumber_data,
                                };
                            @endphp
                            <span class="inline-block text-[10.5px] font-semibold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200/80">
                                {{ $sumberText }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-slate-500 font-semibold text-xs">Belum ada data presensi yang cocok dengan filter pencarian</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kehadirans->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $kehadirans->links() }}
        </div>
        @endif
    </div>

</div>

