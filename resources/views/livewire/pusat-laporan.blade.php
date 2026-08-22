<div class="space-y-8">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-3xl font-extrabold text-[#064E3B] tracking-tight">Pusat Laporan & Rekapitulasi</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Cetak & unduh dokumen resmi presensi standar Pemerintah Desa</p>
        </div>
        <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white border border-[#C9A84C]/40 rounded-2xl shadow-sm">
            <svg class="w-5 h-5 text-[#064E3B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span class="text-xs font-bold text-[#064E3B]">Format Standar Permendagri</span>
        </div>
    </div>

    {{-- ===== GRID LAPORAN ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── CARD 1: LAPORAN HARIAN ── --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 hover:shadow-md transition-all duration-200 border border-[#C9A84C]/30 bg-white">
            <div class="space-y-5">
                {{-- Header Card --}}
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow shrink-0" style="background-color: #064E3B; border: 1.5px solid #C9A84C;">
                        <svg class="w-6 h-6 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-outfit text-lg font-bold text-slate-900">Laporan Presensi Harian</h2>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Rekap kehadiran seluruh perangkat desa per hari lengkap dengan bukti tanda tangan sah. Format A4 Portrait.</p>
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            <span class="text-[10px] px-2.5 py-0.5 bg-emerald-50 text-[#064E3B] border border-emerald-200 rounded-md font-bold">A4 Portrait</span>
                            <span class="text-[10px] px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md font-semibold">Kop Surat Resmi</span>
                            <span class="text-[10px] px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md font-semibold">Kolom Tanda Tangan</span>
                        </div>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="space-y-2 bg-[#F9F8F5] rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Tanggal Presensi</label>
                    <input type="date" wire:model.live="tanggalHarian"
                        class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] outline-none transition text-slate-900 font-semibold cursor-pointer"
                        max="{{ date('Y-m-d') }}">
                </div>
            </div>

            {{-- Button --}}
            <a href="{{ $urlHarian }}" target="_blank"
                style="background-color: #064E3B; color: #FFFFFF; border: 1px solid #C9A84C;"
                class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl font-outfit text-sm font-bold shadow hover:bg-[#04392B] transition cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Cetak / Unduh PDF Harian</span>
            </a>
        </div>

        {{-- ── CARD 2: LAPORAN BULANAN ── --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 hover:shadow-md transition-all duration-200 border border-[#C9A84C]/30 bg-white">
            <div class="space-y-5">
                {{-- Header Card --}}
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow shrink-0" style="background-color: #064E3B; border: 1.5px solid #C9A84C;">
                        <svg class="w-6 h-6 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-outfit text-lg font-bold text-slate-900">Laporan Rekap Bulanan</h2>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Matriks presensi tanggal 1–31 + rekapitulasi kehadiran per bulan. Format A4 Landscape.</p>
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            <span class="text-[10px] px-2.5 py-0.5 bg-emerald-50 text-[#064E3B] border border-emerald-200 rounded-md font-bold">A4 Landscape</span>
                            <span class="text-[10px] px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md font-semibold">Matriks Tanggal</span>
                            <span class="text-[10px] px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md font-semibold">% Kehadiran</span>
                        </div>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="space-y-2 bg-[#F9F8F5] rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Periode Bulan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[11px] text-slate-600 font-semibold mb-1 block">Bulan</label>
                            <select wire:model.live="bulanBulanan" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] outline-none transition text-slate-900 font-semibold cursor-pointer">
                                @foreach ($listBulan as $num => $nama)
                                    <option value="{{ $num }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] text-slate-600 font-semibold mb-1 block">Tahun</label>
                            <select wire:model.live="tahunBulanan" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] outline-none transition text-slate-900 font-semibold cursor-pointer">
                                @foreach ($tahunOptions as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Button --}}
            <a href="{{ $urlBulanan }}" target="_blank"
                style="background-color: #064E3B; color: #FFFFFF; border: 1px solid #C9A84C;"
                class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl font-outfit text-sm font-bold shadow hover:bg-[#04392B] transition cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Cetak / Unduh PDF Bulanan</span>
            </a>
        </div>

        {{-- ── CARD 3: LAPORAN TAHUNAN ── --}}
        <div class="sadi-card p-6 flex flex-col justify-between gap-5 hover:shadow-md transition-all duration-200 border border-[#C9A84C]/30 bg-white">
            <div class="space-y-5">
                {{-- Header Card --}}
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow shrink-0" style="background-color: #064E3B; border: 1.5px solid #C9A84C;">
                        <svg class="w-6 h-6 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-outfit text-lg font-bold text-slate-900">Laporan Rekap Tahunan</h2>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Rekapitulasi 12 bulan dalam 1 tahun anggaran per perangkat desa. Format A4 Landscape.</p>
                        <div class="flex flex-wrap gap-1.5 mt-2.5">
                            <span class="text-[10px] px-2.5 py-0.5 bg-emerald-50 text-[#064E3B] border border-emerald-200 rounded-md font-bold">A4 Landscape</span>
                            <span class="text-[10px] px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md font-semibold">12 Bulan Anggaran</span>
                            <span class="text-[10px] px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md font-semibold">% Kehadiran</span>
                        </div>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="space-y-2 bg-[#F9F8F5] rounded-xl p-4 border border-slate-200">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Tahun Anggaran</label>
                    <select wire:model.live="tahunTahunan" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-300 bg-white focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B] outline-none transition text-slate-900 font-semibold cursor-pointer">
                        @foreach ($tahunOptions as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Button --}}
            <a href="{{ $urlTahunan }}" target="_blank"
                style="background-color: #064E3B; color: #FFFFFF; border: 1px solid #C9A84C;"
                class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl font-outfit text-sm font-bold shadow hover:bg-[#04392B] transition cursor-pointer">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Cetak / Unduh PDF Tahunan</span>
            </a>
        </div>
    </div>

    {{-- ===== INFO STANDAR ===== --}}
    <div class="sadi-card p-5 bg-white border border-[#C9A84C]/30 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-outfit font-bold text-[#064E3B]">Keterangan Standar Laporan & Dokumen Kedinasan</h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Semua berkas laporan resmi dicetak menggunakan kop surat baku <strong>Pemerintah Desa Nangtang</strong> lengkap dengan penomoran dokumen kedinasan, kolom tanda tangan Kepala Desa, dan paraf Sekretaris Desa.
                </p>
                <div class="flex flex-wrap gap-2 mt-3 text-[11px] font-semibold text-slate-700">
                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded">H = Hadir Tepat Waktu</span>
                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded">T = Terlambat</span>
                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded">A = Alpa / Tanpa Keterangan</span>
                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded">I = Izin / Sakit</span>
                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded">D = Dinas Luar (SPT)</span>
                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded">L = Libur / Akhir Pekan</span>
                </div>
            </div>
        </div>
    </div>

</div>
