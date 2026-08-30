@extends('staf.layout')

@section('title', 'Layanan Izin & Sakit — Portal Staf Desa')

@section('content')
<div class="space-y-4" x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }}, kategori: '{{ old('kategori', 'izin') }}' }">

    <!-- Header Banner -->
    <div class="sadi-card p-5 text-white rounded-3xl shadow-lg border border-[#C9A84C]/40 relative overflow-hidden" style="background: linear-gradient(135deg, #064E3B 0%, #04392B 100%) !important;">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#C9A84C]/15 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E2C268] text-[#064E3B] uppercase tracking-wider shadow-xs">
                    Layanan Mandiri Staf
                </span>
                <h1 class="font-outfit text-lg font-bold text-white mt-1.5">Layanan Izin & Sakit</h1>
                <p class="text-xs text-emerald-200 mt-0.5">Pengajuan permohonan izin kedinasan, urusan keluarga & surat sakit</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#C9A84C] text-[#064E3B] flex items-center justify-center font-bold shadow shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Alert Flash Message -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs flex items-center gap-3 shadow-xs">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs flex items-center gap-3 shadow-xs">
        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs space-y-1 shadow-xs">
        <div class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Mohon periksa kembali formulir:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] text-rose-700 pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- OVERVIEW HUB & CTA BUTTON (TAMPIL KETIKA FORM BELUM DIBUKA)             -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div x-show="!showForm" x-transition:enter="transition ease-out duration-200" class="space-y-4">
        
        <!-- Action Hub Card -->
        <div class="sadi-card p-5 bg-white border border-slate-200 shadow-sm rounded-3xl space-y-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#064E3B] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-outfit font-extrabold text-slate-800 text-sm">Permohonan Izin / Sakit Baru</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Ajukan permohonan digital ke Kepala Desa jika Anda berhalangan hadir di kantor.</p>
                </div>
            </div>

            <button type="button" @click="showForm = true"
                    class="w-full py-3 px-5 rounded-2xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs tracking-wide shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer border border-[#C9A84C]/40">
                <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Ajukan Permohonan Izin / Sakit</span>
            </button>
        </div>

        <!-- Ringkasan Riwayat Pengajuan -->
        <div class="sadi-card p-5 bg-white border border-slate-200 shadow-sm rounded-3xl space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#C9A84C]"></div>
                    <h3 class="font-outfit font-extrabold text-slate-900 text-xs uppercase tracking-wider">Riwayat Pengajuan Terakhir</h3>
                </div>
                <a href="{{ route('staf.riwayat', ['tab' => 'izin']) }}" class="text-[11px] font-extrabold text-[#064E3B] hover:underline flex items-center gap-1">
                    <span>Semua Riwayat</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($riwayats as $item)
                <div class="py-3 flex items-start justify-between gap-3 text-xs">
                    <div class="space-y-0.5 flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800">{{ $item->label_jenis ?? ucfirst($item->jenis) }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $item->jumlah_hari }} Hari)</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->isoFormat('D MMM Y') }}
                            @if($item->tanggal_mulai != $item->tanggal_selesai)
                                – {{ \Carbon\Carbon::parse($item->tanggal_selesai)->isoFormat('D MMM Y') }}
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-600 truncate italic">"{{ $item->keterangan }}"</p>
                    </div>

                    <div class="text-right shrink-0">
                        @if($item->status === 'disetujui')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <span>Disetujui</span>
                            </span>
                        @elseif($item->status === 'ditolak')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                <span>Ditolak</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                <span>Menunggu</span>
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-8 text-center space-y-1.5">
                    <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-xs text-slate-500 font-bold">Belum Ada Pengajuan Izin / Sakit</p>
                    <p class="text-[11px] text-slate-400">Pengajuan yang Anda kirimkan akan tercatat dan dipantau di sini.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- FORMULIR PENGAJUAN (TERBUKA KETIKA TOMBOL AJUKAN DIKLIK)                -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="sadi-card p-5 bg-white space-y-4 rounded-3xl border border-slate-200 shadow-lg">
        
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-[#064E3B]"></div>
                <h2 class="font-outfit font-extrabold text-sm text-[#064E3B]">Formulir Permohonan Izin / Sakit</h2>
            </div>
            <button type="button" @click="showForm = false"
                    class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Tutup</span>
            </button>
        </div>

        <form action="{{ route('staf.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Pilihan Kategori: Izin vs Sakit -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Kategori Permohonan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2.5 p-3 rounded-2xl border-2 cursor-pointer transition font-bold"
                           :class="kategori === 'izin' ? 'border-[#064E3B] bg-emerald-50 text-[#064E3B] shadow-xs ring-2 ring-[#064E3B]/10' : 'border-slate-200 bg-slate-50/50 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="kategori" value="izin" x-model="kategori" class="hidden">
                        <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Izin Tidak Masuk</span>
                    </label>

                    <label class="flex items-center justify-center gap-2.5 p-3 rounded-2xl border-2 cursor-pointer transition font-bold"
                           :class="kategori === 'sakit' ? 'border-rose-600 bg-rose-50 text-rose-900 shadow-xs ring-2 ring-rose-600/10' : 'border-slate-200 bg-slate-50/50 text-slate-600 hover:bg-slate-100'">
                        <input type="radio" name="kategori" value="sakit" x-model="kategori" class="hidden">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
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
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Mulai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-semibold bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-semibold bg-white">
                </div>
            </div>

            <!-- Alasan / Keterangan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan / Keterangan Lengkap <span class="text-rose-500">*</span></label>
                <textarea name="keterangan" rows="3" required placeholder="Jelaskan alasan izin / sakit secara jelas..."
                          class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#064E3B] text-slate-800 font-medium bg-white placeholder-slate-400">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Upload Surat Bukti / Surat Dokter (Opsional) -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        Upload Surat Bukti / Surat Dokter
                    </label>
                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-slate-200 text-slate-600 uppercase">Opsional</span>
                </div>
                <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white hover:file:bg-[#04392B] cursor-pointer">
                <p class="text-[10px] text-slate-400">Format PDF, JPG, atau PNG (Maksimal 5 MB). Jika tidak ada surat dokter/bukti, formulir tetap dapat dikirimkan.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-1">
                <button type="button" @click="showForm = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs tracking-wide shadow-md transition active:scale-[0.98] flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Kirim Permohonan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
