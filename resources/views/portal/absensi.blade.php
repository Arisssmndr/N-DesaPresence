<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>N-DesaPresence — Portal Absensi Desa Nangtang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tasikmalaya.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <style>
        body {
            background: linear-gradient(160deg, #064E3B 0%, #044535 40%, #022B1F 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            overscroll-behavior: none;
        }
        .card-glass {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(201,168,76,0.2);
        }
        .card-white {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(201,168,76,0.15);
        }
        .btn-gold {
            background: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%);
            color: #064E3B;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            transition: all 0.2s ease;
            box-shadow: 0 6px 20px rgba(201,168,76,0.35);
        }
        .btn-gold:active { transform: scale(0.97); }
        .btn-red {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            box-shadow: 0 6px 20px rgba(220,38,38,0.35);
            transition: all 0.2s ease;
        }
        .btn-red:active { transform: scale(0.97); }
        #canvas-container {
            position: relative;
            border: 2px dashed rgba(6,78,59,0.3);
            border-radius: 16px;
            background: #FAFAF8;
            touch-action: none;
        }
        #signature-canvas {
            width: 100%;
            display: block;
            border-radius: 14px;
            cursor: crosshair;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up { animation: slideUp 0.4s ease forwards; }
        @keyframes tickPop {
            0%   { transform: scale(0); }
            70%  { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .tick-pop { animation: tickPop 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .clock-digit {
            font-family: 'Outfit', sans-serif;
            font-variant-numeric: tabular-nums;
        }
        select { -webkit-appearance: none; appearance: none; }
        .step-inactive { display: none; }
        .step-active { display: block; }
        .badge-hadir   { background:#D1FAE5; color:#065F46; }
        .badge-alpa    { background:#FEE2E2; color:#991B1B; }
        .badge-default { background:#F3F4F6; color:#374151; }
    </style>
</head>
<body class="p-4 pb-10">

    {{-- ═══════════════════ HEADER ═══════════════════ --}}
    <div class="text-center pt-4 pb-6">
        <div class="mb-3 flex justify-center">
            <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kab. Tasikmalaya" class="h-20 w-auto object-contain filter drop-shadow-md">
        </div>
        <h1 style="font-family:'Outfit',sans-serif;" class="text-2xl font-extrabold text-white leading-tight">N-DesaPresence</h1>
        <p class="text-[#C9A84C] text-xs font-semibold tracking-widest uppercase mt-1">Pemerintah Desa Nangtang — Kab. Tasikmalaya</p>

        {{-- Jam Real-Time --}}
        <div class="mt-4 inline-flex items-center gap-2 bg-white/10 border border-white/15 rounded-2xl px-5 py-2.5">
            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="live-clock" class="clock-digit text-white text-lg font-bold">--:--:--</span>
            <span class="text-white/50 text-xs" id="live-date">--</span>
        </div>

        {{-- ═══════════════════ REAL-TIME WIFI STATUS BANNER ═══════════════════ --}}
        <div class="max-w-md mx-auto mt-3.5 px-1">
            {{-- State: Loading --}}
            <div id="wifi-loading" class="flex items-center justify-between gap-2.5 px-4 py-3 rounded-2xl bg-amber-500/15 border border-amber-400/30 text-amber-200 text-xs shadow-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping shrink-0"></div>
                    <span class="font-semibold text-xs text-amber-100">Memeriksa koneksi WiFi Kantor Desa...</span>
                </div>
                <span class="font-mono text-[10px] text-amber-300/80 px-2 py-0.5 rounded bg-black/20">Cek IP</span>
            </div>

            {{-- State: Valid (Connected to Village WiFi) --}}
            <div id="wifi-valid" class="hidden flex items-center justify-between gap-2.5 px-4 py-3 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-100 shadow-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-3 h-3 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)] shrink-0"></div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-white truncate" id="wifi-valid-text">WiFi Kantor Desa Terverifikasi</p>
                        <p class="text-[10px] text-emerald-200 font-medium mt-0.5">Jaringan kantor terdaftar — Siap melakukan absensi</p>
                    </div>
                </div>
                <span class="text-[10.5px] font-bold bg-emerald-400 text-emerald-950 px-2.5 py-1 rounded-lg uppercase tracking-wider shrink-0 shadow-xs">
                    Aktif
                </span>
            </div>

            {{-- State: Invalid (Outside Village WiFi / Cellular Data) --}}
            <div id="wifi-invalid" class="hidden flex flex-col gap-2.5 p-4 rounded-2xl bg-rose-950/70 border-2 border-rose-500/50 text-rose-100 shadow-xl backdrop-blur-md">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-3 h-3 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.9)] animate-pulse shrink-0 mt-0.5"></div>
                        <span class="text-xs font-extrabold text-rose-100 uppercase tracking-wide">Akses Absensi Dinonaktifkan</span>
                    </div>
                    <button type="button" onclick="checkWifiStatus()" class="px-2.5 py-1 rounded-lg bg-rose-500/30 hover:bg-rose-500/50 border border-rose-400/40 text-[11px] font-bold text-rose-100 transition active:scale-95 flex items-center gap-1 cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Cek Ulang</span>
                    </button>
                </div>
                <p class="text-[11.5px] text-rose-200 leading-snug">
                    Perangkat Anda tidak terhubung ke <strong>WiFi Kantor Desa Nangtang</strong> (IP Anda: <code class="font-mono font-bold bg-rose-900/60 text-white px-1.5 py-0.5 rounded border border-rose-700/50" id="wifi-client-ip">--</code>).
                </p>
                <div class="pt-2 border-t border-rose-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[10.5px] text-rose-300">
                    <span>💡 Harap sambungkan HP ke WiFi Kantor Desa atau ajukan <strong>Absen Luar</strong> jika sedang bertugas luar.</span>
                    <a href="{{ route('staf.ajukan.form') }}" class="text-[#C9A84C] font-extrabold underline hover:text-amber-200 shrink-0">Buka Form Absen Luar →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto space-y-4">

        {{-- ═══════════════════ STEP 1: Pilih Pegawai ═══════════════════ --}}
        <div id="step-1" class="slide-up">
            <div class="card-glass rounded-3xl p-6 shadow-xl">
                <h2 class="font-outfit font-bold text-white text-base mb-1">Langkah 1: Pilih Nama Anda</h2>
                <p class="text-emerald-300/60 text-xs mb-5">Pilih nama dari daftar staf desa di bawah ini</p>

                {{-- Search / Select --}}
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-emerald-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <select id="pegawai-select"
                        class="w-full pl-11 pr-4 py-4 rounded-2xl bg-white/10 border border-white/20 text-white text-sm font-medium focus:outline-none focus:border-[#C9A84C] focus:ring-2 focus:ring-[#C9A84C]/30 transition-all"
                        onchange="onPegawaiSelected(this.value)">
                        <option value="" class="bg-emerald-900">— Pilih Nama Pegawai —</option>
                        @foreach($pegawais as $p)
                            <option value="{{ $p->id }}" data-nama="{{ $p->nama_lengkap }}" data-jabatan="{{ $p->jabatan->nama_jabatan ?? '-' }}"
                                class="bg-emerald-900 text-white">
                                {{ $p->nama_lengkap }} — {{ $p->jabatan->nama_jabatan ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Status Hari Ini (muncul setelah pilih) --}}
                <div id="status-card" class="hidden mt-4 bg-white/8 border border-white/12 rounded-2xl p-4 slide-up">
                    <p class="text-emerald-300/60 text-[10px] uppercase font-semibold tracking-wider mb-2">Status Hari Ini</p>
                    <div id="status-content" class="space-y-2"></div>
                </div>
            </div>

            {{-- Tombol Lanjut --}}
            <button id="btn-lanjut" onclick="goToStep2()"
                class="hidden w-full mt-4 py-5 rounded-2xl text-base btn-gold">
                Lanjut ke Tanda Tangan →
            </button>
        </div>

        {{-- ═══════════════════ STEP 2: Tanda Tangan + Submit ═══════════════════ --}}
        <div id="step-2" class="step-inactive">
            {{-- Card Info Pegawai --}}
            <div class="card-glass rounded-3xl p-5 mb-4 slide-up">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#C9A84C]/20 border border-[#C9A84C]/40 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="info-nama" class="text-white font-bold text-base truncate"></p>
                        <p id="info-jabatan" class="text-[#C9A84C] text-xs font-semibold"></p>
                    </div>
                    <button onclick="backToStep1()" class="text-emerald-300/60 hover:text-white transition text-xs underline shrink-0">Ganti</button>
                </div>
            </div>

            {{-- Pilih Jenis Absen --}}
            <div class="card-glass rounded-3xl p-6 mb-4 slide-up">
                <h2 class="font-outfit font-bold text-white text-base mb-4">Langkah 2: Pilih Jenis Absen</h2>
                <div class="grid grid-cols-2 gap-3">
                    <button id="btn-jenis-masuk" onclick="setJenisAbsen('masuk')"
                        class="jenis-btn py-4 rounded-2xl border-2 text-sm font-bold transition-all duration-200 border-emerald-400/30 text-emerald-200 hover:border-[#C9A84C] hover:text-[#C9A84C] flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Absen Masuk</span>
                    </button>
                    <button id="btn-jenis-pulang" onclick="setJenisAbsen('pulang')"
                        class="jenis-btn py-4 rounded-2xl border-2 text-sm font-bold transition-all duration-200 border-emerald-400/30 text-emerald-200 hover:border-[#C9A84C] hover:text-[#C9A84C] flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <span>Absen Pulang</span>
                    </button>
                </div>
            </div>

            {{-- Kanvas Tanda Tangan --}}
            <div id="signature-section" class="hidden card-white rounded-3xl p-5 mb-4 slide-up shadow-xl">
                <h2 class="font-outfit font-bold text-emerald-900 text-base mb-1">Langkah 3: Tanda Tangan</h2>
                <p class="text-slate-500 text-xs mb-4">Tandatangan di kotak di bawah ini menggunakan jari Anda</p>

                <div id="canvas-container" class="mb-4 overflow-hidden">
                    <canvas id="signature-canvas" height="220"></canvas>
                    {{-- Watermark guide --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20">
                        <p class="text-emerald-600 text-xs font-semibold rotate-[-15deg] tracking-widest">TANDA TANGAN DI SINI</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button onclick="clearSignature()"
                        class="flex-1 py-3 rounded-xl border-2 border-slate-200 text-slate-500 text-sm font-semibold hover:border-red-300 hover:text-red-500 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus</span>
                    </button>
                    <button id="btn-submit" onclick="submitAbsen()"
                        class="flex-[2] py-3 rounded-xl btn-gold text-sm flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Absensi</span>
                    </button>
                </div>
            </div>

            {{-- Loading State --}}
            <div id="loading-state" class="hidden text-center py-8">
                <div class="inline-flex flex-col items-center gap-3">
                    <div class="w-12 h-12 border-4 border-[#C9A84C]/30 border-t-[#C9A84C] rounded-full animate-spin"></div>
                    <p class="text-white font-semibold">Menyimpan absensi...</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════ STEP 3: SUKSES ═══════════════════ --}}
        <div id="step-success" class="step-inactive text-center">
            <div class="card-glass rounded-3xl p-8 shadow-xl">
                <div class="w-20 h-20 bg-emerald-400/20 border-4 border-emerald-400/50 rounded-full flex items-center justify-center mx-auto mb-5 tick-pop">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 style="font-family:'Outfit',sans-serif;" class="text-2xl font-extrabold text-white mb-2" id="success-title">Absensi Berhasil!</h2>
                <p class="text-emerald-200/70 text-sm mb-6" id="success-message"></p>

                {{-- Preview Tanda Tangan --}}
                <div class="bg-white rounded-2xl p-4 mb-6">
                    <p class="text-emerald-700 text-xs font-semibold mb-2 text-left">Preview Tanda Tangan</p>
                    <img id="preview-ttd" src="" alt="Tanda Tangan" class="max-h-24 mx-auto" />
                </div>

                {{-- Detail --}}
                <div class="bg-white/8 rounded-2xl p-4 text-left space-y-2 mb-6">
                    <div class="flex justify-between">
                        <span class="text-emerald-300/60 text-xs">Nama</span>
                        <span class="text-white text-xs font-semibold" id="s-nama"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-300/60 text-xs">Jenis</span>
                        <span class="text-[#C9A84C] text-xs font-bold" id="s-jenis"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-300/60 text-xs">Waktu</span>
                        <span class="text-white text-xs font-semibold" id="s-waktu"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-300/60 text-xs">Status</span>
                        <span class="text-emerald-400 text-xs font-semibold" id="s-status"></span>
                    </div>
                </div>

                <button onclick="resetPortal()"
                    class="w-full py-4 rounded-2xl btn-gold text-base">
                    Absen Selesai — Kembali
                </button>
            </div>
        </div>

        {{-- ═══════════════════ STEP ERROR ═══════════════════ --}}
        <div id="step-error" class="step-inactive">
            <div class="card-glass rounded-3xl p-6 text-center shadow-xl">
                <div class="w-16 h-16 bg-red-500/20 border-2 border-red-400/40 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="font-outfit font-bold text-white text-lg mb-2" id="error-title">Gagal</h2>
                <p class="text-emerald-200/70 text-sm mb-6" id="error-message"></p>
                <button onclick="goBackFromError()" class="w-full py-4 rounded-2xl btn-gold text-base">
                    ← Kembali
                </button>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="text-center mt-8 space-y-1">
        <p class="text-emerald-300/60 text-xs">Crafted with Passion by <strong>Aris Munandar</strong> | KKN 0226 LP3I Tasikmalaya &copy; 2026</p>
        <p class="text-emerald-300/20 text-[10px]">IP: {{ $clientIp }}</p>
    </div>

    <script>
        // ─── JAM REAL-TIME ───────────────────────────────────────────────────
        const HARI = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').textContent =
                now.toTimeString().slice(0, 8);
            document.getElementById('live-date').textContent =
                HARI[now.getDay()] + ', ' + now.getDate() + ' ' + BULAN[now.getMonth()] + ' ' + now.getFullYear();
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ─── STATE ───────────────────────────────────────────────────────────
        let selectedPegawaiId   = null;
        let selectedPegawaiNama = '';
        let selectedJenis       = null;
        let signaturePad        = null;
        let statusHariIni       = { sudah_masuk: false, sudah_pulang: false };
        let isWifiValid         = true;

        // ─── WIFI STATUS REAL-TIME POLLING ──────────────────────────────────
        function setWifiState(state, data = {}) {
            const loadingEl = document.getElementById('wifi-loading');
            const validEl   = document.getElementById('wifi-valid');
            const invalidEl = document.getElementById('wifi-invalid');
            const validText = document.getElementById('wifi-valid-text');
            const clientIpEl= document.getElementById('wifi-client-ip');

            loadingEl.classList.add('hidden');
            validEl.classList.add('hidden');
            invalidEl.classList.add('hidden');

            if (state === 'valid') {
                isWifiValid = true;
                validEl.classList.remove('hidden');
                if (validText && data.matched_network) {
                    validText.textContent = 'Terhubung ke ' + data.matched_network;
                }
                enableAbsensiForm();
            } else if (state === 'invalid') {
                isWifiValid = false;
                invalidEl.classList.remove('hidden');
                if (clientIpEl && data.client_ip) {
                    clientIpEl.textContent = data.client_ip;
                }
                disableAbsensiForm();
            } else {
                loadingEl.classList.remove('hidden');
            }
        }

        async function checkWifiStatus() {
            try {
                const res = await fetch('/absen/wifi-status', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                setWifiState(data.valid ? 'valid' : 'invalid', data);
            } catch (err) {
                // If offline or network error
                setWifiState('invalid', { client_ip: 'Koneksi Terputus' });
            }
        }

        function enableAbsensiForm() {
            const selectEl = document.getElementById('pegawai-select');
            const btnLanjut = document.getElementById('btn-lanjut');
            const step1Card = document.getElementById('step-1');

            if (selectEl) selectEl.disabled = false;
            if (btnLanjut) btnLanjut.classList.remove('pointer-events-none', 'opacity-50');
            if (step1Card) {
                step1Card.style.opacity = '';
                step1Card.style.filter = '';
            }
        }

        function disableAbsensiForm() {
            const selectEl = document.getElementById('pegawai-select');
            const btnLanjut = document.getElementById('btn-lanjut');
            const step1Card = document.getElementById('step-1');
            const statusCard = document.getElementById('status-card');

            if (selectEl) {
                selectEl.disabled = true;
                selectEl.value = '';
            }
            if (btnLanjut) {
                btnLanjut.classList.add('hidden');
                btnLanjut.classList.add('pointer-events-none', 'opacity-50');
            }
            if (statusCard) statusCard.classList.add('hidden');

            // If user was in step-2 or error, pull back to step-1
            if (document.getElementById('step-2') && !document.getElementById('step-2').classList.contains('step-inactive')) {
                backToStep1();
            }

            if (step1Card) {
                step1Card.style.opacity = '0.6';
                step1Card.style.filter = 'grayscale(30%)';
            }
        }

        // Jalankan check WiFi saat pertama kali halaman terbuka & setiap 15 detik
        checkWifiStatus();
        setInterval(checkWifiStatus, 15000);

        // ─── STEP 1: Pilih Pegawai ───────────────────────────────────────────
        async function onPegawaiSelected(id) {
            if (!isWifiValid) {
                alert('⚠️ Anda harus terhubung ke WiFi Kantor Desa Nangtang untuk melakukan absensi.');
                document.getElementById('pegawai-select').value = '';
                return;
            }

            if (!id) {
                document.getElementById('status-card').classList.add('hidden');
                document.getElementById('btn-lanjut').classList.add('hidden');
                return;
            }

            selectedPegawaiId = id;
            const opt = document.querySelector(`#pegawai-select option[value="${id}"]`);
            selectedPegawaiNama = opt.dataset.nama;

            // Fetch status hari ini
            const res  = await fetch(`/absen/status?pegawai_id=${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            statusHariIni = data;

            // Render status card
            document.getElementById('status-card').classList.remove('hidden');
            const content = document.getElementById('status-content');

            let html = '';
            if (data.sudah_masuk) {
                html += `<div class="flex justify-between items-center">
                    <span class="text-emerald-300/70 text-xs">Absen Masuk</span>
                    <span class="badge-hadir text-xs font-bold px-2 py-1 rounded-full">✅ ${data.jam_masuk}</span>
                </div>`;
            } else {
                html += `<div class="flex justify-between items-center">
                    <span class="text-emerald-300/70 text-xs">Absen Masuk</span>
                    <span class="badge-default text-xs font-medium px-2 py-1 rounded-full">Belum absen</span>
                </div>`;
            }
            if (data.sudah_pulang) {
                html += `<div class="flex justify-between items-center">
                    <span class="text-emerald-300/70 text-xs">Absen Pulang</span>
                    <span class="badge-hadir text-xs font-bold px-2 py-1 rounded-full">✅ ${data.jam_pulang}</span>
                </div>`;
            } else {
                html += `<div class="flex justify-between items-center">
                    <span class="text-emerald-300/70 text-xs">Absen Pulang</span>
                    <span class="badge-default text-xs font-medium px-2 py-1 rounded-full">Belum absen</span>
                </div>`;
            }
            content.innerHTML = html;

            document.getElementById('btn-lanjut').classList.remove('hidden');
        }

        function goToStep2() {
            if (!selectedPegawaiId) return;
            document.getElementById('step-1').classList.add('step-inactive');
            document.getElementById('step-2').classList.remove('step-inactive');

            const opt = document.querySelector(`#pegawai-select option[value="${selectedPegawaiId}"]`);
            document.getElementById('info-nama').textContent    = opt.dataset.nama;
            document.getElementById('info-jabatan').textContent = opt.dataset.jabatan;

            // Init signature pad
            initSignaturePad();
        }

        function backToStep1() {
            document.getElementById('step-2').classList.add('step-inactive');
            document.getElementById('step-1').classList.remove('step-inactive');
            document.getElementById('signature-section').classList.add('hidden');
            selectedJenis = null;
            resetJenisBtnStyle();
        }

        // ─── STEP 2: Jenis Absen ─────────────────────────────────────────────
        function setJenisAbsen(jenis) {
            selectedJenis = jenis;
            resetJenisBtnStyle();

            const btnId = jenis === 'masuk' ? 'btn-jenis-masuk' : 'btn-jenis-pulang';
            document.getElementById(btnId).classList.add(
                '!border-[#C9A84C]', '!text-[#064E3B]', '!bg-[#C9A84C]'
            );

            // Cek apakah sudah absen jenis tersebut
            const sudahAbsen = jenis === 'masuk' ? statusHariIni.sudah_masuk : statusHariIni.sudah_pulang;
            if (sudahAbsen) {
                const jam = jenis === 'masuk' ? statusHariIni.jam_masuk : statusHariIni.jam_pulang;
                showError('Sudah Tercatat', `Anda sudah melakukan absen ${jenis} hari ini pukul ${jam}.`);
                return;
            }

            if (jenis === 'pulang' && !statusHariIni.sudah_masuk) {
                showError('Belum Absen Masuk', 'Anda belum melakukan absen masuk hari ini. Silakan absen masuk terlebih dahulu.');
                return;
            }

            document.getElementById('signature-section').classList.remove('hidden');
        }

        function resetJenisBtnStyle() {
            ['btn-jenis-masuk', 'btn-jenis-pulang'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.remove('!border-[#C9A84C]', '!text-[#064E3B]', '!bg-[#C9A84C]');
            });
        }

        // ─── SIGNATURE PAD ───────────────────────────────────────────────────
        function initSignaturePad() {
            const canvas = document.getElementById('signature-canvas');
            // Set canvas width to container width
            const container = document.getElementById('canvas-container');
            canvas.width  = container.offsetWidth;
            canvas.height = 220;

            if (signaturePad) signaturePad.clear();
            signaturePad = new SignaturePad(canvas, {
                minWidth: 1.5,
                maxWidth: 3,
                penColor: '#064E3B',
                backgroundColor: 'rgba(0,0,0,0)',
            });
        }

        function clearSignature() {
            if (signaturePad) signaturePad.clear();
        }

        // ─── SUBMIT ──────────────────────────────────────────────────────────
        async function submitAbsen() {
            if (!signaturePad || signaturePad.isEmpty()) {
                alert('⚠️ Silakan tandatangan terlebih dahulu sebelum mengirim.');
                return;
            }

            const ttdData   = signaturePad.toDataURL('image/png');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const endpoint  = selectedJenis === 'masuk' ? '/absen/masuk' : '/absen/pulang';

            // Show loading
            document.getElementById('step-2').classList.add('step-inactive');
            document.getElementById('loading-state').classList.remove('hidden');
            // Actually show loading inside step-2 area
            document.getElementById('step-2').classList.remove('step-inactive');
            document.querySelectorAll('#step-2 > div:not(#loading-state)').forEach(el => el.classList.add('hidden'));
            document.getElementById('loading-state').classList.remove('hidden');

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        pegawai_id:   selectedPegawaiId,
                        tanda_tangan: ttdData,
                    }),
                });

                const data = await res.json();

                if (data.status === 'berhasil') {
                    showSuccess(data);
                } else {
                    showError(data.status === 'sudah_absen' ? 'Sudah Tercatat' : 'Gagal', data.message);
                }
            } catch (err) {
                showError('Kesalahan Jaringan', 'Terjadi kesalahan koneksi. Pastikan Anda masih terhubung ke WiFi desa.');
            }
        }

        // ─── SUCCESS / ERROR ─────────────────────────────────────────────────
        function showSuccess(data) {
            document.getElementById('step-2').classList.add('step-inactive');

            const opt = document.querySelector(`#pegawai-select option[value="${selectedPegawaiId}"]`);
            document.getElementById('success-title').textContent  = data.jenis === 'masuk' ? '✅ Absen Masuk Berhasil!' : '✅ Absen Pulang Berhasil!';
            document.getElementById('success-message').textContent = data.message;
            document.getElementById('s-nama').textContent   = opt.dataset.nama;
            document.getElementById('s-jenis').textContent  = data.jenis === 'masuk' ? '🌅 Absen Masuk' : '🌆 Absen Pulang';
            document.getElementById('s-waktu').textContent  = new Date().toLocaleTimeString('id-ID');
            document.getElementById('s-status').textContent = data.data?.status ?? 'Hadir';
            document.getElementById('preview-ttd').src      = signaturePad.toDataURL('image/png');

            document.getElementById('step-success').classList.remove('step-inactive');
        }

        function showError(title, message) {
            document.getElementById('step-2').classList.add('step-inactive');
            document.getElementById('error-title').textContent   = title;
            document.getElementById('error-message').textContent = message;
            document.getElementById('step-error').classList.remove('step-inactive');
        }

        function goBackFromError() {
            document.getElementById('step-error').classList.add('step-inactive');
            document.getElementById('step-2').classList.remove('step-inactive');
            document.querySelectorAll('#step-2 > div').forEach(el => el.classList.remove('hidden'));
            document.getElementById('loading-state').classList.add('hidden');
            selectedJenis = null;
            resetJenisBtnStyle();
            document.getElementById('signature-section').classList.add('hidden');
        }

        function resetPortal() {
            location.reload();
        }

        // Resize canvas on orientation change
        window.addEventListener('resize', () => {
            if (signaturePad) initSignaturePad();
        });
    </script>
</body>
</html>
