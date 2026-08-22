@extends('staf.layout')

@section('title', 'Pengajuan Izin & Sakit — Portal Staf Desa')

@section('content')
<div class="space-y-4">

    <!-- Header Banner -->
    <div class="sadi-card p-5 bg-linear-to-br from-[#064E3B] to-[#04392B] text-white rounded-2xl shadow-lg border border-[#C9A84C]/40 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#C9A84C]/15 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider">
                    Layanan Mandiri Perangkat
                </span>
                <h1 class="font-outfit text-lg font-bold text-white mt-1">Pengajuan Izin & Sakit</h1>
                <p class="text-xs text-emerald-200 mt-0.5"></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#C9A84C] text-[#064E3B] flex items-center justify-center font-bold text-xl shadow shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Alert Flash Message -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs flex items-center gap-3 shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-xs space-y-1 shadow-sm">
        <div class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Mohon periksa kembali formulir:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] text-red-700 pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Pengajuan Card -->
    <div class="sadi-card p-5 bg-white space-y-4 rounded-2xl border border-slate-200 shadow-sm" x-data="{ kategori: '{{ old('kategori', 'izin') }}' }">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <h2 class="font-outfit font-bold text-sm text-[#064E3B]">Formulir Permohonan</h2>
            <span class="text-[10px] text-slate-400 font-semibold">* Wajib Diisi</span>
        </div>

        <form action="{{ route('staf.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Pilihan Kategori: Izin vs Sakit -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Kategori Permohonan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition font-bold"
                           :class="kategori === 'izin' ? 'border-[#064E3B] bg-emerald-50/80 text-[#064E3B] shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="kategori" value="izin" x-model="kategori" class="hidden">
                        <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Izin Tidak Masuk</span>
                    </label>

                    <label class="flex items-center justify-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition font-bold"
                           :class="kategori === 'sakit' ? 'border-red-600 bg-red-50 text-red-900 shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="kategori" value="sakit" x-model="kategori" class="hidden">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Sakit</span>
                    </label>
                </div>
            </div>

            <!-- Detail Jenis Izin (Jika kategori = izin) -->
            <div x-show="kategori === 'izin'" class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Jenis Keperluan Izin</label>
                <select name="jenis_detail" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-semibold bg-white">
                    <option value="izin_pribadi">Izin Keperluan Pribadi / Keluarga</option>
                    <option value="izin_kedinasan">Izin Urusan Kedinasan Luar</option>
                    <option value="cuti_tahunan">Cuti Tahunan</option>
                    <option value="duka_cita">Izin Duka Cita</option>
                    <option value="melahirkan">Izin / Cuti Melahirkan</option>
                </select>
            </div>

            <!-- Periode Tanggal -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Mulai Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-semibold bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-semibold bg-white">
                </div>
            </div>

            <!-- Alasan / Keterangan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan / Keterangan Lengkap <span class="text-red-500">*</span></label>
                <textarea name="keterangan" rows="3" required placeholder="Jelaskan alasan izin / sakit secara jelas..."
                          class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-medium bg-white placeholder-slate-400">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Upload Surat Bukti / Surat Dokter (Opsional) -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        Upload Surat Bukti / Surat Dokter
                    </label>
                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-slate-200 text-slate-600 uppercase">Opsional</span>
                </div>
                <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white hover:file:bg-[#04392B] cursor-pointer">
                <p class="text-[10px] text-slate-400">Format PDF, JPG, atau PNG (Maksimal 5 MB). Jika tidak ada surat dokter/bukti, formulir tetap dapat dikirimkan.</p>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white font-outfit font-extrabold text-xs tracking-wide shadow-md transition active:scale-[0.98] flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>Kirim Permohonan Izin / Sakit</span>
            </button>
        </form>
    </div>

    <!-- Tautan Langsung ke Buku Riwayat Presensi -->
    <div class="p-4 bg-emerald-50/80 rounded-2xl border border-emerald-200 flex items-center justify-between text-xs shadow-xs">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-[#064E3B] text-white flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="font-bold text-slate-800">Lihat Catatan Riwayat Izin Anda</p>
                <p class="text-[11px] text-slate-600">Semua status pengajuan Izin & Sakit tersimpan rapi di Buku Riwayat Presensi.</p>
            </div>
        </div>
        <a href="{{ route('staf.riwayat', ['tab' => 'izin']) }}"
           class="px-3.5 py-2 rounded-xl bg-[#064E3B] text-white text-xs font-bold shadow hover:bg-[#04392B] transition shrink-0 inline-flex items-center gap-1">
            <span>Buka Riwayat &rarr;</span>
        </a>
    </div>

</div>
@endsection
