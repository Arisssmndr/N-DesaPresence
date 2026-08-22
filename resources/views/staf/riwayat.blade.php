@extends('staf.layout', ['title' => 'Buku Riwayat Presensi — ' . $pegawai->nama_lengkap])

@section('content')
<div class="space-y-4 pb-6">

    <!-- Header Buku Riwayat -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-outfit font-extrabold text-[#064E3B] text-lg">Buku Riwayat Presensi</h2>
            <p class="text-xs text-slate-500">Catatan presensi, permohonan izin/sakit, & tugas luar Anda</p>
        </div>
        <span class="text-xs font-bold text-emerald-800 bg-white px-3 py-1.5 rounded-xl border border-emerald-800 shadow-xs">
            @if(($tab ?? 'presensi') === 'izin')
                Total: {{ $riwayatIzin->total() }}
            @elseif(($tab ?? 'presensi') === 'absen_luar')
                Total: {{ $riwayatAbsenLuar->total() }}
            @else
                Total: {{ $riwayats->total() }}
            @endif
        </span>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex bg-slate-100 p-1 rounded-2xl border border-slate-200/80 text-xs font-bold gap-1 shadow-inner">
        <a href="{{ route('staf.riwayat', ['tab' => 'presensi']) }}"
           class="flex-1 text-center py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 {{ ($tab ?? 'presensi') === 'presensi' ? 'bg-[#064E3B] text-white shadow' : 'text-slate-600 hover:bg-slate-200/60' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Presensi Harian</span>
        </a>

        <a href="{{ route('staf.riwayat', ['tab' => 'izin']) }}"
           class="flex-1 text-center py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 {{ ($tab ?? 'presensi') === 'izin' ? 'bg-[#064E3B] text-white shadow' : 'text-slate-600 hover:bg-slate-200/60' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Izin & Sakit</span>
        </a>

        <a href="{{ route('staf.riwayat', ['tab' => 'absen_luar']) }}"
           class="flex-1 text-center py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 {{ ($tab ?? 'presensi') === 'absen_luar' ? 'bg-[#064E3B] text-white shadow' : 'text-slate-600 hover:bg-slate-200/60' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Absen Luar</span>
        </a>
    </div>

    <!-- TAB CONTENT: PRESENSI HARIAN -->
    @if(($tab ?? 'presensi') === 'presensi')
    <div class="space-y-3">
        @forelse($riwayats as $r)
        <div class="sadi-card p-4 bg-white space-y-3 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div>
                    <p class="font-bold text-slate-800 text-sm">{{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                    <p class="text-[10px] text-slate-400">Durasi: {{ $r->durasi_kerja_menit ? floor($r->durasi_kerja_menit / 60) . 'j ' . ($r->durasi_kerja_menit % 60) . 'm' : '—' }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $r->status === 'Hadir' ? 'bg-emerald-100 text-emerald-800' : ($r->status === 'Izin' || $r->status === 'Sakit' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                    {{ $r->status }}
                </span>
            </div>

            @if($r->keterangan)
            <div class="text-[11px] text-slate-600 bg-slate-50 p-2 rounded-xl border border-slate-100">
                <span class="font-bold text-slate-500">Ket:</span> {{ $r->keterangan }}
            </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Jam Masuk</span>
                    <p class="font-mono font-bold text-[#064E3B] text-sm mt-0.5">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '—' }}</p>
                    @if($r->tanda_tangan_masuk_src)
                        <img src="{{ $r->tanda_tangan_masuk_src }}" alt="TTD Masuk" class="h-8 mx-auto mt-1 border rounded bg-white p-0.5">
                    @endif
                </div>

                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Jam Pulang</span>
                    <p class="font-mono font-bold text-blue-700 text-sm mt-0.5">{{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '—' }}</p>
                    @if($r->tanda_tangan_pulang_src)
                        <img src="{{ $r->tanda_tangan_pulang_src }}" alt="TTD Pulang" class="h-8 mx-auto mt-1 border rounded bg-white p-0.5">
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2 rounded-2xl border border-slate-200">
            <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-xs">Belum ada riwayat kehadiran yang tercatat.</p>
        </div>
        @endforelse

        @if($riwayats->hasPages())
        <div class="pt-2">
            {{ $riwayats->appends(['tab' => 'presensi'])->links('vendor.pagination.sadi-mobile') }}
        </div>
        @endif
    </div>
    @endif

    <!-- TAB CONTENT: IZIN & SAKIT -->
    @if(($tab ?? 'presensi') === 'izin')
    <div class="space-y-3">
        @forelse($riwayatIzin as $r)
        @php
            $isSakit = str_contains($r->jenis, 'sakit');
        @endphp
        <div class="sadi-card p-4 bg-white space-y-3 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $isSakit ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $isSakit ? ' SAKIT' : ' IZIN' }}
                    </span>
                    <span class="text-[10px] font-mono text-slate-500 font-semibold">
                        {{ $r->jumlah_hari }} Hari
                    </span>
                </div>

                @switch($r->status)
                    @case('disetujui')
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-600 text-white shadow-xs">
                            ✓ Disetujui
                        </span>
                        @break
                    @case('ditolak')
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-red-600 text-white shadow-xs">
                            ✕ Ditolak
                        </span>
                        @break
                    @default
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-500 text-white animate-pulse">
                            ⏳ Menunggu Persetujuan
                        </span>
                @endswitch
            </div>

            <div class="text-xs space-y-1">
                <p class="font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($r->tanggal_mulai)->isoFormat('D MMMM Y') }}
                    @if($r->tanggal_mulai != $r->tanggal_selesai)
                        s/d {{ \Carbon\Carbon::parse($r->tanggal_selesai)->isoFormat('D MMMM Y') }}
                    @endif
                </p>
                <p class="text-[11px] text-slate-600 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ $r->keterangan }}</p>
            </div>

            @if($r->file_lampiran)
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-slate-400 font-medium">Bukti Surat / Dokter:</span>
                <a href="{{ asset('storage/' . $r->file_lampiran) }}" target="_blank"
                   class="text-[10px] font-bold text-[#064E3B] underline hover:text-emerald-900 inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <span>Lihat Berkas Lampiran</span>
                </a>
            </div>
            @endif
        </div>
        @empty
        <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2 rounded-2xl border border-slate-200">
            <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-xs">Belum ada riwayat permohonan izin atau sakit.</p>
        </div>
        @endforelse

        @if($riwayatIzin->hasPages())
        <div class="pt-2">
            {{ $riwayatIzin->appends(['tab' => 'izin'])->links('vendor.pagination.sadi-mobile') }}
        </div>
        @endif
    </div>
    @endif

    <!-- TAB CONTENT: ABSEN LUAR -->
    @if(($tab ?? 'presensi') === 'absen_luar')
    <div class="space-y-3">
        @forelse($riwayatAbsenLuar as $r)
        <div class="sadi-card p-4 bg-white space-y-3 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-100 text-blue-800 uppercase tracking-wider">
                        {{ str_replace('_', ' ', $r->jenis) }}
                    </span>
                    <p class="font-bold text-slate-800 text-xs mt-1">{{ $r->judul }}</p>
                </div>

                @switch($r->status)
                    @case('disetujui')
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-600 text-white shadow-xs">
                            ✓ Disetujui
                        </span>
                        @break
                    @case('ditolak')
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-red-600 text-white shadow-xs">
                            ✕ Ditolak
                        </span>
                        @break
                    @default
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-500 text-white animate-pulse">
                            ⏳ Menunggu Persetujuan
                        </span>
                @endswitch
            </div>

            <div class="text-xs space-y-1">
                <p class="font-bold text-[#064E3B]">
                    {{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y') }}
                </p>
                @if($r->instansi_pengundang)
                <p class="text-[11px] text-slate-500"><strong>Instansi/Lokasi:</strong> {{ $r->instansi_pengundang }}</p>
                @endif
                <p class="text-[11px] text-slate-600 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ $r->deskripsi }}</p>
            </div>

            @if($r->foto_kegiatan)
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-slate-400 font-medium">Foto Kegiatan/Dokumen:</span>
                <a href="{{ asset('storage/' . $r->foto_kegiatan) }}" target="_blank"
                   class="text-[10px] font-bold text-[#064E3B] underline hover:text-emerald-900 inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Lihat Foto/Dokumen</span>
                </a>
            </div>
            @endif
        </div>
        @empty
        <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2 rounded-2xl border border-slate-200">
            <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-xs">Belum ada riwayat pengajuan absen luar.</p>
        </div>
        @endforelse

        @if($riwayatAbsenLuar->hasPages())
        <div class="pt-2">
            {{ $riwayatAbsenLuar->appends(['tab' => 'absen_luar'])->links('vendor.pagination.sadi-mobile') }}
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
