<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-outfit font-extrabold text-[#064E3B]">Konfigurasi WiFi Absensi</h1>
            <p class="text-slate-600 text-xs font-medium mt-1">Kelola daftar IP jaringan WiFi yang diizinkan untuk absensi tanda tangan staf</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Link Portal --}}
            <a href="{{ route('staf.login') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-[#C9A84C] text-[#064E3B] text-xs font-bold hover:bg-[#C9A84C]/10 transition-all">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>Buka Portal Absensi</span>
            </a>
            <button wire:click="tambahBaru"
                class="btn-sadi-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-xs font-bold shadow-md transition-all cursor-pointer">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah IP Baru</span>
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-300 rounded-2xl">
        <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-900 text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- IP Client Saat Ini & Quick Add --}}
    <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs" style="background:linear-gradient(135deg,#064E3B,#04392B)">
                    <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-xs text-slate-700 font-bold uppercase tracking-wider">IP Perangkat / Jaringan Anda Saat Ini</p>
                        @if(str_starts_with($clientIp, '192.168.') || str_starts_with($clientIp, '10.') || str_starts_with($clientIp, '172.'))
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-800 border border-blue-200">IP Lokal (LAN)</span>
                        @elseif($clientIp === '127.0.0.1' || $clientIp === '::1')
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">Localhost Server</span>
                        @else
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">IP Publik (Online WAN)</span>
                        @endif
                    </div>
                    <p class="text-2xl font-mono font-extrabold text-[#064E3B] mt-0.5">{{ $clientIp }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Jika Anda sedang terhubung ke WiFi kantor desa, gunakan tombol di samping untuk mendaftarkannya otomatis.</p>
                </div>
            </div>

            {{-- Quick Add Buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="gunakanIpLangsung('{{ $clientIp }}')"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-300 hover:bg-emerald-100 transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Gunakan IP Ini ({{ $clientIp }})</span>
                </button>
                @if(str_starts_with($clientIp, '192.168.') || str_starts_with($clientIp, '10.') || str_starts_with($clientIp, '172.'))
                <button wire:click="gunakanSubnetLangsung('{{ $clientIp }}')"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold bg-[#064E3B] text-white hover:bg-[#04392B] transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Daftarkan Subnet ({{ $subnetSaran }})</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Petunjuk Singkat --}}
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-700 flex items-start gap-2.5 leading-relaxed">
            <svg class="w-4 h-4 text-[#064E3B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong class="text-slate-900">Cara Kerja Otomatis:</strong> Staf yang terhubung ke WiFi kantor desa akan otomatis diizinkan absen. Staf yang menggunakan paket data seluler sendiri atau WiFi dari luar kantor desa akan otomatis <strong class="text-rose-700">ditolak / diblokir</strong> oleh sistem.
            </div>
        </div>
    </div>

    {{-- Form Tambah/Edit --}}
    @if($showForm)
    <div class="sadi-card p-6 bg-white border-2 border-[#064E3B]/20 rounded-2xl shadow-md">
        <h3 class="font-outfit font-extrabold text-[#064E3B] text-base mb-5 flex items-center gap-2">
            @if($editingId)
                <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Konfigurasi WiFi</span>
            @else
                <svg class="w-5 h-5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Konfigurasi WiFi Baru</span>
            @endif
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Nama Jaringan *</label>
                <input type="text" wire:model="form.nama_jaringan" placeholder="WiFi Kantor Desa Nangtang"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                @error('form.nama_jaringan') <p class="text-rose-600 font-bold text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Alamat IP / CIDR *</label>
                <input type="text" wire:model="form.ip_address" placeholder="192.168.1.0/24 atau 192.168.1.1"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                @error('form.ip_address') <p class="text-rose-600 font-bold text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-[11px] text-slate-500 font-medium mt-1">Format: IP tunggal, range CIDR (x.x.x.x/24), atau wildcard (192.168.1.*)</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-800 mb-1.5 uppercase tracking-wide">Keterangan</label>
                <input type="text" wire:model="form.keterangan" placeholder="Deskripsi singkat jaringan ini"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-900 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
            </div>

            {{-- Toggle Aktifkan Jaringan Ini (Geometrically Perfect) --}}
            <div class="sm:col-span-2 pt-2">
                <label class="inline-flex items-center gap-3.5 cursor-pointer select-none p-3.5 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition w-full sm:w-auto">
                    <div class="relative shrink-0 inline-flex items-center">
                        <input type="checkbox" wire:model="form.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:bg-[#064E3B] transition-colors duration-200 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-200 after:shadow-sm peer-checked:after:translate-x-5"></div>
                    </div>
                    <div>
                        <span class="text-xs font-extrabold text-slate-900 block">Aktifkan jaringan ini</span>
                        <span class="text-[10.5px] text-slate-500 font-medium block">Hanya jaringan aktif yang diizinkan untuk verifikasi absensi masuk & pulang</span>
                    </div>
                </label>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button wire:click="$set('showForm', false)"
                class="px-6 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                Batal
            </button>
            <button wire:click="simpan"
                class="btn-sadi-primary px-8 py-2.5 rounded-xl text-white text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-md">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span wire:loading.remove wire:target="simpan">Simpan Konfigurasi</span>
                <span wire:loading wire:target="simpan">Menyimpan...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Tabel Daftar WiFi --}}
    <div class="sadi-card overflow-hidden bg-white border border-slate-200/80 rounded-2xl shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-outfit font-extrabold text-[#064E3B] text-sm">Daftar Jaringan Terdaftar</h3>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Hanya 1 jaringan WiFi yang aktif sebagai titik presensi resmi kantor desa</p>
            </div>
            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">Total: {{ $daftarWifi->count() }} jaringan</span>
        </div>

        @if($daftarWifi->isEmpty())
        <div class="text-center py-12">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
            <p class="text-slate-600 text-xs font-bold">Belum ada jaringan WiFi terdaftar</p>
            <p class="text-slate-400 text-xs mt-1">Klik "Tambah IP Baru" untuk mendaftarkan jaringan WiFi desa</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#064E3B] text-white border-b border-[#064E3B]">
                    <tr>
                        <th class="px-5 py-3 text-left font-extrabold uppercase tracking-wider text-[11px]">Nama Jaringan</th>
                        <th class="px-5 py-3 text-left font-extrabold uppercase tracking-wider text-[11px]">Alamat IP</th>
                        <th class="px-5 py-3 text-left font-extrabold uppercase tracking-wider text-[11px]">Keterangan</th>
                        <th class="px-5 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Status</th>
                        <th class="px-5 py-3 text-center font-extrabold uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($daftarWifi as $wifi)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ $wifi->is_active ? 'bg-emerald-50/20' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-900">{{ $wifi->nama_jaringan }}</p>
                                @if($wifi->is_active)
                                    <span class="text-[9.5px] font-bold text-[#064E3B] bg-emerald-100/90 px-1.5 py-0.5 rounded border border-emerald-300 uppercase tracking-wider">Utama</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-bold text-[#064E3B] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                {{ $wifi->ip_address }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 font-medium text-xs">{{ $wifi->keterangan ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <button wire:click="toggleAktif({{ $wifi->id }})" class="focus:outline-none cursor-pointer group" title="Klik untuk mengaktifkan/menonaktifkan jaringan ini">
                                @if($wifi->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-50 text-emerald-900 border border-emerald-300 group-hover:bg-emerald-100 text-[11px] font-bold transition shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                        <span>WiFi Utama Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200 group-hover:bg-slate-200 text-[11px] font-semibold transition">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Cadangan (Nonaktif)</span>
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <button wire:click="editData({{ $wifi->id }})" title="Edit"
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-[#064E3B] hover:bg-slate-100 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="hapus({{ $wifi->id }})"
                                    wire:confirm="Yakin ingin menghapus konfigurasi '{{ $wifi->nama_jaringan }}'? Aksi ini tidak dapat dibatalkan."
                                    title="Hapus"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Panduan Format IP --}}
    <div class="sadi-card p-5 bg-white border border-slate-200/80 rounded-2xl shadow-xs">
        <h4 class="font-outfit font-extrabold text-[#064E3B] text-xs mb-3 uppercase tracking-wider">📌 Panduan Format Alamat IP</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-50/80 border border-blue-200 rounded-xl p-3.5">
                <p class="text-xs font-bold text-blue-900 mb-1">IP Tunggal</p>
                <code class="text-xs text-blue-700 font-mono font-bold">192.168.1.100</code>
                <p class="text-[11px] text-blue-800 font-medium mt-1">Satu perangkat atau gateway spesifik</p>
            </div>
            <div class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3.5">
                <p class="text-xs font-bold text-emerald-900 mb-1">Range CIDR</p>
                <code class="text-xs text-emerald-700 font-mono font-bold">192.168.1.0/24</code>
                <p class="text-[11px] text-emerald-800 font-medium mt-1">Seluruh subnet (192.168.1.1–254)</p>
            </div>
            <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3.5">
                <p class="text-xs font-bold text-amber-900 mb-1">Wildcard</p>
                <code class="text-xs text-amber-800 font-mono font-bold">192.168.1.*</code>
                <p class="text-[11px] text-amber-900 font-medium mt-1">Semua IP dalam kelompok tersebut</p>
            </div>
        </div>
    </div>

</div>

