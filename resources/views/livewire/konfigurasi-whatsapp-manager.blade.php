<div class="space-y-6">

    <!-- Header Page (Luxury & Clean with SVG Library Icons) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-[#064E3B]/10 text-[#064E3B] text-[11px] font-extrabold uppercase tracking-wider border border-[#064E3B]/20">Gateway Notifikasi</span>
                <span class="text-xs text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">Fonnte API Remote Management</span>
            </div>
            <h1 class="font-outfit text-2xl sm:text-3xl font-extrabold text-[#064E3B] tracking-tight mt-1">Konfigurasi WhatsApp</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Kelola perangkat WhatsApp, pantau kuota siaran, dan scan QR langsung dari panel admin.</p>
        </div>

        <!-- Quick Tabs (Sleek Green Active Pill, Single Line, No Wrap) -->
        <div class="flex items-center p-1 bg-slate-200/80 rounded-2xl border border-slate-200 shadow-inner shrink-0">
            <button wire:click="$set('activeTab', 'device')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $activeTab === 'device' ? 'bg-[#064E3B] text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <svg class="w-3.5 h-3.5 {{ $activeTab === 'device' ? 'text-[#F3E5AB]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Perangkat & QR</span>
            </button>

            <button wire:click="$set('activeTab', 'settings')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $activeTab === 'settings' ? 'bg-[#064E3B] text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <svg class="w-3.5 h-3.5 {{ $activeTab === 'settings' ? 'text-[#F3E5AB]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan & Uji</span>
            </button>

            <button wire:click="$set('activeTab', 'logs')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $activeTab === 'logs' ? 'bg-[#064E3B] text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <svg class="w-3.5 h-3.5 {{ $activeTab === 'logs' ? 'text-[#F3E5AB]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Log Riwayat</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Metrics (Sleek Modern Cards with SVG & Quota) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="sadi-card p-4 flex items-center gap-3.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#064E3B] border border-emerald-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Status Gateway</p>
                <p class="text-sm font-extrabold {{ $form['wa_notifikasi_enabled'] ? 'text-emerald-700' : 'text-slate-500' }}">
                    {{ $form['wa_notifikasi_enabled'] ? 'AKTIF' : 'NONAKTIF' }}
                </p>
            </div>
        </div>

        <div class="sadi-card p-4 flex items-center gap-3.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Nomor Pengirim</p>
                <p class="text-xs font-mono font-bold text-slate-800 truncate max-w-[130px]" title="{{ $form['fonnte_sender_number'] ?: 'Belum diatur' }}">
                    {{ $form['fonnte_sender_number'] ?: '-' }}
                </p>
            </div>
        </div>

        <div class="sadi-card p-4 flex items-center gap-3.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Sisa Kuota Kupon</p>
                <p class="text-sm font-extrabold text-amber-800">
                    {{ $deviceInfo['quota'] ?? '-' }} <span class="text-[11px] font-normal text-slate-400">kuota</span>
                </p>
            </div>
        </div>

        <div class="sadi-card p-4 flex items-center gap-3.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-800 border border-indigo-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Total Terkirim</p>
                <p class="text-sm font-extrabold text-indigo-900">{{ number_format($totalTerkirim) }} <span class="text-[11px] font-normal text-slate-400">pesan</span></p>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- TAB 1: KELOLA PERANGKAT & QR CODE (REMOTE FONNTE)             -->
    <!-- ============================================================= -->
    @if ($activeTab === 'device')
        <div class="space-y-6">

            <!-- Card 1: Master Account Token Fonnte -->
            <div class="sadi-card p-5 sm:p-6 bg-white border border-[#C9A84C]/25 rounded-3xl shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#064E3B]/10 text-[#064E3B] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-outfit text-base font-bold text-[#064E3B]">Master Account Token (Fonnte)</h2>
                            <p class="text-xs text-slate-500">Token akun master untuk sinkronisasi perangkat WhatsApp dan scan QR dari admin.</p>
                        </div>
                    </div>
                    <a href="https://md.fonnte.com/new/setting.php" target="_blank" class="text-xs font-bold text-[#064E3B] hover:underline flex items-center gap-1">
                        <span>Buka Menu Setting Fonnte</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <input type="password" wire:model.defer="form.fonnte_account_token"
                            placeholder="Tempel Account Token Fonnte di sini (misal: iTtfQiDnC88Ls...)"
                            class="w-full px-4 py-3 text-xs font-mono rounded-xl bg-slate-50 border border-slate-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800">
                    </div>
                    <button wire:click="saveAccountToken" wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-5 py-3 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-2 shrink-0 cursor-pointer"
                        style="border: 1px solid #C9A84C;">
                        <svg wire:loading.remove wire:target="saveAccountToken" class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <svg wire:loading wire:target="saveAccountToken" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Simpan & Muat Device</span>
                    </button>
                </div>
                @error('form.fonnte_account_token') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Card 2: Daftar Perangkat WhatsApp (Easy 1-Click Switcher) -->
            <div class="sadi-card p-5 sm:p-6 bg-white border border-slate-200 rounded-3xl shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-[#064E3B]">Daftar Perangkat WhatsApp (Fonnte Devices)</h3>
                        <p class="text-xs text-slate-500">Pilih perangkat yang ingin digunakan atau hubungkan nomor baru.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="loadDevices" wire:loading.attr="disabled"
                            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                            <svg wire:loading.class="animate-spin" wire:target="loadDevices" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Refresh</span>
                        </button>

                        <button wire:click="openAddDeviceModal"
                            class="px-4 py-2 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-extrabold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer"
                            style="border: 1px solid #C9A84C;">
                            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Hubungkan Perangkat Baru</span>
                        </button>
                    </div>
                </div>

                @if ($isLoadingDevices)
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <div class="w-8 h-8 border-3 border-[#064E3B] border-t-transparent rounded-full animate-spin mx-auto"></div>
                        <p class="text-xs font-semibold">Menghubungi server Fonnte...</p>
                    </div>
                @elseif (!empty($devicesList))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($devicesList as $dev)
                            @php
                                $devNum = $dev['device'] ?? ($dev['whatsapp'] ?? '-');
                                $devName = $dev['name'] ?? 'Perangkat WhatsApp';
                                $devStatus = strtolower($dev['status'] ?? ($dev['device_status'] ?? ''));
                                $isConnected = in_array($devStatus, ['connect', 'connected']);
                                $isActiveInSystem = ($form['fonnte_sender_number'] === $devNum) && !empty($form['fonnte_api_key']);
                                $devToken = $dev['token'] ?? null;
                            @endphp
                            <div class="p-4 rounded-2xl border {{ $isActiveInSystem ? 'border-[#064E3B] ring-2 ring-[#064E3B]/20 bg-emerald-50/40' : 'border-slate-200 bg-white hover:border-[#C9A84C]/60 hover:shadow-md' }} transition duration-200 space-y-3">
                                
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-2xl {{ $isConnected ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-50 text-rose-700' }} border {{ $isConnected ? 'border-emerald-200' : 'border-rose-200' }} flex items-center justify-center font-bold shrink-0">
                                            @if ($isConnected)
                                                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-outfit font-bold text-slate-900 text-sm leading-tight">
                                                {{ $devName }}
                                            </h4>
                                            <p class="font-mono text-xs text-slate-600 font-bold mt-0.5">{{ $devNum }}</p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        @if ($isActiveInSystem)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#064E3B] text-[#F3E5AB] text-[10px] font-extrabold tracking-tight shadow-xs">
                                                <svg class="w-3 h-3 text-[#C9A84C]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span>PENGIRIM AKTIF</span>
                                            </span>
                                        @elseif ($isConnected)
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                                TERHUBUNG
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                                                TERPUTUS
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                                    <!-- Direct Action Button: 1 Click to Switch or Scan -->
                                    <div class="flex items-center gap-1.5">
                                        @if ($isActiveInSystem)
                                            <span class="text-[11px] font-semibold text-emerald-800 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Sedang Digunakan</span>
                                            </span>
                                        @elseif ($isConnected && !empty($devToken))
                                            <button wire:click="setActiveDevice('{{ $devNum }}', '{{ $devToken }}')"
                                                class="px-3 py-1.5 rounded-lg bg-[#064E3B] hover:bg-[#043327] text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3 h-3 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Gunakan Device Ini</span>
                                            </button>
                                        @elseif (!$isConnected)
                                            <button wire:click="openQrModalForDevice('{{ $devNum }}', '{{ $devToken }}')"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3 h-3 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                <span>Scan QR Code</span>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Info & Detail Button -->
                                    <button wire:click="openDeviceDetail({{ json_encode($dev) }})"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Detail & Token</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    @php
                        $friendlyError = $deviceListError;
                        if (str_contains(strtolower($deviceListError ?? ''), 'timeout') || str_contains(strtolower($deviceListError ?? ''), 'curl error')) {
                            $friendlyError = 'Koneksi ke server Fonnte sedang mengalami latensi. Silakan klik tombol Refresh untuk menyinkronkan daftar perangkat.';
                        }
                    @endphp
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300 space-y-3">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 text-[#064E3B] flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-outfit font-bold text-slate-800 text-sm">Belum Ada Perangkat Terdaftar</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            {{ $friendlyError ?: 'Masukkan Master Account Token di atas, lalu klik tombol "+ Hubungkan Perangkat Baru" untuk menghubungkan WhatsApp desa.' }}
                        </p>
                        <div class="flex items-center justify-center gap-2 pt-1">
                            <button wire:click="loadDevices" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer">
                                ↻ Refresh Daftar
                            </button>
                            <button wire:click="openAddDeviceModal"
                                class="px-5 py-2 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-extrabold text-xs shadow-md transition inline-flex items-center gap-2 cursor-pointer"
                                style="border: 1px solid #C9A84C;">
                                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Hubungkan Perangkat Baru</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- ============================================================= -->
    <!-- TAB 2: PENGATURAN GATEWAY & UJI COBA (READONLY STATIS)       -->
    <!-- ============================================================= -->
    @if ($activeTab === 'settings')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Kolom Kiri: Status Gateway & Kredensial Statis -->
            <div class="lg:col-span-6 space-y-6">
                <div class="sadi-card p-6 bg-white border border-slate-200 rounded-3xl shadow-sm space-y-5">
                    
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-outfit text-base font-bold text-[#064E3B]">Perangkat Gateway Pengirim Aktif</h3>
                            <p class="text-xs text-slate-500">Data statis yang otomatis tersinkronisasi dari Tab Perangkat & QR.</p>
                        </div>
                        <button wire:click="$set('activeTab', 'device')" class="text-xs font-bold text-[#064E3B] hover:underline flex items-center gap-1">
                            <span>Ganti Device</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Master Switch Pixel-Perfect -->
                    <div class="p-4 rounded-2xl bg-[#FAF6F0] border border-[#C9A84C]/30 flex items-center justify-between gap-4">
                        <div>
                            <label class="font-outfit text-sm font-bold text-[#064E3B] block">Sakelar Notifikasi WhatsApp Otomatis</label>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jika aktif, setiap pengumuman bercentang WA akan langsung dikirim ke staf.</p>
                        </div>
                        <button type="button" wire:click="toggleWaEnabled"
                            class="relative inline-flex items-center shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none {{ $form['wa_notifikasi_enabled'] ? 'bg-[#064E3B]' : 'bg-slate-300' }}"
                            style="width: 48px; height: 26px; padding: 2px;">
                            <span class="pointer-events-none inline-block rounded-full bg-white shadow-sm transition-transform duration-200 ease-in-out"
                                style="width: 22px; height: 22px; transform: translateX({{ $form['wa_notifikasi_enabled'] ? '22px' : '0px' }});"></span>
                        </button>
                    </div>

                    <!-- Device Info Box (Readonly Display dengan Token) -->
                    @if (!empty($form['fonnte_sender_number']))
                        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider">Perangkat Utama Terpilih</span>
                                <span class="px-2 py-0.5 rounded-md bg-[#064E3B] text-[#F3E5AB] text-[10px] font-bold">
                                    ★ AKTIF
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-400 text-[10.5px]">Nomor WhatsApp:</span>
                                    <p class="font-mono font-bold text-slate-900 text-sm">{{ $form['fonnte_sender_number'] }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10.5px]">Status Live:</span>
                                    <p class="font-bold {{ ($deviceInfo['connected'] ?? false) ? 'text-emerald-700' : 'text-slate-600' }}">
                                        {{ ($deviceInfo['connected'] ?? false) ? '🟢 Terhubung' : '⚪ Standby' }}
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-emerald-200/60" x-data="{ showToken: false }">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-slate-500 font-medium text-[11px]">Device Token API:</span>
                                    <button type="button" @click="showToken = !showToken" class="text-[10.5px] font-bold text-[#064E3B] hover:underline">
                                        <span x-text="showToken ? 'Sembunyikan' : 'Tampilkan'"></span>
                                    </button>
                                </div>
                                <div class="p-2 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-700 break-all select-all">
                                    <span x-show="!showToken">{{ substr($form['fonnte_api_key'], 0, 8) }}••••••••••••••••••••••••</span>
                                    <span x-show="showToken" x-cloak>{{ $form['fonnte_api_key'] }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-6 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300 space-y-2">
                            <p class="text-xs text-slate-500 font-medium">Belum ada perangkat WhatsApp yang dipilih sebagai pengirim utama.</p>
                            <button wire:click="$set('activeTab', 'device')" class="px-4 py-2 rounded-xl bg-[#064E3B] text-white font-bold text-xs shadow-sm cursor-pointer">
                                Pilih / Hubungkan Perangkat di Tab 1
                            </button>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Kolom Kanan: Panel Uji Coba Pengiriman -->
            <div class="lg:col-span-6 space-y-6">
                <div class="sadi-card p-6 bg-white border border-[#C9A84C]/30 rounded-3xl shadow-sm space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#064E3B] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-outfit text-base font-bold text-[#064E3B]">Uji Coba Pengiriman WhatsApp</h3>
                            <p class="text-[11px] text-slate-500">Pesan akan dikirimkan melalui nomor pengirim aktif: <strong>{{ $form['fonnte_sender_number'] ?: 'Belum diatur' }}</strong></p>
                        </div>
                    </div>

                    <form wire:submit.prevent="testKirim" class="space-y-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nomor WhatsApp Tujuan
                            </label>
                            <input type="text" wire:model.defer="testNomorHp" placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-2.5 text-xs font-mono rounded-xl bg-slate-50 border border-slate-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800">
                            @error('testNomorHp') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Isi Pesan Uji Coba
                            </label>
                            <textarea wire:model.defer="testPesan" rows="4" placeholder="Tuliskan pesan yang ingin dikirim..."
                                class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 leading-relaxed"></textarea>
                            @error('testPesan') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer"
                            style="border: 1px solid #C9A84C;">
                            <svg wire:loading.remove wire:target="testKirim" class="w-4 h-4 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <svg wire:loading wire:target="testKirim" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>KIRIM PESAN UJI COBA</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    @endif

    <!-- ============================================================= -->
    <!-- TAB 3: LOG RIWAYAT PENGIRIMAN                                 -->
    <!-- ============================================================= -->
    @if ($activeTab === 'logs')
        <div class="sadi-card p-6 bg-white border border-slate-200 rounded-3xl shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-outfit text-base font-bold text-[#064E3B]">Riwayat Log Pengiriman Terakhir</h3>
                    <p class="text-xs text-slate-500">Daftar notifikasi siaran WhatsApp yang dikirimkan oleh sistem ke perangkat desa.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-2">Penerima</th>
                            <th class="py-3 px-2">Nomor WhatsApp</th>
                            <th class="py-3 px-2">Perihal / Pengumuman</th>
                            <th class="py-3 px-2">Waktu</th>
                            <th class="py-3 px-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logsTerbaru as $log)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-2 font-bold text-slate-800">
                                    {{ $log->nama_penerima ?: ($log->pegawai->nama_lengkap ?? 'Perangkat') }}
                                </td>
                                <td class="py-3 px-2 font-mono text-slate-600">
                                    {{ $log->no_hp }}
                                </td>
                                <td class="py-3 px-2 text-slate-600 max-w-xs truncate">
                                    {{ $log->pengumuman->judul ?? 'Pesan Uji Coba' }}
                                </td>
                                <td class="py-3 px-2 text-slate-400 text-[11px]">
                                    {{ $log->created_at ? $log->created_at->isoFormat('D MMM Y, HH:mm') : '-' }}
                                </td>
                                <td class="py-3 px-2 text-right">
                                    @if ($log->status === 'terkirim')
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                            ✓ TERKIRIM
                                        </span>
                                    @elseif ($log->status === 'pending')
                                        <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-bold text-[10px]">
                                            ⏳ PENDING
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-bold text-[10px]" title="{{ $log->error_message }}">
                                            ✕ GAGAL
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Belum ada catatan log pengiriman WhatsApp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ============================================================= -->
    <!-- MODAL 1: DETAIL INFORMASI PERANGKAT WHATSAPP                  -->
    <!-- ============================================================= -->
    @if ($showDeviceDetailModal && $selectedDeviceDetail)
        @php
            $modalDevNum = $selectedDeviceDetail['device'] ?? ($selectedDeviceDetail['whatsapp'] ?? '-');
            $modalDevName = $selectedDeviceDetail['name'] ?? 'Perangkat WhatsApp';
            $modalDevStatus = strtolower($selectedDeviceDetail['status'] ?? ($selectedDeviceDetail['device_status'] ?? ''));
            $modalIsConnected = in_array($modalDevStatus, ['connect', 'connected']);
            $modalIsActive = ($form['fonnte_sender_number'] === $modalDevNum) && !empty($form['fonnte_api_key']);
            $modalToken = $selectedDeviceDetail['token'] ?? null;
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/50 my-8">
                
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-outfit text-base font-bold text-white">Detail Perangkat WhatsApp</h3>
                            <p class="text-[11px] text-emerald-200/80">{{ $modalDevName }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDeviceDetail" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-xs">
                    
                    <!-- Status Banner -->
                    <div class="p-3.5 rounded-2xl flex items-center justify-between border {{ $modalIsConnected ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $modalIsConnected ? 'bg-emerald-600 animate-pulse' : 'bg-rose-600' }}"></span>
                            <span class="font-bold">Status: {{ $modalIsConnected ? 'Terhubung & Siap Kirim' : 'Terputus / Perlu Scan QR' }}</span>
                        </div>
                        @if ($modalIsActive)
                            <span class="px-2 py-0.5 rounded-md bg-[#064E3B] text-[#F3E5AB] text-[10px] font-extrabold">
                                PENGIRIM UTAMA SISTEM
                            </span>
                        @endif
                    </div>

                    <!-- Info Table -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2.5">
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Nomor WhatsApp:</span>
                            <span class="font-mono font-bold text-slate-900">{{ $modalDevNum }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Paket Akun Fonnte:</span>
                            <span class="font-bold text-slate-800">{{ $selectedDeviceDetail['package'] ?? 'Free' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Sisa Kuota Kupon:</span>
                            <span class="font-bold text-amber-800">{{ $selectedDeviceDetail['quota'] ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Total Pesan Dibuat:</span>
                            <span class="font-bold text-slate-800">{{ $selectedDeviceDetail['messages'] ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Masa Berlaku:</span>
                            <span class="font-bold text-slate-800">{{ $selectedDeviceDetail['expired'] ?? '-' }}</span>
                        </div>
                        @if (!empty($modalToken))
                            <div class="pt-1" x-data="{ copied: false }">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-slate-500 font-medium text-[11px]">Device Token:</span>
                                    <button type="button" @click="navigator.clipboard.writeText('{{ $modalToken }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                        class="text-[10.5px] font-bold text-[#064E3B] hover:underline flex items-center gap-1">
                                        <span x-text="copied ? '✓ Tersalin!' : 'Salin Token'"></span>
                                    </button>
                                </div>
                                <div class="p-2.5 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-800 break-all select-all font-semibold">
                                    {{ $modalToken }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Actions (Clean, Well Aligned, Green Primary) -->
                    <div class="pt-2 flex items-center justify-between gap-2">
                        <!-- Hapus Button (Instan, Non-blocking) -->
                        <button type="button" wire:click="deleteDevice('{{ $modalDevNum }}', '{{ $modalToken }}')" wire:loading.attr="disabled"
                            class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Device</span>
                        </button>

                        <div class="flex items-center gap-2">
                            @if (!$modalIsConnected)
                                <button wire:click="openQrModalForDevice('{{ $modalDevNum }}', '{{ $modalToken }}')"
                                    class="px-4 py-2.5 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Scan QR Code</span>
                                </button>
                            @elseif (!$modalIsActive && !empty($modalToken))
                                <button wire:click="setActiveDevice('{{ $modalDevNum }}', '{{ $modalToken }}')"
                                    class="px-4 py-2.5 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer shadow-sm"
                                    style="border: 1px solid #C9A84C;">
                                    <svg class="w-3.5 h-3.5 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Gunakan Device Ini</span>
                                </button>
                            @endif

                            @if ($modalIsConnected)
                                <button wire:click="disconnectDevice('{{ $modalDevNum }}', '{{ $modalToken }}')"
                                    class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Putuskan
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================= -->
    <!-- MODAL 2: TAMBAH PERANGKAT BARU & SCAN QR (AUTO-CONNECT)      -->
    <!-- ============================================================= -->
    @if ($showAddDeviceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-[#C9A84C]/50 my-8">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/50">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-white">Hubungkan Perangkat WhatsApp</h3>
                        <p class="text-[11px] text-emerald-200/80">Langkah {{ $modalStep }} dari 2: {{ $modalStep === 1 ? 'Data Perangkat' : ($modalStep === 2 ? 'Scan QR Code' : 'Selesai') }}</p>
                    </div>
                    <button wire:click="closeModals" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- STEP 1: Form Input Nama & Nomor -->
                @if ($modalStep === 1)
                    <form wire:submit.prevent="submitAddDevice" class="p-6 space-y-4 text-xs">
                        @if ($qrErrorMessage)
                            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-medium">
                                {{ $qrErrorMessage }}
                            </div>
                        @endif

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Perangkat <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.defer="newDeviceName" placeholder="Contoh: WA Kantor Desa Nangtang"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#C9A84C]/50 outline-none">
                            @error('newDeviceName') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp Pengirim <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.defer="newDeviceNumber" placeholder="08xxxxxxxxxx"
                                class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#C9A84C]/50 outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Nomor WhatsApp yang akan digunakan untuk scan QR.</p>
                            @error('newDeviceNumber') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" wire:click="closeModals" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white shadow-md transition cursor-pointer flex items-center gap-2"
                                style="background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%); border: 1px solid #C9A84C;">
                                <svg wire:loading wire:target="submitAddDevice" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Buat & Tampilkan QR Code →</span>
                            </button>
                        </div>
                    </form>
                @endif

                <!-- STEP 2: Tampilan Scan QR Code (Auto-Polling 2.5s like WhatsApp Web) -->
                @if ($modalStep === 2)
                    <div class="p-6 space-y-4 text-center" wire:poll.2500ms="autoCheckQrStatus">
                        <div class="space-y-1">
                            <h4 class="font-outfit font-bold text-slate-900 text-sm">Scan QR Code dengan WhatsApp</h4>
                            <p class="text-xs text-slate-500">Buka WA di HP → <strong>Perangkat Tertaut</strong> → <strong>Tautkan Perangkat</strong> → Arahkan kamera ke QR:</p>
                        </div>

                        <!-- Gambar QR Code -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 inline-block mx-auto shadow-inner">
                            @if ($isGeneratingQr)
                                <div class="w-56 h-56 flex flex-col items-center justify-center text-slate-400 space-y-2">
                                    <div class="w-8 h-8 border-3 border-[#064E3B] border-t-transparent rounded-full animate-spin"></div>
                                    <p class="text-xs">Membuat QR Code...</p>
                                </div>
                            @elseif ($qrCodeData)
                                @if (str_starts_with($qrCodeData, 'data:image') || str_starts_with($qrCodeData, 'http'))
                                    <img src="{{ $qrCodeData }}" alt="Scan QR Code" class="w-56 h-56 mx-auto rounded-xl shadow-md">
                                @else
                                    <img src="data:image/png;base64,{{ $qrCodeData }}" alt="Scan QR Code" class="w-56 h-56 mx-auto rounded-xl shadow-md">
                                @endif
                            @else
                                <div class="w-56 h-56 flex flex-col items-center justify-center text-slate-400 p-4">
                                    <p class="text-xs text-rose-500 font-semibold leading-relaxed">{{ $qrErrorMessage ?: 'QR Code belum dapat dimuat.' }}</p>
                                    <button wire:click="fetchQrCode" class="mt-2 text-xs text-[#064E3B] font-bold underline cursor-pointer">Coba Lagi</button>
                                </div>
                            @endif
                        </div>

                        <!-- Auto-Detect Status Bar -->
                        <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs flex items-center justify-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-600 animate-ping"></div>
                            <span>Menunggu scan HP... Sistem akan otomatis terkoneksi seperti WA Web.</span>
                        </div>

                        <p class="text-[11px] text-slate-400 font-mono">Nomor Perangkat: {{ $activeQrDevice }}</p>

                        <!-- Action Buttons -->
                        <div class="pt-2 flex items-center justify-center gap-2">
                            <button wire:click="fetchQrCode" wire:loading.attr="disabled"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg wire:loading.class="animate-spin" wire:target="fetchQrCode" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Refresh QR</span>
                            </button>

                            <button wire:click="checkQrConnection" wire:loading.attr="disabled"
                                class="px-6 py-2.5 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer"
                                style="border: 1px solid #C9A84C;">
                                <svg wire:loading wire:target="checkQrConnection" class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Cek Koneksi</span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- STEP 3: Sukses Terhubung (Auto-redirect) -->
                @if ($modalStep === 3)
                    <div class="p-8 text-center space-y-4" x-data x-init="setTimeout(() => $wire.closeModals(), 2000)">
                        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto shadow-md animate-bounce">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-outfit font-extrabold text-emerald-800 text-lg">WhatsApp Berhasil Terhubung!</h4>
                            <p class="text-xs text-slate-600">Perangkat <strong>{{ $activeQrDevice }}</strong> telah aktif sebagai pengirim utama. Mengalihkan...</p>
                        </div>
                        <button wire:click="closeModals" class="px-8 py-2.5 rounded-xl bg-[#064E3B] text-white font-extrabold text-xs shadow-md transition cursor-pointer"
                            style="border: 1px solid #C9A84C;">
                            Tutup Sekarang
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif

    <!-- ============================================================= -->
    <!-- MODAL 3: SCAN QR UNTUK PERANGKAT YANG SUDAH ADA (AUTO-CONNECT)-->
    <!-- ============================================================= -->
    @if ($showQrModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl overflow-hidden border border-[#C9A84C]/50 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/50">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-white">Scan QR WhatsApp</h3>
                        <p class="text-[11px] text-emerald-200/80">{{ $activeQrDevice }}</p>
                    </div>
                    <button wire:click="closeModals" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-center" wire:poll.2500ms="autoCheckQrStatus">
                    <p class="text-xs text-slate-500">Scan QR Code dengan WhatsApp di HP Anda:</p>

                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 inline-block mx-auto">
                        @if ($isGeneratingQr)
                            <div class="w-52 h-52 flex flex-col items-center justify-center text-slate-400 space-y-2">
                                <div class="w-8 h-8 border-3 border-[#064E3B] border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs">Memuat QR Code...</p>
                            </div>
                        @elseif ($qrCodeData)
                            @if (str_starts_with($qrCodeData, 'data:image') || str_starts_with($qrCodeData, 'http'))
                                <img src="{{ $qrCodeData }}" alt="Scan QR Code" class="w-52 h-52 mx-auto rounded-xl shadow-md">
                            @else
                                <img src="data:image/png;base64,{{ $qrCodeData }}" alt="Scan QR Code" class="w-52 h-52 mx-auto rounded-xl shadow-md">
                            @endif
                        @else
                            <div class="w-52 h-52 flex flex-col items-center justify-center text-slate-400 p-3">
                                <p class="text-xs text-rose-500 font-semibold leading-relaxed">{{ $qrErrorMessage ?: 'Gagal memuat QR Code.' }}</p>
                                <button wire:click="fetchQrCode" class="mt-2 text-xs text-[#064E3B] font-bold underline cursor-pointer">Coba Lagi</button>
                            </div>
                        @endif
                    </div>

                    <!-- Auto-Detect Status Bar -->
                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs flex items-center justify-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-600 animate-ping"></div>
                        <span>Otomatis mendeteksi setelah di-scan...</span>
                    </div>

                    <div class="flex items-center justify-center gap-2">
                        <button wire:click="fetchQrCode" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Refresh
                        </button>
                        <button wire:click="checkQrConnection" class="px-5 py-2 rounded-xl bg-[#064E3B] hover:bg-[#043327] text-white font-extrabold text-xs shadow-sm transition cursor-pointer"
                            style="border: 1px solid #C9A84C;">
                            Cek Koneksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
