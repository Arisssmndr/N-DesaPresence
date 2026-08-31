<div class="space-y-6">

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 1. HEADER HALAMAN                                                      -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pengaturan Jadwal Kedinasan</span>
                </span>
                <span class="text-xs text-slate-400 font-medium">Presence Desa Nangtang</span>
            </div>
            <h1 class="font-outfit text-2xl font-extrabold text-[#064E3B] tracking-tight mt-1">
                Jam Kerja & Waktu Absensi
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pusat kendali jadwal jam dinas resmi, toleransi keterlambatan, dan jendela buka/tutup tombol presensi staf
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-slate-700 text-xs font-bold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                <span>Server: <strong class="font-mono text-[#064E3B]">{{ $nowTime }} WIB</strong></span>
            </div>
            <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white font-bold text-xs shadow-md transition cursor-pointer">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Shift</span>
            </button>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2. BAGIAN 1: JAM KERJA RESMI & SHIFT APARATUR (ACUAN HITUNGAN KINERJA)  -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-outfit text-sm font-extrabold text-[#064E3B] flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>1. Jam Kerja Resmi Kantor Desa (Standar Jam Dinas)</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Dipakai sebagai patokan penilaian <strong>Tepat Waktu</strong>, <strong>Terlambat (menit)</strong>, dan target <strong>7.5 Jam Kerja Efektif/Hari</strong>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($shifts as $s)
                <div class="p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-slate-50 transition space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="font-outfit text-sm font-bold text-slate-900">{{ $s->nama_shift }}</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $s->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-200 text-slate-600' }}">
                            {{ $s->is_active ? 'Aktif Digunakan' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-white p-3 rounded-lg border border-slate-200">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Jam Masuk</p>
                            <p class="font-bold text-emerald-800 text-sm mt-0.5">{{ substr($s->jam_masuk, 0, 5) }} WIB</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Jam Pulang</p>
                            <p class="font-bold text-slate-800 text-sm mt-0.5">{{ substr($s->jam_pulang, 0, 5) }} WIB</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                        <span class="text-[11px]">Toleransi: <strong class="text-amber-700 font-mono">{{ $s->toleransi_menit }} Menit</strong></span>
                        <span class="text-[11px]">Aparatur: <strong class="text-[#064E3B] font-bold">{{ $s->pegawais_count }} Orang</strong></span>
                    </div>

                    <div class="pt-2 border-t border-slate-200/70 flex justify-end">
                        <button wire:click="openEditModal({{ $s->id }})" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100 transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Ubah Jam Shift</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 3. BAGIAN 2: JENDELA WAKTU BUKA / TUTUP PORTAL (GATE PRESENSI MANDIRI)  -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="sadi-card p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-outfit text-sm font-extrabold text-[#064E3B] flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>2. Jendela Waktu Buka/Tutup Tombol Presensi di HP Staf</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Menentukan rentang waktu tombol presensi <strong>bisa diklik</strong> oleh staf agar tidak melakukan presensi di luar batas jam dinas
                </p>
            </div>
        </div>

        <form wire:submit.prevent="simpanJendelaAbsensi" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                {{-- Panel Gate Masuk --}}
                <div class="p-4 rounded-xl bg-emerald-50/40 border border-emerald-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-outfit font-bold text-slate-900 text-xs">Jendela Presensi Masuk</h4>
                                <p class="text-[10px] text-slate-500">Rentang tombol absen masuk dibuka</p>
                            </div>
                        </div>
                        @if($isMasukNow)
                            <span class="px-2 py-0.5 bg-emerald-600 text-white rounded-full text-[9px] font-extrabold uppercase tracking-wider">Sedang Buka</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full text-[9px] font-bold uppercase tracking-wider">Tutup</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Buka Pukul</label>
                            <input type="time" wire:model="jam_masuk_mulai"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                            @error('jam_masuk_mulai') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Tutup Pukul</label>
                            <input type="time" wire:model="jam_masuk_selesai"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                            @error('jam_masuk_selesai') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Panel Gate Pulang --}}
                <div class="p-4 rounded-xl bg-amber-50/40 border border-amber-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-outfit font-bold text-slate-900 text-xs">Jendela Presensi Pulang</h4>
                                <p class="text-[10px] text-slate-500">Rentang tombol absen pulang dibuka</p>
                            </div>
                        </div>
                        @if($isPulangNow)
                            <span class="px-2 py-0.5 bg-amber-600 text-white rounded-full text-[9px] font-extrabold uppercase tracking-wider">Sedang Buka</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full text-[9px] font-bold uppercase tracking-wider">Tutup</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Buka Pukul</label>
                            <input type="time" wire:model="jam_pulang_mulai"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                            @error('jam_pulang_mulai') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Tutup Pukul</label>
                            <input type="time" wire:model="jam_pulang_selesai"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono text-slate-800 focus:outline-none focus:border-[#064E3B] focus:ring-1 focus:ring-[#064E3B]">
                            @error('jam_pulang_selesai') <p class="text-rose-600 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <p class="text-[11px] text-slate-400 italic">
                    * Perubahan jendela waktu langsung berlaku secara instan pada portal presensi seluruh perangkat desa.
                </p>
                <button type="submit" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2 rounded-xl text-white text-xs font-bold shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Jendela Absensi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL FORM TAMBAH / EDIT SHIFT                                         -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-base font-extrabold text-white">{{ $isEdit ? 'Ubah Jam Shift Kerja' : 'Tambah Shift Kerja Baru' }}</h3>
                        <p class="text-[10px] text-emerald-200">Konfigurasi patokan jam dinas resmi</p>
                    </div>
                    <button wire:click="closeModal" class="p-1 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Shift <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama_shift" placeholder="Contoh: Shift Reguler Kantor Desa" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B]">
                        @error('nama_shift') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Masuk Resmi <span class="text-rose-500">*</span></label>
                            <input type="time" wire:model="jam_masuk" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-mono focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Pulang Resmi <span class="text-rose-500">*</span></label>
                            <input type="time" wire:model="jam_pulang" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-mono focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batas Toleransi Keterlambatan (Menit) <span class="text-rose-500">*</span></label>
                        <input type="number" wire:model="toleransi_menit" min="0" max="120" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#064E3B] focus:border-[#064E3B]">
                        <p class="text-[10px] text-slate-400 mt-1">Contoh: 15 menit. Masuk jam 08:15 tetap dihitung tepat waktu.</p>
                        @error('toleransi_menit') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">Batal</button>
                        <button type="submit" class="btn-sadi-primary px-5 py-2 rounded-xl text-xs font-bold text-white shadow-md transition cursor-pointer">Simpan Shift</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
