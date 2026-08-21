<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Presensi Staf — Desa Nangtang' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Alpine.js & SweetAlert2 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        body { background-color: #F5F0E8; color: #1C2826; }
        .sadi-card { background: #FFFFFF; border-radius: 20px; box-shadow: 0px 4px 20px rgba(27, 77, 62, 0.06); border: 1px solid rgba(201, 168, 76, 0.2); }
        .btn-gold {
            background: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%);
            color: #064E3B;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#F5F0E8] min-h-screen text-slate-800 flex flex-col justify-between">

    <!-- Top Mobile Navigation Header -->
    <header class="bg-[#064E3B] text-white px-5 py-4 sticky top-0 z-30 shadow-lg border-b border-[#C9A84C]/30">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full border-2 border-[#C9A84C] flex items-center justify-center bg-[#04392B] shadow shrink-0">
                    <span class="font-outfit text-xl font-extrabold text-[#C9A84C]">N</span>
                </div>
                <div>
                    <h1 class="font-outfit text-base font-bold tracking-tight text-white leading-tight">DESA NANGTANG</h1>
                    <p class="text-[10px] text-[#C9A84C] tracking-wider font-semibold uppercase">Portal Presensi Digital Staf</p>
                </div>
            </div>

            @auth
            <div class="flex items-center gap-2">
                <form action="{{ route('staf.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar Akun" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-emerald-100 transition text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-lg w-full mx-auto p-4 sm:p-5">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (For Authenticated Staff) -->
    @auth
    <nav class="bg-white border-t-2 border-[#C9A84C] sticky bottom-0 z-30 shadow-2xl py-2.5 px-6">
        <div class="max-w-lg mx-auto flex items-center justify-around">
            <a href="{{ route('staf.beranda') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('staf.beranda*') ? 'text-[#064E3B] font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                <div class="{{ request()->routeIs('staf.beranda*') ? 'p-1.5 rounded-xl bg-emerald-100/80 text-[#064E3B]' : 'p-1.5 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-[11px]">Beranda</span>
            </a>


            {{-- Tab Pengajuan Luar --}}
            @php $pengajuanMenungguStaf = \App\Models\PengajuanAbsenLuar::where('pegawai_id', auth()->user()->pegawai?->id)->where('status','menunggu')->count(); @endphp
            <a href="{{ route('staf.ajukan.form') }}" class="flex flex-col items-center gap-1 relative {{ request()->routeIs('staf.ajukan*','staf.riwayat.pengajuan*') ? 'text-[#064E3B] font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                @if($pengajuanMenungguStaf > 0)
                <span class="absolute -top-1 -right-0 w-4 h-4 bg-red-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center">{{ $pengajuanMenungguStaf }}</span>
                @endif
                <div class="{{ request()->routeIs('staf.ajukan*','staf.riwayat.pengajuan*') ? 'p-1.5 rounded-xl bg-amber-100/80 text-amber-700' : 'p-1.5 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-[11px]">Absen Luar</span>
            </a>

            <a href="{{ route('staf.riwayat') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('staf.riwayat') ? 'text-[#064E3B] font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                <div class="{{ request()->routeIs('staf.riwayat') ? 'p-1.5 rounded-xl bg-emerald-100/80 text-[#064E3B]' : 'p-1.5 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="text-[11px]">Riwayat</span>
            </a>

            <a href="{{ route('staf.profil') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('staf.profil*') ? 'text-[#064E3B] font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold' }}">
                <div class="{{ request()->routeIs('staf.profil*') ? 'p-1.5 rounded-xl bg-emerald-100/80 text-[#064E3B]' : 'p-1.5 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-[11px]">Profil Saya</span>
            </a>
        </div>
    </nav>
    @endauth

    <footer class="text-center py-4 text-[11px] text-slate-400 border-t border-[#C9A84C]/10">
        Pemerintah Desa Nangtang &copy; {{ date('Y') }} — SADI v2.0
    </footer>

    @yield('scripts')
</body>
</html>
