<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'N-DesaPresence — Sistem Absensi Desa Nangtang' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tasikmalaya.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & SweetAlert2 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sadiCream: '#F5F0E8',
                        sadiCreamDark: '#EBE4D8',
                        sadiGreenDark: '#064E3B',
                        sadiGreenPrimary: '#1B4D3E',
                        sadiGreenLight: '#059669',
                        sadiGold: '#C9A84C',
                        sadiGoldLight: '#E2C268',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { background-color: #F5F0E8; color: #1C2826; }
        .sadi-sidebar { background: linear-gradient(180deg, #064E3B 0%, #04392B 100%); }
        .sadi-card { background: #FFFFFF; border-radius: 16px; box-shadow: 0px 4px 20px rgba(27, 77, 62, 0.05); border: 1px solid rgba(201, 168, 76, 0.15); }
        .custom-sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .custom-sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(201, 168, 76, 0.25); border-radius: 4px; }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(201, 168, 76, 0.5); }

        /* High Contrast Global Elements & Buttons */
        .btn-sadi-primary, .btn-primary-dark {
            background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            border: 1px solid #C9A84C !important;
            box-shadow: 0 4px 14px rgba(6, 78, 59, 0.4) !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-sadi-primary:hover, .btn-primary-dark:hover {
            background: linear-gradient(135deg, #04392B 0%, #064E3B 100%) !important;
            box-shadow: 0 6px 20px rgba(6, 78, 59, 0.5) !important;
            transform: translateY(-1px);
        }
        
        .btn-gold {
            background: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%) !important;
            color: #064E3B !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 800 !important;
            border: 1px solid #997A24 !important;
            box-shadow: 0 4px 14px rgba(201, 168, 76, 0.45) !important;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #C9A84C 0%, #B59339 100%) !important;
            box-shadow: 0 6px 20px rgba(201, 168, 76, 0.55) !important;
            transform: translateY(-1px);
        }

        /* Luxury Emerald & Gold Table Headers */
        thead tr {
            background: linear-gradient(135deg, #064E3B 0%, #083327 100%) !important;
            border-bottom: 2px solid #C9A84C !important;
        }
        thead th {
            color: #FFFFFF !important;
            font-weight: 800 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
            padding-top: 0.85rem !important;
            padding-bottom: 0.85rem !important;
        }

        /* High Contrast Nav Items */
        .sadi-nav-active {
            background: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%) !important;
            color: #064E3B !important;
            font-weight: 800 !important;
            box-shadow: 0px 4px 14px rgba(201, 168, 76, 0.5) !important;
            border: 1px solid #F5E6B3 !important;
        }
        .sadi-nav-active span {
            color: #064E3B !important;
            font-weight: 800 !important;
        }
        .sadi-nav-active svg {
            color: #064E3B !important;
            stroke-width: 2.5px !important;
        }

        .sadi-nav-inactive {
            color: #E2E8F0 !important;
            font-weight: 600;
        }
        .sadi-nav-inactive:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #FFFFFF !important;
        }
        .sadi-nav-inactive span {
            color: #F1F5F9 !important;
            font-weight: 600 !important;
        }
        .sadi-nav-inactive:hover span {
            color: #FFFFFF !important;
        }
        .sadi-nav-inactive svg {
            color: #6EE7B7 !important;
            stroke: #6EE7B7 !important;
        }
        .sadi-nav-inactive:hover svg {
            color: #FFFFFF !important;
            stroke: #FFFFFF !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#F5F0E8] min-h-screen text-slate-800">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"></div>

        <!-- Sidebar Navigation (Fixed Height & Categorized Menu with wire:navigate SPA) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 h-screen sadi-sidebar text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between shadow-2xl shrink-0">
            <!-- Brand Header -->
            <div class="px-5 py-4 border-b border-emerald-800/50 shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kab. Tasikmalaya" class="h-10 w-auto object-contain shrink-0 filter drop-shadow">
                    <div class="overflow-hidden">
                        <h1 class="font-outfit text-base font-bold tracking-tight text-white leading-tight truncate">N-DesaPresence</h1>
                        <p class="text-[10px] text-[#C9A84C] tracking-wider font-semibold uppercase leading-tight truncate">Desa Nangtang — Kab. Tasikmalaya</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu (Categorized & Flex-1 Scrollable with wire:navigate SPA) -->
            <nav class="px-3 py-3 space-y-4 flex-1 overflow-y-auto custom-sidebar-scroll">

                <!-- KATEGORI 1: DASHBOARD & MONITORING -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#C9A84C]">Dashboard & Monitoring</div>
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Dashboard Real-Time</span>
                    </a>
                    <a href="{{ route('analitik.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('analitik.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Analitik Kedisiplinan</span>
                    </a>
                </div>

                <!-- KATEGORI 2: DATA MASTER -->
                <div class="space-y-1 pt-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#C9A84C]">Data Master</div>
                    <a href="{{ route('pegawai.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('pegawai.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Data Pegawai & Perangkat</span>
                    </a>
                    <a href="{{ route('user-staf.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('user-staf.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Akun Pengguna Staf</span>
                    </a>
                    <a href="{{ route('shift.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('shift.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Master Shift Kerja</span>
                    </a>
                    <a href="{{ route('hari-libur.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('hari-libur.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kalender & Hari Libur</span>
                    </a>
                </div>

                <!-- KATEGORI 3: LAYANAN PRESENSI & KEDINASAN -->
                <div class="space-y-1 pt-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#C9A84C]">Layanan Presensi & Kedinasan</div>
                    <a href="{{ route('matriks.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('matriks.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        <span>Buku Matriks Presensi</span>
                    </a>
                    {{-- PENGAJUAN ABSEN LUAR (dengan badge notifikasi) --}}
                    @php 
                        $jmlPengajuanMenunggu = \Illuminate\Support\Facades\Cache::remember('sidebar_pengajuan_menunggu_count', 30, function() {
                            return \App\Models\PengajuanAbsenLuar::where('status','menunggu')->count();
                        });
                    @endphp
                    <a href="{{ route('pengajuan-absen.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('pengajuan-absen.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 4h.01M9 12h.01M9 16h.01M13 12h4m-4 4h2"/></svg>
                        <span class="flex-1">Pengajuan Absen Luar</span>
                        @if($jmlPengajuanMenunggu > 0)
                        <span class="text-[10px] font-extrabold bg-rose-500 text-white px-1.5 py-0.5 rounded-full leading-none animate-pulse">{{ $jmlPengajuanMenunggu }}</span>
                        @endif
                    </a>
                    @php 
                        $jmlIzinMenunggu = \Illuminate\Support\Facades\Cache::remember('sidebar_izin_menunggu_count', 30, function() {
                            return \App\Models\IzinSakit::where('status','menunggu')->count();
                        });
                    @endphp
                    <a href="{{ route('izin.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('izin.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="flex-1">Izin, Cuti & Sakit</span>
                        @if($jmlIzinMenunggu > 0)
                        <span class="text-[10px] font-extrabold bg-amber-500 text-white px-1.5 py-0.5 rounded-full leading-none animate-pulse">{{ $jmlIzinMenunggu }}</span>
                        @endif
                    </a>
                    <a href="{{ route('spt.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('spt.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Surat Perintah Tugas (SPT)</span>
                    </a>
                    <a href="{{ route('jadwal-piket.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('jadwal-piket.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Jadwal Piket Kantor</span>
                    </a>
                    <a href="{{ route('attendance.override') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('attendance.override*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Override Presensi Manual</span>
                    </a>
                </div>

                <!-- KATEGORI 4: PUSAT LAPORAN & SPJ -->
                <div class="space-y-1 pt-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#C9A84C]">Pusat Laporan & SPJ</div>
                    <a href="{{ route('laporan.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('laporan.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Pusat Laporan Kedinasan</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('laporan-disesuaikan.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('laporan-disesuaikan.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span class="flex-1 text-[#E2C268] font-bold">Laporan Disesuaikan</span>
                        <span class="text-[9px] font-extrabold bg-[#C9A84C] text-[#064E3B] px-1.5 py-0.5 rounded-full leading-none">Sekdes</span>
                    </a>
                    @endif
                </div>

                <!-- KATEGORI 5: KOMUNIKASI & SIARAN -->
                <div class="space-y-1 pt-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#C9A84C]">Komunikasi & Siaran</div>
                    <a href="{{ route('pengumuman.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('pengumuman.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span>Pengumuman & Notifikasi</span>
                    </a>
                </div>

                <!-- KATEGORI 6: PENGATURAN & KONFIGURASI SISTEM -->
                <div class="space-y-1 pt-1">
                    <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#C9A84C]">Pengaturan & Konfigurasi</div>
                    <a href="{{ route('konfigurasi-absensi.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('konfigurasi-absensi.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Jam & Waktu Absensi</span>
                    </a>
                    <a href="{{ route('konfigurasi-wifi.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('konfigurasi-wifi.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                        <span>Konfigurasi WiFi Desa</span>
                    </a>
                    <a href="{{ route('konfigurasi-wa.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('konfigurasi-wa.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.554z"/></svg>
                        <span>Konfigurasi WhatsApp</span>
                    </a>
                    <a href="{{ route('attendance.import') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('attendance.import*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Log Absensi Mesin</span>
                    </a>
                    <a href="{{ route('admin.profil') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('admin.profil*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Pengaturan Akun & Profil</span>
                    </a>
                </div>

                <!-- PORTAL ABSENSI LINK (untuk dishare ke staf) -->
                <div class="mt-2 pt-3 border-t border-emerald-800/40">
                    <a href="{{ route('staf.login') }}" target="_blank"
                       class="flex items-center gap-2 px-3 py-2.5 rounded-lg bg-[#C9A84C]/15 border border-[#C9A84C]/30 hover:bg-[#C9A84C]/25 transition-all">
                        <div class="w-6 h-6 rounded-md bg-[#C9A84C] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[#C9A84C] text-[10px] font-bold leading-tight">Portal Absensi Staf</p>
                            <p class="text-emerald-300/50 text-[9px] truncate">/staf/login — via WiFi Desa</p>
                        </div>
                        <svg class="w-3 h-3 text-[#C9A84C]/60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>

            </nav>

            <!-- Footer Sidebar Info -->
            <div class="p-3 border-t border-emerald-800/50 text-center shrink-0">
                <p class="text-[11px] text-emerald-200/80 font-medium">KKN 0226 LP3I Tasikmalaya &copy; 2026</p>
                <p class="text-[10px] text-[#C9A84C] font-semibold">Pemerintah Desa Nangtang</p>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col overflow-y-auto bg-[#F5F0E8]">

            <!-- Top Header Navbar -->
            <header class="bg-[#F5F0E8]/80 backdrop-blur-md sticky top-0 z-30 px-6 py-4 border-b border-[#C9A84C]/20 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-emerald-900 hover:bg-emerald-100/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h2 class="font-outfit text-xl font-bold text-emerald-950 tracking-tight">N-DESAPRESENCE DESA NANGTANG</h2>
                        <p class="text-xs text-slate-500 font-medium">Selamat Datang, {{ auth()->user()->name ?? 'User' }}</p>
                    </div>
                </div>

                <!-- Right Action Bar: Clock/Date badge, Bell, User Profile -->
                <div class="flex items-center gap-4">
                    <!-- Tanggal & Jam Realtime Info -->
                    <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#C9A84C]/30 text-xs font-semibold text-slate-700 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ now()->translatedFormat('l, d M Y') }}</span>
                    </div>

                    <!-- Bell Notifications (Livewire Component with Dynamic Badge & Dropdown) -->
                    <livewire:admin-notification-center />

                    <!-- User Menu Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 pl-1.5 pr-3 py-1.5 bg-white border border-[#C9A84C]/30 rounded-full shadow-sm hover:bg-slate-50 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-[#064E3B] text-[#C9A84C] font-bold flex items-center justify-center text-sm shadow overflow-hidden shrink-0">
                                @if(auth()->user()->foto_profil || (auth()->user()->pegawai && auth()->user()->pegawai->foto_profil))
                                    <img src="{{ Storage::url(auth()->user()->foto_profil ?? auth()->user()->pegawai->foto_profil) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                @endif
                            </div>
                            <div class="text-left hidden md:block">
                                <p class="text-xs font-bold text-emerald-950 leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
                                <p class="text-[10px] text-amber-700 font-semibold uppercase">{{ auth()->user()->role ?? 'Guest' }}</p>
                            </div>
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50" style="display: none;">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'User' }}</p>
                                <p class="text-[11px] text-slate-500">{{ auth()->user()->username ?? '' }}</p>
                            </div>
                            <a href="{{ route('admin.profil') }}" wire:navigate class="w-full text-left px-4 py-2 text-xs text-slate-700 hover:bg-emerald-50 hover:text-[#064E3B] font-semibold flex items-center gap-2 transition">
                                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Pengaturan Akun</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Keluar / Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content View -->
            <main class="flex-1 p-6 md:p-8">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 rounded-r-xl shadow-sm text-emerald-800 text-sm font-medium flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-700 hover:text-emerald-900 text-xs font-bold ml-2">✕</button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-r-xl shadow-sm text-red-800 text-sm font-medium flex items-center justify-between">
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="text-red-700 hover:text-red-900 text-xs font-bold ml-2">✕</button>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div> 

    @livewireScripts
    
    <!-- Real-Time Interactive Toast Notification Listener (No Page Reload Needed) -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Livewire Dispatch Notification Listener
            Livewire.on('notify', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                const message = typeof payload === 'string' ? payload : (payload.message || 'Operasi berhasil dilakukan');
                const type = payload.type || 'success';
                
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type,
                        title: message,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        background: '#FFFFFF',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl border border-slate-200'
                        }
                    });
                }
            });

            Livewire.on('toast', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                const message = typeof payload === 'string' ? payload : (payload.message || 'Operasi berhasil');
                const type = payload.type || 'success';
                
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type,
                        title: message,
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        background: '#FFFFFF',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl border border-slate-200'
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>