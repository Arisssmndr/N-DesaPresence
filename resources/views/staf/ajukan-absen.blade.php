@extends('staf.layout', ['title' => 'Layanan Absen Luar — Portal Staf Desa Nangtang'])

@section('content')
<div class="space-y-4 pb-6" x-data="ajukanAbsenHub()">

    {{-- Header Banner (HANYA TAMPIL DI HUB OVERVIEW, TIDAK TAMPIL KETIKA FORM SEDANG DIBUKA) --}}
    <div x-show="!showForm" class="sadi-card p-5 text-white rounded-3xl shadow-lg border border-[#C9A84C]/40 relative overflow-hidden" style="background: linear-gradient(135deg, #064E3B 0%, #04392B 100%) !important;">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#C9A84C]/15 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider shadow-xs">
                    Layanan Kedinasan Luar
                </span>
                <h1 class="font-outfit text-lg font-bold text-white mt-1.5">Penugasan Luar Kantor</h1>
                <p class="text-xs text-emerald-200 mt-0.5">Pengajuan presensi dinas luar, kegiatan sosial & tugas lapangan</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#C9A84C] text-[#064E3B] flex items-center justify-center font-bold shadow shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Flash Error & Messages --}}
    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl shadow-xs flex items-start gap-2.5 text-xs text-rose-800 font-semibold">
        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl shadow-xs space-y-1 text-xs">
        <p class="font-bold text-rose-800">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside text-rose-700 space-y-0.5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- 1. SUDAH ABSEN LANGSUNG HARI INI --}}
    @if(isset($kehadiranHariIni) && $kehadiranHariIni && ($kehadiranHariIni->jam_masuk || in_array(strtolower($kehadiranHariIni->status), ['hadir', 'terlambat', 'dinas luar'])))
    <div class="sadi-card p-5 bg-white border border-emerald-300 text-center space-y-3.5 shadow-sm rounded-3xl">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto text-[#064E3B] shadow-xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-900 text-[11px] font-extrabold mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Presensi Langsung Tercatat</span>
            </span>
            <h4 class="font-outfit font-extrabold text-[#064E3B] text-base">Anda Sudah Melakukan Absensi Hari Ini</h4>
            <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">
                Kehadiran Anda pada hari ini (<strong class="text-slate-800">{{ \Carbon\Carbon::today()->isoFormat('dddd, D MMMM Y') }}</strong>) sudah terekam di kantor desa.
            </p>
        </div>

        <div class="pt-1 flex items-center justify-center gap-2">
            <a href="{{ route('staf.beranda') }}"
               class="px-4 py-2 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white text-xs font-bold shadow transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('staf.riwayat.pengajuan') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition inline-flex items-center gap-1.5">
                <span>Riwayat Pengajuan</span>
            </a>
        </div>
    </div>

    {{-- 2. SUDAH ADA PENGAJUAN ABSEN LUAR HARI INI --}}
    @elseif($pengajuanHariIni)
    <div class="sadi-card p-5 bg-white border border-amber-300 text-center space-y-3 shadow-sm rounded-3xl">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center mx-auto text-amber-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h4 class="font-outfit font-extrabold text-slate-800 text-sm">Pengajuan Hari Ini Sudah Terkirim</h4>
            <p class="text-xs text-slate-600 mt-0.5">Judul: <strong>{{ $pengajuanHariIni->judul }}</strong></p>
        </div>
        <div>
            <span class="inline-block text-[11px] font-bold px-3 py-0.5 rounded-full border {{ $pengajuanHariIni->badge_class }}">
                {{ $pengajuanHariIni->label_status }}
            </span>
        </div>
        <div class="pt-1">
            <a href="{{ route('staf.riwayat.pengajuan') }}" class="text-xs font-extrabold text-[#064E3B] underline">Lihat Detail Pengajuan &rarr;</a>
        </div>
    </div>

    @else
    {{-- 3. HUB OVERVIEW (SEBELUM KLIK BUAT PENGAJUAN) --}}
    <div x-show="!showForm" x-transition:enter="transition ease-out duration-200" class="space-y-4">
        
        <!-- Action Hub Card -->
        <div class="sadi-card p-5 bg-white border border-slate-200 shadow-sm rounded-3xl space-y-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-outfit font-extrabold text-slate-800 text-sm">Pengajuan Absen Luar Kantor</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Khusus untuk staf yang bertugas di luar kantor desa (Dinas Undangan, Surat Tugas SPT, atau Kegiatan Sosial).</p>
                </div>
            </div>

            <button type="button" @click="openForm()"
                    class="w-full py-3 px-5 rounded-2xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs tracking-wide shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer border border-[#C9A84C]/40">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajukan Absen Luar Baru</span>
            </button>
        </div>

        <!-- Ketentuan Pengajuan Card -->
        <div class="sadi-card p-5 bg-white border border-slate-200 shadow-sm rounded-3xl space-y-3">
            <div class="border-b border-slate-100 pb-2.5">
                <h3 class="font-outfit font-extrabold text-slate-900 text-xs uppercase tracking-wider">Kategori Penugasan Luar</h3>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-2xl space-y-1">
                    <p class="font-bold text-indigo-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Dinas Luar (Undangan)</span>
                    </p>
                    <p class="text-[11px] text-slate-600">Undangan rapat dinas dari Kecamatan, Pemkab, atau instansi terkait.</p>
                </div>

                <div class="p-3 bg-teal-50/70 border border-teal-200 rounded-2xl space-y-1">
                    <p class="font-bold text-teal-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Dinas Luar (Mandiri)</span>
                    </p>
                    <p class="text-[11px] text-slate-600">Inisiatif tugas lapangan atau urusan warga di wilayah desa.</p>
                </div>

                <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-2xl space-y-1">
                    <p class="font-bold text-blue-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Surat Perintah Tugas (SPT)</span>
                    </p>
                    <p class="text-[11px] text-slate-600">Penugasan resmi langsung dari Kepala Desa Nangtang.</p>
                </div>

                <div class="p-3 bg-rose-50/70 border border-rose-200 rounded-2xl space-y-1">
                    <p class="font-bold text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span>Kegiatan Sosial</span>
                    </p>
                    <p class="text-[11px] text-slate-600">Gotong royong kemasyarakatan atau pendampingan warga di lapangan.</p>
                </div>
            </div>
        </div>

    </div>

    {{-- 4. FORMULIR PENGAJUAN INTERAKTIF DENGAN GERBANG GPS --}}
    <form x-show="showForm"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 translate-y-3"
          x-transition:enter-end="opacity-100 translate-y-0"
          action="{{ route('staf.ajukan.store') }}" method="POST" enctype="multipart/form-data"
          id="form-ajukan-absen"
          @submit="validateAndSubmit($event)"
          class="space-y-4">
        @csrf

        {{-- Hidden GPS & Location metadata fields --}}
        <input type="hidden" name="latitude" :value="lat">
        <input type="hidden" name="longitude" :value="lng">
        <input type="hidden" name="alamat_gps" :value="alamatGps">
        <input type="hidden" name="sumber_koordinat" value="gps">
        <input type="hidden" name="akurasi_gps_meter" :value="gpsAccuracy">

        {{-- Form Header Bar (Simpel, Bersih, Tanpa Bulatan) --}}
        <div class="sadi-card p-4 bg-white border border-slate-200 rounded-3xl shadow-sm flex items-center justify-between">
            <div>
                <h2 class="font-outfit font-extrabold text-sm text-[#064E3B]">Formulir Absen Luar Kantor</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Lengkapi koordinat lokasi dan rincian penugasan</p>
            </div>
            <button type="button" @click="showForm = false"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Tutup</span>
            </button>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        {{-- ⭐ LANGKAH 1: GERBANG WAJIB TITIK KOORDINAT GPS REALTIME               --}}
        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        <div class="sadi-card p-5 border-2 transition-all duration-300 shadow-sm space-y-3.5 rounded-3xl"
             :class="{
                 'bg-gradient-to-br from-emerald-50 via-white to-emerald-50 border-emerald-400': gpsStatus === 'success',
                 'bg-amber-50/90 border-amber-400': gpsStatus === 'loading',
                 'bg-rose-50/90 border-rose-300': gpsStatus === 'error' || gpsStatus === 'idle'
             }">
            
            <div class="flex items-center justify-between border-b pb-3"
                 :class="gpsStatus === 'success' ? 'border-emerald-200' : 'border-slate-200/80'">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center font-extrabold text-xs shadow-xs text-white"
                         :class="gpsStatus === 'success' ? 'bg-emerald-600' : 'bg-slate-700'">
                        1
                    </div>
                    <h3 class="font-outfit font-extrabold text-sm text-slate-900">
                        <span x-show="gpsStatus === 'success'">Titik Koordinat Terkunci</span>
                        <span x-show="gpsStatus !== 'success'">Deteksi Koordinat Lokasi GPS</span>
                    </h3>
                </div>

                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border"
                      :class="{
                          'bg-emerald-100 text-emerald-800 border-emerald-300': gpsStatus === 'success',
                          'bg-amber-100 text-amber-900 border-amber-300 animate-pulse': gpsStatus === 'loading',
                          'bg-rose-100 text-rose-900 border-rose-300': gpsStatus === 'error' || gpsStatus === 'idle'
                      }">
                    <span x-show="gpsStatus === 'success'">Lokasi Terkunci</span>
                    <span x-show="gpsStatus === 'loading'">Mencari Satelit...</span>
                    <span x-show="gpsStatus === 'error' || gpsStatus === 'idle'">GPS Belum Terdeteksi</span>
                </span>
            </div>

            {{-- KONDISI 1: SUKSES MENGUNCI GPS REALTIME --}}
            <template x-if="gpsStatus === 'success' && lat && lng">
                <div class="space-y-3 pt-1">
                    <div class="p-3.5 rounded-2xl bg-white border border-emerald-300 shadow-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                <span class="text-xs font-extrabold text-[#064E3B]">Koordinat GPS Presisi:</span>
                            </div>
                            <span class="text-[10px] font-mono font-bold bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded border border-emerald-200"
                                  x-text="'Akurasi ±' + (gpsAccuracy || 0) + ' meter'"></span>
                        </div>

                        <p class="font-mono text-xs font-extrabold text-slate-900 bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-center select-all"
                           x-text="lat + ', ' + lng"></p>

                        <div class="flex items-start gap-2 pt-1 text-xs text-slate-700">
                            <svg class="w-4 h-4 text-[#064E3B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Wilayah Terdeteksi:</span>
                                <p class="font-bold text-slate-900 text-xs mt-0.5 leading-snug" x-text="alamatGps || 'Memuat alamat wilayah...'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <a :href="'https://maps.google.com/?q=' + lat + ',' + lng" target="_blank"
                           class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka Google Maps</span>
                        </a>

                        <button type="button" @click="requestLocation()"
                                class="px-3.5 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 text-xs font-extrabold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Perbarui Koordinat GPS</span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- KONDISI 2: SEDANG MENGUNCI LOKASI --}}
            <template x-if="gpsStatus === 'loading'">
                <div class="p-5 text-center space-y-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center mx-auto shadow-md animate-spin">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h4 class="font-outfit font-extrabold text-amber-900 text-xs">Mencari Sinyal Satelit GPS Realtime...</h4>
                        <p class="text-[11px] text-amber-800 mt-0.5" x-text="gpsLoadingText"></p>
                    </div>
                </div>
            </template>

            {{-- KONDISI 3: BELUM MENGAMBIL LOKASI ATAU GPS MATI / ERROR --}}
            <template x-if="gpsStatus === 'idle' || gpsStatus === 'error'">
                <div class="space-y-3 pt-1">
                    {{-- Banner Pengingat Aktif Menyalakan GPS --}}
                    <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-left space-y-2 shadow-xs">
                        <div class="flex items-center gap-2 text-rose-800 font-extrabold text-xs">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Wajib Mengaktifkan GPS & Izin Lokasi Perangkat</span>
                        </div>
                        <p class="text-[11px] text-rose-700 leading-relaxed"
                           x-text="gpsErrorMessage || 'Sistem presensi luar kantor membutuhkan koordinat GPS realtime untuk memvalidasi lokasi penugasan. Harap nyalakan GPS / Lokasi pada HP/Laptop Anda dan izinkan akses browser.'"></p>
                        
                        <div class="p-2.5 bg-white/80 rounded-xl border border-rose-200 text-[10.5px] text-slate-700 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                            <span>Pastikan setelan <strong>Lokasi (GPS)</strong> di perangkat Anda telah dinyalakan, lalu tekan tombol di bawah:</span>
                        </div>
                    </div>

                    {{-- Tombol Deteksi / Cari Koordinat --}}
                    <button type="button" @click="requestLocation()"
                            class="w-full py-2.5 px-4 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white text-center text-xs font-extrabold shadow transition flex items-center justify-center gap-2 cursor-pointer border border-[#C9A84C]/50 active:scale-98">
                        <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Cari & Deteksi Titik Koordinat GPS</span>
                    </button>
                </div>
            </template>

        </div>

        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        {{-- PLACEHOLDER TERKUNCI JIKA KOORDINAT BELUM DIDAPATKAN                   --}}
        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        <div x-show="gpsStatus !== 'success' || !lat || !lng"
             class="p-6 bg-slate-100/90 border border-dashed border-slate-300 rounded-3xl text-center space-y-2">
            <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-400 flex items-center justify-center mx-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h4 class="font-outfit font-extrabold text-slate-700 text-xs">Formulir Rincian Penugasan Terkunci</h4>
                <p class="text-[11px] text-slate-500 max-w-sm mx-auto mt-0.5 leading-relaxed">
                    Klik tombol <strong>"Cari & Deteksi Titik Koordinat GPS"</strong> di atas terlebih dahulu untuk membuka formulir pengisian data tugas luar.
                </p>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        {{-- ⭐ LANGKAH 2: FORMULIR PENUGASAN (HANYA TERBUKA JIKA GPS SUKSES)       --}}
        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        <div x-show="gpsStatus === 'success' && lat && lng"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-4">

            {{-- 2. Pilih Jenis Pengajuan --}}
            <div class="sadi-card p-5 bg-white shadow-md space-y-3.5 rounded-3xl">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-600 flex items-center justify-center font-extrabold text-xs shadow-xs text-white">
                        2
                    </div>
                    <div>
                        <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm">Pilih Kategori Kehadiran Luar <span class="text-rose-500">*</span></h4>
                        <p class="text-[11px] text-slate-500">Pilih salah satu kategori penugasan:</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- 1. Dinas Luar Undangan --}}
                    <label :class="jenisAbsen === 'dinas_luar_undangan' ? 'border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-600/20 shadow-xs' : 'border-slate-200 bg-white hover:border-indigo-300'"
                           class="cursor-pointer rounded-2xl border-2 p-3 flex items-start gap-2.5 transition-all">
                        <input type="radio" name="jenis" value="dinas_luar_undangan" x-model="jenisAbsen" class="sr-only">
                        <div :class="jenisAbsen === 'dinas_luar_undangan' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-indigo-100 text-indigo-700'"
                             class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-extrabold" :class="jenisAbsen === 'dinas_luar_undangan' ? 'text-indigo-900' : 'text-slate-800'">Dinas Luar (Undangan)</p>
                            <p class="text-[10.5px] text-slate-500 leading-tight">Undangan resmi dari pihak luar/Kecamatan/Pemkab.</p>
                            <span class="inline-block text-[9px] font-bold text-indigo-700 bg-indigo-100/80 px-1.5 py-0.5 rounded mt-0.5">Lampirkan Surat Undangan</span>
                        </div>
                    </label>

                    {{-- 2. Dinas Luar Pengajuan (Mandiri / Inisiatif) --}}
                    <label :class="jenisAbsen === 'dinas_luar_pengajuan' ? 'border-teal-600 bg-teal-50/70 ring-2 ring-teal-600/20 shadow-xs' : 'border-slate-200 bg-white hover:border-teal-300'"
                           class="cursor-pointer rounded-2xl border-2 p-3 flex items-start gap-2.5 transition-all">
                        <input type="radio" name="jenis" value="dinas_luar_pengajuan" x-model="jenisAbsen" class="sr-only">
                        <div :class="jenisAbsen === 'dinas_luar_pengajuan' ? 'bg-teal-600 text-white shadow-xs' : 'bg-teal-100 text-teal-700'"
                             class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-extrabold" :class="jenisAbsen === 'dinas_luar_pengajuan' ? 'text-teal-900' : 'text-slate-800'">Dinas Luar (Mandiri)</p>
                            <p class="text-[10.5px] text-slate-500 leading-tight">Inisiatif tugas urusan lapangan di wilayah desa.</p>
                            <span class="inline-block text-[9px] font-bold text-teal-700 bg-teal-100/80 px-1.5 py-0.5 rounded mt-0.5">Lampirkan Foto Lapangan</span>
                        </div>
                    </label>

                    {{-- 3. Dinas Luar Surat Tugas (SPT) --}}
                    <label :class="jenisAbsen === 'dinas_luar_surat_tugas' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-600/20 shadow-xs' : 'border-slate-200 bg-white hover:border-blue-300'"
                           class="cursor-pointer rounded-2xl border-2 p-3 flex items-start gap-2.5 transition-all">
                        <input type="radio" name="jenis" value="dinas_luar_surat_tugas" x-model="jenisAbsen" class="sr-only">
                        <div :class="jenisAbsen === 'dinas_luar_surat_tugas' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-100 text-blue-700'"
                             class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-extrabold" :class="jenisAbsen === 'dinas_luar_surat_tugas' ? 'text-blue-900' : 'text-slate-800'">Surat Tugas (SPT)</p>
                            <p class="text-[10.5px] text-slate-500 leading-tight">Penugasan resmi langsung dari Kepala Desa / Pemdes.</p>
                            <span class="inline-block text-[9px] font-bold text-blue-700 bg-blue-100/80 px-1.5 py-0.5 rounded mt-0.5">Lampirkan Dokumen SPT</span>
                        </div>
                    </label>

                    {{-- 4. Kegiatan Sosial --}}
                    <label :class="jenisAbsen === 'kegiatan_sosial' ? 'border-pink-600 bg-pink-50/70 ring-2 ring-pink-600/20 shadow-xs' : 'border-slate-200 bg-white hover:border-pink-300'"
                           class="cursor-pointer rounded-2xl border-2 p-3 flex items-start gap-2.5 transition-all">
                        <input type="radio" name="jenis" value="kegiatan_sosial" x-model="jenisAbsen" class="sr-only">
                        <div :class="jenisAbsen === 'kegiatan_sosial' ? 'bg-pink-600 text-white shadow-xs' : 'bg-pink-100 text-pink-700'"
                             class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-extrabold" :class="jenisAbsen === 'kegiatan_sosial' ? 'text-pink-900' : 'text-slate-800'">Kegiatan Sosial</p>
                            <p class="text-[10.5px] text-slate-500 leading-tight">Gotong royong kemasyarakatan / pendampingan warga.</p>
                            <span class="inline-block text-[9px] font-bold text-pink-700 bg-pink-100/80 px-1.5 py-0.5 rounded mt-0.5">Lampirkan Foto Bukti</span>
                        </div>
                    </label>
                </div>
                @error('jenis')
                <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            {{-- 3. Detail Kegiatan (muncul setelah pilih jenis) --}}
            <div x-show="jenisAbsen !== ''" x-transition class="space-y-4">

                {{-- Tanggal & Detail Form --}}
                <div class="sadi-card p-5 bg-white shadow-md space-y-3.5 rounded-3xl">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Informasi Kegiatan & Penugasan</span>
                        </h4>
                    </div>

                    {{-- Tanggal Absensi --}}
                    <div class="text-xs space-y-1">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider">Tanggal Absensi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $today) }}" max="{{ $today }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none font-semibold">
                        @error('tanggal') <p class="text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Instansi Pengundang (Hanya untuk Dinas Luar Undangan) --}}
                    <div x-show="jenisAbsen === 'dinas_luar_undangan'" x-transition class="text-xs space-y-1">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider">Instansi / Pihak Pengundang <span class="text-rose-500">*</span></label>
                        <input type="text" name="instansi_pengundang" value="{{ old('instansi_pengundang') }}" placeholder="Contoh: Kantor Kecamatan Cigalontang"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                        @error('instansi_pengundang') <p class="text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nomor Surat Tugas / SPT (Hanya untuk Dinas Luar Surat Tugas) --}}
                    <div x-show="jenisAbsen === 'dinas_luar_surat_tugas'" x-transition class="text-xs space-y-1">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider">Nomor Surat Perintah Tugas (SPT)</label>
                        <input type="text" name="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}" placeholder="Contoh: 090/045/SPT/Pemdes/2026"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                        @error('nomor_surat_tugas') <p class="text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Judul Kegiatan --}}
                    <div class="text-xs space-y-1">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider">Judul Ringkas Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Rapat Koordinasi Aparatur Desa" required maxlength="150"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none font-semibold">
                        @error('judul') <p class="text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Keterangan Lengkap --}}
                    <div class="text-xs space-y-1">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider">Keterangan / Uraian Kegiatan <span class="text-rose-500">*</span></label>
                        <textarea name="deskripsi" rows="3" required placeholder="Jelaskan uraian kegiatan, agenda, hasil, atau keterangan tugas..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none resize-none">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <p class="text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Bukti Dokumen (Untuk Dinas Luar Undangan & Surat Tugas) --}}
                <div x-show="jenisAbsen === 'dinas_luar_undangan' || jenisAbsen === 'dinas_luar_surat_tugas' || jenisAbsen === 'dinas_luar'" x-transition class="sadi-card p-5 bg-white shadow-md space-y-2.5 rounded-3xl">
                    <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Dokumen / Surat Undangan Resmi <span class="text-rose-500">*</span></span>
                    </h4>
                    <p class="text-[11px] text-slate-500">Unggah berkas scan surat / foto dokumen resmi (PDF / Gambar, Maks 5MB).</p>

                    <label class="block cursor-pointer border border-dashed border-slate-300 hover:border-[#064E3B] rounded-2xl p-4 text-center transition bg-slate-50/50 hover:bg-emerald-50/30">
                        <input type="file" name="file_dokumen" accept=".pdf,image/*" class="hidden"
                            @change="namaDokumen = $event.target.files[0] ? $event.target.files[0].name : ''">
                        <svg class="w-7 h-7 text-slate-400 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <p x-show="!namaDokumen" class="text-xs text-slate-600 font-semibold">Klik untuk memilih berkas dokumen</p>
                        <p x-show="namaDokumen" class="text-xs text-[#064E3B] font-extrabold" x-text="namaDokumen"></p>
                    </label>
                    @error('file_dokumen') <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Bukti Foto Lokasi (Untuk Pengajuan Mandiri & Kegiatan Sosial) --}}
                <div x-show="jenisAbsen === 'dinas_luar_pengajuan' || jenisAbsen === 'kegiatan_sosial' || jenisAbsen === 'dinas_luar_undangan' || jenisAbsen === 'dinas_luar_surat_tugas'" x-transition class="sadi-card p-5 bg-white shadow-md space-y-2.5 rounded-3xl">
                    <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Foto Bukti Keberadaan / Situasi Lapangan</span>
                    </h4>
                    <p class="text-[11px] text-slate-500">Ambil foto dokumentasi di lokasi tugas sebagai bukti autentik.</p>

                    <div x-show="previewFoto" x-transition class="rounded-2xl overflow-hidden border-2 border-[#C9A84C]">
                        <img :src="previewFoto" class="w-full max-h-44 object-cover">
                    </div>

                    <label class="block cursor-pointer border border-dashed border-slate-300 hover:border-[#064E3B] rounded-2xl p-4 text-center transition bg-slate-50/50 hover:bg-emerald-50/30">
                        <input type="file" name="foto_lokasi" accept="image/*" class="hidden"
                            @change="
                                const f = $event.target.files[0];
                                if (f) {
                                    namaFoto = f.name;
                                    const reader = new FileReader();
                                    reader.onload = e => { previewFoto = e.target.result };
                                    reader.readAsDataURL(f);
                                }
                            ">
                        <svg class="w-7 h-7 text-slate-400 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p x-show="!namaFoto" class="text-xs text-slate-600 font-semibold">Klik untuk memilih foto dari galeri / jepret kamera</p>
                        <p x-show="namaFoto" class="text-xs text-[#064E3B] font-extrabold" x-text="namaFoto"></p>
                    </label>
                    @error('foto_lokasi') <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Tanda Tangan Digital --}}
                <div class="sadi-card p-5 bg-white shadow-md space-y-3 rounded-3xl">
                    <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Tanda Tangan Digital <span class="text-rose-500">*</span></span>
                    </h4>
                    <p class="text-[11px] text-slate-500">Goreskan tanda tangan Anda sebagai bukti sah pengajuan penugasan luar.</p>

                    <div class="relative rounded-2xl bg-slate-50 border-2 border-slate-200 overflow-hidden"
                         :class="hasSignature ? 'border-[#064E3B]' : 'border-slate-200'">
                        <canvas x-ref="sigCanvas" width="380" height="150" class="w-full touch-none cursor-crosshair"
                            @mousedown="startDraw($event)"
                            @mousemove="draw($event)"
                            @mouseup="stopDraw()"
                            @mouseleave="stopDraw()"
                            @touchstart.prevent="startDraw($event)"
                            @touchmove.prevent="draw($event)"
                            @touchend="stopDraw()">
                        </canvas>
                        <div x-show="!hasSignature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <p class="text-slate-300 text-xs font-semibold">Tanda tangan di sini...</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" @click="clearSig()"
                            class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Ulangi</span>
                        </button>
                        <div x-show="hasSignature" class="text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Tanda tangan terekam</span>
                        </div>
                    </div>

                    <input type="hidden" name="tanda_tangan" x-ref="ttdInput">
                    @error('tanda_tangan') <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button" @click="showForm = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs tracking-wide shadow-md transition active:scale-[0.98] flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Kirim Pengajuan Absen Luar</span>
                    </button>
                </div>

            </div>{{-- end x-show jenis dipilih --}}

        </div>{{-- end x-show gps success --}}

    </form>
    @endif

