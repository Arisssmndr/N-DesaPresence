<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Tahunan — {{ $tahun }}</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 6mm 8mm 5mm 8mm; 
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Arial', Helvetica, sans-serif; 
            font-size: 7.5pt; 
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
            width: 60px; 
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
        .doc-title { text-align: center; margin: 0 0 1px; }
        .doc-title h2 { 
            font-size: 11pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-decoration: underline; 
            margin: 0; 
            letter-spacing: 0.5px;
        }
        .doc-nomor { text-align: center; font-size: 8.5pt; margin: 2px 0 4px; }

        /* ══════════ TABEL ══════════ */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 5.8pt; table-layout: fixed; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 1.5px 1px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f1f5f9; color: #000; font-weight: bold; }
        table.data-table th.sub { background-color: #e2e8f0; color: #000; font-size: 5.5pt; }
        table.data-table th.dark { background-color: #cbd5e1; color: #000; }
        table.data-table td.nama { text-align: left; padding-left: 3px; font-size: 5.8pt; overflow: hidden; white-space: nowrap; }
        table.data-table tfoot td { background-color: #f5f5f5; font-weight: bold; }
        table.data-table tr.even td { background-color: #fafbfc; }

        /* WARNA KOLOM */
        .col-hadir  { background: #dcfce7; color: #166534; }
        .col-alpa   { background: #fee2e2; color: #991b1b; }
        .col-persen { background: #f0fdf4; color: #166534; font-weight: bold; }
        .col-total-hadir  { background: #dcfce7; color: #166534; font-weight: bold; }
        .col-total-alpa   { background: #fee2e2; color: #991b1b; font-weight: bold; }
        .col-total-persen { background: #ecfdf5; color: #064E3B; font-weight: bold; }

        /* GRADE */
        .grade-A { color: #059669; font-weight: bold; }
        .grade-B { color: #2563eb; font-weight: bold; }
        .grade-C { color: #d97706; font-weight: bold; }
        .grade-D { color: #dc2626; font-weight: bold; }

        /* TTD TABLE (4X ENTER LEGA) */
        .ttd-table { width: 100%; margin-top: 6px; page-break-inside: avoid; }
        .ttd-cell { width: 50%; text-align: center; font-size: 8.5pt; vertical-align: top; line-height: 1.25; }
        .ttd-space { height: 48px; /* Ruang 4x Enter lega */ }
        .ttd-name { font-weight: bold; text-decoration: underline; font-size: 9.5pt; }
        .ttd-nipd { font-size: 8pt; color: #111; }

        .footer-note { margin-top: 3px; font-size: 6.5pt; color: #555; font-style: italic; }
        .legend-table { border-collapse: collapse; margin-top: 4px; font-size: 6.8pt; }
        .legend-table td { padding: 1.5px 6px; border: 1px solid #ddd; }
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

    <!-- JUDUL -->
    <div class="doc-title">
        <h2>LAPORAN REKAPITULASI PRESENSI TAHUNAN PERANGKAT DESA NANGTANG</h2>
    </div>
    <p class="doc-nomor">Tahun Anggaran: <strong>{{ $tahun }}</strong></p>

    <!-- TABEL TAHUNAN -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="3" style="width:15px;">No</th>
                <th rowspan="3" style="width:80px;">Nama Perangkat Desa</th>
                <th rowspan="3" style="width:52px;">Jabatan</th>
                @foreach ($namaBulanArr as $m => $nm)
                    <th colspan="3" style="width:34px;">{{ $nm }}</th>
                @endforeach
                <th colspan="3" class="dark" style="width:38px;">TOTAL</th>
                <th rowspan="3" class="dark" style="width:20px;">GRD</th>
            </tr>
            <tr>
                @foreach ($namaBulanArr as $m => $nm)
                    <th class="sub" style="width:11px;">H</th>
                    <th class="sub" style="width:11px;">A</th>
                    <th class="sub" style="width:12px;">%</th>
                @endforeach
                <th class="sub dark" style="width:13px;">H</th>
                <th class="sub dark" style="width:13px;">A</th>
                <th class="sub dark" style="width:12px;">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                @php
                    $d    = $dataRekap[$p->id];
                    $pct  = $d['persen_tahunan'];
                    $grade = $pct >= 95 ? 'A' : ($pct >= 85 ? 'B' : ($pct >= 75 ? 'C' : 'D'));
                    $rowClass = $idx % 2 !== 0 ? 'even' : '';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $idx + 1 }}</td>
                    <td class="nama"><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td style="text-align:left; padding-left:2px; font-size:5.5pt; white-space:nowrap;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    @foreach ($namaBulanArr as $m => $nm)
                        @php $mb = $d['per_bulan'][$m] ?? []; @endphp
                        <td class="col-hadir">{{ $mb['hadir'] ?? 0 }}</td>
                        <td class="col-alpa">{{ $mb['alpa'] ?? 0 }}</td>
                        <td class="col-persen">{{ $mb['persen'] ?? 0 }}%</td>
                    @endforeach
                    <td class="col-total-hadir">{{ $d['total_hadir'] }}</td>
                    <td class="col-total-alpa">{{ $d['total_alpa'] }}</td>
                    <td class="col-total-persen">{{ $pct }}%</td>
                    <td class="grade-{{ $grade }}">{{ $grade }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold;">TOTAL / RATA-RATA</td>
                @foreach ($namaBulanArr as $m => $nm)
                    <td style="background:#dcfce7; color:#166534; font-weight:bold;">{{ collect($dataRekap)->sum(fn($r) => $r['per_bulan'][$m]['hadir'] ?? 0) }}</td>
                    <td style="background:#fee2e2; color:#991b1b; font-weight:bold;">{{ collect($dataRekap)->sum(fn($r) => $r['per_bulan'][$m]['alpa'] ?? 0) }}</td>
                    <td style="background:#f0fdf4; font-weight:bold;">
                        @php
                            $vals = collect($dataRekap)->map(fn($r) => $r['per_bulan'][$m]['persen'] ?? 0)->filter(fn($v) => $v > 0);
                            echo $vals->count() ? round($vals->avg(), 0).'%' : '-';
                        @endphp
                    </td>
                @endforeach
                <td style="background:#dcfce7; color:#166534; font-weight:bold;">{{ collect($dataRekap)->sum('total_hadir') }}</td>
                <td style="background:#fee2e2; color:#991b1b; font-weight:bold;">{{ collect($dataRekap)->sum('total_alpa') }}</td>
                <td style="background:#ecfdf5; font-weight:bold;">
                    @php
                        $avgAll = collect($dataRekap)->map(fn($r) => $r['persen_tahunan'])->filter(fn($v) => $v > 0);
                        echo $avgAll->count() ? round($avgAll->avg(), 1).'%' : '-';
                    @endphp
                </td>
                <td>—</td>
            </tr>
        </tfoot>
    </table>

    <!-- KETERANGAN GRADE -->
    <table class="legend-table" cellspacing="0">
        <tr>
            <td><strong>Keterangan Grade:</strong></td>
            <td class="grade-A">A = Sangat Baik (&#x2265;95%)</td>
            <td class="grade-B">B = Baik (85–94%)</td>
            <td class="grade-C">C = Cukup (75–84%)</td>
            <td class="grade-D">D = Kurang (&lt;75%)</td>
            <td style="color:#555;">H = Hadir &nbsp;|&nbsp; A = Alpa &nbsp;|&nbsp; % = Persentase Kehadiran</td>
        </tr>
    </table>

    <p class="footer-note">
        * Laporan ini dicetak otomatis dari Sistem Presensi Digital Desa Nangtang — {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </p>

    <!-- TTD -->
    <table class="ttd-table no-break" cellspacing="0" cellpadding="0">
        <tr>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Mengetahui / Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $kades->nama_lengkap ?? 'DADAY DAHYAT' }}</p>
                <p class="ttd-nipd">NIPD: {{ $kades->nipd ?? '141.1/Kep.053-Pemdes/2019' }}</p>
            </td>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Nangtang, 31 Desember {{ $tahun }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                <p class="ttd-nipd">NIPD: {{ $sekdes->nipd ?? '141.1/KEP.01/DES/2020' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
