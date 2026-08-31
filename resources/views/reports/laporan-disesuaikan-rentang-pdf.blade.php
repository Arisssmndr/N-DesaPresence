<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Periode {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') }}</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 6mm 10mm 5mm 10mm; 
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Arial', Helvetica, sans-serif; 
            font-size: 8pt; 
            color: #000; 
            line-height: 1.2; 
            margin: 0; 
            padding: 0;
        }

        /* ══════════ KOP SURAT STANDAR TATA NASKAH DINAS PEMKAB TASIKMALAYA ══════════ */
        .kop-table { 
            width: 100%; 
            border-bottom: 3px double #000; 
            padding-bottom: 4px; 
            margin-bottom: 10px;
        }
        .kop-logo-cell { 
            width: 65px; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-logo-img { 
            height: 52px; 
            width: auto; 
            max-width: 52px; 
            display: inline-block; 
            vertical-align: middle; 
        }
        .kop-logo-circle { 
            width: 44px; height: 44px; 
            border: 2px solid #000; border-radius: 50%; 
            text-align: center; vertical-align: middle; 
            font-size: 15pt; font-weight: bold; color: #000; 
            line-height: 44px; display: inline-block; 
        }
        .kop-text-cell { 
            text-align: center; 
            vertical-align: middle; 
            padding: 0 6px; 
        }
        .kop-kab { 
            font-size: 11.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 0; 
            line-height: 1.2;
            letter-spacing: 0.5px;
        }
        .kop-kec { 
            font-size: 10.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 1px 0; 
            line-height: 1.2; 
        }
        .kop-desa { 
            font-size: 13pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 1px 0; 
            letter-spacing: 0.8px; 
            line-height: 1.2; 
        }
        .kop-alamat { 
            font-size: 8pt; 
            color: #111; 
            font-style: italic; 
            margin: 2px 0 0; 
        }

        /* ══════════ JUDUL DOKUMEN & NOMOR ══════════ */
        .doc-title { text-align: center; margin: 0 0 2px; }
        .doc-title h2 { 
            font-size: 11pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-decoration: underline; 
            margin: 0; 
            letter-spacing: 0.5px; 
        }
        .doc-nomor { text-align: center; font-size: 8.5pt; margin: 2px 0 4px; }
        .info-row  { font-size: 8pt; margin-bottom: 4px; }
        .info-row span { margin-right: 18px; }

        /* ══════════ TABEL MATRIKS / DATA ══════════ */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 7.2pt; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 2px 1px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f1f5f9; color: #000; font-weight: bold; font-size: 6.8pt; padding: 2.5px 1px; }
        table.data-table th.col-pegawai { text-align: left; padding-left: 4px; }
        table.data-table td.col-pegawai { text-align: left; padding-left: 4px; }
        table.data-table th.col-rekap,
        table.data-table td.col-rekap { font-weight: bold; background-color: #f8fafc; }
        table.data-table th.col-persen,
        table.data-table td.col-persen { font-weight: bold; background-color: #f1f5f9; }

        /* Status Colors */
        .c-H { font-weight: bold; color: #000; }
        .c-T { color: #000; }
        .c-A { font-weight: bold; color: #000; }
        .c-I { color: #000; }
        .c-D { font-weight: bold; color: #000; }
        .c-L { color: #999; background-color: #f3f4f6; }
        .c-dash { color: #ccc; }

        th.libur-hdr { background-color: #e2e8f0; color: #555; }
        td.libur-cell { background-color: #f8fafc; color: #aaa; }

        /* ══════════ LEGENDA & STATISTIK ══════════ */
        .legend-row { margin-top: 5px; font-size: 7.2pt; display: table; width: 100%; }
        .legend-cell { display: table-cell; vertical-align: top; }
        .legend-items span { margin-right: 10px; }

        /* ══════════ TANDA TANGAN ══════════ */
        .ttd-table { width: 100%; margin-top: 8px; page-break-inside: avoid; }
        .ttd-cell  { width: 50%; text-align: center; font-size: 8pt; vertical-align: top; line-height: 1.2; }
        .ttd-space { height: 44px; }
        .ttd-name  { font-weight: bold; text-decoration: underline; margin: 0; font-size: 8.5pt; }
        .ttd-nipd  { font-size: 7.5pt; margin: 1px 0 0; }
        .footer-note { margin-top: 3px; font-size: 6pt; color: #444; font-style: italic; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        $logoPath   = public_path('images/logo-tasikmalaya.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- ══════════ KOP SURAT ══════════ -->
    <table class="kop-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="kop-logo-cell">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="kop-logo-img" alt="Logo Kab. Tasikmalaya">
                @else
                    <div class="kop-logo-circle">N</div>
                @endif
            </td>
            <td class="kop-text-cell">
                <div class="kop-kab">PEMERINTAH KABUPATEN TASIKMALAYA</div>
                <div class="kop-kec">KECAMATAN CIGALONTANG</div>
                <div class="kop-desa">PEMERINTAH DESA NANGTANG</div>
                <div class="kop-alamat">Jalan Raya Desa Nangtang, Kode Pos 46463 — Pos-el: pemdes@desanangtang.go.id</div>
            </td>
            <td class="kop-logo-cell">&nbsp;</td>
        </tr>
    </table>

    <!-- ══════════ JUDUL DOKUMEN ══════════ -->
    <div class="doc-title">
        <h2>REKAPITULASI PRESENSI PERIODE TERTENTU</h2>
    </div>
    <div class="doc-nomor">Nomor: {{ $nomorLaporan }}</div>

    <div class="info-row">
        <span>Rentang Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}</strong></span>
        <span>Durasi: <strong>{{ count($dateRange) }} Hari Kalender ({{ $totalHariKerjaPeriode }} Hari Kerja)</strong></span>
        <span>Unit Kerja: <strong>Pemerintah Desa Nangtang</strong></span>
    </div>

    <!-- ══════════ TABEL MATRIKS RENTANG TANGGAL ══════════ -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:16px;">No</th>
                <th rowspan="2" class="col-pegawai" style="width:130px;">Nama Perangkat Desa / NIPD</th>
                <th rowspan="2" style="width:85px;">Jabatan</th>
                @if(count($dateRange) <= 31)
                    <th colspan="{{ count($dateRange) }}" style="letter-spacing: 0.5px;">Tanggal Presensi</th>
                @endif
                <th colspan="4" style="width:75px; background-color:#e2e8f0;">Rekapitulasi</th>
                <th rowspan="2" class="col-persen" style="width:32px;">%</th>
            </tr>
            <tr>
                @if(count($dateRange) <= 31)
                    @foreach ($dateRange as $dtStr)
                        @php
                            $dt = \Carbon\Carbon::parse($dtStr);
                            $isWk = $dt->isWeekend();
                        @endphp
                        <th style="width:14px;" class="{{ $isWk ? 'libur-hdr' : '' }}" title="{{ $dt->translatedFormat('d M Y') }}">{{ $dt->format('d') }}</th>
                    @endforeach
                @endif
                <th style="width:18px;" title="Hadir">H</th>
                <th style="width:18px;" title="Izin">I</th>
                <th style="width:18px;" title="Sakit">S</th>
                <th style="width:18px;" title="Alpa / Tanpa Keterangan">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                @php
                    $pSum = $summary[$p->id] ?? ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0, 'persen' => 0];
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="col-pegawai">
                        <strong>{{ $p->nama_lengkap }}</strong>
                        @if($p->nipd)
                            <br><span style="font-size:6.5pt; color:#444;">{{ $p->nipd }}</span>
                        @endif
                    </td>
                    <td style="font-size:6.8pt;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>

                    @if(count($dateRange) <= 31)
                        @foreach ($dateRange as $dtStr)
                            @php
                                $code = $matrix[$p->id][$dtStr] ?? '-';
                                $classMap = [
                                    'H' => 'c-H', 'A' => 'c-A',
                                    'I' => 'c-I', 'S' => 'c-S', 'L' => 'c-L', '-' => 'c-dash'
                                ];
                                $cClass = $classMap[$code] ?? '';
                            @endphp
                            <td class="{{ $cClass }}">{{ $code }}</td>
                        @endforeach
                    @endif

                    <td class="col-rekap">{{ $pSum['H'] }}</td>
                    <td class="col-rekap">{{ $pSum['I'] }}</td>
                    <td class="col-rekap">{{ $pSum['S'] }}</td>
                    <td class="col-rekap">{{ $pSum['A'] }}</td>
                    <td class="col-persen">{{ $pSum['persen'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ══════════ LEGENDA KODE ══════════ -->
    <div class="legend-row">
        <div class="legend-cell legend-items">
            <strong>Keterangan Kode:</strong>
            <span><strong>H</strong> = Hadir</span>
            <span><strong>I</strong> = Izin</span>
            <span><strong>S</strong> = Sakit</span>
            <span><strong>A</strong> = Alpa</span>
            <span><strong>L</strong> = Libur Resmi / Akhir Pekan</span>
            <span><strong>-</strong> = Belum Berjalan</span>
        </div>
    </div>

    <!-- ══════════ TANDA TANGAN PENGESAHAN ══════════ -->
    <div class="no-break">
        <table class="ttd-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="ttd-cell">
                    <p style="margin:0 0 2px;">Mengetahui / Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $kades->nama_lengkap ?? 'DADAY DAHYAT' }}</p>
                    <p class="ttd-nipd">NIPD: {{ $kades->nipd ?? '141.1/Kep.053-Pemdes/2019' }}</p>
                </td>
                <td class="ttd-cell">
                    <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                    <p class="ttd-nipd">NIPD: {{ $sekdes->nipd ?? '141.1/KEP.01/DES/2020' }}</p>
                </td>
            </tr>
        </table>

        <p class="footer-note">
            * Dokumen ini sah dan diterbitkan secara resmi melalui Sistem Informasi Kehadiran Aparatur Desa Nangtang berdasarkan UU No. 6/2014 tentang Desa, PP No. 94/2021 tentang Disiplin Aparatur, dan Permendagri tentang Administrasi Desa pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.
        </p>
    </div>

</body>
</html>
