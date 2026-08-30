<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPJ Presensi Desa Nangtang — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 12mm 6mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', Helvetica, sans-serif;
            font-size: 8.5pt;
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
            width: 44px;
            height: 44px;
            border: 2px solid #000;
            border-radius: 50%;
            text-align: center;
            vertical-align: middle;
            font-size: 15pt;
            font-weight: bold;
            color: #000;
            line-height: 44px;
            display: inline-block;
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

        /* ══════════ JUDUL DOKUMEN ══════════ */
        .title-section {
            text-align: center;
            margin: 0 0 4px 0;
        }
        .title-section h4 {
            font-size: 11pt;
            margin: 0;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-section p {
            font-size: 8.5pt;
            margin: 2px 0 0 0;
            color: #000;
        }

        /* ══════════ DATA TABLE ══════════ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
            margin-top: 2px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #222;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #000;
            font-size: 6.8pt;
            padding: 2.5px 1px;
        }
        table.data-table th.sub {
            background-color: #e2e8f0;
            color: #000;
        }
        table.data-table td.nama {
            text-align: left;
            padding-left: 3px;
            font-size: 7.2pt;
            white-space: nowrap;
        }
        table.data-table td.jabatan {
            text-align: left;
            padding-left: 2px;
            font-size: 6.8pt;
            white-space: nowrap;
        }
        table.data-table tr.even td {
            background-color: #fafbfc;
        }

        /* Attendance Status Colors */
        .code-H { background-color: #dcfce7; font-weight: bold; color: #166534; }
        .code-I { background-color: #fef3c7; font-weight: bold; color: #92400e; }
        .code-S { background-color: #f3e8ff; font-weight: bold; color: #6b21a8; }
        .code-A { background-color: #fee2e2; font-weight: bold; color: #991b1b; }
        .code-L { background-color: #f1f5f9; color: #64748b; font-size: 5.8pt; }
        .code-- { background-color: #fafafa; color: #94a3b8; }

        /* Summary Column Backgrounds */
        .sum-H { background-color: #dcfce7; font-weight: bold; color: #166534; }
        .sum-I { background-color: #fef3c7; font-weight: bold; color: #92400e; }
        .sum-S { background-color: #f3e8ff; font-weight: bold; color: #6b21a8; }
        .sum-A { background-color: #fee2e2; font-weight: bold; color: #991b1b; }

        /* ══════════ KETERANGAN ══════════ */
        .keterangan-row {
            font-size: 7pt;
            margin: 3px 0 2px 0;
            color: #222;
        }

        /* ══════════ TANDA TANGAN (TTD) TABLE (4X ENTER LEGA) ══════════ */
        .ttd-table {
            width: 100%;
            margin-top: 6px;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        .ttd-cell {
            width: 45%;
            text-align: center;
            vertical-align: top;
            line-height: 1.25;
        }
        .ttd-space {
            height: 48px; /* Ruang 4x Enter lega */
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9.5pt;
        }
        .ttd-nipd {
            font-size: 8pt;
            color: #111;
        }
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
                <div class="kop-alamat">Jalan Raya Desa Nangtang Kode Pos 46463 — Pos-el: pemdes@desanangtang.go.id</div>
            </td>
            <td class="kop-logo-cell">&nbsp;</td>
        </tr>
    </table>

    <!-- ══════════ JUDUL DOKUMEN RESMI ══════════ -->
    <div class="title-section">
        <h4>LAPORAN REKAPITULASI PRESENSI PERANGKAT DESA (SPJ)</h4>
        <p>Bulan: <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>
    </div>

    <!-- ══════════ TABEL MATRIKS PRESENSI ══════════ -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 22px;">No</th>
                <th rowspan="2" style="width: 145px;">Nama Perangkat Desa</th>
                <th rowspan="2" style="width: 105px;">Jabatan</th>
                <th colspan="{{ $daysInMonth }}" class="sub">TANGGAL</th>
                <th colspan="4" class="sub">REKAPITULASI</th>
            </tr>
            <tr>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <th style="width: 14px; font-size: 6.5pt;">{{ $d }}</th>
                @endfor
                <th style="width: 20px; font-size: 7.5pt;">H</th>
                <th style="width: 20px; font-size: 7.5pt;">I</th>
                <th style="width: 20px; font-size: 7.5pt;">S</th>
                <th style="width: 20px; font-size: 7.5pt;">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                <tr class="{{ $idx % 2 !== 0 ? 'even' : '' }}">
                    <td>{{ $idx + 1 }}</td>
                    <td class="nama"><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td class="jabatan">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php $code = $matrix[$p->id][$d] ?? 'A'; @endphp
                        <td class="code-{{ $code }}">{{ $code }}</td>
                    @endfor
                    <td class="sum-H">{{ $summary[$p->id]['H'] ?? 0 }}</td>
                    <td class="sum-I">{{ $summary[$p->id]['I'] ?? 0 }}</td>
                    <td class="sum-S">{{ $summary[$p->id]['S'] ?? 0 }}</td>
                    <td class="sum-A">{{ $summary[$p->id]['A'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="keterangan-row">
        <em>Keterangan:</em> <strong>H</strong> = Hadir &nbsp;|&nbsp; <strong>I</strong> = Izin &nbsp;|&nbsp; <strong>S</strong> = Sakit &nbsp;|&nbsp; <strong>A</strong> = Alpa/Tanpa Keterangan &nbsp;|&nbsp; <strong>L</strong> = Libur/Akhir Pekan
    </div>

    <!-- ══════════ LEMBAR TANDA TANGAN RESMI KADES & SEKDES ══════════ -->
    <table class="ttd-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="ttd-cell">
                <p style="margin: 0 0 2px;">Mengetahui/Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name" style="margin: 0;">{{ $kades->nama_lengkap ?? 'DADAY DAHYAT' }}</p>
                <p class="ttd-nipd" style="margin: 2px 0 0;">NIPD: {{ $kades->nipd ?? '141.1/Kep.053-Pemdes/2019' }}</p>
            </td>
            <td style="width: 10%;">&nbsp;</td>
            <td class="ttd-cell">
                <p style="margin: 0 0 2px;">Nangtang, {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name" style="margin: 0;">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                <p class="ttd-nipd" style="margin: 2px 0 0;">NIPD: {{ $sekdes->nipd ?? '141.1/KEP.01/DES/2020' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
