<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Presence Desa — Sistem Absensi Desa Nangtang' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN Fallback for offline/local rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
    <style>
        body { background-color: #F5F0E8; color: #1C2826; }
        .sadi-sidebar { background: linear-gradient(180deg, #064E3B 0%, #04392B 100%); }
        .sadi-card { background: #FFFFFF; border-radius: 16px; box-shadow: 0px 4px 20px rgba(27, 77, 62, 0.05); border: 1px solid rgba(201, 168, 76, 0.15); }
        .custom-sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .custom-sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(201, 168, 76, 0.25); border-radius: 4px; }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(201, 168, 76, 0.5); }

        /* High Contrast Nav Items */
        .sadi-nav-active {
            background-color: #C9A84C !important;
            background-image: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%) !important;
            color: #064E3B !important;
            font-weight: 800 !important;
            box-shadow: 0px 4px 14px rgba(201, 168, 76, 0.45) !important;
        }
        .sadi-nav-active span {
            color: #064E3B !important;
            font-weight: 800 !important;
        }
        .sadi-nav-active svg {
            color: #064E3B !important;
            stroke: #064E3B !important;
        }

        .sadi-nav-inactive {
            color: #E2E8F0 !important;
        }
        .sadi-nav-inactive:hover {
            background-color: rgba(255, 255, 255, 0.12) !important;
            color: #FFFFFF !important;
        }
        .sadi-nav-inactive span {
            color: #E2E8F0 !important;
        }
        .sadi-nav-inactive:hover span {
            color: #FFFFFF !important;
        }
        .sadi-nav-inactive svg {
            color: #A7F3D0 !important;
            stroke: #A7F3D0 !important;
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

        <!-- Sidebar Navigation (Fixed Height & Categorized Menu) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 h-screen sadi-sidebar text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between shadow-2xl shrink-0">
            <!-- Brand Header -->
            <div class="px-5 py-4 border-b border-emerald-800/50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full border-2 border-[#C9A84C] flex items-center justify-center bg-[#04392B] shadow-md shrink-0">
                        <span class="font-outfit text-xl font-extrabold text-[#C9A84C]">N</span>
                    </div>
                    <div class="overflow-hidden">
                        <h1 class="font-outfit text-base font-bold tracking-tight text-white leading-tight truncate">DESA NANGTANG</h1>
                        <p class="text-[10px] text-[#C9A84C] tracking-wider font-semibold uppercase leading-tight truncate">Presence Desa — Presensi Digital</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu (Categorized & Flex-1 Scrollable) -->
            <nav class="px-3 py-3 space-y-4 flex-1 overflow-y-auto custom-sidebar-scroll">

                <!-- KATEGORI 1: UTAMA -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-300/60">Utama</div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Dashboard Real-Time</span>
                    </a>
                    <a href="{{ route('analitik.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('analitik.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Analitik Kedisiplinan</span>
                    </a>
                </div>

                <!-- KATEGORI 2: PRESENSI & KEDINASAN -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-300/60">Presensi & Kedinasan</div>
                    <a href="{{ route('pegawai.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('pegawai.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Master Pegawai</span>
                    </a>
                    <a href="{{ route('matriks.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('matriks.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        <span>Buku Matriks Presensi</span>
                    </a>
                    <a href="{{ route('siltap.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('siltap.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kalkulasi Siltap</span>
                    </a>
                    <a href="{{ route('spt.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('spt.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>SPT Kedinasan</span>
                    </a>
                    <a href="{{ route('izin.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('izin.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Izin & Sakit</span>
                    </a>
                </div>

                <!-- KATEGORI 3: PENGATURAN & OPERASIONAL -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-300/60">Pengaturan & Operasional</div>
                    <a href="{{ route('pengumuman.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('pengumuman.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span>Pengumuman</span>
                    </a>
                    <a href="{{ route('shift.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('shift.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Shift Kerja</span>
                    </a>
                    <a href="{{ route('hari-libur.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('hari-libur.*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Hari Libur</span>
                    </a>
                    <a href="{{ route('attendance.import') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('attendance.import*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Import Log USB</span>
                    </a>
                    <a href="{{ route('attendance.override') }}" class="flex items-center gap-3 px-3 py-2 text-xs rounded-lg transition-all {{ request()->routeIs('attendance.override*') ? 'sadi-nav-active' : 'sadi-nav-inactive' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Override Manual</span>
                    </a>
                </div>

            </nav>

            <!-- Footer Sidebar Info -->
            <div class="p-3 border-t border-emerald-800/50 text-center shrink-0">
                <p class="text-[11px] text-emerald-200/70">KKN Universitas &copy; 2025</p>
                <p class="text-[10px] text-[#C9A84C] font-semibold">Pemerintah Desa Nangtang</p>
            </div>
        </aside>

        <!-- Main Workspace Area (Cream Background 60%) -->
        <div class="flex-1 flex flex-col overflow-y-auto bg-[#F5F0E8]">

            <!-- Top Header Navbar -->
            <header class="bg-[#F5F0E8]/80 backdrop-blur-md sticky top-0 z-30 px-6 py-4 border-b border-[#C9A84C]/20 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-emerald-900 hover:bg-emerald-100/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h2 class="font-outfit text-xl font-bold text-emerald-950 tracking-tight">PRESENCE DESA NANGTANG</h2>
                        <p class="text-xs text-slate-500 font-medium">Selamat Datang, {{ auth()->user()->name ?? 'User' }}</p>
                    </div>
                </div>

                <!-- Right Action Bar: Search, Bell, User Profile -->
                <div class="flex items-center gap-4">
                    <!-- Search Input -->
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="Cari..." class="w-56 pl-9 pr-4 py-2 text-xs rounded-full bg-white/80 border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-700 shadow-sm">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <!-- Bell Notifications -->
                    <button class="p-2 rounded-full bg-white border border-[#C9A84C]/30 text-slate-600 hover:text-emerald-800 transition relative shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- User Menu Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 pl-2 pr-3 py-1.5 bg-white border border-[#C9A84C]/30 rounded-full shadow-sm hover:bg-slate-50 transition">
                            <div class="w-8 h-8 rounded-full bg-[#064E3B] text-[#C9A84C] font-bold flex items-center justify-center text-sm shadow">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
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
                    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 rounded-r-xl shadow-sm text-emerald-800 text-sm font-medium flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-r-xl shadow-sm text-red-800 text-sm font-medium">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
