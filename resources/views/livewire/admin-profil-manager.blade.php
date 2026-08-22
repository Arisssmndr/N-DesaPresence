<div class="p-6 space-y-8 max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#C9A84C]/20 pb-5">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#064E3B]/10 border border-[#C9A84C]/30 text-[#064E3B] text-xs font-bold mb-2">
                <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Pengaturan Akun & Keamanan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-outfit font-extrabold text-[#064E3B]">Profil Administrator</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Perbarui username, data profil, dan kata sandi akun login Anda</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-100/80 border border-emerald-300 text-emerald-900 text-xs font-extrabold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Role: {{ strtoupper($user->role ?? 'ADMIN') }}</span>
            </span>
        </div>
    </div>

    {{-- Identity Overview Card --}}
    <div class="sadi-card p-6 bg-gradient-to-r from-white via-white to-amber-50/40 border-2 border-[#C9A84C]/30 shadow-md">
        <div class="flex flex-col sm:flex-row items-center gap-5">
            <div class="relative group">
                <div class="w-20 h-20 rounded-2xl bg-[#064E3B] text-[#C9A84C] font-outfit font-extrabold text-2xl flex items-center justify-center border-2 border-[#C9A84C] shadow-lg overflow-hidden shrink-0">
                    @if($currentFoto)
                        <img src="{{ Storage::url($currentFoto) }}" class="w-full h-full object-cover" alt="Foto Profil">
                    @else
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    @endif
                </div>
            </div>
            <div class="text-center sm:text-left space-y-1 flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h2 class="text-xl font-outfit font-extrabold text-slate-800">{{ $user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#C9A84C]/20 text-amber-900 border border-[#C9A84C]/40">
                        {{ $user->role === 'admin' ? 'Administrator / Sekdes' : ($user->role === 'kepala_desa' ? 'Kepala Desa' : ucfirst($user->role)) }}
                    </span>
                </div>
                <p class="text-xs font-mono font-bold text-[#064E3B] flex items-center justify-center sm:justify-start gap-1">
                    <span>@</span><span>{{ $user->username }}</span>
                </p>
                <p class="text-[11px] text-slate-400">
                    Terdaftar sejak: {{ $user->created_at ? $user->created_at->isoFormat('D MMMM Y') : '-' }} · Login terakhir: {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Sesi ini' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 2 Kolom Form: Profil (Kiri) & Ganti Password (Kanan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ─────── FORM 1: GANTI PROFIL & USERNAME ─────── --}}
        <div class="lg:col-span-6 space-y-6">
            <div class="sadi-card p-6 bg-white border border-[#C9A84C]/30 shadow-md">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-[#064E3B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-outfit font-extrabold text-[#064E3B] text-base">Ubah Username & Profil</h3>
                        <p class="text-[11px] text-slate-500">Sesuaikan nama lengkap dan username login Anda</p>
                    </div>
                </div>

                @if (session('success_profil'))
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-300 rounded-xl text-xs text-emerald-900 font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success_profil') }}</span>
                    </div>
                @endif

                <form wire:submit="updateProfil" class="space-y-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name" placeholder="Nama Lengkap Anda" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none transition">
                        @error('name') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username Login <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono font-bold text-sm">@</span>
                            <input type="text" wire:model="username" placeholder="username_anda" required
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none transition">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Gunakan huruf kecil, angka, dan tanpa spasi untuk kemudahan login.</p>
                        @error('username') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email (Opsional)
                        </label>
                        <input type="email" wire:model="email" placeholder="admin@desanangtang.go.id"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none transition">
                        @error('email') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Foto Profil Upload --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ganti Foto Profil (Opsional)
                        </label>
                        <input type="file" wire:model="foto_profil" accept="image/*"
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#064E3B] hover:file:bg-emerald-100 cursor-pointer">
                        <div wire:loading wire:target="foto_profil" class="text-xs text-amber-600 mt-1 font-semibold">Mengunggah foto...</div>
                        @error('foto_profil') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-3">
                        <button type="submit" wire:loading.attr="disabled"
                            class="btn-sadi-primary w-full py-3 px-5 rounded-xl text-white font-extrabold text-xs tracking-wide shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <svg wire:loading.remove wire:target="updateProfil" class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span wire:loading.remove wire:target="updateProfil">SIMPAN PERUBAHAN PROFIL</span>
                            <span wire:loading wire:target="updateProfil" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ─────── FORM 2: GANTI PASSWORD ─────── --}}
        <div class="lg:col-span-6 space-y-6">
            <div class="sadi-card p-6 bg-white border border-[#C9A84C]/30 shadow-md">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-outfit font-extrabold text-[#064E3B] text-base">Ganti Kata Sandi (Password)</h3>
                        <p class="text-[11px] text-slate-500">Pastikan menggunakan kombinasi password yang aman</p>
                    </div>
                </div>

                @if (session('success_password'))
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-300 rounded-xl text-xs text-emerald-900 font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success_password') }}</span>
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-4">
                    {{-- Password Saat Ini --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password_saat_ini" placeholder="••••••••" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none transition">
                        @error('password_saat_ini') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password_baru" placeholder="Minimal 6 karakter" required minlength="6"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none transition">
                        @error('password_baru') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ulangi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password_baru_confirmation" placeholder="Ulangi password baru" required minlength="6"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none transition">
                    </div>

                    {{-- Tips Keamanan --}}
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-900 space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Petunjuk Keamanan:</span>
                        </p>
                        <p class="text-amber-800/90 leading-relaxed">
                            Setelah mengganti password, Anda tetap masuk pada sesi ini. Gunakan password baru untuk masuk pada perangkat lain atau login berikutnya.
                        </p>
                    </div>

                    <div class="pt-3">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-3 px-5 rounded-xl btn-gold text-[#064E3B] font-extrabold text-xs tracking-wide shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <svg wire:loading.remove wire:target="updatePassword" class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span wire:loading.remove wire:target="updatePassword">PERBARUI PASSWORD</span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Memperbarui...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
