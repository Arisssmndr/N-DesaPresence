@extends('staf.layout', ['title' => 'Buku Riwayat Presensi — ' . $pegawai->nama_lengkap])

@section('content')
<div class="space-y-4 pb-8" x-data="riwayatHub()">

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- VIEW 1: DAFTAR RIWAYAT (TAMPILAN UTAMA LIST)                           -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div x-show="activeView === 'list'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="space-y-4">

        <!-- Header Buku Riwayat -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-outfit font-extrabold text-[#064E3B] text-lg">Buku Riwayat Presensi</h2>
                <p class="text-xs text-slate-500">Catatan presensi, permohonan izin/sakit, & tugas luar Anda</p>
            </div>
            <span class="text-xs font-bold text-[#064E3B] bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
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
        <div class="flex bg-slate-100 p-1 rounded-2xl border border-slate-200 text-xs font-bold gap-1 shadow-inner">
            <a href="{{ route('staf.riwayat', ['tab' => 'presensi']) }}"
               class="flex-1 text-center py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 {{ ($tab ?? 'presensi') === 'presensi' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Presensi Harian</span>
            </a>

            <a href="{{ route('staf.riwayat', ['tab' => 'izin']) }}"
               class="flex-1 text-center py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 {{ ($tab ?? 'presensi') === 'izin' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Izin & Sakit</span>
            </a>

            <a href="{{ route('staf.riwayat', ['tab' => 'absen_luar']) }}"
               class="flex-1 text-center py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 {{ ($tab ?? 'presensi') === 'absen_luar' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Absen Luar</span>
            </a>
        </div>

        <!-- 1. TAB PRESENSI HARIAN -->
        @if(($tab ?? 'presensi') === 'presensi')
        <div class="space-y-3">
            @forelse($riwayats as $r)
            @php
                $durasiText = $r->durasi_kerja_menit ? (floor($r->durasi_kerja_menit / 60) . ' jam ' . ($r->durasi_kerja_menit % 60) . ' mnt') : '—';
                $dataJson = [
                    'id' => $r->id,
                    'tanggal_formatted' => \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y'),
                    'status' => $r->status,
                    'jam_masuk' => $r->jam_masuk ? substr($r->jam_masuk, 0, 5) . ' WIB' : '—',
                    'jam_pulang' => $r->jam_pulang ? substr($r->jam_pulang, 0, 5) . ' WIB' : '—',
                    'durasi' => $durasiText,
                    'terlambat_menit' => $r->terlambat_menit ?? 0,
                    'keterangan' => $r->keterangan ?? 'Hadir bertugas sesuai jadwal kedinasan.',
                    'ip_masuk' => $r->ip_absensi_masuk ?? '—',
                    'ip_pulang' => $r->ip_absensi_pulang ?? '—',
                    'ttd_masuk' => $r->tanda_tangan_masuk_src,
                    'ttd_pulang' => $r->tanda_tangan_pulang_src,
                ];
            @endphp
            <div @click="showDetail('presensi', {{ json_encode($dataJson) }})"
                 class="sadi-card p-4 bg-white rounded-2xl border border-slate-200 hover:border-[#064E3B]/60 hover:shadow-md transition duration-150 cursor-pointer space-y-2.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-[#064E3B] border border-emerald-200 uppercase tracking-wider">
                            Presensi Harian
                        </span>
                        <span class="text-[11px] font-mono text-slate-400 font-semibold">{{ $durasiText }}</span>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        @if(strtolower($r->status) === 'hadir')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <span>Hadir</span>
                            </span>
                        @elseif(in_array(strtolower($r->status), ['izin', 'sakit', 'dinas luar']))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                <span>{{ $r->status }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                <span>{{ $r->status }}</span>
                            </span>
                        @endif
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div>
                        <h3 class="font-outfit font-extrabold text-slate-900 text-sm">{{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y') }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Masuk: <strong class="text-[#064E3B] font-mono">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '—' }}</strong> &bull; Pulang: <strong class="text-blue-700 font-mono">{{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '—' }}</strong>
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-[#064E3B] flex items-center gap-0.5">
                        <span>Detail</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </div>
            @empty
            <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2 rounded-2xl border border-slate-200">
                <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-xs font-semibold text-slate-500">Belum ada riwayat presensi harian yang tercatat.</p>
            </div>
            @endforelse

            @if($riwayats->hasPages())
            <div class="pt-2">
                {{ $riwayats->appends(['tab' => 'presensi'])->links('vendor.pagination.sadi-mobile') }}
            </div>
            @endif
        </div>
        @endif

        <!-- 2. TAB IZIN & SAKIT -->
        @if(($tab ?? 'presensi') === 'izin')
        <div class="space-y-3">
            @forelse($riwayatIzin as $r)
            @php
                $isSakit = str_contains($r->jenis, 'sakit');
                $dataJson = [
                    'id' => $r->id,
                    'kategori' => $isSakit ? 'Sakit' : 'Izin',
                    'label_jenis' => $r->label_jenis ?? ucfirst(str_replace('_', ' ', $r->jenis)),
                    'tanggal_mulai' => \Carbon\Carbon::parse($r->tanggal_mulai)->isoFormat('D MMMM Y'),
                    'tanggal_selesai' => \Carbon\Carbon::parse($r->tanggal_selesai)->isoFormat('D MMMM Y'),
                    'jumlah_hari' => $r->jumlah_hari,
                    'keterangan' => $r->keterangan,
                    'status' => $r->status,
                    'file_lampiran' => $r->file_lampiran ? asset('storage/' . $r->file_lampiran) : null,
                    'created_at' => $r->created_at ? $r->created_at->isoFormat('D MMMM Y, HH:mm') . ' WIB' : '—',
                ];
            @endphp
            <div @click="showDetail('izin', {{ json_encode($dataJson) }})"
                 class="sadi-card p-4 bg-white rounded-2xl border border-slate-200 hover:border-[#064E3B]/60 hover:shadow-md transition duration-150 cursor-pointer space-y-2.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $isSakit ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-emerald-50 text-[#064E3B] border border-emerald-200' }}">
                            {{ $isSakit ? 'SAKIT' : 'IZIN' }}
                        </span>
                        <span class="text-[10px] font-mono text-slate-500 font-semibold">{{ $r->jumlah_hari }} Hari</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @switch($r->status)
                            @case('disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>Disetujui</span>
                                </span>
                                @break
                            @case('ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                    <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Ditolak</span>
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Menunggu</span>
                                </span>
                        @endswitch
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div class="space-y-0.5 flex-1 min-w-0 pr-2">
                        <h3 class="font-outfit font-extrabold text-slate-900 text-sm truncate">{{ $r->label_jenis ?? ucfirst(str_replace('_', ' ', $r->jenis)) }}</h3>
                        <p class="text-[11px] text-slate-500">
                            {{ \Carbon\Carbon::parse($r->tanggal_mulai)->isoFormat('D MMM Y') }}
                            @if($r->tanggal_mulai != $r->tanggal_selesai)
                                – {{ \Carbon\Carbon::parse($r->tanggal_selesai)->isoFormat('D MMM Y') }}
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-600 italic truncate">"{{ $r->keterangan }}"</p>
                    </div>
                    <span class="text-[11px] font-bold text-[#064E3B] shrink-0 flex items-center gap-0.5">
                        <span>Detail</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </div>
            @empty
            <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2 rounded-2xl border border-slate-200">
                <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-xs font-semibold text-slate-500">Belum ada riwayat permohonan izin atau sakit.</p>
            </div>
            @endforelse

            @if($riwayatIzin->hasPages())
            <div class="pt-2">
                {{ $riwayatIzin->appends(['tab' => 'izin'])->links('vendor.pagination.sadi-mobile') }}
            </div>
            @endif
        </div>
        @endif

        <!-- 3. TAB ABSEN LUAR -->
        @if(($tab ?? 'presensi') === 'absen_luar')
        <div class="space-y-3">
            @forelse($riwayatAbsenLuar as $r)
            @php
                $dataJson = [
                    'id' => $r->id,
                    'judul' => $r->judul,
                    'jenis_label' => $r->label_jenis ?? ucwords(str_replace('_', ' ', $r->jenis)),
                    'tanggal_formatted' => \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y'),
                    'instansi' => $r->instansi_pengundang,
                    'nomor_spt' => $r->nomor_surat_tugas,
                    'deskripsi' => $r->deskripsi ?? '—',
                    'lat' => $r->latitude,
                    'lng' => $r->longitude,
                    'alamat_gps' => $r->alamat_gps ?? 'Lokasi penugasan luar wilayah',
                    'akurasi' => $r->akurasi_gps_meter,
                    'status' => $r->status,
                    'catatan_admin' => $r->catatan_admin,
                    'foto_kegiatan' => $r->foto_lokasi ? asset('storage/' . $r->foto_lokasi) : null,
                    'file_dokumen' => $r->file_dokumen ? asset('storage/' . $r->file_dokumen) : null,
                    'ttd_src' => $r->tanda_tangan_src,
                    'created_at' => $r->created_at ? $r->created_at->isoFormat('D MMMM Y, HH:mm') . ' WIB' : '—',
                ];
            @endphp
            <div @click="showDetail('absen_luar', {{ json_encode($dataJson) }})"
                 class="sadi-card p-4 bg-white rounded-2xl border border-slate-200 hover:border-[#064E3B]/60 hover:shadow-md transition duration-150 cursor-pointer space-y-2.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200 uppercase tracking-wider">
                        {{ $r->label_jenis ?? str_replace('_', ' ', $r->jenis) }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        @switch($r->status)
                            @case('disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>Disetujui</span>
                                </span>
                                @break
                            @case('ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                    <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Ditolak</span>
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Menunggu</span>
                                </span>
                        @endswitch
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div class="space-y-0.5 flex-1 min-w-0 pr-2">
                        <h3 class="font-outfit font-extrabold text-slate-900 text-sm truncate">{{ $r->judul }}</h3>
                        <p class="text-[11px] text-slate-500">
                            {{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMMM Y') }}
                        </p>
                        <p class="text-[11px] text-slate-600 truncate italic">"{{ $r->deskripsi }}"</p>
                    </div>
                    <span class="text-[11px] font-bold text-[#064E3B] shrink-0 flex items-center gap-0.5">
                        <span>Detail</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </div>
            @empty
            <div class="sadi-card p-8 bg-white text-center text-slate-400 space-y-2 rounded-2xl border border-slate-200">
                <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-xs font-semibold text-slate-500">Belum ada riwayat pengajuan absen luar.</p>
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

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- VIEW 2: HALAMAN DETAIL RINCIAN PENUH (BUKAN POPUP / BUKAN MENGAMBANG)  -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div x-show="activeView === 'detail'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4"
         style="display: none;">

        <!-- Top Navigation Header -->
        <div class="flex items-center justify-between">
            <button type="button" @click="backToList()"
                    class="inline-flex items-center gap-2 text-xs font-bold text-[#064E3B] hover:text-emerald-800 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs transition cursor-pointer active:scale-98">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Riwayat</span>
            </button>

            <span class="text-[11px] font-mono font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-full border border-slate-200">
                Arsip ID: #<span x-text="detailData ? detailData.id : ''"></span>
            </span>
        </div>

        <!-- Slip Rincian Data (Halaman Penuh) -->
        <div class="sadi-card p-5 bg-white space-y-4 rounded-3xl border border-slate-200 shadow-sm">

            <!-- Title & Status Header -->
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#064E3B] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-outfit font-extrabold text-sm text-slate-900">Rincian Arsip Presensi</h3>
                        <p class="text-[10.5px] text-slate-400 font-mono">Tercatat di Server Presensi Desa</p>
                    </div>
                </div>

                <div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold border shadow-2xs capitalize"
                          :class="{
                              'bg-emerald-50 text-emerald-800 border-emerald-300': detailData && (detailData.status === 'Hadir' || detailData.status === 'disetujui'),
                              'bg-amber-50 text-amber-900 border-amber-300': detailData && (detailData.status === 'menunggu' || detailData.status === 'Izin' || detailData.status === 'Sakit'),
                              'bg-rose-50 text-rose-800 border-rose-300': detailData && (detailData.status === 'ditolak' || detailData.status === 'Alpha' || detailData.status === 'Terlambat')
                          }">
                        <span x-text="detailData ? detailData.status : ''"></span>
                    </span>
                </div>
            </div>

            <!-- Card Identitas Pegawai -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-1.5">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Identitas Staf</span>
                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded">Terverifikasi</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Nama Lengkap</span>
                        <strong class="text-slate-800">{{ $pegawai->nama_lengkap }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Jabatan</span>
                        <strong class="text-[#064E3B]">{{ $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Rincian Sesuai Tipe: 1. Presensi Harian -->
            <template x-if="detailType === 'presensi' && detailData">
                <div class="space-y-3.5">
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block border-b border-slate-100 pb-1">
                            Waktu & Jaringan Presensi
                        </span>
                        
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-200">
                                <span class="text-[9.5px] font-bold text-emerald-900 uppercase">Jam Masuk</span>
                                <p class="font-mono font-extrabold text-sm text-[#064E3B] mt-0.5" x-text="detailData.jam_masuk"></p>
                                <span class="text-[9px] text-slate-400 font-mono block mt-1" x-text="'IP: ' + detailData.ip_masuk"></span>
                            </div>
                            <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200">
                                <span class="text-[9.5px] font-bold text-blue-900 uppercase">Jam Pulang</span>
                                <p class="font-mono font-extrabold text-sm text-blue-700 mt-0.5" x-text="detailData.jam_pulang"></p>
                                <span class="text-[9px] text-slate-400 font-mono block mt-1" x-text="'IP: ' + detailData.ip_pulang"></span>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-between text-xs text-slate-700 border-t border-slate-100">
                            <span>Durasi Efektif Kerja:</span>
                            <strong class="font-mono text-slate-900" x-text="detailData.durasi"></strong>
                        </div>
                    </div>

                    <!-- Tanda Tangan Basah -->
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2 text-xs">
                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block border-b border-slate-100 pb-1">
                            Bukti Tanda Tangan Digital Basah
                        </span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="text-center p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-500 block mb-1 font-bold">TTD Masuk</span>
                                <template x-if="detailData.ttd_masuk">
                                    <img :src="detailData.ttd_masuk" class="max-h-20 mx-auto bg-white p-1 rounded-lg border object-contain">
                                </template>
                                <template x-if="!detailData.ttd_masuk">
                                    <span class="text-[10px] text-slate-400 italic block py-4">Tidak ada TTD</span>
                                </template>
                            </div>
                            <div class="text-center p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-500 block mb-1 font-bold">TTD Pulang</span>
                                <template x-if="detailData.ttd_pulang">
                                    <img :src="detailData.ttd_pulang" class="max-h-20 mx-auto bg-white p-1 rounded-lg border object-contain">
                                </template>
                                <template x-if="!detailData.ttd_pulang">
                                    <span class="text-[10px] text-slate-400 italic block py-4">Tidak ada TTD</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Rincian Sesuai Tipe: 2. Izin & Sakit -->
            <template x-if="detailType === 'izin' && detailData">
                <div class="space-y-3.5 text-xs">
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2.5">
                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block border-b border-slate-100 pb-1">
                            Rincian Permohonan Izin / Sakit
                        </span>
                        <div class="space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Jenis Keperluan:</span>
                                <strong class="text-slate-900" x-text="detailData.label_jenis"></strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Periode Tanggal:</span>
                                <strong class="text-[#064E3B]" x-text="detailData.tanggal_mulai + (detailData.tanggal_mulai !== detailData.tanggal_selesai ? ' s/d ' + detailData.tanggal_selesai : '')"></strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Durasi:</span>
                                <strong class="font-mono text-slate-900" x-text="detailData.jumlah_hari + ' Hari'"></strong>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-500 block mb-0.5">Alasan / Keterangan:</span>
                            <p class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700 leading-relaxed italic" x-text="detailData.keterangan"></p>
                        </div>

                        <template x-if="detailData.file_lampiran">
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[10px] text-slate-500 font-medium">Berkas Surat / Bukti:</span>
                                <a :href="detailData.file_lampiran" target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white text-xs font-bold shadow-xs transition inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh / Buka Berkas</span>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Rincian Sesuai Tipe: 3. Absen Luar Kantor -->
            <template x-if="detailType === 'absen_luar' && detailData">
                <div class="space-y-3.5 text-xs">
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2.5">
                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block border-b border-slate-100 pb-1">
                            Informasi Kegiatan Penugasan
                        </span>
                        
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Judul Kegiatan</span>
                            <h4 class="font-outfit font-extrabold text-base text-slate-900 mt-0.5" x-text="detailData.judul"></h4>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Kategori</span>
                                <strong class="text-[#064E3B]" x-text="detailData.jenis_label"></strong>
                            </div>
                            <template x-if="detailData.instansi">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Instansi / Lokasi</span>
                                    <strong class="text-slate-800" x-text="detailData.instansi"></strong>
                                </div>
                            </template>
                            <template x-if="detailData.nomor_spt">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Nomor SPT</span>
                                    <strong class="text-slate-800 font-mono" x-text="detailData.nomor_spt"></strong>
                                </div>
                            </template>
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Uraian / Deskripsi Lengkap</span>
                            <p class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700 leading-relaxed" x-text="detailData.deskripsi"></p>
                        </div>
                    </div>

                    <!-- Lokasi GPS -->
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Titik Koordinat Lokasi GPS</span>
                            <template x-if="detailData.akurasi">
                                <span class="text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded border border-emerald-200" x-text="'Akurasi ±' + detailData.akurasi + 'm'"></span>
                            </template>
                        </div>

                        <p class="font-mono text-xs font-extrabold text-slate-900 bg-slate-50 p-2.5 rounded-xl border text-center select-all"
                           x-text="detailData.lat + ', ' + detailData.lng"></p>
                        
                        <p class="text-[11px] text-slate-600 leading-snug" x-text="detailData.alamat_gps"></p>

                        <div class="pt-1">
                            <a :href="'https://maps.google.com/?q=' + detailData.lat + ',' + detailData.lng" target="_blank"
                               class="w-full py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs border border-blue-200 transition flex items-center justify-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Buka Lokasi di Google Maps</span>
                            </a>
                        </div>
                    </div>

                    <!-- Bukti Dokumentasi & TTD -->
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2.5">
                        <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block border-b border-slate-100 pb-1">
                            Bukti Lapangan & Tanda Tangan
                        </span>

                        <div class="grid grid-cols-2 gap-2.5">
                            <template x-if="detailData.foto_kegiatan">
                                <div class="text-center p-2 rounded-xl bg-slate-50 border">
                                    <span class="text-[9.5px] text-slate-500 block mb-1 font-bold">Foto Lapangan</span>
                                    <img :src="detailData.foto_kegiatan" class="max-h-28 w-full object-cover rounded-lg border">
                                </div>
                            </template>
                            <template x-if="detailData.ttd_src">
                                <div class="text-center p-2 rounded-xl bg-slate-50 border">
                                    <span class="text-[9.5px] text-slate-500 block mb-1 font-bold">TTD Pemohon</span>
                                    <img :src="detailData.ttd_src" class="max-h-28 mx-auto bg-white p-1 rounded-lg border object-contain">
                                </div>
                            </template>
                        </div>

                        <template x-if="detailData.file_dokumen">
                            <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                                <span class="text-[10px] text-slate-500 font-bold">Berkas Surat Undangan/SPT:</span>
                                <a :href="detailData.file_dokumen" target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-[#064E3B] hover:bg-[#04392B] text-white text-xs font-bold shadow-xs">
                                    Buka Berkas
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Bottom Back Button -->
            <div class="pt-2">
                <button type="button" @click="backToList()"
                        class="w-full py-3 rounded-2xl bg-[#064E3B] hover:bg-[#04392B] text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center justify-center gap-1.5 active:scale-98">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Riwayat</span>
                </button>
            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
function riwayatHub() {
    return {
        activeView: 'list', // 'list' | 'detail'
        detailType: null,
        detailData: null,

        showDetail(type, data) {
            this.detailType = type;
            this.detailData = data;
            this.activeView = 'detail';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        backToList() {
            this.activeView = 'list';
            this.detailData = null;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('riwayatHub', riwayatHub);
});
</script>
@endsection
