<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Harian — {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 15mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            color: #000;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        /* ══════════ KOP SURAT STANDAR TATA NASKAH DINAS RI ══════════ */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .kop-logo-cell {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-logo-img {
            height: 68px;
            width: auto;
            max-width: 65px;
            display: inline-block;
            vertical-align: middle;
        }
        .kop-logo-circle {
            width: 54px;
            height: 54px;
            border: 2px solid #000;
            border-radius: 50%;
            text-align: center;
            vertical-align: middle;
            font-size: 20pt;
            font-weight: bold;
            color: #000;
            line-height: 54px;
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
            line-height: 1.2;
            letter-spacing: 0.3px;
        }
        .kop-kec {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0;
            line-height: 1.2;
            letter-spacing: 0.3px;
        }
        .kop-desa {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
            letter-spacing: 0.8px;
            line-height: 1.2;
        }
        .kop-alamat {
            font-size: 8pt;
            color: #111;
            font-style: italic;
            margin: 2px 0 0;
            line-height: 1.2;
        }

        /* JUDUL LAPORAN */
        .doc-title { text-align: center; margin: 4px 0 1px; }
        .doc-title h2 {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .doc-nomor {
            text-align: center;
            font-size: 8.5pt;
            margin: 1px 0 4px;
        }

        /* INFO TABLE */
        .info-table {
            width: 100%;
            margin-bottom: 4px;
            font-size: 8pt;
        }
        .info-table td {
            padding: 0.5px 0;
            vertical-align: top;
        }
        .info-table td.label { width: 110px; }
        .info-table td.sep   { width: 10px; }

        /* TABEL PRESENSI HARIAN */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            font-size: 8pt;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: middle;
            text-align: center;
            color: #000;
        }
        table.data-table th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            padding: 3.5px 2px;
        }
        table.data-table td.left { text-align: left; }
        table.data-table tr.even td { background-color: #fafafa; }
        table.data-table tfoot td {
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 3px 4px;
        }

        /* STATUS STYLING */
        .status-badge {
            font-weight: bold;
            font-size: 7.5pt;
            padding: 1px 2px;
            display: inline-block;
        }
        .s-hadir     { color: #000; }
        .s-terlambat { color: #000; }
        .s-alpa      { color: #000; font-weight: bold; }
        .s-izin      { color: #000; }
        .s-sakit     { color: #000; }
        .s-dinas     { color: #000; }
        .s-libur     { color: #555; }

        /* TTD CELL IN TABLE (LARGE & CLEAR) */
        .ttd-col {
            height: 38px;
            min-height: 38px;
            vertical-align: middle !important;
            padding: 2px 4px !important;
        }
        .ttd-box-left {
            text-align: left;
            width: 100%;
            line-height: 34px;
        }
        .ttd-box-right {
            text-align: left;
            width: 100%;
            padding-left: 28px;
            line-height: 34px;
        }
        .ttd-num {
            font-size: 8pt;
            font-weight: bold;
            margin-right: 3px;
            display: inline-block;
            vertical-align: middle;
        }
        .ttd-dots {
            font-size: 8pt;
            color: #888;
            letter-spacing: 1px;
            display: inline-block;
            vertical-align: middle;
        }
        .ttd-img {
            max-height: 34px;
            max-width: 90px;
            vertical-align: middle;
            display: inline-block;
        }

        /* RINGKASAN REKAP */
        .rekap-container {
            margin-top: 4px;
            font-size: 7.5pt;
        }
        .rekap-table {
            border-collapse: collapse;
            margin-top: 1px;
            font-size: 7.5pt;
            width: 100%;
        }
        .rekap-table td {
            border: 1px solid #333;
            padding: 2.5px 4px;
            text-align: center;
            background-color: #fcfcfc;
        }
        .rekap-val {
            font-size: 8.5pt;
            font-weight: bold;
            display: block;
        }
        .rekap-label {
            font-size: 6.5pt;
            color: #333;
            display: block;
            margin-top: 1px;
        }

        /* TANDA TANGAN PEJABAT PENGESAH */
        .ttd-table {
            width: 100%;
            margin-top: 8px;
        }
        .ttd-cell {
            width: 50%;
            text-align: center;
            font-size: 8pt;
            vertical-align: top;
            line-height: 1.2;
        }
        .ttd-space { height: 32px; }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            font-size: 8.5pt;
        }
        .ttd-nipd {
            font-size: 7.5pt;
            margin: 1px 0 0;
        }

        .footer-note {
            margin-top: 3px;
            font-size: 6.5pt;
            color: #555;
            font-style: italic;
        }

        /* PAGE BREAK UTILITY */
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
                <p class="kop-kab">PEMERINTAH KABUPATEN TASIKMALAYA</p>
                <p class="kop-kec">KECAMATAN CIGALONTANG</p>
                <p class="kop-desa">KANTOR KEPALA DESA NANGTANG</p>
                <p class="kop-alamat">Jalan Raya Desa Nangtang, Kode Pos 46463 — Pos-el: pemdes@desanangtang.go.id</p>
            </td>
            <td class="kop-logo-cell">&nbsp;</td>
        </tr>
    </table>

    <!-- ══════════ JUDUL ══════════ -->
    <div class="doc-title">
        <h2>DAFTAR HADIR & REKAPITULASI PRESENSI HARIAN</h2>
    </div>
    <div class="doc-nomor">
        Nomor: {{ $nomorLaporan }}
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
                <th style="width:22px;">No</th>
                <th style="width:125px;">Nama Perangkat Desa / NIPD</th>
                <th style="width:95px;">Jabatan</th>
                <th style="width:42px;">Masuk</th>
                <th style="width:42px;">Pulang</th>
                <th style="width:40px;">Durasi</th>
                <th style="width:50px;">Status</th>
                <th style="width:125px;">Tanda Tangan / Bukti</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($pegawais as $p)
                @php
                    $k = $p->kehadirans->first();
                    if ($isWeekend || $hariLiburs) {
                        $statusLabel = 'Libur';
                        $statusClass = 's-libur';
                    } elseif (!$k) {
                        $statusLabel = 'Alpa';
                        $statusClass = 's-alpa';
                    } else {
                        $statusLabel = match($k->status) {
                            'Tepat Waktu' => 'Hadir',
                            default       => $k->status,
                        };
                        $statusClass = match($k->status) {
                            'Tepat Waktu', 'Hadir' => 's-hadir',
                            'Terlambat'            => 's-terlambat',
                            'Izin'                 => 's-izin',
                            'Sakit'                => 's-sakit',
                            'Dinas Luar'           => 's-dinas',
                            default                => 's-alpa',
                        };
                    }
                    $durasi = ($k && $k->durasi_kerja_menit)
                        ? floor($k->durasi_kerja_menit/60).'j '.($k->durasi_kerja_menit % 60).'m'
                        : '-';
                    $rowClass = ($no % 2 === 0) ? 'even' : '';
                    $currentNo = $no;
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $no++ }}</td>
                    <td class="left">
                        <strong>{{ $p->nama_lengkap }}</strong>
                        @if($p->nipd)
                            <br><span style="font-size:6.8pt; color:#333;">NIPD. {{ $p->nipd }}</span>
                        @endif
                    </td>
                    <td class="left">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    <td>{{ $k?->jam_masuk ? substr($k->jam_masuk, 0, 5) : '-' }}</td>
                    <td>{{ $k?->jam_pulang ? substr($k->jam_pulang, 0, 5) : '-' }}</td>
                    <td>{{ $durasi }}</td>
                    <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td class="ttd-col">
                        @if($currentNo % 2 === 1)
                            {{-- Format Ganjil (Kiri) --}}
                            <div class="ttd-box-left">
                                <span class="ttd-num">{{ $currentNo }}.</span>
                                @if($k && ($k->pdf_tanda_tangan_masuk || $k->pdf_tanda_tangan_pulang))
                                    <img src="{{ $k->pdf_tanda_tangan_masuk ?? $k->pdf_tanda_tangan_pulang }}" class="ttd-img" alt="TTD">
                                @elseif($k && in_array($k->status, ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar']))
                                    <span style="font-size:7.5pt; font-style:italic; font-weight:bold;">[Sah Hadir]</span>
                                @else
                                    <span class="ttd-dots">....................</span>
                                @endif
                            </div>
                        @else
                            {{-- Format Genap (Kanan/Tengah) --}}
                            <div class="ttd-box-right">
                                <span class="ttd-num">{{ $currentNo }}.</span>
                                @if($k && ($k->pdf_tanda_tangan_masuk || $k->pdf_tanda_tangan_pulang))
                                    <img src="{{ $k->pdf_tanda_tangan_masuk ?? $k->pdf_tanda_tangan_pulang }}" class="ttd-img" alt="TTD">
                                @elseif($k && in_array($k->status, ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar']))
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
                <td colspan="2" style="font-weight:bold; text-align:center;">
                    {{ $rekap['hadir'] + $rekap['terlambat'] + $rekap['dinas'] }} / {{ $pegawais->count() }} Pegawai
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- ══════════ RINGKASAN REKAPITULASI ══════════ -->
    <div class="no-break rekap-container">
        <table class="rekap-table" cellspacing="0" cellpadding="0">
            <tr>
                <td style="font-weight:bold; width:14%;">
                    <span class="rekap-val">{{ $rekap['hadir'] }}</span>
                    <span class="rekap-label">Hadir Tepat</span>
                </td>
                <td style="width:14%;">
                    <span class="rekap-val">{{ $rekap['terlambat'] }}</span>
                    <span class="rekap-label">Terlambat</span>
                </td>
                <td style="width:14%;">
                    <span class="rekap-val">{{ $rekap['dinas'] }}</span>
                    <span class="rekap-label">Dinas Luar</span>
                </td>
                <td style="width:14%;">
                    <span class="rekap-val">{{ $rekap['izin'] }}</span>
                    <span class="rekap-label">Izin</span>
                </td>
                <td style="width:14%;">
                    <span class="rekap-val">{{ $rekap['sakit'] }}</span>
                    <span class="rekap-label">Sakit</span>
                </td>
                <td style="width:14%;">
                    <span class="rekap-val">{{ $rekap['alpa'] }}</span>
                    <span class="rekap-label">Tanpa Ket. (Alpa)</span>
                </td>
                @if($rekap['libur'] > 0)
                <td style="width:16%;">
                    <span class="rekap-val">{{ $rekap['libur'] }}</span>
                    <span class="rekap-label">Libur</span>
                </td>
                @endif
            </tr>
        </table>
    </div>

    <!-- ══════════ TANDA TANGAN PEJABAT PENGESAH ══════════ -->
    <div class="no-break">
        <table class="ttd-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="ttd-cell">
                    <p style="margin:0 0 2px;">Mengetahui / Mengesahkan,<br><strong>KEPALA DESA NANGTANG</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $kades->nama_lengkap ?? 'H. AHMAD SUPRIYADI, S.IP' }}</p>
                    <p class="ttd-nipd">NIPD. {{ $kades->nipd ?? '-' }}</p>
                </td>
                <td class="ttd-cell">
                    <p style="margin:0 0 2px;">Nangtang, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA / OPERATOR</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'HJ. NURLAILA RAHMAWATI, S.AP' }}</p>
                    <p class="ttd-nipd">NIPD. {{ $sekdes->nipd ?? '-' }}</p>
                </td>
            </tr>
        </table>

        <p class="footer-note">
            * Dokumen ini sah dan dicetak secara otomatis melalui Sistem Informasi Presensi Digital Desa Nangtang (SADI v2.0) pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.
        </p>
    </div>

</body>
</html>
