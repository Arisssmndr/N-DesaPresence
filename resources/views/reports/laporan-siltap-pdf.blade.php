<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pembayaran Siltap — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { size: A4 landscape; margin: 8mm 12mm 12mm 14mm; }
        * { box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #111; line-height: 1.4; margin: 0; }

        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 6px; }
        .kop-logo-cell { width: 78px; text-align: center; vertical-align: middle; }
        .kop-logo-circle {
            width: 65px; height: 65px;
            border: 2px solid #000; border-radius: 50%;
            text-align: center; vertical-align: middle;
            font-size: 22pt; font-weight: bold; color: #000;
            line-height: 65px; display: inline-block;
        }
        .kop-text-cell { text-align: center; vertical-align: middle; padding: 0 8px; }
        .kop-prov  { font-size: 8.5pt; margin: 0; }
        .kop-kab   { font-size: 10pt; font-weight: bold; text-transform: uppercase; margin: 1px 0; }
        .kop-kec   { font-size: 8.5pt; margin: 1px 0; }
        .kop-desa  { font-size: 13pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; letter-spacing: 1px; }
        .kop-alamat { font-size: 8pt; color: #444; font-style: italic; margin: 2px 0 0; }

        /* JUDUL */
        .doc-title { text-align: center; margin: 5px 0 2px; }
        .doc-title h2 { font-size: 11pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-sub   { text-align: center; font-size: 9.5pt; font-weight: bold; margin: 2px 0; }
        .doc-nomor { text-align: center; font-size: 8.5pt; margin: 0 0 6px; }

        /* INFO TABLE */
        .info-table { width: 100%; font-size: 9pt; margin-bottom: 6px; }
        .info-table td { padding: 1.5px 0; vertical-align: top; }
        .info-table td.label { width: 135px; }
        .info-table td.sep   { width: 12px; }

        /* DATA TABLE */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; margin-top: 6px; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 4px 4px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f2f2f2; color: #000; font-weight: bold; }
        table.data-table th.sub { background-color: #e5e7eb; color: #000; font-size: 8.5pt; }
        table.data-table th.dark { background-color: #d1d5db; color: #000; }
        table.data-table td.left { text-align: left; padding-left: 5px; }
        table.data-table tr.even td { background-color: #fafafa; }
        table.data-table tfoot td { background-color: #f5f5f5; font-weight: bold; }

        /* HIGHLIGHT */
        .col-bruto  { background: #f0fdf4; }
        .col-potong { background: #fef2f2; color: #991b1b; }
        .col-neto   { background: #ecfdf5; color: #064E3B; font-weight: bold; }
        .col-ttd    { background: #fffbeb; height: 36px; }
        .rupiah     { font-family: 'Arial', sans-serif; }

        /* TERBILANG */
        .terbilang-box {
            margin-top: 6px; border: 1px solid #ccc; border-radius: 4px;
            padding: 4px 8px; font-size: 8.5pt; background: #f9fafb;
        }

        /* STAMP PLACEHOLDER */
        .stamp-circle {
            width: 55px; height: 55px; border: 2px dashed #9ca3af; border-radius: 50%;
            text-align: center; vertical-align: middle; color: #9ca3af;
            font-size: 7pt; line-height: 18px; padding-top: 10px;
            display: inline-block; margin-top: 4px;
        }

        /* TTD */
        .ttd-table { width: 100%; margin-top: 12px; }
        .ttd-cell  { width: 50%; text-align: center; font-size: 9.5pt; vertical-align: top; }
        .ttd-space { height: 44px; }
        .ttd-name  { font-weight: bold; text-decoration: underline; font-size: 10pt; }
        .ttd-nipd  { font-size: 8.5pt; }

        .footer-note { margin-top: 5px; font-size: 7.5pt; color: #666; font-style: italic; }
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
                <th rowspan="2" style="width:200px;">Nama Perangkat Desa</th>
                <th rowspan="2" style="width:130px;">Jabatan</th>
                <th rowspan="2" style="width:65px;">Hadir<br>(Hari)</th>
                <th rowspan="2" style="width:65px;">Alpa<br>(Hari)</th>
                <th colspan="3" class="sub">PENGHASILAN (Rp)</th>
                <th rowspan="2" style="width:90px;">Tanda Tangan<br>Penerima</th>
            </tr>
            <tr>
                <th class="sub" style="width:110px;">Siltap Bruto</th>
                <th class="sub" style="width:110px;">Potongan</th>
                <th class="dark" style="width:110px;">Siltap Neto</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($rekaps as $r)
                <tr class="{{ $no % 2 === 0 ? 'even' : '' }}">
                    <td>{{ $no++ }}</td>
                    <td class="left">
                        <strong>{{ $r->pegawai->nama_lengkap ?? '-' }}</strong>
                        <br><span style="color:#666; font-size:7.5pt;">NIPD: {{ $r->pegawai->nipd ?? '-' }}</span>
                    </td>
                    <td class="left" style="font-size:8.5pt;">{{ $r->pegawai->jabatan->nama_jabatan ?? '-' }}</td>
                    <td style="background:#d1fae5; color:#065f46; font-weight:bold;">{{ $r->total_hadir }}</td>
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
                <td class="rupiah" style="background:#d1fae5; color:#065f46; font-weight:bold;">Rp {{ number_format($totalBruto, 0, ',', '.') }}</td>
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
                <p class="ttd-name">{{ $kades->nama_lengkap ?? 'H. AHMAD SUPRIYADI, S.IP' }}</p>
                <p class="ttd-nipd">NIPD: {{ $kades->nipd ?? '-' }}</p>
            </td>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'HJ. NURLAILA RAHMAWATI, S.AP' }}</p>
                <p class="ttd-nipd">NIPD: {{ $sekdes->nipd ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
