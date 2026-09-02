<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang N-DESAPRESENCE — Lembar Dedikasi & Profil Pengembang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tasikmalaya.png') }}">

    <!-- Google Fonts: Cinzel (Royal), Playfair Display (Serif Mewah), Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emeraldDark: '#01170F',
                        emeraldRich: '#064E3B',
                        emeraldMedium: '#0d5c46',
                        goldPure: '#C9A84C',
                        goldLight: '#FFF2C6',
                        goldMuted: '#E5C978',
                        goldDark: '#8F6812',
                        creamParchment: '#FAF6F0',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        cinzel: ['Cinzel', 'serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
                    },
                    keyframes: {
                        pulseGlow: {
                            '0%, 100%': { filter: 'drop-shadow(0 0 15px rgba(201, 168, 76, 0.45))' },
                            '50%': { filter: 'drop-shadow(0 0 35px rgba(201, 168, 76, 0.85))' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: radial-gradient(ellipse at 50% 0%, #064E3B 0%, #02251A 50%, #01120B 100%);
            min-height: 100vh;
            color: #E2E8F0;
            overflow-x: hidden;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .gold-gradient {
            background: linear-gradient(135deg, #FFF6D6 0%, #E6C875 35%, #C9A84C 70%, #99731C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-foil-bg {
            background: linear-gradient(135deg, #DFBE6A 0%, #C9A84C 50%, #99731C 100%);
        }

        /* Surat Piagam Emas Mewah */
        .parchment-letter {
            background-color: #FAF6F0;
            background-image: radial-gradient(#C9A84C 0.65px, transparent 0.65px), radial-gradient(#064E3B 0.65px, #FAF6F0 0.65px);
            background-size: 26px 26px;
            background-position: 0 0, 13px 13px;
            border: 2.5px solid #C9A84C;
            box-shadow: inset 0 0 50px rgba(201, 168, 76, 0.15), 0 25px 60px -10px rgba(0, 0, 0, 0.85), 0 0 35px rgba(201, 168, 76, 0.3);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #01120B; }
        ::-webkit-scrollbar-thumb { background: #C9A84C; border-radius: 9999px; }
    </style>
</head>
<body class="antialiased selection:bg-[#C9A84C] selection:text-[#064E3B]">

    <!-- Decorative Glowing Ambient Orbs -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[400px] bg-[#C9A84C]/15 blur-[160px] rounded-full pointer-events-none z-0"></div>
    <div class="fixed top-1/3 -left-32 w-80 h-80 bg-[#064E3B]/40 blur-[130px] rounded-full pointer-events-none z-0"></div>
    <div class="fixed bottom-20 -right-32 w-96 h-96 bg-[#C9A84C]/15 blur-[140px] rounded-full pointer-events-none z-0"></div>

    <!-- Main Container -->
    <main class="relative z-10 max-w-4xl mx-auto px-4 pt-12 sm:pt-16 pb-28 space-y-16 sm:space-y-20">

        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- 1. TIGA LOGO KEMITRAAN (UKURAN 100% SAMA & CAHAYA EMAS MEWAH SELARAS)       -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <section class="text-center space-y-12 sm:space-y-16">
            
            <!-- Flex 3 Logo: Semua Ukuran 100% Identik & Glow Emas Harmonis -->
            <div class="flex items-center justify-center gap-3 sm:gap-6 md:gap-10">
                
                <!-- 1. KIRI: LOGO POLITEKNIK LP3I -->
                <div class="flex-1 flex flex-col items-center space-y-3">
                    <div class="h-32 sm:h-40 md:h-44 w-32 sm:w-40 md:w-44 flex items-center justify-center transition-transform hover:scale-105 duration-300"
                         style="filter: drop-shadow(0 0 20px rgba(201, 168, 76, 0.55)) drop-shadow(0 8px 24px rgba(0, 0, 0, 0.75));">
                        <img src="{{ asset('images/logo-lp3i.png') }}" alt="Logo Politeknik LP3I" class="max-h-full max-w-full object-contain">
                    </div>
                    <span class="text-xs sm:text-sm font-bold tracking-wider text-[#FFF2C6] uppercase font-cinzel block">
                        POLITEKNIK LP3I
                    </span>
                </div>

                <!-- Simbol Kolaborasi Emas 1 -->
                <div class="text-[#C9A84C] font-serif italic text-2xl sm:text-3xl opacity-80 self-center pb-8 shrink-0">✕</div>

                <!-- 2. TENGAH: LOGO KABUPATEN TASIKMALAYA (PRESISI CENTER) -->
                <div class="flex-1 flex flex-col items-center space-y-3">
                    <div class="h-32 sm:h-40 md:h-44 w-32 sm:w-40 md:w-44 flex items-center justify-center animate-pulse-glow transition-transform hover:scale-105 duration-300"
                         style="filter: drop-shadow(0 0 20px rgba(201, 168, 76, 0.55)) drop-shadow(0 8px 24px rgba(0, 0, 0, 0.75));">
                        <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kabupaten Tasikmalaya" class="max-h-full max-w-full object-contain">
                    </div>
                    <span class="text-xs sm:text-sm md:text-base font-black tracking-widest text-[#FFF2C6] uppercase font-cinzel block">
                        KAB. TASIKMALAYA
                    </span>
                </div>

                <!-- Simbol Kolaborasi Emas 2 -->
                <div class="text-[#C9A84C] font-serif italic text-2xl sm:text-3xl opacity-80 self-center pb-8 shrink-0">✕</div>

                <!-- 3. KANAN: LOGO KKN 0226 LP3I (BULAT TRANSPARAN UTUH) -->
                <div class="flex-1 flex flex-col items-center space-y-3">
                    <div class="h-32 sm:h-40 md:h-44 w-32 sm:w-40 md:w-44 flex items-center justify-center transition-transform hover:scale-105 duration-300"
                         style="filter: drop-shadow(0 0 20px rgba(201, 168, 76, 0.55)) drop-shadow(0 8px 24px rgba(0, 0, 0, 0.75));">
                        <img src="{{ asset('images/logo-kkn-nangtang.png') }}" alt="Logo KKN 0226 LP3I" class="max-h-full max-w-full object-contain">
                    </div>
                    <span class="text-xs sm:text-sm font-bold tracking-wider text-[#FFF2C6] uppercase font-cinzel block">
                        KKN 0226 LP3I
                    </span>
                </div>

            </div>

            <!-- Judul Resmi Aplikasi: N-DESAPRESENCE -->
            <div class="space-y-3 max-w-2xl mx-auto pt-8 sm:pt-12">
                <h1 class="font-cinzel text-4xl sm:text-5xl md:text-6xl font-black tracking-tight gold-gradient drop-shadow-lg">
                    N-DESAPRESENCE
                </h1>
                <p class="font-serif italic text-sm sm:text-base md:text-lg text-emerald-100/95 leading-relaxed">
                    "Sistem Presensi Digital & Administrasi Pemerintahan Desa Nangtang"
                </p>
            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- 2. SATU LEMBAR SURAT RESMI DEDIKASI & TANDA TANGAN ASLI ARIS MUNANDAR      -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <section class="pt-2">
            
            <div class="parchment-letter text-slate-800 rounded-3xl p-6 sm:p-10 md:p-14 relative overflow-hidden space-y-8">
                
                <!-- Ornamen Sudut Emas Kuno -->
                <div class="absolute top-2 left-3 text-[#C9A84C] font-mono text-xl select-none opacity-80">╔══</div>
                <div class="absolute top-2 right-3 text-[#C9A84C] font-mono text-xl select-none opacity-80">══╗</div>
                <div class="absolute bottom-2 left-3 text-[#C9A84C] font-mono text-xl select-none opacity-80">╚══</div>
                <div class="absolute bottom-2 right-3 text-[#C9A84C] font-mono text-xl select-none opacity-80">══╝</div>

                <!-- Watermark Lambang di Tengah Kertas -->
                <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                    <img src="{{ asset('images/logo-tasikmalaya.png') }}" class="w-96 h-96 object-contain" alt="">
                </div>

                <!-- Header Surat: Logo KKN Desa Nangtang Bulat Murni -->
                <div class="relative z-10 text-center space-y-3 pb-4 border-b border-[#C9A84C]/40">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto flex items-center justify-center filter drop-shadow-[0_4px_12px_rgba(0,0,0,0.12)]">
                        <img src="{{ asset('images/logo-kkn-nangtang.png') }}" alt="Logo KKN 0226 LP3I" class="max-h-full max-w-full object-contain">
                    </div>
                    <div>
                        <span class="font-serif italic text-xs text-[#064E3B] font-bold tracking-widest uppercase block">
                            — Lembar Prakata & Catatan Pengembang —
                        </span>
                        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-[#064E3B] tracking-tight mt-1">
                            Bismillaahirrahmaanirrahiim
                        </h2>
                    </div>
                </div>

                <!-- ISI SURAT TUNGGAL YANG MENGALIR INDAH, AKURAT & MENYENTUH -->
                <div class="relative z-10 font-serif text-slate-700 text-sm sm:text-base leading-relaxed space-y-6 text-justify">
                    
                    <!-- PARAGRAF 1: RANGKUMAN UTUH FITUR MODERN N-DESAPRESENCE (TANPA FINGERPRINT) -->
                    <p>
                        <strong class="text-[#064E3B] font-bold">N-DESAPRESENCE</strong> 
                        merupakan sistem tata kelola presensi dan administrasi kepegawaian digital terpadu yang dirancang khusus untuk memodernisasi birokrasi aparatur 
                        Pemerintah Desa Nangtang. Dibangun dengan arsitektur web modern yang responsif, sistem ini mengintegrasikan portal mandiri presensi staf 
                        berbasis validasi jaringan WiFi resmi kantor desa, manajemen persetujuan izin, cuti, dan pengajuan tugas dinas luar (SPT), pengelolaan jadwal piket jaga malam dengan otomasi kompensasi <em>Lepas Piket</em>, 
                        pusat rekapitulasi buku matriks presensi serta analitik kedisiplinan berstandar kedinasan, hingga siaran notifikasi WhatsApp Gateway resmi secara <em>real-time</em> 
                        guna mewujudkan pelayanan birokrasi pemerintahan desa yang transparan, disiplin, dan akuntabel.
                    </p>

                    <!-- PARAGRAF 2: PENGENALAN SANG ARSITEK / PENGEMBANG (ARIS MUNANDAR) -->
                    <p>
                        Sistem dan website ini dirancang, dibangun, dan dikembangkan dengan penuh ketulusan oleh seorang anak desa bernama 
                        <strong class="text-[#064E3B] font-bold">Aris Munandar</strong>, mahasiswa Program Kuliah Kerja Nyata (KKN) 
                        <strong>Politeknik LP3I Kampus Tasikmalaya</strong>. Lahir di <strong class="text-slate-900 font-bold">Ciamis pada tanggal 30 Maret 2006</strong>, 
                        sebagai anak ke-2 dari 3 bersaudara yang memiliki tekad baja dan mimpi besar untuk menjadi seorang 
                        <strong class="text-[#064E3B]">Software Engineer</strong> yang mampu menciptakan karya teknologi nyata dan bermanfaat bagi kemaslahatan masyarakat luas.
                    </p>

                    <!-- PARAGRAF 3: PERSEMBAHAN KARYA & KOLABORASI KKN DENGAN PEMDES NANGTANG -->
                    <p>
                        Karya inovasi ini merupakan wujud nyata implementasi disiplin ilmu <em>Software Engineering</em> dan Sistem Informasi yang kami pelajari di bangku kuliah, 
                        yang kami persembahkan secara utuh dalam kolaborasi pengabdian bersama segenap jajaran 
                        <strong class="text-[#064E3B]">Pemerintah Desa Nangtang, Kecamatan Cigalontang, Kabupaten Tasikmalaya</strong>. 
                        Semoga N-DESAPRESENCE menjadi pilar kemajuan digitalisasi tata kelola desa, membawa keberkahan, serta mempermudah seluruh pelayanan administrasi demi kemakmuran masyarakat Desa Nangtang.
                    </p>

                </div>

                <!-- FOOTER TANDA TANGAN RESMI DI SISI KANAN BAWAH (PRESISI & TANPA 'HORMAT KAMI') -->
                <div class="relative z-10 pt-6 border-t border-[#C9A84C]/40 flex justify-end">
                    <div class="w-64 sm:w-72 text-center space-y-1.5">
                        <p class="text-slate-600 text-xs font-serif">Desa Nangtang, September 2026</p>
                        <p class="text-[11px] text-slate-600 font-sans uppercase tracking-wider font-bold">
                            Lead Software Engineer
                        </p>
                        
                        <!-- Tanda Tangan Asli Aris Munandar -->
                        <div class="h-20 sm:h-24 w-auto flex items-center justify-center py-1">
                            <img src="{{ asset('images/ttd-aris-munandar.png') }}" alt="Tanda Tangan Aris Munandar" class="h-full w-auto object-contain">
                        </div>

                        <div class="pt-0.5 space-y-0.5">
                            <span class="font-cinzel text-lg sm:text-xl font-bold text-[#064E3B] tracking-wider block">
                                ARIS MUNANDAR
                            </span>
                            <p class="text-[10.5px] text-slate-500 font-mono">Ciamis, 30 Maret 2006 &bull; Lead Developer</p>
                        </div>
                    </div>
                </div>

            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <!-- 3. FOOTER RESMI                                                             -->
        <!-- ═══════════════════════════════════════════════════════════════════════════ -->
        <footer class="text-center space-y-2 pt-4 border-t border-[#C9A84C]/30 text-xs text-slate-400">
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