</div>
@endsection

@section('scripts')
<script>
function ajukanAbsenHub() {
    return {
        showForm: {{ $errors->any() ? 'true' : 'false' }},
        jenisAbsen: '{{ old('jenis', '') }}',
        previewFoto: null,
        namaFoto: '',
        namaDokumen: '',
        signatureCanvas: null,
        ctx: null,
        isDrawing: false,
        hasSignature: false,
        
        // Location State
        lat: '',
        lng: '',
        alamatGps: '',
        sumberKoordinat: 'gps',
        gpsStatus: 'idle',       // 'idle' | 'loading' | 'success' | 'error'
        gpsErrorMessage: '',
        gpsAccuracy: null,       // meter
        gpsLoadingText: 'Meminta izin akses GPS realtime...',
        _watchId: null,
        _watchTimer: null,
        _bestAccuracy: Infinity,

        init() {
            this.$nextTick(() => {
                this.initCanvas();
            });
            if (this.showForm) {
                this.requestLocation();
            }
        },

        openForm() {
            this.showForm = true;
            this.requestLocation();
            this.$nextTick(() => {
                this.initCanvas();
            });
        },

        initCanvas() {
            this.signatureCanvas = this.$refs.sigCanvas;
            if (!this.signatureCanvas) return;
            this.ctx = this.signatureCanvas.getContext('2d');
            this.ctx.strokeStyle = '#064E3B';
            this.ctx.lineWidth = 2.5;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
        },

        startDraw(e) {
            if (!this.ctx) this.initCanvas();
            this.isDrawing = true;
            const pos = this.getPos(e);
            this.ctx.beginPath();
            this.ctx.moveTo(pos.x, pos.y);
        },

        draw(e) {
            if (!this.isDrawing) return;
            if (!this.ctx) this.initCanvas();
            e.preventDefault();
            const pos = this.getPos(e);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
            this.hasSignature = true;
        },

        stopDraw() {
            this.isDrawing = false;
        },

        getPos(e) {
            if (!this.signatureCanvas) this.signatureCanvas = this.$refs.sigCanvas;
            const rect = this.signatureCanvas.getBoundingClientRect();
            const src = e.touches ? e.touches[0] : e;
            const scaleX = this.signatureCanvas.width / rect.width;
            const scaleY = this.signatureCanvas.height / rect.height;
            return {
                x: (src.clientX - rect.left) * scaleX,
                y: (src.clientY - rect.top) * scaleY
            };
        },

        clearSig() {
            if (!this.ctx) this.initCanvas();
            if (this.signatureCanvas && this.ctx) {
                this.ctx.clearRect(0, 0, this.signatureCanvas.width, this.signatureCanvas.height);
            }
            this.hasSignature = false;
            if (this.$refs.ttdInput) {
                this.$refs.ttdInput.value = '';
            }
        },

        saveSig() {
            if (!this.hasSignature) return false;
            if (!this.signatureCanvas) this.signatureCanvas = this.$refs.sigCanvas;
            if (this.$refs.ttdInput && this.signatureCanvas) {
                this.$refs.ttdInput.value = this.signatureCanvas.toDataURL('image/png');
            }
            return true;
        },

        _stopWatch() {
            if (this._watchId !== null) {
                navigator.geolocation.clearWatch(this._watchId);
                this._watchId = null;
            }
            if (this._watchTimer !== null) {
                clearTimeout(this._watchTimer);
                this._watchTimer = null;
            }
        },

        _validateNkri(lat, lng) {
            return (lat >= -11.0 && lat <= 6.0) && (lng >= 95.0 && lng <= 141.1);
        },

        _commitPosition(rawLat, rawLng, accuracy = null, source = 'gps') {
            this._stopWatch();
            
            const isNKRI = this._validateNkri(rawLat, rawLng);
            
            if (!isNKRI) {
                this.gpsStatus       = 'error';
                this.gpsAccuracy     = null;
                this.gpsErrorMessage =
                    'Koordinat terdeteksi di luar wilayah Indonesia (' + rawLat.toFixed(5) + ', ' + rawLng.toFixed(5) + '). '
                  + 'Pastikan GPS perangkat aktif dan matikan VPN.';
                return;
            }
            
            this.lat             = rawLat.toFixed(7);
            this.lng             = rawLng.toFixed(7);
            this.sumberKoordinat = 'gps';
            this.gpsAccuracy     = accuracy ? Math.round(accuracy) : null;
            this.gpsStatus       = 'success';
            
            this.alamatGps = 'Koordinat: ' + this.lat + ', ' + this.lng + ' (Akurasi ±' + (this.gpsAccuracy || 0) + 'm)';
            
            this.reverseGeocode(rawLat, rawLng);

            this.$nextTick(() => {
                this.initCanvas();
            });
        },

        requestLocation() {
            this._stopWatch();
            
            this.gpsStatus       = 'loading';
            this.gpsErrorMessage = '';
            this.gpsAccuracy     = null;
            this._bestAccuracy   = Infinity;
            this.lat             = '';
            this.lng             = '';
            this.gpsLoadingText  = 'Mengakses satelit GPS presisi tinggi perangkat Anda...';
            
            if (!navigator.geolocation) {
                this.gpsStatus = 'error';
                this.gpsErrorMessage = 'Browser Anda tidak mendukung deteksi lokasi Geolocation GPS.';
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const rawLat = position.coords.latitude;
                    const rawLng = position.coords.longitude;
                    const acc    = position.coords.accuracy;
                    this._commitPosition(rawLat, rawLng, acc, 'gps');
                },
                (error) => {
                    console.warn('Geolocation Error:', error);
                    let errText = 'Sistem belum dapat mendeteksi koordinat GPS.';
                    if (error.code === 1) {
                        errText = 'Izin lokasi (GPS) belum diberikan atau ditolak. Mohon izinkan akses lokasi pada setelan browser Anda dan nyalakan GPS di HP/Laptop.';
                    } else if (error.code === 2) {
                        errText = 'Sinyal GPS tidak aktif/tidak tersedia. Pastikan fitur Lokasi (GPS) pada perangkat Anda sudah AKTIF.';
                    } else if (error.code === 3) {
                        errText = 'Waktu pencarian satelit GPS habis (timeout). Silakan klik tombol "Cari & Deteksi Titik Koordinat GPS" lagi.';
                    }
                    this.gpsStatus = 'error';
                    this.gpsErrorMessage = errText;
                },
                {
                    enableHighAccuracy: true,
                    timeout:           15000,
                    maximumAge:        0
                }
            );
        },

        async reverseGeocode(lat, lng) {
            try {
                const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=id`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok) {
                    const data = await res.json();
                    const locality = data.locality || data.principalSubdivisionCity || '';
                    const district = data.city || data.localityInfo?.administrative?.[3]?.name || '';
                    const province = data.principalSubdivision || '';
                    const country  = data.countryName || 'Indonesia';

                    let parts = [];
                    if (locality) parts.push(locality);
                    if (district && district !== locality) parts.push(district);
                    if (province) parts.push(province);
                    if (country && !parts.includes(country)) parts.push(country);

                    if (parts.length > 0) {
                        this.alamatGps = parts.join(', ') + ` [${this.lat}, ${this.lng}] (±${this.gpsAccuracy || 0}m)`;
                    }
                }
            } catch (e) {
                console.warn('Reverse geocode skipped:', e);
            }
        },

        validateAndSubmit(e) {
            if (!this.lat || !this.lng || this.gpsStatus !== 'success') {
                e.preventDefault();
                alert('Wajib mencari dan mengunci titik koordinat GPS realtime terlebih dahulu sebelum mengirim pengajuan.');
                this.requestLocation();
                return false;
            }
            
            if (!this.hasSignature) {
                e.preventDefault();
                alert('Silakan bubuhkan tanda tangan digital Anda terlebih dahulu pada kotak yang disediakan.');
                return false;
            }
            
            this.saveSig();
            return true;
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('ajukanAbsenHub', ajukanAbsenHub);
});
</script>
@endsection
