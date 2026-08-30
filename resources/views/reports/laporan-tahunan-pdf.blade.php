<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Presensi Tahunan — {{ $tahun }}</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 6mm 8mm 5mm 8mm; 
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Arial', Helvetica, sans-serif; 
            font-size: 6.8pt; 
            color: #111827; 
            line-height: 1.15; 
            margin: 0; 
            padding: 0;
        }

        /* ══════════ KOP SURAT STANDAR TATA NASKAH DINAS PEMKAB TASIKMALAYA ══════════ */
        .kop-table { 
            width: 100%; 
            border-bottom: 2.5px double #000000; 
            padding-bottom: 3px; 
            margin-bottom: 5px;
        }
        .kop-logo-cell { 
            width: 55px; 
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
            width: 40px; height: 40px; 
            border: 1.5px solid #000; border-radius: 50%; 
            text-align: center; vertical-align: middle; 
            font-size: 13pt; font-weight: bold; color: #000; 
            line-height: 40px; display: inline-block; 
        }
        .kop-text-cell { 
            text-align: center; 
            vertical-align: middle; 
            padding: 0 4px; 
        }
        .kop-kab { 
            font-size: 10.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 0; 
            line-height: 1.15;
            letter-spacing: 0.5px;
        }
        .kop-kec { 
            font-size: 9.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 0; 
            line-height: 1.15; 
        }
        .kop-desa { 
            font-size: 11.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 1px 0 0; 
            letter-spacing: 0.8px; 
            line-height: 1.15; 
        }
        .kop-alamat { 
            font-size: 7pt; 
            color: #374151; 
            font-style: italic; 
            margin: 1.5px 0 0; 
        }

        /* ══════════ JUDUL DOKUMEN (TANPA NOMOR SURAT) ══════════ */
        .doc-title { text-align: center; margin: 3px 0 4px; }
        .doc-title h2 { 
            font-size: 9.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-decoration: underline; 
            margin: 0; 
            letter-spacing: 0.4px; 
        }
        .doc-periode { 
            text-align: center; 
            font-size: 7.5pt; 
            font-weight: bold; 
            color: #1f2937; 
            margin: 1.5px 0 4px; 
            letter-spacing: 0.3px;
        }

        /* ══════════ TABEL REKAPITULASI ══════════ */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 6.2pt; 
            margin-bottom: 4px;
        }
        table.data-table th,
        table.data-table td { 
            border: 0.75pt solid #374151; 
            padding: 2px 1.5px; 
            text-align: center; 
            vertical-align: middle; 
        }
        table.data-table th { 
            background-color: #f1f5f9; 
            color: #0f172a; 
            font-weight: bold; 
            font-size: 6pt;
            line-height: 1.1;
        }
        table.data-table th.th-header-group { 
            background-color: #e2e8f0; 
            font-size: 6.2pt; 
            padding: 2.5px 1px;
            letter-spacing: 0.2px;
        }
        table.data-table th.th-akumulasi { 
            background-color: #cbd5e1; 
            font-size: 6.2pt; 
            padding: 2.5px 1px;
            letter-spacing: 0.2px;
        }
        table.data-table td.col-nama { 
            text-align: left; 
            padding-left: 3px; 
            font-size: 6.2pt;
            line-height: 1.15;
        }
        table.data-table td.col-jabatan { 
            text-align: left; 
            padding-left: 3px; 
            font-size: 5.8pt;
            line-height: 1.1;
        }
        table.data-table td.col-bulan {
            font-size: 6.2pt;
        }
        table.data-table td.col-total { 
            font-weight: bold; 
            background-color: #f8fafc; 
        }
        table.data-table td.col-persen { 
            font-weight: bold; 
            background-color: #ecfdf5; 
            color: #065f46;
        }
        table.data-table tr.even td { 
            background-color: #fbfcfd; 
        }
        table.data-table tfoot td { 
            background-color: #f1f5f9; 
            font-weight: bold; 
            font-size: 6pt;
            padding: 2px 1.5px;
        }

        /* ══════════ PREDIKAT BADGE ══════════ */
        .badge-predikat {
            font-weight: bold;
            font-size: 5.5pt;
            display: inline-block;
            padding: 0.5px 1px;
        }
        .predikat-sangat-baik { color: #047857; }
        .predikat-baik        { color: #1d4ed8; }
        .predikat-cukup       { color: #b45309; }
        .predikat-kurang      { color: #b91c1c; }

        /* ══════════ KETERANGAN & TANDA TANGAN ══════════ */
        .info-bottom {
            width: 100%;
            margin-top: 2px;
            font-size: 5.8pt;
            color: #4b5563;
        }
        .ttd-table { 
            width: 100%; 
            margin-top: 4px; 
            page-break-inside: avoid; 
        }
        .ttd-cell  { 
            width: 50%; 
            text-align: center; 
            font-size: 7.2pt; 
            vertical-align: top; 
            line-height: 1.2; 
        }
        .ttd-space { 
            height: 36px; 
        }
        .ttd-name  { 
            font-weight: bold; 
            text-decoration: underline; 
            margin: 0; 
            font-size: 7.8pt; 
        }
        .ttd-nipd  { 
            font-size: 6.8pt; 
            margin: 1px 0 0; 
            color: #111827;
        }
        .footer-note { 
            margin-top: 2px; 
            font-size: 5.5pt; 
            color: #6b7280; 
            font-style: italic; 
        }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        $logoPath   = public_path('images/logo-tasikmalaya.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- ══════════ KOP SURAT STANDAR PEMKAB TASIKMALAYA ══════════ -->
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

    <!-- ══════════ JUDUL DOKUMEN (TANPA NOMOR SURAT) ══════════ -->
    <div class="doc-title">
        <h2>LAPORAN REKAPITULASI PRESENSI TAHUNAN PERANGKAT DESA NANGTANG</h2>
    </div>
    <div class="doc-periode">TAHUN ANGGARAN {{ $tahun }}</div>

    <!-- ══════════ TABEL REKAPITULASI TAHUNAN ══════════ -->
    <table class="data-table" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2.6%;">NO</th>
                <th rowspan="2" style="width: 17%; text-align: left; padding-left: 4px;">NAMA PERANGKAT DESA / NIPD</th>
                <th rowspan="2" style="width: 13.4%; text-align: left; padding-left: 4px;">JABATAN</th>
                <th colspan="12" class="th-header-group">JUMLAH HARI HADIR PER BULAN</th>
                <th colspan="5" class="th-akumulasi">TOTAL AKUMULASI TAHUNAN</th>
                <th rowspan="2" style="width: 5.5%;" class="th-akumulasi">PREDIKAT</th>
            </tr>
            <tr>
                @foreach ($namaBulanArr as $m => $nm)
                    <th style="width: 3.2%;" title="Bulan {{ $nm }}">{{ $nm }}</th>
                @endforeach
                <th style="width: 3.6%;" title="Total Hari Kerja Efektif">HK</th>
                <th style="width: 3.6%;" title="Total Hadir (H)">H</th>
                <th style="width: 3.2%;" title="Total Izin & Sakit (I/S)">I/S</th>
                <th style="width: 3.2%;" title="Total Alpa / Tanpa Keterangan (A)">A</th>
                <th style="width: 4.5%;" title="Persentase Kehadiran Tahunan">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                @php
                    $d = $dataRekap[$p->id] ?? [
                        'per_bulan'      => [],
                        'total_hadir'    => 0,
                        'total_alpa'     => 0,
                        'total_izin'     => 0,
                        'total_sakit'    => 0,
                        'total_hk'       => 0,
                        'persen_tahunan' => 0,
                    ];
                    $pct = $d['persen_tahunan'];
                    $predikat = $pct >= 95 ? 'Sangat Baik' : ($pct >= 85 ? 'Baik' : ($pct >= 75 ? 'Cukup' : 'Kurang'));
                    $predikatClass = $pct >= 95 ? 'predikat-sangat-baik' : ($pct >= 85 ? 'predikat-baik' : ($pct >= 75 ? 'predikat-cukup' : 'predikat-kurang'));
                    $rowClass = $idx % 2 !== 0 ? 'even' : '';
                    $totalIzinSakit = ($d['total_izin'] ?? 0) + ($d['total_sakit'] ?? 0);
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $idx + 1 }}</td>
                    <td class="col-nama">
                        <strong>{{ $p->nama_lengkap }}</strong>
                        @if($p->nipd)
                            <br><span style="font-size: 5.2pt; color: #4b5563;">NIPD. {{ $p->nipd }}</span>
                        @endif
                    </td>
                    <td class="col-jabatan">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>

                    @foreach ($namaBulanArr as $m => $nm)
                        @php
                            $mb = $d['per_bulan'][$m] ?? ['hadir' => 0, 'hari_kerja' => 0, 'persen' => 0];
                        @endphp
                        <td class="col-bulan">
                            @if(isset($mb['hadir']) && $mb['hadir'] > 0)
                                <strong>{{ $mb['hadir'] }}</strong>
                            @else
                                <span style="color: #9ca3af;">0</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="col-total">{{ $d['total_hk'] }}</td>
                    <td class="col-total" style="color: #047857;">{{ $d['total_hadir'] }}</td>
                    <td class="col-total" style="color: #b45309;">{{ $totalIzinSakit }}</td>
                    <td class="col-total" style="color: #b91c1c;">{{ $d['total_alpa'] }}</td>
                    <td class="col-persen">{{ $d['persen_tahunan'] }}%</td>
                    <td>
                        <span class="badge-predikat {{ $predikatClass }}">{{ $predikat }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; padding-right: 4px;">RATA-RATA / TOTAL DESA:</td>
                @foreach ($namaBulanArr as $m => $nm)
                    @php
                        $totHadirBulan = collect($dataRekap)->sum(fn($r) => $r['per_bulan'][$m]['hadir'] ?? 0);
                    @endphp
                    <td>{{ $totHadirBulan }}</td>
                @endforeach
                <td>{{ collect($dataRekap)->sum('total_hk') }}</td>
                <td style="color: #047857;">{{ collect($dataRekap)->sum('total_hadir') }}</td>
                <td style="color: #b45309;">{{ collect($dataRekap)->sum(fn($r) => ($r['total_izin'] ?? 0) + ($r['total_sakit'] ?? 0)) }}</td>
                <td style="color: #b91c1c;">{{ collect($dataRekap)->sum('total_alpa') }}</td>
                <td style="background-color: #d1fae5; color: #065f46;">
                    @php
                        $pctCol = collect($dataRekap)->map(fn($r) => $r['persen_tahunan'])->filter(fn($v) => $v > 0);
                        $avgPct = $pctCol->count() ? round($pctCol->avg(), 1) : 0;
                    @endphp
                    {{ $avgPct }}%
                </td>
                <td>
                    @php
                        $avgPredikat = $avgPct >= 95 ? 'Sangat Baik' : ($avgPct >= 85 ? 'Baik' : ($avgPct >= 75 ? 'Cukup' : 'Kurang'));
                        $avgPredikatClass = $avgPct >= 95 ? 'predikat-sangat-baik' : ($avgPct >= 85 ? 'predikat-baik' : ($avgPct >= 75 ? 'predikat-cukup' : 'predikat-kurang'));
                    @endphp
                    <span class="badge-predikat {{ $avgPredikatClass }}">{{ $avgPredikat }}</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- ══════════ LEGENDA & PENGESAHAN (TTD) ══════════ -->
    <div class="no-break">
        <div class="info-bottom">
            <strong>Keterangan:</strong> <strong>HK</strong> = Hari Kerja Efektif &bull; <strong>H</strong> = Hadir &bull; <strong>I/S</strong> = Izin & Sakit &bull; <strong>A</strong> = Alpa (Tanpa Keterangan) &bull; <strong>%</strong> = Persentase Kehadiran Tahunan &bull; <strong>Kriteria Predikat:</strong> Sangat Baik (&ge;95%), Baik (85%–94%), Cukup (75%–84%), Kurang (&lt;75%).
        </div>

        <table class="ttd-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="ttd-cell">
                    <p style="margin: 0 0 2px;">Mengetahui / Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $kades->nama_lengkap ?? 'DADAY DAHYAT' }}</p>
                    <p class="ttd-nipd">NIPD. {{ $kades->nipd ?? '141.1/Kep.053-Pemdes/2019' }}</p>
                </td>
                <td class="ttd-cell">
                    <p style="margin: 0 0 2px;">Nangtang, 31 Desember {{ $tahun }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
                    <div class="ttd-space"></div>
                    <p class="ttd-name">{{ $sekdes->nama_lengkap ?? 'SUSANTI, S.Pd' }}</p>
                    <p class="ttd-nipd">NIPD. {{ $sekdes->nipd ?? '141.1/KEP.01/DES/2020' }}</p>
                </td>
            </tr>
        </table>

        <p class="footer-note">
            * Laporan ini sah dan diterbitkan secara elektronik melalui Sistem Informasi Presensi Digital Desa Nangtang pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.
        </p>
    </div>

</body>
</html>
