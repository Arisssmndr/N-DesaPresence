<div class="space-y-6">

    <!-- Page Header & Top Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Master Data Pegawai</h1>
            <p class="text-xs text-slate-500 mt-1">Pengelolaan data identitas Perangkat & Staf Desa Nangtang</p>
        </div>
        <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Pegawai Baru</span>
        </button>
    </div>

    <!-- Filters & Search Bar -->
    <div class="sadi-card p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-80 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIK, atau NIPD..." class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select wire:model.live="filterJabatan" class="px-3 py-2.5 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-700">
                <option value="">Semua Jabatan</option>
                @foreach ($jabatans as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="px-3 py-2.5 text-xs rounded-xl bg-slate-50 border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-700">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
                <option value="">Semua Status</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="sadi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center">Foto</th>
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">Akun Portal Staf</th>
                        <th class="py-3.5 px-4">Jabatan</th>
                        <th class="py-3.5 px-4">NIK / NIPD</th>
                        <th class="py-3.5 px-4">Shift Kerja</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($pegawais as $p)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 text-center">
                                <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden mx-auto border-2 border-[#C9A84C] shadow-sm flex items-center justify-center">
                                    @if ($p->foto_profil)
                                        <img src="{{ Storage::url($p->foto_profil) }}" alt="{{ $p->nama_lengkap }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-7 h-7 text-slate-400 translate-y-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                        </svg>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $p->nama_lengkap }}</span>
                                    @if ($p->jenis_kelamin === 'L')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200" title="Laki-laki">
                                            ♂ L
                                        </span>
                                    @elseif ($p->jenis_kelamin === 'P')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-pink-50 text-pink-700 border border-pink-200" title="Perempuan">
                                            ♀ P
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-slate-400 font-normal">{{ $p->no_hp ?? 'Tidak ada HP' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @if($p->user)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 border border-emerald-300 shadow-sm">
                                        <span class="text-[#064E3B] font-mono font-bold text-xs">@ {{ $p->user->username }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                                        Belum Ada Akun
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $p->jabatan->nama_jabatan ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                <p>NIK: {{ $p->nik }}</p>
                                <p class="text-[10px] text-slate-400">NIPD: {{ $p->nipd ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $p->shiftKerja->nama_shift ?? 'Shift Pagi' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($p->status_aktif)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <button wire:click="openEditModal({{ $p->id }})" class="p-2 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-300 transition shadow-2xs cursor-pointer inline-flex items-center gap-1 text-xs font-bold" title="Edit Pegawai & Akun">
                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </button>
                                <button wire:click="toggleStatus({{ $p->id }})" class="p-2 rounded-lg {{ $p->status_aktif ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-300' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300' }} transition shadow-2xs cursor-pointer inline-flex items-center gap-1 text-xs font-bold" title="{{ $p->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    @if ($p->status_aktif)
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        <span>Nonaktifkan</span>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Aktifkan</span>
                                    @endif
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                Belum ada data pegawai yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $pegawais->links() }}
        </div>
    </div>

    <!-- Modal Form (Tambah / Edit Pegawai) -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/30 my-8">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-lg font-bold text-white">{{ $isEdit ? 'Edit Data Pegawai & Akun Portal' : 'Tambah Pegawai Baru' }}</h3>
                        <p class="text-xs text-emerald-200">Lengkapi data identitas perangkat / staf desa beserta username login portal</p>
                    </div>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="save" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <!-- NIK -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK (Nomor Induk Kependudukan 16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nik" maxlength="16" placeholder="3206..." class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            @error('nik') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nama_lengkap" placeholder="Contoh: Ahmad Sopian, S.IP" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            @error('nama_lengkap') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select wire:model="jenis_kelamin" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-medium text-slate-800">
                                <option value="L">♂ Laki-laki</option>
                                <option value="P">♀ Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username Akun Portal Staf -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username Login Portal Staf</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-[#064E3B] font-bold font-mono text-xs">@</span>
                                <input type="text" wire:model="username" placeholder="contoh: ahmadsopian"
                                       class="w-full pl-8 pr-3 py-2 text-xs font-mono rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Username unik untuk staf login presensi mandiri.</p>
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan Desa <span class="text-red-500">*</span></label>
                            <select wire:model="jabatan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                                <option value="">Pilih Jabatan</option>
                                @foreach ($jabatans as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                            @error('jabatan_id') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIPD -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIPD (Nomor Induk Perangkat)</label>
                            <input type="text" wire:model="nipd" placeholder="NIPD perangkat desa" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        </div>

                        <!-- Shift Kerja -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Shift Kerja</label>
                            <select wire:model="shift_id" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                                @foreach ($shifts as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama_shift }} ({{ substr($s->jam_masuk,0,5) }} - {{ substr($s->jam_pulang,0,5) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Siltap Bruto -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Siltap Bruto (Rp)</label>
                            <input type="number" wire:model="siltap_bruto" placeholder="3000000" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                        </div>

                        <!-- Foto Profil -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Foto Profil</label>
                            <input type="file" wire:model="foto_profil" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white hover:file:bg-[#04392B]">
                            @error('foto_profil') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-xl text-xs font-bold bg-[#064E3B] text-white hover:bg-[#04392B] shadow-md transition">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pegawai' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
