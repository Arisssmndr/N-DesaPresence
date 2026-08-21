@extends('staf.layout', ['title' => 'Akun Belum Terhubung Pegawai'])

@section('content')
<div class="sadi-card p-6 bg-white text-center space-y-4 my-8">
    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <h3 class="font-outfit font-bold text-slate-800 text-lg">Akun Belum Terhubung ke Data Pegawai</h3>
    <p class="text-xs text-slate-500 leading-relaxed">
        Akun <strong>@ {{ $user->username }}</strong> berhasil masuk, namun belum dihubungkan ke data profil pegawai master oleh Admin/Sekdes.
    </p>

    <form action="{{ route('staf.logout') }}" method="POST" class="pt-4">
        @csrf
        <button type="submit" class="w-full py-3 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200">
            Keluar Akun
        </button>
    </form>
</div>
@endsection
