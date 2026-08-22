<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-outfit font-extrabold text-[#064E3B]">Manajemen Akun Staf Desa</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola akun staf perangkat desa untuk login portal absensi tanda tangan (username-only)</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="tambahBaru"
                class="btn-sadi-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-md hover:opacity-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Akun Staf Baru
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

    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Form Tambah/Edit Akun --}}
    @if($showForm)
    <div class="sadi-card p-6 border-2 border-[#C9A84C]/30 bg-white">
        <h3 class="font-outfit font-bold text-[#064E3B] text-base mb-5 flex items-center gap-2">
            @if($editingId)
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Akun Pengguna / Staf</span>
            @else
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Akun Pengguna / Staf Baru</span>
            @endif
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pilih Data Pegawai Terdaftar</label>
                <select wire:model.live="form.pegawai_id"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                    <option value="">— Tidak terhubung / Pegawai Luar —</option>
                    @foreach($daftarPegawai as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? 'Perangkat' }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Memilih pegawai otomatis mengisi nama, saran username, dan email.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Lengkap *</label>
                <input type="text" wire:model="form.name" placeholder="Nama Lengkap Staf"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Username Login (Tanpa Spasi) *</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-sm">@</span>
                    <input type="text" wire:model="form.username" placeholder="budisantoso"
                        class="w-full pl-8 pr-4 py-3 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                </div>
                @error('form.username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mt-1">Username ini digunakan staf saat memilih/memasukkan akun di HP.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Email (Opsional)</label>
                <input type="email" wire:model="form.email" placeholder="staf@desanangtang.go.id"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                @error('form.email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Hak Akses (Role) *</label>
                <select wire:model="form.role"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                    <option value="perangkat">Perangkat Desa (Portal Absensi Staf)</option>
                    <option value="kepala_desa">Kepala Desa (Portal Staf & Monitoring)</option>
                    <option value="admin">Administrator / Sekdes (Full Akses)</option>
                    <option value="auditor">Auditor Inspektorat (Read-Only)</option>
                </select>
                @error('form.role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                    Password / Kata Sandi {{ $editingId ? '(Kosongkan jika tidak ingin diubah)' : '(Opsional - default: admin123 untuk role Admin/Kades)' }}
                </label>
                <input type="password" wire:model="form.password" placeholder="{{ $editingId ? 'Masukkan password baru jika ingin mengubah' : 'Password login minimal 6 karakter' }}"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/10">
                @error('form.password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-[11px] text-slate-400 mt-1">Staf biasa/perangkat dapat login tanpa password menggunakan username via portal mobile WiFi Desa.</p>
            </div>

            <div class="flex items-center gap-3 sm:col-span-2 pt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="form.is_active" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#064E3B]"></div>
                </label>
                <span class="text-sm text-slate-700 font-medium">Akun aktif & dapat melakukan presensi</span>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button wire:click="$set('showForm', false)"
                class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                Batal
            </button>
            <button wire:click="simpan"
                class="btn-sadi-primary px-8 py-2.5 rounded-xl text-white text-sm font-bold shadow-md transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span wire:loading.remove wire:target="simpan">Simpan Akun</span>
                <span wire:loading wire:target="simpan">Menyimpan...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Filter Search & Tabel Akun --}}
    <div class="sadi-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative w-full sm:w-72">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau @username..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#064E3B]">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <span class="text-xs text-slate-500">Total: {{ $users->count() }} akun terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama & Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Username Login</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Role Akses</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#064E3B]/10 border border-[#C9A84C]/30 flex items-center justify-center text-[#064E3B] font-bold text-sm shrink-0 overflow-hidden">
                                    @if($u->foto_profil || ($u->pegawai && $u->pegawai->foto_profil))
                                        <img src="{{ Storage::url($u->foto_profil ?? $u->pegawai->foto_profil) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $u->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $u->pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="bg-emerald-50 text-[#064E3B] px-2.5 py-1 rounded-lg text-xs font-mono font-bold">
                                @ {{ $u->username }}
                            </code>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $roleBadge = match($u->role) {
                                    'admin' => ['Admin Sekdes', 'bg-emerald-100 text-emerald-800 border border-emerald-300'],
                                    'kepala_desa' => ['Kepala Desa', 'bg-amber-100 text-amber-800'],
                                    'perangkat' => ['Perangkat Desa', 'bg-emerald-100 text-emerald-800'],
                                    'auditor' => ['Auditor', 'bg-blue-100 text-blue-800'],
                                    default => [$u->role, 'bg-slate-100 text-slate-800'],
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $roleBadge[1] }}">
                                {{ $roleBadge[0] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleAktif({{ $u->id }})" class="focus:outline-none">
                                @if($u->is_active)
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
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="editData({{ $u->id }})" title="Edit Akun"
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($u->id !== auth()->id())
                                <button wire:click="hapus({{ $u->id }})"
                                    wire:confirm="Yakin ingin menghapus akun @{{ $u->username }} ({{ $u->name }})?"
                                    title="Hapus Akun"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            Tidak ada akun yang sesuai dengan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
