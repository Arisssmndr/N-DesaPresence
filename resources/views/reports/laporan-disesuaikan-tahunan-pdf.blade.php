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
            color: #000000; 
            background-color: #ffffff;
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
            border: 1.5px solid #000000; border-radius: 50%; 
            text-align: center; vertical-align: middle; 
            font-size: 13pt; font-weight: bold; color: #000000; 
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
            color: #000000;
            margin: 0; 
            line-height: 1.15;
            letter-spacing: 0.5px;
        }
        .kop-kec { 
            font-size: 9.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #000000;
            margin: 0; 
            line-height: 1.15; 
        }
        .kop-desa { 
            font-size: 11.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #000000;
            margin: 1px 0 0; 
            letter-spacing: 0.8px; 
            line-height: 1.15; 
        }
        .kop-alamat { 
            font-size: 7pt; 
            color: #000000; 
            font-style: italic; 
            margin: 1.5px 0 0; 
        }

        /* ══════════ JUDUL DOKUMEN (TANPA NOMOR SURAT) ══════════ */
        .doc-title { text-align: center; margin: 3px 0 2px; }
        .doc-title h2 { 
            font-size: 9.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-decoration: underline; 
            color: #000000;
            margin: 0; 
            letter-spacing: 0.4px; 
        }
        .doc-periode { 
            text-align: center; 
            font-size: 7.5pt; 
            font-weight: bold; 
            color: #000000; 
            margin: 2px 0 4px; 
            letter-spacing: 0.3px;
        }

        /* ══════════ TABEL REKAPITULASI HITAM PUTIH RESMI PEMERINTAHAN ══════════ */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 6.2pt; 
            margin-bottom: 4px;
            color: #000000;
            background-color: #ffffff;
        }
        table.data-table th,
        table.data-table td { 
            border: 0.75pt solid #000000; 
            padding: 2.5px 1.5px; 
            text-align: center; 
            vertical-align: middle; 
            color: #000000;
            background-color: #ffffff;
        }
        table.data-table th { 
            background-color: #f2f2f2; 
            color: #000000; 
            font-weight: bold; 
            font-size: 6pt;
            line-height: 1.1;
        }
        table.data-table th.th-header-group { 
            background-color: #e6e6e6; 
            font-size: 6.2pt; 
            padding: 2.5px 1px;
            letter-spacing: 0.2px;
        }
        table.data-table th.th-akumulasi { 
            background-color: #e6e6e6; 
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
        }
        table.data-table td.col-persen { 
            font-weight: bold; 
        }
        table.data-table tfoot td { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            font-size: 6pt;
            padding: 2.5px 1.5px;
        }

        /* ══════════ KETERANGAN & TANDA TANGAN ══════════ */
        .info-bottom {
            width: 100%;
            margin-top: 2px;
            font-size: 5.8pt;
            color: #000000;
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
            color: #000000;
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
            color: #000000;
        }
        .ttd-nipd  { 
            font-size: 6.8pt; 
            margin: 1px 0 0; 
            color: #000000;
        }
        .footer-note { 
            margin-top: 2px; 
            font-size: 5.5pt; 
            color: #000000; 
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
        <h2>REKAPITULASI TINGKAT KEHADIRAN PERANGKAT DESA TAHUNAN</h2>
    </div>
    <div class="doc-periode">TAHUN ANGGARAN {{ $tahun }}</div>

    <!-- ══════════ TABEL REKAPITULASI TAHUNAN HITAM PUTIH RESMI ══════════ -->
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
                @for ($m = 1; $m <= 12; $m++)
                    <th style="width: 3.2%;" title="Bulan {{ $namaBulanArr[$m] }}">{{ $namaBulanArr[$m] }}</th>
                @endfor
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
                    $rekap = $dataRekap[$p->id] ?? [
                        'per_bulan'      => [],
                        'total_hadir'    => 0,
                        'total_alpa'     => 0,
                        'total_izin'     => 0,
                        'total_sakit'    => 0,
                        'total_hk'       => 0,
                        'persen_tahunan' => 0,
                    ];
                    $pct = $rekap['persen_tahunan'];
                    $predikat = $pct >= 95 ? 'Sangat Baik' : ($pct >= 85 ? 'Baik' : ($pct >= 75 ? 'Cukup' : 'Kurang'));
                    $totalIzinSakit = ($rekap['total_izin'] ?? 0) + ($rekap['total_sakit'] ?? 0);
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="col-nama">
                        <strong>{{ $p->nama_lengkap }}</strong>
                        @if($p->nipd)
                            <br><span style="font-size: 5.2pt;">NIPD. {{ $p->nipd }}</span>
                        @endif
                    </td>
                    <td class="col-jabatan">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>

                    @for ($m = 1; $m <= 12; $m++)
                        @php
                            $bm = $rekap['per_bulan'][$m] ?? ['hadir' => 0, 'hari_kerja' => 0, 'persen' => 0];
                        @endphp
                        <td class="col-bulan">
                            {{ $bm['hadir'] ?? 0 }}
                        </td>
                    @endfor

                    <td class="col-total">{{ $rekap['total_hk'] }}</td>
                    <td class="col-total">{{ $rekap['total_hadir'] }}</td>
                    <td class="col-total">{{ $totalIzinSakit }}</td>
                    <td class="col-total">{{ $rekap['total_alpa'] }}</td>
                    <td class="col-persen"><strong>{{ $rekap['persen_tahunan'] }}%</strong></td>
                    <td>{{ $predikat }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; padding-right: 4px;">RATA-RATA / TOTAL DESA:</td>
                @for ($m = 1; $m <= 12; $m++)
                    @php
                        $totHadirBulan = collect($dataRekap)->sum(fn($r) => $r['per_bulan'][$m]['hadir'] ?? 0);
                    @endphp
                    <td>{{ $totHadirBulan }}</td>
                @endfor
                <td>{{ collect($dataRekap)->sum('total_hk') }}</td>
                <td>{{ collect($dataRekap)->sum('total_hadir') }}</td>
                <td>{{ collect($dataRekap)->sum(fn($r) => ($r['total_izin'] ?? 0) + ($r['total_sakit'] ?? 0)) }}</td>
                <td>{{ collect($dataRekap)->sum('total_alpa') }}</td>
                <td>
                    @php
                        $pctCol = collect($dataRekap)->map(fn($r) => $r['persen_tahunan'])->filter(fn($v) => $v > 0);
                        $avgPct = $pctCol->count() ? round($pctCol->avg(), 1) : 0;
                    @endphp
                    <strong>{{ $avgPct }}%</strong>
                </td>
                <td>
                    @php
                        $avgPredikat = $avgPct >= 95 ? 'Sangat Baik' : ($avgPct >= 85 ? 'Baik' : ($avgPct >= 75 ? 'Cukup' : 'Kurang'));
                    @endphp
                    {{ $avgPredikat }}
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
            * Dokumen ini sah dan diterbitkan secara elektronik melalui Sistem Informasi Presensi Digital Desa Nangtang pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.
        </p>
    </div>

</body>
</html>
