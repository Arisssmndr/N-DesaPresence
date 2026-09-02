<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'N-DesaPresence — Portal Presensi Staf Desa Nangtang' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tasikmalaya.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Alpine.js & SignaturePad & SweetAlert2 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
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
        .sadi-card { background-color: #FFFFFF; border-radius: 20px; box-shadow: 0px 4px 20px rgba(27, 77, 62, 0.06); border: 1px solid rgba(201, 168, 76, 0.2); }
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
                <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kab. Tasikmalaya" class="h-10 w-auto object-contain shrink-0 filter drop-shadow">
                <div>
                    <h1 class="font-outfit text-base font-bold tracking-tight text-white leading-tight">N-DesaPresence</h1>
                    <p class="text-[10px] text-[#C9A84C] tracking-wider font-semibold uppercase">Portal Presensi Staf & Perangkat</p>
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
    <nav class="bg-white/95 backdrop-blur-md border-t border-slate-200/80 sticky bottom-0 z-40 shadow-[0_-8px_30px_rgba(15,23,42,0.06)] py-2 px-3 sm:px-6">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            
            {{-- 1. Beranda --}}
            @php $isBeranda = request()->routeIs('staf.beranda*'); @endphp
            <a href="{{ route('staf.beranda') }}" class="flex flex-col items-center group transition flex-1 py-1">
                <div class="{{ $isBeranda ? 'w-12 h-8 rounded-xl bg-[#064E3B] text-[#F3E5AB] shadow-sm shadow-emerald-950/20 ring-1 ring-[#C9A84C]/25' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#064E3B] group-hover:bg-slate-100/80' }} flex items-center justify-center transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isBeranda ? '2.2' : '1.8' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="{{ $isBeranda ? 'text-[10.5px] font-bold text-[#064E3B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600' }} mt-1 tracking-tight">Beranda</span>
            </a>

            {{-- 2. Absen Luar (Dinas Luar) --}}
            @php 
                $isAbsenLuar = request()->routeIs('staf.ajukan*', 'staf.riwayat.pengajuan*');
                $pengajuanMenungguStaf = \App\Models\PengajuanAbsenLuar::where('pegawai_id', auth()->user()->pegawai?->id)->where('status','menunggu')->count(); 
            @endphp
            <a href="{{ route('staf.ajukan.form') }}" class="flex flex-col items-center group transition flex-1 py-1 relative">
                @if($pengajuanMenungguStaf > 0)
                <span class="absolute top-0.5 right-2 min-w-[16px] h-4 px-1 bg-rose-500 rounded-full text-[9px] text-white font-bold flex items-center justify-center shadow-xs ring-2 ring-white z-10">{{ $pengajuanMenungguStaf }}</span>
                @endif
                <div class="{{ $isAbsenLuar ? 'w-12 h-8 rounded-xl bg-[#064E3B] text-[#F3E5AB] shadow-sm shadow-emerald-950/20 ring-1 ring-[#C9A84C]/25' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#064E3B] group-hover:bg-slate-100/80' }} flex items-center justify-center transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isAbsenLuar ? '2.2' : '1.8' }}" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isAbsenLuar ? '2.2' : '1.8' }}" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="{{ $isAbsenLuar ? 'text-[10.5px] font-bold text-[#064E3B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600' }} mt-1 tracking-tight">Absen Luar</span>
            </a>

            {{-- 3. Izin & Sakit --}}
            @php $isIzin = request()->routeIs('staf.izin*'); @endphp
            <a href="{{ route('staf.izin') }}" class="flex flex-col items-center group transition flex-1 py-1">
                <div class="{{ $isIzin ? 'w-12 h-8 rounded-xl bg-[#064E3B] text-[#F3E5AB] shadow-sm shadow-emerald-950/20 ring-1 ring-[#C9A84C]/25' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#064E3B] group-hover:bg-slate-100/80' }} flex items-center justify-center transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isIzin ? '2.2' : '1.8' }}" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="{{ $isIzin ? 'text-[10.5px] font-bold text-[#064E3B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600' }} mt-1 tracking-tight">Izin / Sakit</span>
            </a>

            {{-- 4. Riwayat & SPT --}}
            @php $isRiwayat = request()->routeIs('staf.riwayat*', 'staf.spt.riwayat*'); @endphp
            <a href="{{ route('staf.riwayat') }}" class="flex flex-col items-center group transition flex-1 py-1">
                <div class="{{ $isRiwayat ? 'w-12 h-8 rounded-xl bg-[#064E3B] text-[#F3E5AB] shadow-sm shadow-emerald-950/20 ring-1 ring-[#C9A84C]/25' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#064E3B] group-hover:bg-slate-100/80' }} flex items-center justify-center transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isRiwayat ? '2.2' : '1.8' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="{{ $isRiwayat ? 'text-[10.5px] font-bold text-[#064E3B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600' }} mt-1 tracking-tight">Riwayat</span>
            </a>

            {{-- 5. Profil Saya --}}
            @php $isProfil = request()->routeIs('staf.profil*'); @endphp
            <a href="{{ route('staf.profil') }}" class="flex flex-col items-center group transition flex-1 py-1">
                <div class="{{ $isProfil ? 'w-12 h-8 rounded-xl bg-[#064E3B] text-[#F3E5AB] shadow-sm shadow-emerald-950/20 ring-1 ring-[#C9A84C]/25' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#064E3B] group-hover:bg-slate-100/80' }} flex items-center justify-center transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isProfil ? '2.2' : '1.8' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="{{ $isProfil ? 'text-[10.5px] font-bold text-[#064E3B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600' }} mt-1 tracking-tight">Profil</span>
            </a>

        </div>
    </nav>
    @endauth

    <footer class="text-center py-4 text-[11px] text-slate-400 border-t border-[#C9A84C]/10">
        Pemerintah Desa Nangtang &copy; 2026 — N-DesaPresence (KKN 0226 LP3I Tasikmalaya)
    </footer>

    @yield('scripts')
</body>
</html>
