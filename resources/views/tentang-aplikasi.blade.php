<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sistem & Pengembang — N-DesaPresence (SADI v2.0)</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tasikmalaya.png') }}">

    <!-- Google Fonts: Playfair Display (Royal), Outfit (Modern Bold), Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        royalDark: '#021811',
                        royalGreen: '#064E3B',
                        royalEmerald: '#0f766e',
                        royalGold: '#C9A84C',
                        royalGoldLight: '#F3E5AB',
                        royalGoldDark: '#99731C',
                        royalParchment: '#FAF6F0',
                        royalCard: '#FFFFFF',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        cinzel: ['Cinzel', 'serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        'shimmer': 'shimmer 3s ease-in-out infinite',
                        'float': 'float 4s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 2.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        shimmer: {
                            '0%, 100%': { opacity: '0.9', transform: 'scale(1)' },
                            '50%': { opacity: '1', transform: 'scale(1.02)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-6px)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 15px rgba(201, 168, 76, 0.3)' },
                            '50%': { boxShadow: '0 0 30px rgba(201, 168, 76, 0.7)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        body {
            background: radial-gradient(circle at 50% 0%, #064E3B 0%, #032A20 45%, #01140E 100%);
            min-height: 100vh;
            color: #E2E8F0;
            overflow-x: hidden;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Royal Golden Border Filigree */
        .royal-border {
            border: 1px solid rgba(201, 168, 76, 0.4);
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 20px rgba(201, 168, 76, 0.15);
        }

        .gold-gradient-text {
            background: linear-gradient(135deg, #FFF0BD 0%, #E6C875 35%, #C9A84C 70%, #99731C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-foil-bg {
            background: linear-gradient(135deg, #DFBE6A 0%, #C9A84C 50%, #99731C 100%);
        }

        .parchment-pattern {
            background-color: #FAF6F0;
            background-image: radial-gradient(#C9A84C 0.65px, transparent 0.65px), radial-gradient(#064E3B 0.65px, #FAF6F0 0.65px);
            background-size: 26px 26px;
            background-position: 0 0, 13px 13px;
        }

        .wax-seal {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.4), inset 0 -2px 4px rgba(0, 0, 0, 0.4);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #01140E;
        }
        ::-webkit-scrollbar-thumb {
            background: #C9A84C;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="antialiased selection:bg-[#C9A84C] selection:text-[#064E3B]" x-data="{ activeTab: 'persembahan' }">

    <!-- Decorative Top Glowing Orbs -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-[#C9A84C]/10 blur-[140px] rounded-full pointer-events-none z-0"></div>
    <div class="fixed -bottom-20 -left-20 w-96 h-96 bg-[#064E3B]/30 blur-[120px] rounded-full pointer-events-none z-0"></div>
    <div class="fixed -bottom-20 -right-20 w-96 h-96 bg-[#C9A84C]/15 blur-[120px] rounded-full pointer-events-none z-0"></div>

    <!-- Top Navigation Bar (Floating Luxury Glass) -->
    <header class="sticky top-4 z-40 max-w-5xl mx-auto px-4">
        <div class="backdrop-blur-md bg-[#021811]/85 border border-[#C9A84C]/40 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xl">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('staf.login') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-[#C9A84C]/20 border border-[#C9A84C]/30 text-xs font-bold text-[#F3E5AB] transition duration-200 group">
                <svg class="w-4 h-4 text-[#C9A84C] group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="font-cinzel text-[11px] sm:text-xs font-bold tracking-widest text-[#F3E5AB] uppercase">SADI v2.0 Enterprise</span>
            </div>

            <div class="flex items-center gap-1">
                <a href="{{ route('staf.login') }}" class="text-[11px] font-bold text-slate-300 hover:text-[#F3E5AB] px-2.5 py-1 rounded-lg hover:bg-white/5 transition">Portal Staf</a>
                <span class="text-slate-600">•</span>
                <a href="{{ route('login') }}" class="text-[11px] font-bold text-slate-300 hover:text-[#F3E5AB] px-2.5 py-1 rounded-lg hover:bg-white/5 transition">Admin</a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="relative z-10 max-w-5xl mx-auto px-4 pt-8 pb-20 space-y-10">

        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- HEADER PROTOKOLER: 3 LOGO RESMI KEMITRAAN (KIRI KE KANAN)                 -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <section class="text-center space-y-6 pt-4">
            
            <!-- Tiga Logo Resmi Berurutan (LP3I -> Kab. Tasikmalaya -> KKN Nangtang) -->
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-8 md:gap-12 py-4">
                
                <!-- 1. KIRI: LOGO POLITEKNIK LP3I -->
                <div class="flex flex-col items-center space-y-2 group">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 p-3 rounded-2xl bg-white/95 border-2 border-[#C9A84C]/60 shadow-xl flex items-center justify-center backdrop-blur-sm group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/logo-lp3i.png') }}" alt="Logo Politeknik LP3I" class="max-h-full max-w-full object-contain filter drop-shadow-sm">
                    </div>
                    <span class="text-[10px] font-bold tracking-wider text-slate-300 uppercase font-cinzel">POLITEKNIK LP3I</span>
                    <span class="text-[9px] text-[#C9A84C] -mt-1 font-medium">Institusi Akademik</span>
                </div>

                <!-- Silang Emas / Kolaborasi 1 -->
                <div class="hidden sm:flex flex-col items-center justify-center text-[#C9A84C]">
                    <span class="font-serif italic text-lg opacity-80">✕</span>
                </div>

                <!-- 2. TENGAH: LOGO KABUPATEN TASIKMALAYA (UTAMA / LEBIH MENONJOL) -->
                <div class="flex flex-col items-center space-y-2 group -mt-2">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 p-3 rounded-2xl bg-white/95 border-2 border-[#C9A84C] shadow-2xl flex items-center justify-center backdrop-blur-sm group-hover:scale-105 transition-transform duration-300 animate-pulse-glow">
                        <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kabupaten Tasikmalaya" class="max-h-full max-w-full object-contain filter drop-shadow-md">
                    </div>
                    <span class="text-[11px] font-extrabold tracking-wider text-[#FFF0BD] uppercase font-cinzel">KAB. TASIKMALAYA</span>
                    <span class="text-[9.5px] text-[#C9A84C] -mt-1 font-semibold">Pemerintah Daerah</span>
                </div>

                <!-- Silang Emas / Kolaborasi 2 -->
                <div class="hidden sm:flex flex-col items-center justify-center text-[#C9A84C]">
                    <span class="font-serif italic text-lg opacity-80">✕</span>
                </div>

                <!-- 3. KANAN: LOGO KKN DESA NANGTANG -->
                <div class="flex flex-col items-center space-y-2 group">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 p-2 rounded-2xl bg-white/95 border-2 border-[#C9A84C]/60 shadow-xl flex items-center justify-center backdrop-blur-sm group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/logo-kkn-nangtang.png') }}" alt="Logo KKN Desa Nangtang" class="max-h-full max-w-full object-contain filter drop-shadow-sm rounded-xl">
                    </div>
                    <span class="text-[10px] font-bold tracking-wider text-slate-300 uppercase font-cinzel">DESA NANGTANG</span>
                    <span class="text-[9px] text-[#C9A84C] -mt-1 font-medium">Mitra Pengabdian KKN</span>
                </div>

            </div>

            <!-- Judul & Subjudul Prestisius -->
            <div class="space-y-2 max-w-2xl mx-auto px-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#C9A84C]/15 border border-[#C9A84C]/40 text-[#F3E5AB] text-[11px] font-bold tracking-wider uppercase">
                    <span>★ Mahakarya Kolaborasi KKN Tematik 2026 ★</span>
                </div>
                <h1 class="font-cinzel text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight gold-gradient-text">
                    N-DESAPRESENCE
                </h1>
                <p class="font-serif italic text-sm sm:text-base text-emerald-100/90 leading-relaxed">
                    "Sistem Absensi Desa Integratif (SADI v2.0) — Transformasi Tata Kelola Kepegawaian & Kedisiplinan Berkeadilan Pemerintahan Desa Modern"
                </p>
            </div>

            <!-- Navigasi Tab Eksklusif (Gaya Segel Kerajaan) -->
            <div class="flex items-center justify-center gap-2 pt-2">
                <div class="p-1.5 rounded-2xl bg-black/40 border border-[#C9A84C]/30 backdrop-blur-md inline-flex flex-wrap justify-center gap-1 shadow-xl">
                    
                    <button @click="activeTab = 'persembahan'"
                            :class="activeTab === 'persembahan' ? 'bg-gradient-to-r from-[#C9A84C] to-[#99731C] text-[#021811] font-extrabold shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/5 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Surat Dedikasi</span>
                    </button>

                    <button @click="activeTab = 'arsitek'"
                            :class="activeTab === 'arsitek' ? 'bg-gradient-to-r from-[#C9A84C] to-[#99731C] text-[#021811] font-extrabold shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/5 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Sang Arsitek (Aris Munandar)</span>
                    </button>

                    <button @click="activeTab = 'spesifikasi'"
                            :class="activeTab === 'spesifikasi' ? 'bg-gradient-to-r from-[#C9A84C] to-[#99731C] text-[#021811] font-extrabold shadow-lg' : 'text-slate-300 hover:text-white hover:bg-white/5 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        <span>Spesifikasi Sistem & Mesin</span>
                    </button>

                </div>
            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 1: SURAT DEDIKASI RESMI (GAYA UNDANGAN MEWAH & WAX SEAL)               -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <section x-show="activeTab === 'persembahan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="space-y-6">
            
            <div class="relative bg-[#FAF6F0] text-slate-800 rounded-3xl p-6 sm:p-10 md:p-12 border-4 border-[#C9A84C] shadow-2xl overflow-hidden parchment-pattern">
                
                <!-- Ornamen Emas Sudut Kuno (Four Corners) -->
                <div class="absolute top-2 left-2 text-[#C9A84C] text-xl select-none opacity-80">╔══</div>
                <div class="absolute top-2 right-2 text-[#C9A84C] text-xl select-none opacity-80">══╗</div>
                <div class="absolute bottom-2 left-2 text-[#C9A84C] text-xl select-none opacity-80">╚══</div>
                <div class="absolute bottom-2 right-2 text-[#C9A84C] text-xl select-none opacity-80">══╝</div>

                <!-- Watermark Logo Samar di Tengah Kartu -->
                <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                    <img src="{{ asset('images/logo-tasikmalaya.png') }}" class="w-96 h-96 object-contain" alt="">
                </div>

                <div class="relative z-10 space-y-6 text-center">
                    
                    <!-- Wax Seal Badge Logo -->
                    <div class="w-20 h-20 mx-auto rounded-full gold-foil-bg flex items-center justify-center wax-seal border-2 border-white/60 shadow-xl">
                        <div class="w-16 h-16 rounded-full border border-[#064E3B]/40 flex flex-col items-center justify-center text-[#064E3B]">
                            <span class="font-cinzel text-xs font-black tracking-widest">SADI</span>
                            <span class="text-[9px] font-bold">2026</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="font-serif italic text-xs text-[#064E3B] font-semibold tracking-widest uppercase">
                            — Lembar Persembahan & Dedikasi Karya —
                        </p>
                        <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#064E3B] tracking-tight">
                            Bismillaahirrahmaanirrahiim
                        </h2>
                    </div>

                    <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-[#C9A84C] to-transparent mx-auto"></div>

                    <!-- Teks Narasi Yang Sangat Menyentuh & Puitis -->
                    <div class="max-w-2xl mx-auto font-serif text-slate-700 text-sm sm:text-base leading-relaxed space-y-4 text-justify sm:text-center">
                        <p>
                            Dengan memohon rahmat dan ridho Allah Subhanahu wa Ta'ala, sistem informasi 
                            <strong class="text-[#064E3B] font-bold">N-DesaPresence (Sistem Absensi Desa Integratif v2.0)</strong> 
                            ini lahir dari sebuah ketulusan hati, komitmen keilmuan, dan tekad pengabdian tanpa batas untuk kemajuan bangsa.
                        </p>
                        <p>
                            Aplikasi ini merupakan wujud nyata integrasi dan implementasi disiplin ilmu 
                            <strong>Rekayasa Perangkat Lunak & Sistem Informasi</strong> yang kami pelajari di 
                            <strong class="text-[#064E3B]">Politeknik LP3I Kampus Tasikmalaya</strong>, 
                            yang dipadukan dengan kehangatan kolaborasi bersama segenap jajaran 
                            <strong class="text-[#064E3B]">Pemerintah Desa Nangtang, Kecamatan Cigalontang, Kabupaten Tasikmalaya</strong> 
                            dalam bingkai Program Kuliah Kerja Nyata (KKN) Tematik Tahun 2026.
                        </p>
                        <p>
                            Kami persembahkan teknologi ini sebagai pilar digitalisasi tata kelola birokrasi desa modern—yang 
                            menjunjung tinggi nilai kedisiplinan berkeadilan, transparansi akuntabel, kemudahan pelayanan presensi mandiri, 
                            serta penghargaan penuh bagi setiap pengabdian aparatur desa yang tulus melayani masyarakat.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#C9A84C]/30 max-w-lg mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-serif text-slate-600">
                        <div class="text-center sm:text-left">
                            <span class="text-[10.5px] text-slate-400 uppercase tracking-wider block">Tempat & Tanggal Pengesahan:</span>
                            <strong class="text-slate-800 font-bold">Balai Desa Nangtang, September 2026</strong>
                        </div>
                        <div class="text-center sm:text-right">
                            <span class="text-[10.5px] text-slate-400 uppercase tracking-wider block">Kemitraan Strategis:</span>
                            <strong class="text-[#064E3B] font-bold">LP3I Tasikmalaya × Pemdes Nangtang</strong>
                        </div>
                    </div>

                </div>

            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: PROFIL SANG ARSITEK & PENGEMBANG (ARIS MUNANDAR)                     -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <section x-show="activeTab === 'arsitek'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="space-y-6" x-cloak>
            
            <div class="bg-gradient-to-br from-[#022017] via-[#064E3B] to-[#043327] rounded-3xl p-6 sm:p-10 border-2 border-[#C9A84C]/60 shadow-2xl relative overflow-hidden text-white">
                
                <!-- Background Gold Glow -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#C9A84C]/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                    
                    <!-- Left: Avatar Hologram & Seal -->
                    <div class="md:col-span-5 flex flex-col items-center text-center space-y-4">
                        <div class="relative">
                            <!-- Golden Ring Frame -->
                            <div class="w-36 h-36 sm:w-44 sm:h-44 rounded-full p-1.5 gold-foil-bg shadow-2xl animate-pulse-glow flex items-center justify-center">
                                <div class="w-full h-full rounded-full bg-[#021811] border-2 border-[#FFF0BD] flex flex-col items-center justify-center text-white overflow-hidden relative group">
                                    <span class="font-cinzel text-4xl sm:text-5xl font-black gold-gradient-text tracking-wider">AM</span>
                                    <span class="text-[9.5px] font-mono tracking-widest text-[#F3E5AB] mt-1">LEAD ARCHITECT</span>
                                </div>
                            </div>
                            
                            <!-- Verified Badge -->
                            <div class="absolute bottom-1 right-2 w-9 h-9 rounded-full bg-[#C9A84C] text-[#021811] border-2 border-white flex items-center justify-center shadow-lg" title="Verified Lead Developer">
                                <svg class="w-5 h-5 font-bold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h3 class="font-cinzel text-xl sm:text-2xl font-bold gold-gradient-text">Aris Munandar</h3>
                            <p class="text-xs text-emerald-200 font-mono tracking-wider">Lead Software Engineer & System Architect</p>
                            <span class="inline-block px-3 py-0.5 rounded-full bg-[#C9A84C]/20 border border-[#C9A84C]/40 text-[#FFF0BD] text-[10.5px] font-bold">
                                Mahasiswa Politeknik LP3I Tasikmalaya
                            </span>
                        </div>
                    </div>

                    <!-- Right: Biodata, Visi, & Narasi Pribadi -->
                    <div class="md:col-span-7 space-y-5 text-xs sm:text-sm">
                        
                        <!-- Mini Bio Table -->
                        <div class="p-4 rounded-2xl bg-black/30 border border-[#C9A84C]/30 space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-white/10">
                                <span class="text-slate-400 font-medium">Tempat, Tanggal Lahir:</span>
                                <span class="font-bold text-[#FFF0BD]">Ciamis, 30 Maret 2006</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-white/10">
                                <span class="text-slate-400 font-medium">Latar Belakang Keluarga:</span>
                                <span class="font-bold text-slate-200">Anak ke-2 dari 3 Bersaudara</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-white/10">
                                <span class="text-slate-400 font-medium">Fokus Keahlian:</span>
                                <span class="font-mono font-bold text-emerald-300">Enterprise Web Architecture & Hardware IoT</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Afiliasi Program:</span>
                                <span class="font-bold text-[#FFF0BD]">KKN Tematik Desa Nangtang 2026</span>
                            </div>
                        </div>

                        <!-- Visi & Kutipan Inspiratif -->
                        <div class="relative p-4 rounded-2xl bg-gradient-to-r from-[#C9A84C]/10 to-transparent border-l-4 border-[#C9A84C] space-y-2">
                            <p class="font-serif italic text-emerald-100 text-xs sm:text-sm leading-relaxed">
                                "Lahir di Ciamis sebagai anak kedua dari tiga bersaudara dengan mimpi besar untuk menjadi seorang 
                                <strong class="text-[#FFF0BD]">Software Engineer kelas dunia</strong>. Saya meyakini bahwa baris-baris kode bukan sekadar sintaks, 
                                melainkan amanah untuk menghadirkan solusi nyata bagi peradaban. Aplikasi N-DesaPresence ini adalah bukti cinta dan persembahan 
                                intelektual kami bagi masyarakat Desa Nangtang."
                            </p>
                            <p class="text-[11px] font-mono text-[#C9A84C] text-right font-bold">— Aris Munandar, 2026</p>
                        </div>

                        <!-- Badges Keahlian & Teknologi -->
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block font-cinzel">Arsitektur & Tech Stack:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-900/60 border border-emerald-500/40 text-emerald-200 text-[10.5px] font-mono">Laravel 12 Enterprise</span>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-900/60 border border-emerald-500/40 text-emerald-200 text-[10.5px] font-mono">Livewire 3 Reactive</span>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-900/60 border border-emerald-500/40 text-emerald-200 text-[10.5px] font-mono">Hardware RS232 Serial</span>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-900/60 border border-emerald-500/40 text-emerald-200 text-[10.5px] font-mono">Fonnte WA Gateway API</span>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-900/60 border border-emerald-500/40 text-emerald-200 text-[10.5px] font-mono">AES-256 Crypto Vault</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: SPESIFIKASI SISTEM & MESIN (GAYA ABOUT PHONE / FLAGSHIP SPECS)      -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <section x-show="activeTab === 'spesifikasi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="space-y-6" x-cloak>
            
            <div class="bg-white text-slate-800 rounded-3xl p-6 sm:p-8 border border-[#C9A84C]/40 shadow-2xl space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-[#064E3B] text-[#FFF0BD] flex items-center justify-center font-bold shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-outfit text-lg font-extrabold text-[#064E3B]">Spesifikasi Sistem & Perangkat Lunak</h3>
                            <p class="text-xs text-slate-500">Informasi teknis dan build spesifikasi resmi N-DesaPresence Engine</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-900 text-xs font-bold font-mono">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-ping"></span>
                        <span>STATUS: PRODUCTION BUILD</span>
                    </div>
                </div>

                <!-- Grid Spesifikasi 2 Kolom (Ala Menu About Device Smartphone) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    
                    <!-- Item 1: Nama Sistem -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nama Resmi Sistem</span>
                        <p class="font-outfit font-extrabold text-slate-900 text-sm">N-DesaPresence</p>
                        <p class="text-[11px] text-slate-500">Sistem Absensi Desa Integratif (SADI)</p>
                    </div>

                    <!-- Item 2: Versi Rilis & Code Name -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Versi Rilis & Code Name</span>
                        <p class="font-mono font-bold text-[#064E3B] text-sm">SADI v2.0 Enterprise Release</p>
                        <p class="text-[11px] text-slate-500">Build: 2026.09.02 (Nangtang Golden Crest)</p>
                    </div>

                    <!-- Item 3: Backend Framework -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Framework Backend</span>
                        <p class="font-mono font-bold text-slate-900 text-sm">Laravel 12.x Core Architecture</p>
                        <p class="text-[11px] text-slate-500">PHP 8.4+ Engine with JIT Optimization</p>
                    </div>

                    <!-- Item 4: Frontend Reaktivitas -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Frontend & Interaktivitas</span>
                        <p class="font-mono font-bold text-slate-900 text-sm">Livewire 3.x + Alpine.js 3.x</p>
                        <p class="text-[11px] text-slate-500">TailwindCSS Ultra-Luxury Protocol (60-30-10)</p>
                    </div>

                    <!-- Item 5: Hardware Serial Engine -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Biometric Ingestion Hardware</span>
                        <p class="font-mono font-bold text-emerald-800 text-sm">ZKTeco & MAGIC Series RS232 Serial</p>
                        <p class="text-[11px] text-slate-500">Command: <code class="bg-slate-200 px-1 py-0.5 rounded font-bold text-slate-700">php artisan fingerprint:listen</code></p>
                    </div>

                    <!-- Item 6: WhatsApp Gateway -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">WhatsApp Cloud Gateway</span>
                        <p class="font-mono font-bold text-emerald-800 text-sm">Fonnte Remote Multi-Device API</p>
                        <p class="text-[11px] text-slate-500">Auto QR-Polling, Real-time Synchronous Broadcast</p>
                    </div>

                    <!-- Item 7: Security Vault -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Enkripsi & Keamanan Data</span>
                        <p class="font-mono font-bold text-slate-900 text-sm">AES-256-CBC Cryptographic Suite</p>
                        <p class="text-[11px] text-slate-500">Digital Signature Pad SHA-256 + Permanent Audit Trail</p>
                    </div>

                    <!-- Item 8: Database Engine -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Penyimpanan Basis Data</span>
                        <p class="font-mono font-bold text-slate-900 text-sm">MySQL 8.0 InnoDb Relational</p>
                        <p class="text-[11px] text-slate-500">Double-Layer Persistent Local Device Cache</p>
                    </div>

                </div>

                <!-- Footer Watermark Box -->
                <div class="p-4 rounded-2xl bg-[#064E3B]/10 border border-[#064E3B]/20 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#064E3B]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-bold text-[#064E3B]">Sertifikasi Keaslian & Lisensi Pengabdian Pemerintah Desa Nangtang 2026</span>
                    </div>
                    <span class="font-mono text-slate-500 text-[11px]">Developer: Aris Munandar</span>
                </div>

            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- FOOTER RESMI                                                                -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <footer class="text-center space-y-3 pt-6 border-t border-[#C9A84C]/30 text-xs text-slate-400">
            <div class="flex items-center justify-center gap-3">
                <img src="{{ asset('images/logo-lp3i.png') }}" alt="" class="h-6 w-auto object-contain opacity-75">
                <span class="text-[#C9A84C] font-serif">✕</span>
                <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="" class="h-6 w-auto object-contain opacity-75">
                <span class="text-[#C9A84C] font-serif">✕</span>
                <img src="{{ asset('images/logo-kkn-nangtang.png') }}" alt="" class="h-6 w-auto object-contain opacity-75 rounded">
            </div>
            <p class="font-serif italic text-slate-300">
                "Karya Inovasi Teknologi Mahasiswa KKN Politeknik LP3I Tasikmalaya untuk Kemakmuran dan Kemajuan Desa Nangtang."
            </p>
            <p class="text-[11px] text-slate-500 font-mono">
                Crafted with Passion by <strong>Aris Munandar</strong> &copy; 2026 Pemerintah Desa Nangtang. All Rights Reserved.
            </p>
        </footer>

    </main>

</body>
</html>
