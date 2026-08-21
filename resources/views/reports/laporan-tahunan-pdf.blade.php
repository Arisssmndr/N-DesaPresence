<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Tahunan — {{ $tahun }}</title>
    <style>
        @page { size: A4 landscape; margin: 8mm 8mm 14mm 10mm; }
        * { box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 8pt; color: #111; line-height: 1.3; margin: 0; }

        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 8px; }
        .kop-logo-cell { width: 65px; text-align: center; vertical-align: middle; }
        .kop-logo-circle {
            width: 54px; height: 54px;
            border: 2px solid #000; border-radius: 50%;
            text-align: center; vertical-align: middle;
            font-size: 18pt; font-weight: bold; color: #000;
            line-height: 54px; display: inline-block;
        }
        .kop-text-cell { text-align: center; vertical-align: middle; }
        .kop-prov  { font-size: 7.5pt; margin: 0; }
        .kop-desa  { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; letter-spacing: 0.8px; }
        .kop-alamat { font-size: 7pt; color: #555; font-style: italic; }

        /* JUDUL */
        .doc-title { text-align: center; margin: 7px 0 2px; }
        .doc-title h2 { font-size: 10pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { text-align: center; font-size: 8pt; margin: 0 0 7px; }

        /* TABEL */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 5.8pt; table-layout: fixed; }
        table.data-table th,
        table.data-table td { border: 1px solid #333; padding: 1.5px 1px; text-align: center; vertical-align: middle; }
        table.data-table th { background-color: #f2f2f2; color: #000; font-weight: bold; }
        table.data-table th.sub { background-color: #e5e7eb; color: #000; font-size: 5.5pt; }
        table.data-table th.dark { background-color: #d1d5db; color: #000; }
        table.data-table td.nama { text-align: left; padding-left: 3px; font-size: 5.8pt; overflow: hidden; }
        table.data-table tfoot td { background-color: #f5f5f5; font-weight: bold; }
        table.data-table tr.even td { background-color: #fafafa; }

        /* WARNA KOLOM */
        .col-hadir  { background: #bbf7d0; color: #14532d; }
        .col-alpa   { background: #fecaca; color: #7f1d1d; }
        .col-persen { background: #f0fdf4; color: #14532d; font-weight: bold; }
        .col-total-hadir  { background: #d1fae5; color: #065f46; font-weight: bold; }
        .col-total-alpa   { background: #fee2e2; color: #991b1b; font-weight: bold; }
        .col-total-persen { background: #ecfdf5; color: #064E3B; font-weight: bold; }

        /* GRADE */
        .grade-A { color: #059669; font-weight: bold; }
        .grade-B { color: #2563eb; font-weight: bold; }
        .grade-C { color: #d97706; font-weight: bold; }
        .grade-D { color: #dc2626; font-weight: bold; }

        /* TTD */
        .ttd-table { width: 100%; margin-top: 14px; }
        .ttd-cell { width: 50%; text-align: center; font-size: 8.5pt; vertical-align: top; }
        .ttd-space { height: 48px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }

        .footer-note { margin-top: 6px; font-size: 7pt; color: #666; font-style: italic; }
        .legend-table { border-collapse: collapse; margin-top: 7px; font-size: 7pt; }
        .legend-table td { padding: 2px 8px; border: 1px solid #ddd; }
        .no-break { page-break-inside: avoid; }
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
                    <td style="text-align:left; padding-left:2px; font-size:5.5pt;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
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
                    <td style="background:#d1fae5; color:#14532d; font-weight:bold;">{{ collect($dataRekap)->sum(fn($r) => $r['per_bulan'][$m]['hadir'] ?? 0) }}</td>
                    <td style="background:#fee2e2; color:#991b1b; font-weight:bold;">{{ collect($dataRekap)->sum(fn($r) => $r['per_bulan'][$m]['alpa'] ?? 0) }}</td>
                    <td style="background:#f0fdf4; font-weight:bold;">
                        @php
                            $vals = collect($dataRekap)->map(fn($r) => $r['per_bulan'][$m]['persen'] ?? 0)->filter(fn($v) => $v > 0);
                            echo $vals->count() ? round($vals->avg(), 0).'%' : '-';
                        @endphp
                    </td>
                @endforeach
                <td style="background:#d1fae5; color:#14532d; font-weight:bold;">{{ collect($dataRekap)->sum('total_hadir') }}</td>
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
                <p class="ttd-name">{{ $kades->nama_lengkap ?? 'H. AHMAD SUPRIYADI, S.IP' }}</p>
                <p style="font-size:7.5pt;">NIPD: {{ $kades->nipd ?? '-' }}</p>
            </td>
            <td class="ttd-cell">
                <p style="margin:0 0 2px;">Nangtang, 31 Desember {{ $tahun }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'HJ. NURLAILA RAHMAWATI, S.AP' }}</p>
                <p style="font-size:7.5pt;">NIPD: {{ $sekdes->nipd ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
