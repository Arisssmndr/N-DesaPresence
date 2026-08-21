<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Bulanan — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 12mm 18mm 15mm; }
        * { box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 8.5pt; color: #111; line-height: 1.3; margin: 0; }

        /* KOP SURAT — table layout agar DomPDF support */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 10px; }
        .kop-logo-cell { width: 68px; text-align: center; vertical-align: middle; }
        .kop-logo-circle {
            width: 56px; height: 56px;
            border: 2px solid #000; border-radius: 50%;
            text-align: center; vertical-align: middle;
            font-size: 20pt; font-weight: bold; color: #000;
            line-height: 56px; display: inline-block;
        }
        .kop-text-cell { text-align: center; vertical-align: middle; }
        .kop-prov  { font-size: 8pt; margin: 0; }
        .kop-desa  { font-size: 12.5pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; letter-spacing: 1px; }
        .kop-alamat { font-size: 7.5pt; color: #444; font-style: italic; margin: 2px 0 0; }

        /* JUDUL */
        .doc-title { text-align: center; margin: 8px 0 2px; }
        .doc-title h2 { font-size: 10.5pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { text-align: center; font-size: 8.5pt; margin: 0 0 6px; }
        .info-row  { font-size: 8.5pt; margin-bottom: 6px; }
        .info-row span { margin-right: 20px; }

        /* TABEL MATRIKS */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 7pt; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 2px 1px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f2f2f2; color: #000; font-weight: bold; }
        table.data-table th.sub { background-color: #e5e7eb; color: #000; }
        table.data-table td.nama { text-align: left; padding-left: 4px; }
        table.data-table tfoot td { background-color: #f5f5f5; font-weight: bold; }
        table.data-table tr.even td { background-color: #fafafa; }

        /* KODE WARNA */
        .code-H { background: #bbf7d0; color: #14532d; font-weight: bold; }
        .code-T { background: #fde68a; color: #78350f; font-weight: bold; }
        .code-A { background: #fecaca; color: #7f1d1d; font-weight: bold; }
        .code-I { background: #e9d5ff; color: #4c1d95; font-weight: bold; }
        .code-D { background: #bfdbfe; color: #1e3a8a; font-weight: bold; }
        .code-L { background: #e5e7eb; color: #6b7280; font-size: 6pt; }

        /* TTD — table 2 kolom */
        .ttd-table { width: 100%; margin-top: 14px; }
        .ttd-cell { width: 50%; text-align: center; font-size: 9pt; vertical-align: top; }
        .ttd-space { height: 50px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }

        .footer-note { margin-top: 6px; font-size: 7.5pt; color: #666; font-style: italic; }
</head>
<body>
    @php
        $logoPath = public_path('images/logo-tasikmalaya.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- ══════════ KOP SURAT STANDAR TATA NASKAH DINAS PEMKAB TASIKMALAYA ══════════ -->
    <table class="kop-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="kop-logo-cell" style="width: 65px; text-align: center; vertical-align: middle;">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 60px; width: auto; max-width: 58px;" alt="Logo Kab. Tasikmalaya">
                @else
                    <div class="kop-logo-circle">N</div>
                @endif
            </td>
            <td class="kop-text-cell" style="text-align: center; vertical-align: middle; padding: 0 4px;">
                <p style="font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 0; line-height: 1.2;">PEMERINTAH KABUPATEN TASIKMALAYA</p>
                <p style="font-size: 10.5pt; font-weight: bold; text-transform: uppercase; margin: 1px 0; line-height: 1.2;">KECAMATAN CIGALONTANG</p>
                <p style="font-size: 13.5pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; letter-spacing: 0.5px; line-height: 1.2;">KANTOR KEPALA DESA NANGTANG</p>
                <p style="font-size: 8pt; color: #111; font-style: italic; margin: 2px 0 0;">Jalan Raya Desa Nangtang, Kode Pos 46463 — Pos-el: pemdes@desanangtang.go.id</p>
            </td>
            <td class="kop-logo-cell" style="width: 65px;">&nbsp;</td>
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
                <th rowspan="2" style="width:22px;">No</th>
                <th rowspan="2" style="min-width:115px;">Nama Perangkat Desa</th>
                <th rowspan="2" style="min-width:70px;">Jabatan</th>
                <th colspan="{{ $daysInMonth }}" class="sub">TANGGAL</th>
                <th colspan="5" class="sub">REKAPITULASI</th>
                <th rowspan="2" class="sub" style="width:26px;">%<br>HDR</th>
            </tr>
            <tr>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <th style="width:14px; font-size:6pt;">{{ $d }}</th>
                @endfor
                <th style="width:18px; font-size:7pt;">H</th>
                <th style="width:18px; font-size:7pt;">T</th>
                <th style="width:18px; font-size:7pt;">I</th>
                <th style="width:18px; font-size:7pt;">D</th>
                <th style="width:18px; font-size:7pt;">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                <tr class="{{ $idx % 2 !== 0 ? 'even' : '' }}">
                    <td>{{ $idx + 1 }}</td>
                    <td class="nama"><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td style="text-align:left; padding-left:3px; font-size:6.5pt;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php $code = $matrix[$p->id][$d] ?? 'A'; @endphp
                        <td class="code-{{ $code }}" style="font-size:6pt;">{{ $code }}</td>
                    @endfor
                    <td style="background:#bbf7d0; color:#14532d; font-weight:bold;">{{ $summary[$p->id]['H'] ?? 0 }}</td>
                    <td style="background:#fde68a; color:#78350f; font-weight:bold;">{{ $summary[$p->id]['T'] ?? 0 }}</td>
                    <td style="background:#e9d5ff; color:#4c1d95; font-weight:bold;">{{ $summary[$p->id]['I'] ?? 0 }}</td>
                    <td style="background:#bfdbfe; color:#1e3a8a; font-weight:bold;">{{ $summary[$p->id]['D'] ?? 0 }}</td>
                    <td style="background:#fecaca; color:#7f1d1d; font-weight:bold;">{{ $summary[$p->id]['A'] ?? 0 }}</td>
                    <td style="background:#f0fdf4; color:#14532d; font-weight:bold;">{{ $summary[$p->id]['persen'] ?? 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold;">TOTAL</td>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <td style="font-size:6pt; color:#555;">—</td>
                @endfor
                <td style="background:#bbf7d0; color:#14532d;">{{ collect($summary)->sum('H') }}</td>
                <td style="background:#fde68a; color:#78350f;">{{ collect($summary)->sum('T') }}</td>
                <td style="background:#e9d5ff; color:#4c1d95;">{{ collect($summary)->sum('I') }}</td>
                <td style="background:#bfdbfe; color:#1e3a8a;">{{ collect($summary)->sum('D') }}</td>
                <td style="background:#fecaca; color:#7f1d1d;">{{ collect($summary)->sum('A') }}</td>
                <td style="background:#f0fdf4;">—</td>
            </tr>
        </tfoot>
    </table>

    <!-- KETERANGAN KODE -->
    <p class="footer-note">
        Keterangan: <strong>H</strong> = Hadir Tepat Waktu &nbsp;|&nbsp; <strong>T</strong> = Terlambat &nbsp;|&nbsp; <strong>I</strong> = Izin/Sakit &nbsp;|&nbsp; <strong>D</strong> = Dinas Luar (SPT) &nbsp;|&nbsp; <strong>A</strong> = Alpa/Tanpa Keterangan &nbsp;|&nbsp; <strong>L</strong> = Libur/Akhir Pekan<br>
        * Dicetak otomatis dari Sistem Presensi Digital Desa Nangtang — {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </p>

    <!-- TTD -->
    <table class="ttd-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Mengetahui / Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $kades->nama_lengkap ?? 'H. AHMAD SUPRIYADI, S.IP' }}</p>
                <p style="font-size:8.5pt;">NIPD: {{ $kades->nipd ?? '-' }}</p>
            </td>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'HJ. NURLAILA RAHMAWATI, S.AP' }}</p>
                <p style="font-size:8.5pt;">NIPD: {{ $sekdes->nipd ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
