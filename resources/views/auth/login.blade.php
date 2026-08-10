@extends('layouts.auth')

@section('content')
<div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 border border-[#C9A84C]/20">

    <!-- Left Branding Panel (Dark Emerald Green 30%) -->
    <div class="md:col-span-5 bg-gradient-to-b from-[#064E3B] to-[#04392B] p-8 md:p-12 text-white flex flex-col justify-between items-center text-center relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-[#C9A84C]/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-[#059669]/20 rounded-full blur-2xl"></div>

        <div class="relative z-10 my-auto">
            <!-- Monogram N Logo in Gold Circle -->
            <div class="w-24 h-24 rounded-full border-4 border-[#C9A84C] bg-[#04392B] flex items-center justify-center mx-auto shadow-2xl mb-6">
                <span class="font-outfit text-5xl font-extrabold text-[#C9A84C]">N</span>
            </div>

            <h1 class="font-outfit text-2xl font-bold tracking-tight text-white mb-2">PEMERINTAH DESA NANGTANG</h1>
            <div class="h-1 w-16 bg-[#C9A84C] mx-auto rounded-full mb-3"></div>
            <p class="text-xs text-emerald-200/90 leading-relaxed max-w-xs">Sistem Absensi Desa Integratif (SADI) — Real-Time Presensi Hardware & SPJ Otomatis</p>
        </div>

        <div class="relative z-10 text-[11px] text-emerald-300/60 font-medium">
            Program KKN Universitas &copy; 2025
        </div>
    </div>

    <!-- Right Login Form Panel (Cream/White 60%) -->
    <div class="md:col-span-7 bg-[#FAF6F0] p-8 sm:p-12 flex flex-col justify-center">
        <div class="max-w-md mx-auto w-full">

            <div class="mb-8">
                <h2 class="font-outfit text-2xl font-bold text-[#064E3B]">Masuk ke Sistem</h2>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan username dan password akun Anda</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-xs text-red-700 font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 rounded-r-xl text-xs text-emerald-800 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username</label>
                    <div class="relative">
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username" class="w-full px-4 py-3 text-sm rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-sm transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 text-sm rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-sm transition">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-[#064E3B] rounded border-[#C9A84C] focus:ring-[#C9A84C]">
                        <span class="text-xs text-slate-600 font-medium">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#064E3B] to-[#1B4D3E] text-white font-bold text-sm tracking-wide shadow-lg shadow-emerald-900/20 hover:from-[#04392B] hover:to-[#064E3B] transition duration-200">
                    MASUK SADI
                </button>
            </form>

            <!-- Akun Bawaan Seeder Hint -->
            <div class="mt-8 pt-6 border-t border-slate-200/70 text-center">
                <p class="text-[11px] text-slate-500 font-semibold mb-2 uppercase tracking-wider">Kredensial Pengujian (Seeder Defaults)</p>
                <div class="grid grid-cols-2 gap-2 text-left text-[11px]">
                    <div class="p-2 bg-white rounded-lg border border-[#C9A84C]/20">
                        <p class="font-bold text-[#064E3B]">Admin / Sekdes:</p>
                        <p class="text-slate-600">User: <code class="text-amber-800 font-bold">admin</code></p>
                        <p class="text-slate-600">Pass: <code class="text-amber-800 font-bold">admin123</code></p>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-[#C9A84C]/20">
                        <p class="font-bold text-[#064E3B]">Kepala Desa:</p>
                        <p class="text-slate-600">User: <code class="text-amber-800 font-bold">kades</code></p>
                        <p class="text-slate-600">Pass: <code class="text-amber-800 font-bold">kades123</code></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
