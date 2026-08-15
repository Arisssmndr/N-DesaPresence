<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Kalkulasi Siltap & Potongan Kedisiplinan</h1>
            <p class="text-xs text-slate-500 mt-1">Perhitungan Penghasilan Tetap (Siltap) Neto berdasarkan kehadiran dan kalkulasi potongan otomatis</p>
        </div>

        <button wire:click="generateRekap" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#064E3B] to-[#1B4D3E] text-white font-bold text-xs tracking-wide shadow-lg hover:shadow-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>Kalkulasi Ulang Siltap</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="sadi-card p-4 flex items-center gap-3">
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

    <!-- Siltap Rekap Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-4 text-center">Rekap Absen</th>
                        <th class="py-3.5 px-4 text-right">Siltap Bruto</th>
                        <th class="py-3.5 px-4 text-right">Potongan Alpa</th>
                        <th class="py-3.5 px-4 text-right">Siltap Neto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($rekaps as $r)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $r->pegawai->nama_lengkap ?? '-' }}
                                <p class="text-[10px] text-slate-400 font-normal">{{ $r->pegawai->jabatan->nama_jabatan ?? '' }}</p>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">H: {{ $r->total_hadir }}</span>
                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-bold">A: {{ $r->total_alpa }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-700">
                                Rp {{ number_format($r->siltap_bruto, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-red-600">
                                - Rp {{ number_format($r->potongan_alpa, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-extrabold text-[#064E3B] text-sm bg-emerald-50/50">
                                Rp {{ number_format($r->siltap_neto, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 italic">
                                Belum ada rekap Siltap yang digenerate untuk bulan ini. Klik tombol "Kalkulasi Ulang Siltap" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
