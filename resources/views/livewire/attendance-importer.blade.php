<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-extrabold text-[#064E3B]">Log Absensi Digital</h1>
            <p class="text-xs text-slate-500 mt-1">Riwayat seluruh absensi tanda tangan yang masuk via Portal Absensi Web</p>
        </div>
        <a href="{{ route('staf.login') }}" target="_blank"
           class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Buka Portal Absensi
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="sadi-card p-4 text-center">
            <p class="text-2xl font-outfit font-extrabold text-[#064E3B]">{{ number_format($stats['total']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Kehadiran</p>
        </div>
        <div class="sadi-card p-4 text-center">
            <p class="text-2xl font-outfit font-extrabold text-emerald-600">{{ number_format($stats['web_signature']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Via Tanda Tangan</p>
        </div>
        <div class="sadi-card p-4 text-center">
            <p class="text-2xl font-outfit font-extrabold text-blue-600">{{ number_format($stats['manual_admin']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Override Admin</p>
        </div>
        <div class="sadi-card p-4 text-center">
            <p class="text-2xl font-outfit font-extrabold text-[#C9A84C]">{{ number_format($stats['hari_ini']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Hari Ini</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="sadi-card p-5">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Tanggal</label>
                <input type="date" wire:model.live="filterTanggal"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Pegawai</label>
                <input type="text" wire:model.live.debounce.400ms="filterPegawai" placeholder="Cari nama..."
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Sumber Data</label>
                <select wire:model.live="filterSumber"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B]">
                    <option value="">Semua Sumber</option>
                    <option value="web_signature">Tanda Tangan Web</option>
                    <option value="manual_admin">Override Admin</option>
                    <option value="fingerprint">Fingerprint (Legacy)</option>
                    <option value="import_file">Import File (Legacy)</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="resetFilter" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Log Absensi --}}
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Masuk</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tanda Tangan Masuk</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Pulang</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tanda Tangan Pulang</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Sumber</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($kehadirans as $k)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($k->tanggal)->isoFormat('dddd') }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-semibold text-slate-800 text-sm">{{ $k->pegawai->nama_lengkap }}</p>
                            <p class="text-xs text-slate-400">{{ $k->pegawai->jabatan->nama_jabatan ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($k->jam_masuk)
                                <span class="font-mono text-sm font-bold text-emerald-700">{{ substr($k->jam_masuk, 0, 5) }}</span>
                                @if($k->ip_absensi_masuk)
                                    <p class="text-[9px] text-slate-400 font-mono">{{ $k->ip_absensi_masuk }}</p>
                                @endif
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($k->tanda_tangan_masuk_src)
                                <img src="{{ $k->tanda_tangan_masuk_src }}" alt="TTD Masuk"
                                    class="h-10 mx-auto rounded border border-slate-100 bg-white object-contain cursor-pointer hover:scale-150 transition-transform"
                                    title="Tanda tangan masuk — klik untuk perbesar">
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($k->jam_pulang)
                                <span class="font-mono text-sm font-bold text-blue-700">{{ substr($k->jam_pulang, 0, 5) }}</span>
                                @if($k->ip_absensi_pulang)
                                    <p class="text-[9px] text-slate-400 font-mono">{{ $k->ip_absensi_pulang }}</p>
                                @endif
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($k->tanda_tangan_pulang_src)
                                <img src="{{ $k->tanda_tangan_pulang_src }}" alt="TTD Pulang"
                                    class="h-10 mx-auto rounded border border-slate-100 bg-white object-contain cursor-pointer hover:scale-150 transition-transform"
                                    title="Tanda tangan pulang — klik untuk perbesar">
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $statusColor = match($k->status) {
                                    'Hadir', 'Tepat Waktu' => 'bg-emerald-100 text-emerald-700',
                                    'Terlambat'            => 'bg-amber-100 text-amber-700',
                                    'Izin', 'Sakit'        => 'bg-blue-100 text-blue-700',
                                    'Dinas Luar'           => 'bg-purple-100 text-purple-700',
                                    'Alpa'                 => 'bg-red-100 text-red-700',
                                    'Libur'                => 'bg-slate-100 text-slate-500',
                                    default                => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusColor }}">{{ $k->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $sumberLabel = match($k->sumber_data) {
                                    'web_signature' => ['Web TTD', 'text-emerald-700 bg-emerald-100/80'],
                                    'manual_admin'  => ['Admin Override', 'text-blue-700 bg-blue-100/80'],
                                    'fingerprint'   => ['Hardware PIN', 'text-slate-700 bg-slate-100'],
                                    'import_file'   => ['Import Data', 'text-slate-700 bg-slate-100'],
                                    default         => [$k->sumber_data, 'text-slate-600 bg-slate-100'],
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $sumberLabel[1] }}">{{ $sumberLabel[0] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <svg class="w-10 h-10 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-slate-400 text-sm">Belum ada data absensi yang cocok dengan filter</p>
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
