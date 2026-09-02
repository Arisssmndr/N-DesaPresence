<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak — N-DesaPresence Desa Nangtang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tasikmalaya.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #064E3B 0%, #04392B 60%, #022B1F 100%); }
        .card-glass {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(201,168,76,0.25);
        }
        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.08); opacity: 0.4; }
            100% { transform: scale(1); opacity: 0.8; }
        }
        .pulse-anim { animation: pulse-ring 2.5s ease-in-out infinite; }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }
        .shake { animation: shake 0.6s ease-in-out; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md mx-auto">

        {{-- Logo Desa --}}
        <div class="text-center mb-6">
            <div class="mb-3 flex justify-center">
                <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kab. Tasikmalaya" class="h-20 w-auto object-contain filter drop-shadow-md">
            </div>
            <p class="text-[#C9A84C] text-xs font-semibold tracking-widest uppercase">N-DesaPresence — Desa Nangtang</p>
        </div>

        {{-- Error Card --}}
        <div class="card-glass rounded-3xl p-8 text-center shadow-2xl">

            {{-- Ikon Sinyal Blokir --}}
            <div class="relative flex items-center justify-center mb-6">
                <div class="absolute w-24 h-24 rounded-full border-4 border-red-400/30 pulse-anim"></div>
                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center border-2 border-red-400/50 shake">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M8.464 8.464a5 5 0 000 7.072M5.636 5.636a9 9 0 000 12.728"/>
                    </svg>
                </div>
            </div>

            <h1 class="font-outfit text-2xl font-extrabold text-white mb-2">Jaringan Tidak Diizinkan</h1>
            <p class="text-emerald-200/80 text-sm leading-relaxed mb-6">
                Absensi tanda tangan hanya dapat dilakukan dari <strong class="text-[#C9A84C]">jaringan WiFi resmi Desa Nangtang</strong>.
            </p>

            {{-- Info IP --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 mb-6 text-left">
                <p class="text-xs text-emerald-300/60 uppercase font-semibold tracking-wider mb-2">Informasi Koneksi Anda</p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-500/15 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-mono text-sm font-semibold">{{ $clientIp }}</p>
                        <p class="text-red-400/80 text-xs">IP ini tidak terdaftar di whitelist</p>
                    </div>
                </div>
            </div>

            {{-- Instruksi --}}
            <div class="text-left space-y-3 mb-7">
                <p class="text-xs text-emerald-300/70 uppercase font-semibold tracking-wider">Langkah yang harus dilakukan:</p>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#C9A84C]/20 border border-[#C9A84C]/40 flex items-center justify-center text-[#C9A84C] text-xs font-bold shrink-0 mt-0.5">1</div>
                    <p class="text-emerald-100/80 text-sm">Pastikan HP Anda terhubung ke WiFi kantor desa <span class="text-[#C9A84C] font-semibold">(bukan data seluler)</span></p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#C9A84C]/20 border border-[#C9A84C]/40 flex items-center justify-center text-[#C9A84C] text-xs font-bold shrink-0 mt-0.5">2</div>
                    <p class="text-emerald-100/80 text-sm">Hubungi admin jika sudah terhubung WiFi desa namun tetap muncul pesan ini</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#C9A84C]/20 border border-[#C9A84C]/40 flex items-center justify-center text-[#C9A84C] text-xs font-bold shrink-0 mt-0.5">3</div>
                    <p class="text-emerald-100/80 text-sm">Setelah terhubung ke WiFi yang benar, muat ulang halaman ini</p>
                </div>
            </div>

            {{-- Tombol Coba Lagi --}}
            <a href="{{ url('/absen') }}"
               class="w-full py-4 rounded-2xl font-outfit font-bold text-[#064E3B] text-base transition-all duration-200 shadow-lg flex items-center justify-center gap-2"
               style="background: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%);">
                <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Muat Ulang / Coba Lagi</span>
            </a>
        </div>

        <p class="text-center text-emerald-300/60 text-xs mt-6">
            Crafted with Passion by <strong>Aris Munandar</strong> | KKN 0226 LP3I Tasikmalaya &copy; 2026
        </p>
    </div>
</body>
</html>
