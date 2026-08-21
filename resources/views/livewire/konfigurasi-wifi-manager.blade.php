<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-outfit font-extrabold text-[#064E3B]">Konfigurasi WiFi Absensi</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola daftar IP jaringan WiFi yang diizinkan untuk absensi tanda tangan</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Link Portal --}}
            <a href="{{ route('staf.login') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-[#C9A84C] text-[#064E3B] text-sm font-bold hover:bg-[#C9A84C]/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Portal Absensi
            </a>
            <button wire:click="tambahBaru"
                class="btn-sadi-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah IP Baru
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-800 text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- IP Client Saat Ini --}}
    <div class="sadi-card p-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#064E3B,#04392B)">
                <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">IP Browser Anda Saat Ini</p>
                <p class="text-xl font-mono font-bold text-[#064E3B]">{{ $clientIp }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Gunakan IP ini jika ingin mendaftarkan komputer/jaringan Anda</p>
            </div>
        </div>
    </div>

    {{-- Form Tambah/Edit --}}
    @if($showForm)
    <div class="sadi-card p-6 border-2 border-[#C9A84C]/30">
        <h3 class="font-outfit font-bold text-[#064E3B] text-base mb-5 flex items-center gap-2">
            @if($editingId)
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Konfigurasi WiFi</span>
            @else
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Konfigurasi WiFi Baru</span>
            @endif
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Jaringan *</label>
                <input type="text" wire:model="form.nama_jaringan" placeholder="WiFi Kantor Desa Nangtang"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                @error('form.nama_jaringan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Alamat IP / CIDR *</label>
                <input type="text" wire:model="form.ip_address" placeholder="192.168.1.0/24 atau 192.168.1.1"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                @error('form.ip_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">Format: IP tunggal, range CIDR (x.x.x.x/24), atau wildcard (192.168.1.*)</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Keterangan</label>
                <input type="text" wire:model="form.keterangan" placeholder="Deskripsi singkat jaringan ini"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
            </div>
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="form.is_active" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#064E3B]"></div>
                </label>
                <span class="text-sm text-slate-600 font-medium">Aktifkan jaringan ini</span>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button wire:click="$set('showForm', false)"
                class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </button>
            <button wire:click="simpan"
                class="btn-sadi-primary px-8 py-2.5 rounded-xl text-white text-sm font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span wire:loading.remove wire:target="simpan">Simpan Konfigurasi</span>
                <span wire:loading wire:target="simpan">Menyimpan...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Tabel Daftar WiFi --}}
    <div class="sadi-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-outfit font-bold text-[#064E3B] text-sm">Daftar Jaringan Terdaftar</h3>
            <span class="text-xs text-slate-400">Total: {{ $daftarWifi->count() }} jaringan</span>
        </div>

        @if($daftarWifi->isEmpty())
        <div class="text-center py-12">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
            <p class="text-slate-500 text-sm font-medium">Belum ada jaringan WiFi terdaftar</p>
            <p class="text-slate-400 text-xs mt-1">Klik "Tambah IP Baru" untuk mendaftarkan jaringan WiFi desa</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Jaringan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat IP</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($daftarWifi as $wifi)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ $wifi->nama_jaringan }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <code class="bg-emerald-50 text-[#064E3B] px-2 py-1 rounded-lg text-xs font-mono font-bold">
                                {{ $wifi->ip_address }}
                            </code>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $wifi->keterangan ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleAktif({{ $wifi->id }})" class="focus:outline-none">
                                @if($wifi->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="editData({{ $wifi->id }})" title="Edit"
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="hapus({{ $wifi->id }})"
                                    wire:confirm="Yakin ingin menghapus konfigurasi '{{ $wifi->nama_jaringan }}'? Aksi ini tidak dapat dibatalkan."
                                    title="Hapus"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition">
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
    <div class="sadi-card p-5">
        <h4 class="font-outfit font-bold text-[#064E3B] text-sm mb-3">📌 Panduan Format Alamat IP</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-xl p-3">
                <p class="text-xs font-bold text-blue-700 mb-1">IP Tunggal</p>
                <code class="text-xs text-blue-600 font-mono">192.168.1.100</code>
                <p class="text-xs text-blue-500 mt-1">Satu perangkat atau gateway spesifik</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3">
                <p class="text-xs font-bold text-emerald-700 mb-1">Range CIDR</p>
                <code class="text-xs text-emerald-600 font-mono">192.168.1.0/24</code>
                <p class="text-xs text-emerald-500 mt-1">Seluruh subnet (192.168.1.1–254)</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-3">
                <p class="text-xs font-bold text-amber-700 mb-1">Wildcard</p>
                <code class="text-xs text-amber-600 font-mono">192.168.1.*</code>
                <p class="text-xs text-amber-500 mt-1">Semua IP dalam kelompok tersebut</p>
            </div>
        </div>
    </div>

</div>
