@extends('staf.layout', ['title' => 'Edit Profil — ' . ($pegawai->nama_lengkap ?? $user->name)])

@section('content')
<div class="space-y-5 pb-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('staf.profil') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#064E3B] hover:text-emerald-700 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm transition">
            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Profil</span>
        </a>
        <span class="text-[11px] bg-amber-100 text-amber-900 border border-amber-300 font-bold px-3 py-1 rounded-full">
            Halaman Edit Data
        </span>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
    <div class="p-4 bg-red-50 border-2 border-red-300 rounded-2xl shadow-sm space-y-1">
        <div class="flex items-center gap-2 text-red-800 font-bold text-xs">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Terdapat kesalahan saat pengisian formulir:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] text-red-700 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main Edit Form Card --}}
    <div class="sadi-card p-6 bg-white border-2 border-[#C9A84C] space-y-5 shadow-xl">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="font-outfit font-extrabold text-[#064E3B] text-base flex items-center gap-2">
                <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Formulir Perubahan Profil Staf</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui data foto, nama, username, dan identitas kedinasan Anda</p>
        </div>

        <form action="{{ route('staf.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            {{-- 1. Upload Foto Profil Section --}}
            <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-200/80 space-y-3">
                <label class="block font-extrabold text-[#064E3B] uppercase tracking-wider text-[11px]">
                    Foto Profil Resmi
                </label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-slate-200 border-2 border-[#C9A84C] shadow-md overflow-hidden flex items-center justify-center shrink-0">
                        @if($user->foto_profil || ($pegawai && $pegawai->foto_profil))
                            <img src="{{ asset('storage/' . ($user->foto_profil ?? $pegawai->foto_profil)) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-11 h-11 text-slate-400 translate-y-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <input type="file" name="foto_profil" accept="image/png, image/jpeg, image/jpg, image/webp"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#064E3B] file:text-white hover:file:bg-[#04392B] cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Pilih foto format JPG, PNG, atau WEBP (Maksimal 2MB).</p>
                    </div>
                </div>
            </div>

            {{-- 2. Username Login Portal --}}
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username Login Portal <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-slate-400 font-mono font-bold">@</span>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                        class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm font-bold text-[#064E3B] focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Username ini digunakan untuk login presensi mandiri Anda.</p>
            </div>

            {{-- 3. Nama Lengkap & Gelar --}}
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? $user->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
            </div>

            {{-- 4. No WhatsApp / HP --}}
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp ?? '') }}" placeholder="08..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-mono text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
            </div>

            {{-- 5. Tempat & Tanggal Lahir --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir ?? '') }}" placeholder="Tasikmalaya"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                </div>
            </div>

            {{-- 6. Alamat Domisili --}}
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Domisili</label>
                <textarea name="alamat" rows="2" placeholder="Desa Nangtang, Kec. Cigalontang..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('staf.profil') }}"
                    class="flex-1 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition text-center flex items-center justify-center">
                    Batal
                </a>
                <button type="submit"
                    class="flex-[2] btn-sadi-primary py-3 rounded-xl text-white font-bold flex items-center justify-center gap-2 cursor-pointer shadow-lg">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
