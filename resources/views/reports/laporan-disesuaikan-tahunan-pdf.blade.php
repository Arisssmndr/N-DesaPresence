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
            margin-bottom: 10px;
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
        table.data-table th.col-pegawai { text-align: left; padding-left: 3px; }
        table.data-table td.col-pegawai { text-align: left; padding-left: 3px; }
        table.data-table td.col-total { font-weight: bold; background-color: #f8fafc; }
        table.data-table td.col-persen { font-weight: bold; background-color: #f1f5f9; }

        /* ══════════ TANDA TANGAN ══════════ */
        .ttd-table { width: 100%; margin-top: 8px; page-break-inside: avoid; }
        .ttd-cell  { width: 50%; text-align: center; font-size: 8pt; vertical-align: top; line-height: 1.2; }
        .ttd-space { height: 44px; }
        .ttd-name  { font-weight: bold; text-decoration: underline; margin: 0; font-size: 8.5pt; }
        .ttd-nipd  { font-size: 7.5pt; margin: 1px 0 0; }
        .footer-note { margin-top: 3px; font-size: 6pt; color: #444; font-style: italic; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        $logoPath   = public_path('images/logo-tasikmalaya.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- ══════════ KOP SURAT ══════════ -->
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
        <h2>REKAPITULASI TINGKAT KEHADIRAN PERANGKAT DESA TAHUNAN</h2>
    </div>
    <div class="doc-nomor">Nomor: {{ $nomorLaporan }}</div>

    <!-- ══════════ TABEL TAHUNAN ══════════ -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:14px;">No</th>
                <th rowspan="2" class="col-pegawai" style="width:90px;">Nama Perangkat Desa / NIPD</th>
                <th rowspan="2" style="width:62px;">Jabatan</th>
                @for ($m = 1; $m <= 12; $m++)
                    <th colspan="3" style="font-size:6pt;">{{ $namaBulanArr[$m] }}</th>
                @endfor
                <th colspan="5" class="dark" style="font-size:6pt;">Total Akumulasi {{ $tahun }}</th>
            </tr>
            <tr>
                @for ($m = 1; $m <= 12; $m++)
                    <th class="sub" style="width:14px;" title="Hari Kerja Efektif">HK</th>
                    <th class="sub" style="width:14px;" title="Jumlah Kehadiran">H</th>
                    <th class="sub" style="width:16px;" title="Persentase Kehadiran">%</th>
                @endfor
                <th class="dark" style="width:16px;">HK</th>
                <th class="dark" style="width:16px;">H</th>
                <th class="dark" style="width:14px;">I/S</th>
                <th class="dark" style="width:14px;">A</th>
                <th class="dark" style="width:20px;">%</th>
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
                        'total_dinas'    => 0,
                        'total_hk'       => 0,
                        'persen_tahunan' => 0,
                    ];
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="col-pegawai">
                        <strong>{{ $p->nama_lengkap }}</strong>
                        @if($p->nipd)
                            <br><span style="font-size:5.5pt; color:#444;">{{ $p->nipd }}</span>
                        @endif
                    </td>
                    <td style="font-size:5.8pt;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>

                    @for ($m = 1; $m <= 12; $m++)
                        @php
                            $bm = $rekap['per_bulan'][$m] ?? ['hadir' => 0, 'hari_kerja' => 0, 'persen' => 0];
                        @endphp
                        <td style="color:#555;">{{ $bm['hari_kerja'] }}</td>
                        <td><strong>{{ $bm['hadir'] }}</strong></td>
                        <td style="font-weight:bold;">{{ $bm['persen'] }}%</td>
                    @endfor

                    <td class="col-total">{{ $rekap['total_hk'] }}</td>
                    <td class="col-total">{{ $rekap['total_hadir'] }}</td>
                    <td class="col-total">{{ $rekap['total_izin'] }}</td>
                    <td class="col-total">{{ $rekap['total_alpa'] }}</td>
                    <td class="col-persen">{{ $rekap['persen_tahunan'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ══════════ TANDA TANGAN PENGESAHAN ══════════ -->
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
                    <p style="margin:0 0 2px;">Nangtang, 31 Desember {{ $tahun }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
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
