@extends('staf.layout', ['title' => 'Beranda Presensi — ' . $pegawai->nama_lengkap])

@section('content')
<div class="space-y-4 pb-6">

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 1. PROFILE HEADER CARD (SELARAS, ELEGAN & MEWAH)                       -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-4 sm:p-5 text-white rounded-3xl shadow-lg border border-[#C9A84C]/40 relative overflow-hidden" style="background: linear-gradient(135deg, #064E3B 0%, #04392B 100%) !important;">
        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[#C9A84C]/15 rounded-full blur-xl pointer-events-none"></div>

        <div class="flex items-center gap-3.5 relative z-10">
            @if($user->foto_profil || ($pegawai && $pegawai->foto_profil))
                <img src="{{ asset('storage/' . ($user->foto_profil ?? $pegawai->foto_profil)) }}" alt="{{ $pegawai->nama_lengkap }}"
                    class="w-14 h-14 rounded-2xl object-contain shrink-0"
                    style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
            @else
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center overflow-hidden shrink-0"
                     style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                    <svg class="w-8 h-8 text-emerald-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider shadow-2xs">
                    Perangkat Desa Nangtang
                </span>
                <h2 class="font-outfit font-extrabold text-base sm:text-lg text-white truncate mt-1 leading-tight">{{ $pegawai->nama_lengkap }}</h2>
                <p class="text-xs text-emerald-200 truncate font-medium mt-0.5">{{ $pegawai->jabatan->nama_jabatan ?? 'Staf Perangkat Desa' }}</p>
            </div>
        </div>

        <div class="mt-3.5 pt-2.5 border-t border-emerald-800/80 grid grid-cols-2 gap-2 text-xs relative z-10">
            <div>
                <span class="text-emerald-300/80 text-[10px] uppercase font-bold tracking-wider">NIPD / SK</span>
                <p class="font-mono text-emerald-100 font-bold text-xs mt-0.5">{{ $pegawai->nipd ?? '—' }}</p>
            </div>
            <div>
                <span class="text-emerald-300/80 text-[10px] uppercase font-bold tracking-wider">Shift Kedinasan</span>
                <p class="text-emerald-100 font-bold text-xs mt-0.5 truncate">{{ $pegawai->shiftKerja->nama_shift ?? 'Reguler Kantor Desa' }}</p>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2. MASTER HERO: PRESENSI LANGSUNG UTAMA                                -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-4 sm:p-5 bg-white border border-slate-200 rounded-3xl shadow-sm space-y-3.5">
        
        <!-- Header Banner & Status WiFi -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-[#C9A84C]"></div>
                    <span class="text-[10px] font-extrabold font-outfit text-[#064E3B] uppercase tracking-wider">Presensi Kantor Desa</span>
                </div>
                <h3 class="font-outfit font-extrabold text-sm sm:text-base text-slate-900 mt-0.5">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </h3>
            </div>

            <!-- WiFi Pill Indicator -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 self-start sm:self-auto {{ $isWifiValid ? 'bg-emerald-50 text-[#064E3B] border border-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-300' }}">
                @if($isWifiValid)
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>WiFi Desa Terverifikasi</span>
                @else
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <span>Luar WiFi Desa</span>
                @endif
                <span class="text-[10px] font-mono text-slate-500 pl-1 border-l border-slate-300">{{ $clientIp }}</span>
            </div>
        </div>

        <!-- 2 Grid Status Kehadiran Hari Ini (Masuk & Pulang) -->
        <div class="grid grid-cols-2 gap-2.5">
            <!-- Box Jam Masuk -->
            <div class="p-3.5 rounded-2xl {{ $kehadiranHariIni?->jam_masuk ? 'bg-emerald-50/90 border-2 border-emerald-300' : 'bg-slate-50 border border-slate-200' }} text-center transition">
                <div class="flex items-center justify-center gap-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    <svg class="w-3.5 h-3.5 {{ $kehadiranHariIni?->jam_masuk ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Absen Masuk</span>
                </div>
                <p class="font-mono text-base sm:text-lg font-extrabold {{ $kehadiranHariIni?->jam_masuk ? 'text-[#064E3B]' : 'text-slate-400' }}">
                    {{ $kehadiranHariIni?->jam_masuk ? substr($kehadiranHariIni->jam_masuk, 0, 5) . ' WIB' : '— : —' }}
                </p>
                <div class="mt-1">
                    @if($kehadiranHariIni?->jam_masuk)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-200/80 text-emerald-900 text-[9.5px] font-extrabold">
                            <svg class="w-3 h-3 text-emerald-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Tercatat Sah</span>
                        </span>
                    @else
                        <span class="text-[9.5px] text-slate-500 font-medium">Jadwal: {{ $jamMasukMulai }}–{{ $jamMasukSelesai }}</span>
                    @endif
                </div>
            </div>

            <!-- Box Jam Pulang -->
            <div class="p-3.5 rounded-2xl {{ $kehadiranHariIni?->jam_pulang ? 'bg-blue-50/90 border-2 border-blue-300' : 'bg-slate-50 border border-slate-200' }} text-center transition">
                <div class="flex items-center justify-center gap-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    <svg class="w-3.5 h-3.5 {{ $kehadiranHariIni?->jam_pulang ? 'text-blue-700' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Absen Pulang</span>
                </div>
                <p class="font-mono text-base sm:text-lg font-extrabold {{ $kehadiranHariIni?->jam_pulang ? 'text-blue-800' : 'text-slate-400' }}">
                    {{ $kehadiranHariIni?->jam_pulang ? substr($kehadiranHariIni->jam_pulang, 0, 5) . ' WIB' : '— : —' }}
                </p>
                <div class="mt-1">
                    @if($kehadiranHariIni?->jam_pulang)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-200/80 text-blue-900 text-[9.5px] font-extrabold">
                            <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Tercatat Sah</span>
                        </span>
                    @else
                        <span class="text-[9.5px] text-slate-500 font-medium">Jadwal: {{ $jamPulangMulai }}–{{ $jamPulangSelesai }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Master Button Action (Hijau Aktif / Merah Terkunci) -->
        <div>
            {{-- KONDISI 1: Sedang Izin / Sakit Aktif Hari Ini --}}
            @if(isset($izinAktifHariIni) && $izinAktifHariIni)
                <div class="p-4 rounded-2xl bg-teal-50 border-2 border-teal-300 text-center space-y-1.5 shadow-2xs">
                    <div class="flex items-center justify-center gap-1.5 text-xs font-extrabold text-teal-900">
                        <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>STATUS HARI INI: {{ strtoupper(str_replace('_', ' ', $izinAktifHariIni->jenis)) }} ({{ strtoupper($izinAktifHariIni->status) }})</span>
                    </div>
                    <p class="text-[11px] text-teal-800 font-medium">
                        Periode: <strong class="font-mono">{{ $izinAktifHariIni->tanggal_mulai->format('d/m/Y') }}</strong> s/d <strong class="font-mono">{{ $izinAktifHariIni->tanggal_selesai->format('d/m/Y') }}</strong> ({{ $izinAktifHariIni->jumlah_hari }} Hari)
                    </p>
                    <p class="text-[10.5px] text-teal-700 italic">"{{ $izinAktifHariIni->keterangan }}"</p>
                </div>

            {{-- KONDISI 1B: Dinas Luar / Izin Resmi Tercatat di Kehadiran --}}
            @elseif($kehadiranHariIni && in_array(strtolower($kehadiranHariIni->status), ['dinas luar', 'izin', 'sakit']) && !$kehadiranHariIni->jam_masuk)
                <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 text-center space-y-1 shadow-2xs">
                    <div class="flex items-center justify-center gap-1.5 text-xs font-extrabold text-indigo-900">
                        <svg class="w-4 h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Status Hari Ini: {{ strtoupper($kehadiranHariIni->status) }}</span>
                    </div>
                    <p class="text-[11px] text-indigo-800 font-medium">
                        {{ $kehadiranHariIni->keterangan ?? 'Anda tercatat sedang melaksanakan dinas luar / izin resmi.' }}
                    </p>
                </div>

            {{-- KONDISI 2: Sudah Selesai Masuk & Pulang --}}
            @elseif($kehadiranHariIni && $kehadiranHariIni->jam_masuk && $kehadiranHariIni->jam_pulang)
                <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-800 to-[#064E3B] text-white text-center shadow-md border-2 border-[#C9A84C]/60 space-y-1">
                    <div class="w-8 h-8 rounded-full bg-[#C9A84C] text-[#064E3B] flex items-center justify-center mx-auto shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h4 class="font-outfit font-extrabold text-sm text-white">Presensi Hari Ini Lengkap & Sah</h4>
                    <p class="text-[11px] text-emerald-200 font-medium">
                        Terima kasih atas dedikasi dan pelayanan Anda untuk masyarakat Desa Nangtang hari ini.
                    </p>
                </div>

            {{-- KONDISI 3: BISA ABSEN MASUK SEKARANG (TOMBOL HIJAU AKTIF) --}}
            @elseif((!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk) && $bisaAbsenMasuk)
                <a href="{{ route('staf.absen.form', 'masuk') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-[#064E3B] to-emerald-700 text-white text-center text-xs sm:text-sm font-extrabold shadow-md hover:scale-[1.01] active:scale-98 transition duration-200 flex flex-col items-center justify-center gap-0.5 border border-[#C9A84C]">
                    <div class="flex items-center gap-1.5 text-white">
                        <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>BUKA LEMBAR ABSEN MASUK</span>
                    </div>
                    <span class="text-[10.5px] text-emerald-200 font-semibold">WiFi Desa Terverifikasi & Jadwal Masuk Aktif</span>
                </a>

            {{-- KONDISI 4: BISA ABSEN PULANG SEKARANG (TOMBOL HIJAU/BIRU AKTIF) --}}
            @elseif($kehadiranHariIni && $kehadiranHariIni->jam_masuk && !$kehadiranHariIni->jam_pulang && $bisaAbsenPulang)
                <a href="{{ route('staf.absen.form', 'pulang') }}"
                   class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-700 via-teal-800 to-[#064E3B] text-white text-center text-xs sm:text-sm font-extrabold shadow-md hover:scale-[1.01] active:scale-98 transition duration-200 flex flex-col items-center justify-center gap-0.5 border border-[#C9A84C]">
                    <div class="flex items-center gap-1.5 text-white">
                        <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>BUKA LEMBAR ABSEN PULANG</span>
                    </div>
                    <span class="text-[10.5px] text-emerald-200 font-semibold">WiFi Desa Terverifikasi & Jadwal Pulang Aktif</span>
                </a>

            {{-- KONDISI 5: ABSEN TERKUNCI (MERAH DISABLE) --}}
            @else
                <div class="space-y-2.5">
                    <button type="button" disabled
                            class="w-full py-3 px-4 rounded-2xl bg-rose-50 text-rose-900 border border-rose-300 text-center text-xs sm:text-sm font-extrabold flex flex-col items-center justify-center gap-0.5 cursor-not-allowed select-none shadow-2xs transition">
                        <div class="flex items-center gap-1.5 text-rose-700">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>
                                @if(!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk)
                                    ABSEN MASUK TERKUNCI
                                @else
                                    ABSEN PULANG TERKUNCI
                                @endif
                            </span>
                        </div>
                        <span class="text-[10.5px] text-rose-700 font-bold mt-0.5 flex items-center justify-center gap-1">
                            @if(!$isWifiValid)
                                <span>Wajib Terhubung ke WiFi Resmi Kantor Desa Nangtang</span>
                            @elseif(!$isWaktuMasuk && (!$kehadiranHariIni || !$kehadiranHariIni->jam_masuk))
                                <span>Belum / Lewat Jadwal Absen Masuk ({{ $jamMasukMulai }}–{{ $jamMasukSelesai }} WIB)</span>
                            @elseif(!$isWaktuPulang && ($kehadiranHariIni && $kehadiranHariIni->jam_masuk && !$kehadiranHariIni->jam_pulang))
                                <span>Belum Jadwal Absen Pulang ({{ $jamPulangMulai }}–{{ $jamPulangSelesai }} WIB)</span>
                            @else
                                <span>Absensi Langsung Belum Tersedia</span>
                            @endif
                        </span>
                    </button>

                    @if(!$isWifiValid)
                        <div class="p-2.5 bg-amber-50 border border-amber-300 rounded-xl text-center flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-amber-900">
                            <span class="flex items-center gap-1.5 text-[11px] font-medium">
                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Hubungkan ke WiFi Kantor Desa, lalu tekan tombol cek:</span>
                            </span>
                            <button type="button" onclick="window.location.reload()"
                                    class="px-3 py-1 rounded-lg bg-white border border-amber-400 text-amber-900 text-xs font-extrabold hover:bg-amber-100 transition cursor-pointer shrink-0 shadow-2xs flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Cek Ulang</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2.5. NOTIFIKASI SURAT PERINTAH TUGAS (SPT) MENUNGGU KONFIRMASI STAF    -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if(isset($sptMenunggu) && $sptMenunggu->count() > 0)
    <div class="space-y-3" x-data="{ activeSptTerimaModal: null, activeSptTolakModal: null }">
        @foreach($sptMenunggu as $spt)
            <div class="sadi-card p-4 sm:p-5 bg-gradient-to-br from-amber-50 via-white to-amber-50/50 border-2 border-amber-300 shadow-md rounded-3xl space-y-3.5 relative overflow-hidden">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-extrabold shadow-sm shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold bg-amber-500 text-white uppercase tracking-wider animate-pulse">
                                    PENUGASAN KEDINASAN BARU
                                </span>
                                @if($spt->nomor_spt)
                                    <span class="text-[10.5px] font-mono font-bold text-slate-500">{{ $spt->nomor_spt }}</span>
                                @endif
                            </div>
                            <h4 class="font-outfit font-extrabold text-sm sm:text-base text-slate-900 mt-1">
                                {{ $spt->tujuan }}
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Detail Box -->
                <div class="p-3.5 bg-white/90 rounded-2xl border border-amber-200/80 text-xs space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Periode Tugas:</span>
                            <p class="font-mono font-bold text-emerald-800 mt-0.5">
                                {{ $spt->tanggal_mulai->format('d/m/Y') }} s/d {{ $spt->tanggal_selesai->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Diterbitkan Oleh:</span>
                            <p class="font-bold text-slate-800 mt-0.5">
                                {{ $spt->pembuat->name ?? 'Pemerintah Desa Nangtang' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Keperluan / Agenda:</span>
                        <p class="text-[11.5px] text-slate-700 font-medium mt-0.5 leading-relaxed">
                            {{ $spt->keperluan }}
                        </p>
                    </div>

                    @if($spt->file_undangan)
                        <div class="pt-1.5 flex items-center justify-between">
                            <span class="text-[10.5px] text-slate-500">Lampiran / Surat Undangan:</span>
                            <a href="{{ asset('storage/' . $spt->file_undangan) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[11px] font-extrabold text-blue-600 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Berkas Lampiran</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons: TERIMA / TOLAK -->
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" @click="activeSptTolakModal = {{ $spt->id }}"
                            class="px-3.5 py-2 rounded-xl bg-white border border-rose-300 text-rose-700 font-extrabold text-xs hover:bg-rose-50 transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak Tugas</span>
                    </button>

                    <button type="button" @click="activeSptTerimaModal = {{ $spt->id }}; setTimeout(() => { initSptPad({{ $spt->id }}) }, 100);"
                            class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-extrabold text-xs hover:bg-[#04392B] transition cursor-pointer flex items-center gap-1.5 shadow-md">
                        <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Terima & Tanda Tangani</span>
                    </button>
                </div>

                <!-- MODAL TANDA TANGAN PENERIMAAN SPT -->
                <div x-show="activeSptTerimaModal === {{ $spt->id }}"
                     x-transition.opacity
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                     style="display: none;"
                     @keydown.escape.window="activeSptTerimaModal = null">
                    <div @click.away="activeSptTerimaModal = null"
                         class="bg-white text-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 my-6 flex flex-col max-h-[92vh]">
                        
                        <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                            <div>
                                <h3 class="font-outfit text-sm font-bold text-white">Konfirmasi Penerimaan SPT</h3>
                                <p class="text-[10px] text-emerald-200 font-mono">{{ $spt->nomor_spt }}</p>
                            </div>
                            <button type="button" @click="activeSptTerimaModal = null" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form id="form-spt-terima-{{ $spt->id }}" action="{{ route('staf.spt.terima', $spt->id) }}" method="POST" onsubmit="return submitSptForm(event, {{ $spt->id }})" class="p-4 space-y-3 text-xs">
                            @csrf
                            <input type="hidden" name="tanda_tangan" id="input-ttd-spt-{{ $spt->id }}">

                            <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200 space-y-1">
                                <p class="font-bold text-emerald-950">{{ $spt->tujuan }}</p>
                                <p class="text-[11px] text-emerald-800">
                                    Periode: <strong class="font-mono">{{ $spt->tanggal_mulai->format('d/m/Y') }} — {{ $spt->tanggal_selesai->format('d/m/Y') }}</strong>
                                </p>
                                <p class="text-[10.5px] text-emerald-700 italic">
                                    * Dengan menandatangani SPT ini, status kehadiran Anda pada rentang tanggal tersebut akan otomatis tercatat sebagai Hadir (Dinas Luar SPT).
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Tanda Tangan Digital Anda <span class="text-rose-500">*</span></label>
                                    <button type="button" onclick="clearSptPad({{ $spt->id }})" class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer">Hapus / Ulangi</button>
                                </div>
                                <div id="wrapper-spt-{{ $spt->id }}" class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 overflow-hidden relative">
                                    <canvas id="canvas-spt-{{ $spt->id }}" class="w-full h-36 cursor-crosshair touch-none"></canvas>
                                </div>
                            </div>

                            <div class="pt-1 flex justify-end gap-2">
                                <button type="button" @click="activeSptTerimaModal = null" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold hover:bg-[#04392B] transition cursor-pointer flex items-center gap-1.5 shadow-md">
                                    <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Konfirmasi & Terima</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL ALASAN PENOLAKAN SPT -->
                <div x-show="activeSptTolakModal === {{ $spt->id }}"
                     x-transition.opacity
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                     style="display: none;"
                     @keydown.escape.window="activeSptTolakModal = null">
                    <div @click.away="activeSptTolakModal = null"
                         class="bg-white text-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 my-6 flex flex-col max-h-[92vh]">
                        
                        <div class="px-5 py-3.5 bg-rose-700 text-white flex items-center justify-between border-b border-rose-800 shrink-0">
                            <div>
                                <h3 class="font-outfit text-sm font-bold text-white">Tolak Penugasan SPT</h3>
                                <p class="text-[10px] text-rose-200 font-mono">{{ $spt->nomor_spt }}</p>
                            </div>
                            <button type="button" @click="activeSptTolakModal = null" class="p-1 rounded-lg hover:bg-rose-800 text-rose-200 hover:text-white transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('staf.spt.tolak', $spt->id) }}" method="POST" class="p-4 space-y-3 text-xs">
                            @csrf

                            <div class="p-3 bg-rose-50 rounded-2xl border border-rose-200 text-rose-900 space-y-1">
                                <p class="font-bold">{{ $spt->tujuan }}</p>
                                <p class="text-[11px] text-rose-700 leading-relaxed">
                                    Mohon berikan alasan penolakan yang jelas (contoh: berbeda tupoksi jabatan, bentrok agenda desa lain, dsb.) untuk diketahui oleh Kepala Desa / Admin.
                                </p>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Alasan Penolakan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="alasan_tolak" rows="3" required minlength="5" placeholder="Tuliskan alasan penolakan penugasan ini..."
                                          class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                            </div>

                            <div class="pt-1 flex justify-end gap-2">
                                <button type="button" @click="activeSptTolakModal = null" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 transition cursor-pointer flex items-center gap-1.5 shadow-md">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Kirim Penolakan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2.6. BANNER STATUS HARI INI: DINAS LUAR SPT RESMI                      -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if(isset($sptAktifHariIni) && $sptAktifHariIni)
    <div class="sadi-card p-4 bg-gradient-to-r from-teal-900 via-[#064E3B] to-emerald-900 text-white border border-[#E2C268] shadow-sm rounded-3xl flex items-start gap-3.5 relative overflow-hidden">
        <div class="w-10 h-10 rounded-2xl bg-[#E2C268] text-[#064E3B] flex items-center justify-center font-bold shadow-md shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                Status Hari Ini: Dinas Luar SPT Resmi
            </span>
            <h4 class="font-outfit font-extrabold text-sm text-white mt-1">
                {{ $sptAktifHariIni->tujuan }}
            </h4>
            <p class="text-[11px] text-emerald-200 mt-0.5 leading-relaxed">
                Nomor: <strong class="font-mono text-white">{{ $sptAktifHariIni->nomor_spt }}</strong> | Periode: <strong class="font-mono text-white">{{ $sptAktifHariIni->tanggal_mulai->format('d/m/Y') }} s/d {{ $sptAktifHariIni->tanggal_selesai->format('d/m/Y') }}</strong>
            </p>
            <p class="text-[10.5px] text-emerald-300 mt-1 italic">
                Kehadiran Anda hari ini telah <strong>otomatis tercatat Hadir</strong> berdasarkan surat perintah tugas yang telah Anda terima.
            </p>
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 3. BANNER STATUS HARI INI: LEPAS PIKET                                 -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if(isset($isLepasPiketHariIni) && $isLepasPiketHariIni)
    <div class="sadi-card p-4 bg-gradient-to-r from-emerald-900 via-[#064E3B] to-emerald-800 text-white border border-[#E2C268] shadow-sm rounded-3xl flex items-start gap-3.5 relative overflow-hidden">
        <div class="w-10 h-10 rounded-2xl bg-[#E2C268] text-[#064E3B] flex items-center justify-center font-bold shadow-md shrink-0">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                Status Hari Ini: Lepas Piket
            </span>
            <h4 class="font-outfit font-extrabold text-sm text-white mt-1">
                Istirahat Lepas Tugas Piket Malam
            </h4>
            <p class="text-[11px] text-emerald-200 mt-0.5 leading-relaxed">
                Kehadiran Anda hari ini telah <strong>otomatis dicatat sebagai Hadir / Lepas Piket</strong>.
            </p>
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 4. NOTIFIKASI JADWAL PIKET DESA (H-1, HARI INI, & LEPAS PIKET)        -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if(isset($notifPikets) && $notifPikets->count() > 0)
    <div class="space-y-3" x-data="{ activePiketModal: null, activePiketType: 'masuk' }">
        @foreach($notifPikets as $piket)
            @php
                $isToday = $piket->tanggal_piket->isToday();
                $isTomorrow = $piket->tanggal_piket->isTomorrow();
                $isYesterday = $piket->tanggal_piket->isYesterday();
                
                $isSudahMasuk = $piket->isSudahMasuk();
                $isSudahPulang = $piket->isSudahPulang();
                $isSelesaiLengkap = $piket->isSelesaiLengkap();
                
                $isWaktuMasukTiba = $piket->isWaktuMasukTiba();
                $isWaktuPulangTiba = $piket->isWaktuPulangTiba();
                
                $isBisaMasuk = $piket->isBisaAbsenMasuk();
                $isBisaPulang = $piket->isBisaAbsenPulang();
                
                $jamMulaiFormat = substr($piket->jam_mulai, 0, 5);
                $jamSelesaiFormat = substr($piket->jam_selesai, 0, 5);
            @endphp
            <div class="sadi-card p-4 {{ $isToday || ($isYesterday && !$isSudahPulang) ? 'bg-gradient-to-br from-[#064E3B] to-[#04392B] text-white border border-[#C9A84C]' : 'bg-slate-900 text-white border border-slate-700' }} shadow-md relative overflow-hidden rounded-3xl">
                <div class="flex items-start gap-3.5 relative z-10">
                    <div class="w-10 h-10 rounded-2xl {{ $isSelesaiLengkap ? 'bg-emerald-500 text-white' : ($isBisaMasuk || $isBisaPulang ? 'bg-[#C9A84C] text-[#064E3B] animate-bounce' : ($isSudahMasuk ? 'bg-blue-600 text-white' : 'bg-slate-800 text-amber-400')) }} flex items-center justify-center shrink-0 shadow-md">
                        @if($isSelesaiLengkap)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @elseif($isSudahMasuk && !$isSudahPulang)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $isToday ? 'bg-[#E2C268] text-[#064E3B]' : ($isYesterday ? 'bg-purple-900 text-purple-200 border border-purple-700' : 'bg-blue-900 text-blue-200 border border-blue-700') }}">
                                {{ $isToday ? 'JADWAL PIKET HARI INI' : ($isYesterday ? 'PIKET KEMARIN' : ($isTomorrow ? 'JADWAL PIKET BESOK (H-1)' : 'JADWAL PIKET DESA')) }}
                            </span>
                            
                            @if($isSelesaiLengkap)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                                    ✓ Hadir Lengkap (Masuk & Pulang)
                                </span>
                            @elseif($isSudahMasuk && !$isWaktuPulangTiba)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500 text-white shadow-xs">
                                    Sedang Bertugas Piket
                                </span>
                            @elseif($isBisaPulang)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-slate-900 animate-pulse">
                                    ⚠️ Wajib Absen Pulang
                                </span>
                            @elseif($isBisaMasuk)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-slate-900 animate-pulse">
                                    ⚠️ Wajib Absen Masuk
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">
                                    Terjadwal
                                </span>
                            @endif
                        </div>

                        <h4 class="font-outfit font-extrabold text-sm text-white mt-1 leading-snug">
                            {{ $piket->keterangan }}
                        </h4>
                        
                        <p class="text-xs text-emerald-100 font-semibold mt-0.5">
                            Waktu Piket: <span class="text-[#E2C268] font-mono font-bold">{{ \Carbon\Carbon::parse($piket->tanggal_piket)->isoFormat('dddd, D MMMM Y') }} ({{ $jamMulaiFormat }} - {{ $jamSelesaiFormat }} WIB)</span>
                        </p>

                        <!-- Log Absensi Real-Time Info -->
                        <div class="mt-2 grid grid-cols-2 gap-2 text-[11px] bg-black/20 p-2 rounded-xl border border-white/10">
                            <div class="space-y-0.5">
                                <span class="text-emerald-300 block text-[10px] uppercase font-bold">1. Absen Masuk:</span>
                                @if($isSudahMasuk)
                                    <span class="text-emerald-200 font-mono font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        {{ $piket->waktu_absen?->format('H:i') }} WIB
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum absen</span>
                                @endif
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-emerald-300 block text-[10px] uppercase font-bold">2. Absen Pulang:</span>
                                @if($isSudahPulang)
                                    <span class="text-emerald-200 font-mono font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        {{ $piket->waktu_pulang?->format('H:i') }} WIB
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum absen</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-2.5 pt-2 border-t border-emerald-800/80 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="text-[10px] text-emerald-300">
                                <span>Kompensasi:</span>
                                <strong class="text-white block font-sans">Otomatis Hadir (Lepas Piket)</strong>
                            </div>

                            <!-- Interactive Action Buttons with Precise Time Gates -->
                            <div class="flex items-center gap-2">
                                @if(!$isSudahMasuk)
                                    @if($isBisaMasuk)
                                        <button type="button" @click="activePiketModal = {{ $piket->id }}; activePiketType = 'masuk'; setTimeout(() => { initPiketPad({{ $piket->id }}) }, 100);"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#C9A84C] text-[#064E3B] font-outfit text-xs font-extrabold shadow hover:bg-[#E2C268] transition cursor-pointer active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                            <span>Tanda Tangan Absen Masuk</span>
                                        </button>
                                    @else
                                        <button type="button" disabled
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800/80 text-slate-400 border border-slate-700 text-[11px] font-semibold cursor-not-allowed"
                                                title="Absen masuk hanya dapat diisi saat jam mulai piket telah tiba (pukul {{ $jamMulaiFormat }} WIB)">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Masuk Buka Pukul {{ $jamMulaiFormat }} WIB</span>
                                        </button>
                                    @endif
                                @elseif(!$isSudahPulang)
                                    @if($isBisaPulang)
                                        <button type="button" @click="activePiketModal = {{ $piket->id }}; activePiketType = 'pulang'; setTimeout(() => { initPiketPad({{ $piket->id }}) }, 100);"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-outfit text-xs font-extrabold shadow hover:scale-105 transition cursor-pointer active:scale-95 animate-pulse">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            <span>Tanda Tangan Absen Pulang</span>
                                        </button>
                                    @else
                                        <button type="button" disabled
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-900/60 text-blue-200 border border-blue-700/60 text-[11px] font-semibold cursor-not-allowed"
                                                title="Absen pulang dapat diisi setelah jam piket selesai (pukul {{ $jamSelesaiFormat }} WIB)">
                                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Pulang Buka Pukul {{ $jamSelesaiFormat }} WIB</span>
                                        </button>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-900/60 text-emerald-300 border border-emerald-700/60 text-[11px] font-bold">
                                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        <span>Tugas Piket Selesai</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL TANDA TANGAN PIKET (MASUK / PULANG) -->
                <div x-show="activePiketModal === {{ $piket->id }}"
                     x-transition.opacity
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
                     style="display: none;"
                     @keydown.escape.window="activePiketModal = null">
                    <div @click.away="activePiketModal = null"
                         class="bg-white text-slate-800 rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 my-6 flex flex-col max-h-[92vh]">
                        
                        <div class="px-5 py-3.5 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/40 shrink-0">
                            <div>
                                <h3 class="font-outfit text-sm font-bold text-white flex items-center gap-1.5">
                                    <span x-text="activePiketType === 'pulang' ? 'Tanda Tangan Presensi PULANG Piket' : 'Tanda Tangan Presensi MASUK Piket'"></span>
                                </h3>
                                <p class="text-[10px] text-emerald-200 mt-0.5" x-text="activePiketType === 'pulang' ? 'Konfirmasi penyelesaian tugas piket & kepulangan' : 'Konfirmasi kehadiran awal pelaksanaan piket'"></p>
                            </div>
                            <button type="button" @click="activePiketModal = null" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form id="form-piket-{{ $piket->id }}" action="{{ route('staf.piket.absen') }}" method="POST" onsubmit="return submitPiketForm(event, {{ $piket->id }})" class="p-4 space-y-3 text-xs">
                            @csrf
                            <input type="hidden" name="piket_id" value="{{ $piket->id }}">
                            <input type="hidden" name="tipe" :value="activePiketType">
                            <input type="hidden" name="tanda_tangan" id="input-ttd-{{ $piket->id }}">

                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                                <p class="font-bold text-slate-800">{{ $piket->keterangan }}</p>
                                <p class="text-[11px] text-slate-600">Pelaksanaan: {{ \Carbon\Carbon::parse($piket->tanggal_piket)->isoFormat('dddd, D MMMM Y') }} ({{ $jamMulaiFormat }} - {{ $jamSelesaiFormat }} WIB)</p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                        <span x-text="activePiketType === 'pulang' ? 'Bubuhkan TTD Pulang Anda' : 'Bubuhkan TTD Hadir Anda'"></span>
                                    </label>
                                    <button type="button" onclick="clearPiketPad({{ $piket->id }})" class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer">Hapus / Ulangi</button>
                                </div>
                                <div id="wrapper-piket-{{ $piket->id }}" class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 overflow-hidden relative">
                                    <canvas id="canvas-piket-{{ $piket->id }}" class="w-full h-36 cursor-crosshair touch-none"></canvas>
                                </div>
                            </div>

                            <div class="pt-1 flex justify-end gap-2">
                                <button type="button" @click="activePiketModal = null" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold hover:bg-[#04392B] transition cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span x-text="activePiketType === 'pulang' ? 'Simpan Presensi Pulang' : 'Simpan Presensi Masuk'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 5. MENU LAYANAN LAINNYA (ABSEN LUAR, IZIN SAKIT, SPT)                 -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="space-y-3">
        <!-- Baris Atas: 2 Kartu Sejajar (Dinas Luar & Izin Sakit) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Kartu 1: Pengajuan Absen Luar -->
            @php
                $pengajuanAktif = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
                    ->where('tanggal', \Carbon\Carbon::today()->toDateString())
                    ->first();
            @endphp
            <div class="sadi-card p-4 bg-white space-y-3 border border-slate-200 shadow-sm rounded-3xl flex flex-col justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-outfit font-extrabold text-slate-800 text-xs">Sedang Dinas Luar?</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ajukan presensi tugas luar kantor</p>
                    </div>
                </div>

                @if($pengajuanAktif)
                    <div class="p-2.5 rounded-xl {{ $pengajuanAktif->status === 'menunggu' ? 'bg-amber-50 border border-amber-200' : ($pengajuanAktif->status === 'disetujui' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200') }} text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold truncate text-[11px] {{ $pengajuanAktif->status === 'menunggu' ? 'text-amber-900' : ($pengajuanAktif->status === 'disetujui' ? 'text-emerald-900' : 'text-rose-900') }}">
                                {{ $pengajuanAktif->judul }}
                            </span>
                            <span class="font-extrabold px-2 py-0.5 rounded-full border text-[9px] {{ $pengajuanAktif->badge_class }}">
                                {{ $pengajuanAktif->label_status }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('staf.riwayat', ['tab' => 'absen_luar']) }}"
                       class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs text-center hover:bg-slate-50 transition">
                        Lihat Rincian &rarr;
                    </a>
                @elseif(!$bisaAjukanAbsenLuar)
                    <div class="space-y-1.5">
                        <button type="button" disabled
                           class="w-full py-2.5 px-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed select-none">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Absen Luar Terkunci</span>
                        </button>
                        <p class="text-[10px] text-slate-500 font-medium text-center px-1 leading-tight">
                            {{ $alasanKunciAbsenLuar ?? 'Tidak dapat mengajukan absen luar hari ini.' }}
                        </p>
                    </div>
                @else
                    <a href="{{ route('staf.ajukan.form') }}"
                       class="w-full py-2.5 rounded-xl border border-amber-400 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Ajukan Absen Luar</span>
                    </a>
                @endif
            </div>

            <!-- Kartu 2: Pengajuan Izin / Sakit -->
            <div class="sadi-card p-4 bg-white space-y-3 border border-slate-200 shadow-sm rounded-3xl flex flex-col justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#064E3B] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-outfit font-extrabold text-slate-800 text-xs">Izin / Sakit?</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ajukan permohonan izin digital</p>
                    </div>
                </div>

                @if(!$bisaAjukanIzin)
                    <div class="space-y-1.5">
                        <button type="button" disabled
                           class="w-full py-2.5 px-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed select-none">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Pengajuan Izin Terkunci</span>
                        </button>
                        <p class="text-[10px] text-slate-500 font-medium text-center px-1 leading-tight">
                            {{ $alasanKunciIzin ?? 'Tidak dapat mengajukan izin hari ini.' }}
                        </p>
                    </div>
                @else
                    <a href="{{ route('staf.izin') }}"
                       class="w-full py-2.5 rounded-xl border border-emerald-400 bg-emerald-50 hover:bg-emerald-100 text-[#064E3B] font-bold text-xs flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Ajukan Izin / Sakit</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Baris Bawah: Kartu Khusus Arsip Surat Perintah Tugas (SPT) Full-Width -->
        <div class="sadi-card p-4 bg-gradient-to-r from-emerald-50/80 via-white to-teal-50/80 border border-teal-200/90 shadow-sm rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-[#064E3B] text-[#E2C268] flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-outfit font-extrabold text-slate-900 text-xs sm:text-sm">Surat Perintah Tugas (SPT)</h3>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                            Resmi Desa
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Arsip & riwayat seluruh surat tugas kedinasan yang Anda terima/tolak</p>
                </div>
            </div>
            <a href="{{ route('staf.spt.riwayat') }}"
               class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs transition flex items-center justify-center gap-2 shadow-xs cursor-pointer active:scale-95 shrink-0">
                <span>Buka Riwayat SPT</span>
                <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 6. PAPAN PENGUMUMAN DESA (SEKUNDER)                                   -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if(isset($pengumumans) && $pengumumans->count() > 0)
    <div class="space-y-2 pt-1" x-data="{ currentSlide: 0, total: {{ $pengumumans->count() }} }">
        <div class="flex items-center justify-between px-1">
            <h3 class="font-outfit font-extrabold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Papan Pengumuman Desa</span>
            </h3>
            
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono">
                <span x-text="currentSlide + 1">1</span> / {{ $pengumumans->count() }}
            </span>
        </div>

        {{-- Slider Wrapper with Side Arrow Buttons --}}
        <div class="relative group">
            
            {{-- Left Arrow Button --}}
            @if($pengumumans->count() > 1)
                <button type="button"
                        @click="if (currentSlide > 0) currentSlide--"
                        :disabled="currentSlide === 0"
                        :class="currentSlide === 0 ? 'opacity-30 cursor-not-allowed text-slate-400 bg-white/70' : 'opacity-90 hover:opacity-100 hover:scale-105 text-[#064E3B] bg-white shadow-md cursor-pointer border-[#C9A84C]/60'"
                        class="absolute -left-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full border flex items-center justify-center transition duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
            @endif

            {{-- Cards Container --}}
            <div class="overflow-hidden rounded-3xl">
                @foreach($pengumumans as $idx => $p)
                    @php
                        $badgeBg = match($p->kategori) {
                            'penting' => 'bg-rose-100 text-rose-800 border border-rose-300',
                            'rapat' => 'bg-amber-100 text-amber-900 border border-amber-300',
                            'kegiatan' => 'bg-blue-100 text-blue-800 border border-blue-300',
                            default => 'bg-emerald-100 text-emerald-900 border border-emerald-300',
                        };
                        $cardBorder = $p->is_pinned ? 'border-2 border-[#C9A84C]' : 'border border-slate-200';
                    @endphp
                    <div x-show="currentSlide === {{ $idx }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="sadi-card p-4 {{ $pengumumans->count() > 1 ? 'px-6' : 'px-4' }} bg-white {{ $cardBorder }} shadow-sm relative overflow-hidden rounded-3xl"
                         style="{{ $idx > 0 ? 'display: none;' : '' }}">

                        @if($p->is_pinned)
                            <div class="absolute top-0 right-0 bg-[#C9A84C] text-[#064E3B] text-[9px] font-extrabold px-3 py-0.5 rounded-bl-xl uppercase tracking-wider flex items-center gap-1 shadow-2xs">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                <span>Disematkan</span>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200 text-[#064E3B] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                @if($p->kategori === 'rapat')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                @elseif($p->kategori === 'penting')
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @elseif($p->kategori === 'kegiatan')
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 pr-4">
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider {{ $badgeBg }}">
                                        {{ ucfirst($p->kategori) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        {{ $p->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB
                                    </span>
                                </div>

                                <h4 class="font-outfit font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">{{ $p->judul }}</h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed whitespace-pre-line">{{ $p->isi }}</p>

                                @if($p->berlaku_hingga)
                                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px]">
                                        <span class="text-slate-400 font-medium">Batas Waktu:</span>
                                        <span class="font-bold text-amber-900 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            Sampai {{ \Carbon\Carbon::parse($p->berlaku_hingga)->isoFormat('D MMMM Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Right Arrow Button --}}
            @if($pengumumans->count() > 1)
                <button type="button"
                        @click="if (currentSlide < total - 1) currentSlide++"
                        :disabled="currentSlide === total - 1"
                        :class="currentSlide === total - 1 ? 'opacity-30 cursor-not-allowed text-slate-400 bg-white/70' : 'opacity-90 hover:opacity-100 hover:scale-105 text-[#064E3B] bg-white shadow-md cursor-pointer border-[#C9A84C]/60'"
                        class="absolute -right-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full border flex items-center justify-center transition duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif

        </div>

        {{-- Dots Indicators if > 1 --}}
        @if($pengumumans->count() > 1)
            <div class="flex items-center justify-center gap-1 pt-0.5">
                @foreach($pengumumans as $idx => $p)
                    <button type="button" @click="currentSlide = {{ $idx }}"
                            :class="currentSlide === {{ $idx }} ? 'w-4 bg-[#064E3B]' : 'w-1.5 bg-slate-300'"
                            class="h-1.5 rounded-full transition-all duration-200 cursor-pointer">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 7. RIWAYAT 5 HARI TERAKHIR                                             -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-4 sm:p-5 bg-white space-y-3 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs uppercase tracking-wider">Riwayat Presensi Terakhir</h4>
            <a href="{{ route('staf.riwayat') }}" class="text-[11px] font-extrabold text-[#064E3B] hover:underline flex items-center gap-0.5">
                <span>Semua Riwayat</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($riwayatTerakhir as $r)
            <div class="py-2.5 flex items-center justify-between text-xs">
                <div>
                    <p class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMM Y') }}</p>
                    <p class="text-[10.5px] text-slate-400 font-mono mt-0.5">
                        {{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '—' }} - {{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '—' }} WIB
                    </p>
                </div>
                <div>
                    @if(strtolower($r->status) === 'hadir')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span>Hadir</span>
                        </span>
                    @elseif(in_array(strtolower($r->status), ['izin', 'sakit', 'dinas luar']))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                            <span>{{ $r->status }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                            <span>{{ $r->status }}</span>
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400 text-center py-4 font-semibold">Belum ada riwayat presensi terbaru.</p>
            @endforelse
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL WARNING / PEMBERITAHUAN SUDAH PRESENSI (GAYA FOTO 2 - CLEAN)     -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if(session('conflict_modal'))
    @php $c = session('conflict_modal'); @endphp
    <div x-data="{ open: true }" x-show="open"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl overflow-hidden border border-amber-200 p-6 space-y-4"
             @click.away="open = false">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-outfit text-base font-extrabold text-slate-900 leading-snug">{{ $c['title'] ?? 'Presensi Sudah Tercatat' }}</h3>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pemberitahuan Sistem Presensi</p>
                </div>
            </div>

            <!-- Box Detail Penjelasan Simpel -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nama Staf:</span>
                    <span class="font-bold text-slate-800">{{ $c['nama'] ?? auth()->user()->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Tanggal:</span>
                    <span class="font-bold text-slate-800">{{ $c['tanggal'] ?? '-' }}</span>
                </div>
                <div class="pt-1.5 border-t border-slate-200 text-[11px] text-slate-600 leading-relaxed">
                    {{ $c['pesan'] ?? '' }}
                </div>
            </div>

            <!-- Tombol Action Simple (Orange Button - Text Tutup) -->
            <div class="pt-1">
                <button type="button" @click="open = false"
                        class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center justify-center gap-1.5">
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    const piketPads = {};

    function initPiketPad(piketId) {
        const canvas = document.getElementById('canvas-piket-' + piketId);
        const wrapper = document.getElementById('wrapper-piket-' + piketId);
        if (!canvas || !wrapper) return;

        canvas.width = wrapper.offsetWidth || 350;
        canvas.height = 140;

        if (!piketPads[piketId]) {
            piketPads[piketId] = new SignaturePad(canvas, {
                minWidth: 1.5,
                maxWidth: 3.5,
                penColor: '#064E3B',
                backgroundColor: 'rgba(0,0,0,0)'
            });
        }
    }

    function clearPiketPad(piketId) {
        if (piketPads[piketId]) {
            piketPads[piketId].clear();
        }
    }

    function submitPiketForm(e, piketId) {
        const pad = piketPads[piketId];
        if (!pad || pad.isEmpty()) {
            e.preventDefault();
            alert('Harap bubuhkan tanda tangan digital Anda terlebih dahulu sebelum menyimpan absensi piket.');
            return false;
        }

        const inputTtd = document.getElementById('input-ttd-' + piketId);
        if (inputTtd) {
            inputTtd.value = pad.toDataURL('image/png');
        }
        return true;
    }

    // ─── SPT Signature Pad Handlers ──────────────────────────────────────────
    const sptPads = {};

    function initSptPad(sptId) {
        const canvas = document.getElementById('canvas-spt-' + sptId);
        const wrapper = document.getElementById('wrapper-spt-' + sptId);
        if (!canvas || !wrapper) return;

        canvas.width = wrapper.offsetWidth || 350;
        canvas.height = 140;

        if (!sptPads[sptId]) {
            sptPads[sptId] = new SignaturePad(canvas, {
                minWidth: 1.5,
                maxWidth: 3.5,
                penColor: '#064E3B',
                backgroundColor: 'rgba(0,0,0,0)'
            });
        }
    }

    function clearSptPad(sptId) {
        if (sptPads[sptId]) {
            sptPads[sptId].clear();
        }
    }

    function submitSptForm(e, sptId) {
        const pad = sptPads[sptId];
        if (!pad || pad.isEmpty()) {
            e.preventDefault();
            alert('Harap bubuhkan tanda tangan digital Anda terlebih dahulu sebelum mengonfirmasi penerimaan SPT.');
            return false;
        }

        const inputTtd = document.getElementById('input-ttd-spt-' + sptId);
        if (inputTtd) {
            inputTtd.value = pad.toDataURL('image/png');
        }
        return true;
    }
</script>
@endsection
