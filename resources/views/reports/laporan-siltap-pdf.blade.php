<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pembayaran Siltap — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 8mm 12mm 6mm 12mm; 
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Arial', Helvetica, sans-serif; 
            font-size: 9pt; 
            color: #000; 
            line-height: 1.25; 
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
        .doc-sub   { text-align: center; font-size: 9pt; font-weight: bold; margin: 1px 0; }
        .doc-nomor { text-align: center; font-size: 8.5pt; margin: 2px 0 4px; }

        /* ══════════ INFO TABLE ══════════ */
        .info-table { width: 100%; font-size: 8pt; margin-bottom: 4px; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        .info-table td.label { width: 130px; }
        .info-table td.sep   { width: 12px; }

        /* ══════════ DATA TABLE ══════════ */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 2px; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 2.5px 3px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f1f5f9; color: #000; font-weight: bold; font-size: 7.8pt; }
        table.data-table th.sub { background-color: #e2e8f0; color: #000; font-size: 7.2pt; }
        table.data-table th.dark { background-color: #cbd5e1; color: #000; }
        table.data-table td.left { text-align: left; padding-left: 4px; }
        table.data-table tr.even td { background-color: #fafbfc; }
        table.data-table tfoot td { background-color: #f5f5f5; font-weight: bold; }

        /* HIGHLIGHT */
        .col-bruto  { background: #f0fdf4; }
        .col-potong { background: #fef2f2; color: #991b1b; }
        .col-neto   { background: #ecfdf5; color: #064E3B; font-weight: bold; }
        .col-ttd    { background: #fffbeb; height: 26px; min-height: 26px; }
        .rupiah     { font-family: 'Arial', sans-serif; }

        /* TERBILANG */
        .terbilang-box {
            margin-top: 4px; border: 1px solid #ccc; border-radius: 4px;
            padding: 3px 6px; font-size: 8pt; background: #f9fafb;
        }

        /* STAMP PLACEHOLDER */
        .stamp-circle {
            width: 44px; height: 44px; border: 2px dashed #9ca3af; border-radius: 50%;
            text-align: center; vertical-align: middle; color: #9ca3af;
            font-size: 6pt; line-height: 14px; padding-top: 7px;
            display: inline-block; margin-top: 1px;
        }

        /* TTD TABLE (4X ENTER LEGA) */
        .ttd-table { width: 100%; margin-top: 6px; page-break-inside: avoid; }
        .ttd-cell  { width: 50%; text-align: center; font-size: 8.5pt; vertical-align: top; line-height: 1.25; }
        .ttd-space { height: 48px; /* Ruang 4x Enter lega */ }
        .ttd-name  { font-weight: bold; text-decoration: underline; font-size: 9.5pt; }
        .ttd-nipd  { font-size: 8pt; color: #111; }

        .footer-note { margin-top: 3px; font-size: 6.5pt; color: #555; font-style: italic; }
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
        <h2>DAFTAR PEMBAYARAN PENGHASILAN TETAP DAN TUNJANGAN</h2>
        <p class="doc-sub">PERANGKAT DESA NANGTANG</p>
    </div>
    <p class="doc-nomor">Bulan: <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>

    <!-- INFO -->
    <table class="info-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="label">Unit Kerja</td>
            <td class="sep">:</td>
            <td>Pemerintah Desa Nangtang, Kec. Cigalontang, Kab. Tasikmalaya</td>
        </tr>
        <tr>
            <td class="label">Periode Pembayaran</td>
            <td class="sep">:</td>
            <td><strong>{{ $namaBulan }} {{ $tahun }}</strong></td>
        </tr>
        <tr>
            <td class="label">Jumlah Penerima</td>
            <td class="sep">:</td>
            <td>{{ $rekaps->count() }} Orang Perangkat Desa</td>
        </tr>
        <tr>
            <td class="label">Sumber Dana</td>
            <td class="sep">:</td>
            <td>APBDes Desa Nangtang Tahun Anggaran {{ $tahun }}</td>
        </tr>
    </table>

    <!-- TABEL SILTAP -->
    @if ($rekaps->isEmpty())
        <div style="text-align:center; padding:20px; color:#999; font-style:italic; border:1px solid #ccc; border-radius:4px;">
            Belum ada data rekap Siltap untuk periode {{ $namaBulan }} {{ $tahun }}.<br>
            Silakan jalankan Kalkulasi Siltap terlebih dahulu.
        </div>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:28px;">No</th>
                <th rowspan="2" style="width:180px;">Nama Perangkat Desa</th>
                <th rowspan="2" style="width:120px;">Jabatan</th>
                <th rowspan="2" style="width:60px;">Hadir<br>(Hari)</th>
                <th rowspan="2" style="width:60px;">Alpa<br>(Hari)</th>
                <th colspan="3" class="sub">PENGHASILAN (Rp)</th>
                <th rowspan="2" style="width:85px;">Tanda Tangan<br>Penerima</th>
            </tr>
            <tr>
                <th class="sub" style="width:105px;">Siltap Bruto</th>
                <th class="sub" style="width:105px;">Potongan</th>
                <th class="dark" style="width:105px;">Siltap Neto</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($rekaps as $r)
                <tr class="{{ $no % 2 === 0 ? 'even' : '' }}">
                    <td>{{ $no++ }}</td>
                    <td class="left">
                        <strong>{{ $r->pegawai->nama_lengkap ?? '-' }}</strong>
                        <br><span style="color:#666; font-size:7pt;">NIPD: {{ $r->pegawai->nipd ?? '-' }}</span>
                    </td>
                    <td class="left" style="font-size:8pt;">{{ $r->pegawai->jabatan->nama_jabatan ?? '-' }}</td>
                    <td style="background:#dcfce7; color:#166534; font-weight:bold;">{{ $r->total_hadir }}</td>
                    <td style="background:#fee2e2; color:#991b1b; font-weight:bold;">{{ $r->total_alpa }}</td>
                    <td class="col-bruto rupiah">Rp {{ number_format($r->siltap_bruto, 0, ',', '.') }}</td>
                    <td class="col-potong rupiah">
                        @php $pot = (float)$r->potongan_alpa + (float)$r->potongan_terlambat; @endphp
                        {{ $pot > 0 ? '(Rp '.number_format($pot, 0, ',', '.').')' : '-' }}
                    </td>
                    <td class="col-neto rupiah"><strong>Rp {{ number_format($r->siltap_neto, 0, ',', '.') }}</strong></td>
                    <td class="col-ttd">&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right; font-weight:bold; background:#ecfdf5;">JUMLAH TOTAL</td>
                <td class="rupiah" style="background:#dcfce7; color:#166534; font-weight:bold;">Rp {{ number_format($totalBruto, 0, ',', '.') }}</td>
                <td class="rupiah" style="background:#fee2e2; color:#991b1b; font-weight:bold;">(Rp {{ number_format($totalPotongan, 0, ',', '.') }})</td>
                <td class="rupiah col-neto"><strong>Rp {{ number_format($totalNeto, 0, ',', '.') }}</strong></td>
                <td style="background:#ecfdf5;">—</td>
            </tr>
        </tfoot>
    </table>

    <!-- TERBILANG -->
    <div class="terbilang-box">
        <strong>Total Pembayaran Bersih:</strong>
        <span class="rupiah" style="font-style:italic;">Rp {{ number_format($totalNeto, 0, ',', '.') }},-</span>
    </div>
    @endif

    <p class="footer-note">
        * Laporan ini dicetak secara otomatis dari Sistem Presensi Digital Desa Nangtang pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.<br>
        * Potongan dihitung berdasarkan hari alpa dan keterlambatan sesuai ketentuan Peraturan Desa Nangtang.
    </p>

    <!-- TTD -->
    <table class="ttd-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Mengetahui / Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                <div class="ttd-space">
                    <div class="stamp-circle">Cap<br>Desa</div>
                </div>
                <p class="ttd-name">{{ $kades->nama_lengkap ?? 'DADAY DAHYAT' }}</p>
                <p class="ttd-nipd">NIPD: {{ $kades->nipd ?? '141.1/Kep.053-Pemdes/2019' }}</p>
            </td>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                <p class="ttd-nipd">NIPD: {{ $sekdes->nipd ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
