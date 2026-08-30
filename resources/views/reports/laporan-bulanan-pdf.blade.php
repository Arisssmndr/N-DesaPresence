<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Bulanan — {{ $namaBulan }} {{ $tahun }}</title>
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
            margin-bottom: 10px; /* Jarak enter standar naskah dinas dari Kop Surat */
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

        /* ══════════ TABEL MATRIKS ══════════ */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 7.2pt; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 2px 1px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f1f5f9; color: #000; font-weight: bold; font-size: 6.8pt; padding: 2.5px 1px; }
        table.data-table th.sub { background-color: #e2e8f0; color: #000; }
        table.data-table td.nama { text-align: left; padding-left: 3px; font-size: 7.2pt; white-space: nowrap; }
        table.data-table tfoot td { background-color: #f5f5f5; font-weight: bold; }
        table.data-table tr.even td { background-color: #fafbfc; }

        /* KODE WARNA */
        .code-H { background: #dcfce7; color: #166534; font-weight: bold; }
        .code-I { background: #fef3c7; color: #92400e; font-weight: bold; }
        .code-S { background: #f3e8ff; color: #6b21a8; font-weight: bold; }
        .code-A { background: #fee2e2; color: #991b1b; font-weight: bold; }
        .code-L { background: #f1f5f9; color: #64748b; font-size: 5.8pt; }
        .code-- { background: #fafafa; color: #94a3b8; }

        /* ══════════ TTD TABLE (4X ENTER LEGA) ══════════ */
        .ttd-table { width: 100%; margin-top: 8px; page-break-inside: avoid; }
        .ttd-cell { width: 50%; text-align: center; font-size: 8.5pt; vertical-align: top; line-height: 1.25; }
        .ttd-space { height: 48px; /* Ruang 4x Enter lega */ }
        .ttd-name { font-weight: bold; text-decoration: underline; font-size: 9.5pt; }
        .ttd-nipd { font-size: 8pt; color: #111; }

        .footer-note { margin-top: 3px; font-size: 6.5pt; color: #444; font-style: italic; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo-tasikmalaya.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- ══════════ KOP SURAT STANDAR TATA NASKAH DINAS PEMKAB TASIKMALAYA ══════════ -->
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

    <!-- JUDUL -->
    <div class="doc-title">
        <h2>LAPORAN REKAPITULASI PRESENSI BULANAN PERANGKAT DESA NANGTANG</h2>
    </div>
    <p class="doc-nomor">Bulan: <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>

    <!-- INFO -->
    <div class="info-row">
        <span><strong>Unit Kerja:</strong> Pemerintah Desa Nangtang, Kec. Cigalontang, Kab. Tasikmalaya</span>
        <span><strong>Jumlah Perangkat:</strong> {{ $pegawais->count() }} Orang</span>
        <span><strong>Hari Kalender:</strong> {{ $daysInMonth }} Hari</span>
    </div>

    <!-- TABEL MATRIKS -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:20px;">No</th>
                <th rowspan="2" style="width:130px;">Nama Perangkat Desa</th>
                <th rowspan="2" style="width:90px;">Jabatan</th>
                <th colspan="{{ $daysInMonth }}" class="sub">TANGGAL</th>
                <th colspan="4" class="sub">REKAPITULASI</th>
                <th rowspan="2" class="sub" style="width:28px;">%<br>HDR</th>
            </tr>
            <tr>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <th style="width:13px; font-size:5.8pt;">{{ $d }}</th>
                @endfor
                <th style="width:17px; font-size:6.5pt;">H</th>
                <th style="width:17px; font-size:6.5pt;">I</th>
                <th style="width:17px; font-size:6.5pt;">S</th>
                <th style="width:17px; font-size:6.5pt;">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                <tr class="{{ $idx % 2 !== 0 ? 'even' : '' }}">
                    <td>{{ $idx + 1 }}</td>
                    <td class="nama"><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td style="text-align:left; padding-left:2px; font-size:6.2pt; white-space:nowrap;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php $code = $matrix[$p->id][$d] ?? 'A'; @endphp
                        <td class="code-{{ $code }}" style="font-size:6pt;">{{ $code }}</td>
                    @endfor
                    <td style="background:#dcfce7; color:#166534; font-weight:bold;">{{ $summary[$p->id]['H'] ?? 0 }}</td>
                    <td style="background:#fef3c7; color:#92400e; font-weight:bold;">{{ $summary[$p->id]['I'] ?? 0 }}</td>
                    <td style="background:#f3e8ff; color:#6b21a8; font-weight:bold;">{{ $summary[$p->id]['S'] ?? 0 }}</td>
                    <td style="background:#fee2e2; color:#991b1b; font-weight:bold;">{{ $summary[$p->id]['A'] ?? 0 }}</td>
                    <td style="background:#f0fdf4; color:#166534; font-weight:bold;">{{ $summary[$p->id]['persen'] ?? 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold; font-size:6.8pt;">TOTAL</td>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <td style="font-size:5.5pt; color:#555;">—</td>
                @endfor
                <td style="background:#dcfce7; color:#166534;">{{ collect($summary)->sum('H') }}</td>
                <td style="background:#fef3c7; color:#92400e;">{{ collect($summary)->sum('I') }}</td>
                <td style="background:#f3e8ff; color:#6b21a8;">{{ collect($summary)->sum('S') }}</td>
                <td style="background:#fee2e2; color:#991b1b;">{{ collect($summary)->sum('A') }}</td>
                <td style="background:#f0fdf4;">—</td>
            </tr>
        </tfoot>
    </table>

    <!-- KETERANGAN KODE -->
    <p class="footer-note">
        Keterangan: <strong>H</strong> = Hadir &nbsp;|&nbsp; <strong>I</strong> = Izin &nbsp;|&nbsp; <strong>S</strong> = Sakit &nbsp;|&nbsp; <strong>A</strong> = Alpa/Tanpa Keterangan &nbsp;|&nbsp; <strong>L</strong> = Libur/Akhir Pekan<br>
        * Dicetak otomatis dari Sistem Presensi Digital Desa Nangtang — {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </p>

    <!-- TTD -->
    <table class="ttd-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Mengetahui / Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $kades->nama_lengkap ?? 'DADAY DAHYAT' }}</p>
                <p class="ttd-nipd">NIPD: {{ $kades->nipd ?? '141.1/Kep.053-Pemdes/2019' }}</p>
            </td>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                <p class="ttd-nipd">NIPD: {{ $sekdes->nipd ?? '141.1/KEP.01/DES/2020' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
