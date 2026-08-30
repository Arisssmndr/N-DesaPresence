<div class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">Gateway Notifikasi</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs font-medium text-slate-500">Standar Pemerintahan Terintegrasi</span>
            </div>
            <h1 class="font-outfit text-2xl sm:text-3xl font-extrabold text-[#064E3B] tracking-tight mt-1">Konfigurasi WhatsApp (Fonnte API)</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Manajemen token gateway, nomor pengirim desa, template siaran pengumuman, dan pemantauan pengiriman.</p>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="cekStatusPerangkat" wire:loading.attr="disabled"
                class="px-4 py-2.5 rounded-xl bg-white border border-[#C9A84C]/40 text-[#064E3B] font-bold text-xs hover:bg-[#FAF6F0] transition shadow-xs flex items-center gap-2">
                <svg wire:loading.remove wire:target="cekStatusPerangkat" class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg wire:loading wire:target="cekStatusPerangkat" class="w-4 h-4 animate-spin text-[#064E3B]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Cek Status Perangkat Fonnte</span>
            </button>
        </div>
    </div>

    <!-- Alert / Feedback Notifikasi -->
    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-2xl text-xs text-emerald-900 font-bold flex items-center gap-2 shadow-xs">
            <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Summary Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="sadi-card p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-[#064E3B] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status Gateway</p>
                <p class="text-base font-extrabold {{ $form['wa_notifikasi_enabled'] ? 'text-emerald-700' : 'text-slate-500' }}">
                    {{ $form['wa_notifikasi_enabled'] ? '🟢 AKTIF' : '⚪ NONAKTIF' }}
                </p>
            </div>
        </div>

        <div class="sadi-card p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Perangkat dg No. HP</p>
                <p class="text-base font-extrabold text-slate-800">{{ $pegawaiDenganHp }} <span class="text-xs font-normal text-slate-400">/ {{ $totalPegawai }}</span></p>
            </div>
        </div>

        <div class="sadi-card p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total WA Terkirim</p>
                <p class="text-base font-extrabold text-emerald-800">{{ number_format($totalTerkirim) }} <span class="text-xs font-normal text-slate-400">pesan</span></p>
            </div>
        </div>

        <div class="sadi-card p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gagal Kirim</p>
                <p class="text-base font-extrabold text-rose-700">{{ number_format($totalGagal) }} <span class="text-xs font-normal text-slate-400">pesan</span></p>
            </div>
        </div>
    </div>

    <!-- Live Device Status Modal/Card (if checked) -->
    @if ($deviceInfo)
        <div class="sadi-card p-5 border-2 {{ ($deviceInfo['connected'] ?? false) ? 'border-emerald-500/50 bg-emerald-50/40' : 'border-amber-500/50 bg-amber-50/40' }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl {{ ($deviceInfo['connected'] ?? false) ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0 shadow-sm">
                        @if ($deviceInfo['connected'] ?? false)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-outfit text-base font-bold text-slate-900">{{ $deviceInfo['message'] }}</h3>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Status: <strong class="uppercase font-mono font-bold">{{ $deviceInfo['device_status'] ?? '-' }}</strong> |
                            Nomor Perangkat: <strong class="font-mono text-emerald-800">{{ $deviceInfo['device'] ?? 'Belum terhubung' }}</strong> |
                            Paket: <strong>{{ $deviceInfo['package'] ?? 'Free' }}</strong> |
                            Kuota Kupon: <strong>{{ $deviceInfo['quota'] ?? '-' }}</strong>
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="$set('deviceInfo', null)" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Tutup Status
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Konfigurasi Utama -->
        <div class="lg:col-span-7 space-y-6">
            <div class="sadi-card p-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#C9A84C]/20 mb-5">
                    <div>
                        <h2 class="font-outfit text-lg font-bold text-[#064E3B]">Kredensial & Pengaturan Gateway</h2>
                        <p class="text-xs text-slate-500">Konfigurasi API Fonnte WhatsApp untuk pengiriman siaran instan</p>
                    </div>
                    <div class="px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[#064E3B] text-[10px] font-bold">
                        API v1.0 (Fonnte)
                    </div>
                </div>

                <form wire:submit.prevent="simpan" class="space-y-5">

                    <!-- Master Switch -->
                    <div class="p-4 rounded-2xl bg-[#FAF6F0] border border-[#C9A84C]/30 flex items-center justify-between">
                        <div>
                            <label for="wa_notifikasi_enabled" class="font-outfit text-sm font-bold text-[#064E3B] block">Aktifkan Notifikasi WhatsApp Otomatis</label>
                            <p class="text-xs text-slate-500 mt-0.5">Jika aktif, pengumuman dengan centang WA akan langsung dikirim ke seluruh perangkat.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="wa_notifikasi_enabled" wire:model.defer="form.wa_notifikasi_enabled" class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#064E3B]"></div>
                        </label>
                    </div>

                    <!-- API Key / Token Fonnte -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="fonnte_api_key" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Token API Fonnte (API Key) <span class="text-rose-500">*</span>
                            </label>
                            <a href="https://fonnte.com" target="_blank" class="text-[11px] text-[#064E3B] hover:underline font-semibold flex items-center gap-1">
                                <span>Buka Fonnte.com</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                        <input type="password" id="fonnte_api_key" wire:model.defer="form.fonnte_api_key"
                            placeholder="Masukkan token Fonnte (contoh: aB1cD2eF3gH4iJ5kL6...)"
                            class="w-full px-4 py-3 text-sm font-mono rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-xs">
                        @error('form.fonnte_api_key') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-slate-400 mt-1.5">Token disimpan secara terenkripsi (AES-256) di database server.</p>
                    </div>

                    <!-- Nomor Pengirim & Country Code -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="fonnte_sender_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor WhatsApp Pengirim (Opsional)
                            </label>
                            <input type="text" id="fonnte_sender_number" wire:model.defer="form.fonnte_sender_number"
                                placeholder="contoh: 6281234567890"
                                class="w-full px-4 py-3 text-sm font-mono rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-xs">
                            @error('form.fonnte_sender_number') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="wa_country_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kode Negara
                            </label>
                            <input type="text" id="wa_country_code" wire:model.defer="form.wa_country_code"
                                placeholder="62"
                                class="w-full px-4 py-3 text-sm font-mono rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-xs">
                            @error('form.wa_country_code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Template Pesan Pengumuman -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="wa_template_pengumuman" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Template Pesan Siaran Pengumuman <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" wire:click="resetTemplate" class="text-[11px] text-amber-700 hover:text-amber-900 font-semibold">
                                Reset ke Default
                            </button>
                        </div>
                        <textarea id="wa_template_pengumuman" wire:model.defer="form.wa_template_pengumuman" rows="7"
                            class="w-full px-4 py-3 text-xs font-mono rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-xs"></textarea>
                        @error('form.wa_template_pengumuman') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror

                        <!-- Variabel Placeholder Chips -->
                        <div class="mt-2 pt-2 border-t border-slate-100">
                            <p class="text-[11px] font-bold text-slate-600 mb-1.5">Variabel Otomatis yang Dapat Digunakan:</p>
                            <div class="flex flex-wrap gap-1.5 text-[11px] font-mono">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{kategori}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{judul}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{isi}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{berlaku_hingga}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{pembuat}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{nama_penerima}</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold">{tanggal}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-3 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-3 rounded-xl text-white font-extrabold text-xs tracking-wide shadow-md transition flex items-center gap-2 cursor-pointer"
                            style="background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%); border: 1px solid #C9A84C;">
                            <svg wire:loading.remove wire:target="simpan" class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg wire:loading wire:target="simpan" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>SIMPAN KONFIGURASI</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Panduan Cepat & Panel Uji Coba -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Panel Uji Coba Kirim WA -->
            <div class="sadi-card p-6">
                <div class="flex items-center gap-2 pb-3 border-b border-[#C9A84C]/20 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-[#C9A84C]/20 text-[#064E3B] flex items-center justify-center font-bold">
                        <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-outfit text-base font-bold text-[#064E3B]">Uji Coba Pengiriman Pesan</h3>
                        <p class="text-[11px] text-slate-500">Kirim pesan sampel untuk memverifikasi token API Fonnte</p>
                    </div>
                </div>

                <form wire:submit.prevent="testKirim" class="space-y-3.5">
                    <div>
                        <label for="testNomorHp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nomor WhatsApp Tujuan
                        </label>
                        <input type="text" id="testNomorHp" wire:model.defer="testNomorHp"
                            placeholder="contoh: 081234567890 atau 6281234567890"
                            class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-xs">
                        @error('testNomorHp') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="testPesan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Isi Pesan Uji Coba
                        </label>
                        <textarea id="testPesan" wire:model.defer="testPesan" rows="3"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-xs"></textarea>
                        @error('testPesan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-2.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                        <svg wire:loading.remove wire:target="testKirim" class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg wire:loading wire:target="testKirim" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>KIRIM PESAN UJI COBA</span>
                    </button>
                </form>

                @if ($testResult)
                    <div class="mt-4 p-3.5 rounded-xl text-xs {{ ($testResult['success'] ?? false) ? 'bg-emerald-50 border border-emerald-300 text-emerald-900' : 'bg-rose-50 border border-rose-300 text-rose-900' }}">
                        <p class="font-bold flex items-center gap-1.5">
                            @if ($testResult['success'] ?? false)
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Hasil Uji Coba: BERHASIL</span>
                            @else
                                <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Hasil Uji Coba: GAGAL</span>
                            @endif
                        </p>
                        <p class="mt-1 text-[11px] font-mono leading-relaxed">{{ $testResult['message'] ?? 'Tidak ada pesan balasan' }}</p>
                    </div>
                @endif
            </div>

            <!-- Panduan Penggunaan Bagi Staf / Admin Desa -->
            <div class="sadi-card p-6 bg-linear-to-br from-white to-[#FAF6F0]">
                <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Panduan Setup Fonnte (Mudah)</span>
                </h3>

                <ol class="text-xs text-slate-700 space-y-2.5 list-decimal pl-4 leading-relaxed">
                    <li>Buka dan daftar akun gratis di <a href="https://fonnte.com" target="_blank" class="text-[#064E3B] font-bold underline">Fonnte.com</a>.</li>
                    <li>Tambah Device baru di dashboard Fonnte, lalu scan QR Code menggunakan WhatsApp nomor kantor desa.</li>
                    <li>Salin <strong>Token API</strong> dari Fonnte dan tempel pada kolom <em>Token API Fonnte</em> di sebelah kiri.</li>
                    <li>Nyalakan tombol <strong>Aktifkan Notifikasi WhatsApp Otomatis</strong> dan klik <strong>SIMPAN</strong>.</li>
                    <li>Pastikan nomor HP seluruh perangkat desa sudah diisi di menu <a href="{{ route('pegawai.index') }}" class="text-[#064E3B] font-bold underline">Data Pegawai</a>.</li>
                </ol>
            </div>

        </div>
    </div>

    <!-- Riwayat Log Notifikasi WhatsApp Terbaru -->
    <div class="sadi-card p-6 mt-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#C9A84C]/20 mb-4">
            <div>
                <h2 class="font-outfit text-lg font-bold text-[#064E3B]">Riwayat Notifikasi WhatsApp Terbaru</h2>
                <p class="text-xs text-slate-500">Log pengiriman siaran pesan ke seluruh nomor perangkat desa</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-[#FAF6F0] text-slate-700 font-bold uppercase tracking-wider border-b border-[#C9A84C]/20">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Penerima</th>
                        <th class="py-3 px-4">No. WhatsApp</th>
                        <th class="py-3 px-4">Pengumuman</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logsTerbaru as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $log->nama_penerima ?: ($log->pegawai->nama_lengkap ?? 'Perangkat') }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">
                                {{ $log->no_hp }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="truncate block max-w-xs font-medium text-[#064E3B]">
                                    {{ $log->pengumuman->judul ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if ($log->status === 'terkirim')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                        ✓ TERKIRIM
                                    </span>
                                @elseif ($log->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                                        ⏳ PENDING
                                    </span>
                                @elseif ($log->status === 'dilewati')
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">
                                        DILEWATI
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                                        ✕ GAGAL
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-[11px] text-slate-500 truncate max-w-xs">
                                {{ $log->error_message ?: ($log->status === 'terkirim' ? 'Pesan berhasil diterima gateway' : '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada riwayat pengiriman notifikasi WhatsApp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
