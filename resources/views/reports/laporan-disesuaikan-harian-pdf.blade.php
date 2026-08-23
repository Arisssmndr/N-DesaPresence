<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Harian — {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 4mm 10mm 4mm 10mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Arial', Helvetica, sans-serif;
            font-size: 7.5pt;
            color: #000;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }

        /* ══════════ KOP SURAT STANDAR TATA NASKAH DINAS PEMKAB TASIKMALAYA ══════════ */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 3px;
            margin-bottom: 7px;
        }
        .kop-logo-cell {
            width: 58px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-logo-img {
            height: 48px;
            width: auto;
            max-width: 48px;
            display: inline-block;
            vertical-align: middle;
        }
        .kop-logo-circle {
            width: 38px;
            height: 38px;
            border: 2px solid #000;
            border-radius: 50%;
            text-align: center;
            vertical-align: middle;
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            line-height: 38px;
            display: inline-block;
        }
        .kop-text-cell {
            text-align: center;
            vertical-align: middle;
            padding: 0 4px;
        }
        .kop-kab {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.15;
            letter-spacing: 0.5px;
        }
        .kop-kec {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0;
            line-height: 1.15;
        }
        .kop-desa {
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0;
            letter-spacing: 0.8px;
            line-height: 1.15;
        }
        .kop-alamat {
            font-size: 7.5pt;
            color: #111;
            font-style: italic;
            margin: 1px 0 0;
            line-height: 1.15;
        }

        /* ══════════ JUDUL DOKUMEN ══════════ */
        .doc-title { 
            text-align: center; 
            margin: 0 0 6px 0; 
        }
        .doc-title h2 {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
            letter-spacing: 0.5px;
            line-height: 1.15;
        }

        /* ══════════ INFO TABLE ══════════ */
        .info-table {
            width: 100%;
            margin-bottom: 4px;
            font-size: 7.5pt;
        }
        .info-table td {
            padding: 0.5px 0;
            vertical-align: top;
        }
        .info-table td.label { width: 90px; }
        .info-table td.sep   { width: 8px; }

        /* ══════════ TABEL PRESENSI HARIAN ══════════ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1px;
            font-size: 7.2pt;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 1.5px 2.5px;
            vertical-align: middle;
            text-align: center;
            color: #000;
        }
        table.data-table th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: bold;
            font-size: 7.2pt;
            text-transform: uppercase;
            padding: 2px 2px;
        }
        table.data-table td.left { text-align: left; }
        table.data-table tr.even td { background-color: #fafbfc; }
        table.data-table tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
            padding: 1.5px 3px;
            color: #000;
            font-size: 7.2pt;
        }

        /* STATUS STYLING */
        .status-text {
            color: #000;
            font-size: 7.2pt;
        }
        .status-bold {
            color: #000;
            font-size: 7.2pt;
            font-weight: bold;
        }

        /* TTD CELL IN TABLE */
        .ttd-col {
            height: 24px;
            min-height: 24px;
            max-height: 28px;
            vertical-align: middle !important;
            padding: 1px 3px !important;
            overflow: hidden;
        }
        .ttd-box-left {
            text-align: left;
            width: 100%;
            display: block;
            white-space: nowrap;
        }
        .ttd-box-right {
            text-align: left;
            width: 100%;
            padding-left: 10px;
            display: block;
            white-space: nowrap;
        }
        .ttd-num {
            font-size: 7pt;
            font-weight: bold;
            margin-right: 3px;
            display: inline-block;
            vertical-align: middle;
        }
        .ttd-dots {
            font-size: 7pt;
            color: #555;
            letter-spacing: 0.5px;
            display: inline-block;
            vertical-align: middle;
        }
        .ttd-img {
            height: 22px;
            max-height: 22px;
            width: auto;
            max-width: 65px;
            vertical-align: middle;
            display: inline-block;
            margin: 0 0 0 2px;
        }

        /* TANDA TANGAN PEJABAT PENGESAH */
        .ttd-table {
            width: 100%;
            margin-top: 6px;
            page-break-inside: avoid;
        }
        .ttd-cell {
            width: 50%;
            text-align: center;
            font-size: 8pt;
            vertical-align: top;
            line-height: 1.15;
            color: #000;
        }
        .ttd-space { 
            height: 44px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            font-size: 9pt;
            color: #000;
        }
        .ttd-nipd {
            font-size: 7.5pt;
            margin: 1px 0 0;
            color: #000;
        }

        .footer-note {
            margin-top: 3px;
            font-size: 6pt;
            color: #444;
            font-style: italic;
        }

        .no-break { page-break-inside: avoid; }
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

    <!-- ══════════ JUDUL DOKUMEN ══════════ -->
    <div class="doc-title">
        <h2>DAFTAR HADIR & REKAPITULASI PRESENSI HARIAN</h2>
    </div>

    <!-- ══════════ INFORMASI ══════════ -->
    <table class="info-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="label">Hari / Tanggal</td>
            <td class="sep">:</td>
            <td><strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong></td>
            <td style="text-align: right;">Unit Kerja: <strong>Pemerintah Desa Nangtang</strong></td>
        </tr>
    </table>

    <!-- ══════════ TABEL DATA PRESENSI BESERTA TTD ══════════ -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:20px;">No</th>
                <th style="width:125px;">Nama Perangkat Desa / NIPD</th>
                <th style="width:95px;">Jabatan</th>
                <th style="width:38px;">Masuk</th>
                <th style="width:38px;">Pulang</th>
                <th style="width:36px;">Durasi</th>
                <th style="width:50px;">Status</th>
                <th style="width:145px;">Tanda Tangan / Bukti</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($pegawais as $p)
                @php
                    $k = $p->resolved_kehadiran;
                    if ($k) {
                        $statusVal = $k->status_disesuaikan ?? $k->status;
                        $statusLabel = match($statusVal) {
                            'Tepat Waktu' => 'Hadir',
                            default       => $statusVal,
                        };
                        $statusClass = match($statusVal) {
                            'Tepat Waktu', 'Hadir' => 'status-bold',
                            'Terlambat'            => 'status-text',
                            'Izin'                 => 'status-text',
                            'Sakit'                => 'status-text',
                            'Dinas Luar'           => 'status-bold',
                            default                => 'status-bold',
                        };
                    } elseif ($isWeekend || $hariLiburs) {
                        $statusLabel = 'Libur';
                        $statusClass = 'status-text';
                    } else {
                        $statusLabel = 'Alpa';
                        $statusClass = 'status-bold';
                    }
                    $durasi = ($k && $k->durasi_kerja_menit)
                        ? floor($k->durasi_kerja_menit/60).'j '.($k->durasi_kerja_menit % 60).'m'
                        : '-';
                    $rowClass = ($no % 2 === 0) ? 'even' : '';
                    $currentNo = $no;
                    $ttdPdf = $k ? ($k->pdf_tanda_tangan ?? $k->pdf_tanda_tangan_masuk ?? $k->pdf_tanda_tangan_pulang) : null;
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $no++ }}</td>
                    <td class="left">
                        <strong>{{ $p->nama_lengkap }}</strong>
                        @if($p->nipd)
                            <br><span style="font-size:7pt; color:#333;">NIPD. {{ $p->nipd }}</span>
                        @endif
                    </td>
                    <td class="left">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    <td>{{ $k?->jam_masuk ? substr($k->jam_masuk, 0, 5) : '-' }}</td>
                    <td>{{ $k?->jam_pulang ? substr($k->jam_pulang, 0, 5) : '-' }}</td>
                    <td>{{ $durasi }}</td>
                    <td><span class="{{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td class="ttd-col">
                        @if($currentNo % 2 === 1)
                            {{-- Format Ganjil (Kiri) --}}
                            <div class="ttd-box-left">
                                <span class="ttd-num">{{ $currentNo }}.</span>
                                @if($ttdPdf)
                                    <img src="{{ $ttdPdf }}" class="ttd-img" alt="TTD">
                                @elseif($k && in_array($k->status_disesuaikan ?? $k->status, ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar']))
                                    <span style="font-size:7.5pt; font-style:italic; font-weight:bold;">[Sah Hadir]</span>
                                @else
                                    <span class="ttd-dots">....................</span>
                                @endif
                            </div>
                        @else
                            {{-- Format Genap (Kanan/Tengah) --}}
                            <div class="ttd-box-right">
                                <span class="ttd-num">{{ $currentNo }}.</span>
                                @if($ttdPdf)
                                    <img src="{{ $ttdPdf }}" class="ttd-img" alt="TTD">
                                @elseif($k && in_array($k->status_disesuaikan ?? $k->status, ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar']))
                                    <span style="font-size:7.5pt; font-style:italic; font-weight:bold;">[Sah Hadir]</span>
                                @else
                                    <span class="ttd-dots">....................</span>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right; font-weight:bold;">TOTAL KEHADIRAN HARI INI :</td>
                <td colspan="2" style="font-weight:bold; text-align:center; color:#000;">
                    {{ $rekap['hadir'] + $rekap['terlambat'] + $rekap['dinas'] }} / {{ $pegawais->count() }} Pegawai
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- ══════════ TANDA TANGAN PEJABAT PENGESAH ══════════ -->
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
                    <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                    <p class="ttd-nipd">NIPD: {{ $sekdes->nipd ?? '141.1/KEP.01/DES/2020' }}</p>
                </td>
            </tr>
        </table>

        <p class="footer-note">
            * Dokumen ini sah dan dicetak secara otomatis melalui Sistem Informasi Presensi Digital Desa Nangtang pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.
        </p>
    </div>

</body>
</html>
