@extends('layouts.auth')

@section('content')
<div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 border border-[#C9A84C]/20">

    <!-- Left Branding Panel (Dark Emerald Green) -->
    <div class="md:col-span-5 auth-panel-left p-8 md:p-12 text-white flex flex-col justify-between items-center text-center relative overflow-hidden"
         style="background: linear-gradient(165deg, #064E3B 0%, #04392B 100%) !important; color: #FFFFFF !important;">
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-[#C9A84C]/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-[#059669]/20 rounded-full blur-2xl"></div>

        <div class="relative z-10 my-auto">
            <!-- Logo Resmi Pemerintah Kabupaten Tasikmalaya (Pure Logo) -->
            <div class="mb-5 flex justify-center">
                <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Kabupaten Tasikmalaya" class="h-28 w-auto object-contain filter drop-shadow-md">
            </div>

            <h1 class="font-outfit text-2xl font-bold tracking-tight text-white mb-2">PEMERINTAH DESA NANGTANG</h1>
            <div class="h-1 w-16 bg-[#C9A84C] mx-auto rounded-full mb-3"></div>
            <p class="text-xs text-emerald-200/90 leading-relaxed max-w-xs">N-DesaPresence — Portal Presensi Mandiri Staf & Perangkat Desa</p>
        </div>

        <div class="relative z-10 text-[11px] text-emerald-300/70 font-medium">
            KKN 0226 LP3I Tasikmalaya &copy; 2026
        </div>
    </div>

    <!-- Right Login Form Panel -->
    <div class="md:col-span-7 bg-[#FAF6F0] p-8 sm:p-12 flex flex-col justify-center">
        <div class="max-w-md mx-auto w-full">

            <div class="mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#064E3B]/10 border border-[#C9A84C]/30 text-[#064E3B] text-xs font-bold mb-3">
                    <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Portal Presensi Staf</span>
                </div>
                <h2 class="font-outfit text-2xl font-bold text-[#064E3B]">Masuk Akun Perangkat</h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan username akun perangkat desa Anda untuk melanjutkan presensi</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-2xl text-xs text-emerald-900 font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('staf.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username Pegawai / Staf</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#064E3B] font-mono font-bold text-base">@</span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                            placeholder="contoh: budisantoso"
                            class="w-full pl-10 pr-4 py-3.5 text-sm font-mono rounded-xl bg-white border border-[#C9A84C]/30 focus:outline-none focus:ring-2 focus:ring-[#C9A84C] text-slate-800 shadow-sm transition">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Masukkan username unik yang telah didaftarkan oleh Administrator / Sekdes.</p>
                </div>

                <button type="submit"
                    class="w-full py-4 px-6 rounded-xl btn-sadi-primary text-white font-extrabold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%) !important; color: #FFFFFF !important; border: 1px solid #C9A84C !important;">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>MASUK KE PORTAL PRESENSI</span>
                </button>
            </form>

            <!-- Akses Login Admin / Kedinasan -->
            <div class="mt-8 pt-6 border-t border-slate-200/70 text-center space-y-2">
                <p class="text-xs text-slate-500 font-medium">Administrator, Kepala Desa, atau Auditor?</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-100/80 border border-[#064E3B]/20 text-[#064E3B] text-xs font-extrabold shadow-sm transition active:scale-[0.99]">
                    <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Masuk sebagai Administrator / Kedinasan</span>
                    <svg class="w-3.5 h-3.5 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <p class="text-[10.5px] text-slate-400 pt-1">Belum memiliki / lupa username staf? Hubungi Administrator / Sekdes.</p>
            </div>

        </div>
    </div>
</div>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Swal) {
            Swal.fire({
                title: '<span style="font-family: Outfit, sans-serif; font-weight: 800; color: #064E3B;">Akun Tidak Ditemukan</span>',
                html: `
                    <div style="text-align: left; font-size: 13px; color: #334155; line-height: 1.6;">
                        <p style="margin-bottom: 8px; font-weight: 600; color: #991B1B;">Gagal masuk ke portal presensi:</p>
                        <ul style="padding-left: 20px; list-style-type: disc; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#064E3B',
                background: '#FAF6F0',
                customClass: {
                    popup: 'rounded-3xl border border-[#C9A84C]/40 shadow-2xl',
                    confirmButton: 'rounded-xl px-7 py-3 font-bold text-sm shadow-md'
                }
            });
        }
    });
</script>
@endif
@endsection
