<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analitik Kedisiplinan — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { 
            size: A4 portrait; 
            margin: 8mm 12mm 8mm 12mm; 
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Arial', Helvetica, sans-serif; 
            font-size: 8pt; 
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
            margin-bottom: 8px;
        }
        .kop-logo-cell { 
            width: 60px; 
            text-align: center; 
            vertical-align: middle; 
        }
        .kop-logo-img { 
            height: 50px; 
            width: auto; 
            max-width: 50px; 
            display: inline-block;
            vertical-align: middle;
        }
        .kop-logo-circle {
            width: 44px; height: 44px;
            border: 2px solid #000; border-radius: 50%;
            text-align: center; vertical-align: middle;
            font-size: 14pt; font-weight: bold; color: #000;
            line-height: 44px; display: inline-block;
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
            letter-spacing: 0.5px;
        }
        .kop-kec { 
            font-size: 10pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 1px 0; 
            line-height: 1.2;
        }
        .kop-desa { 
            font-size: 12.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 1px 0; 
            letter-spacing: 0.8px; 
            line-height: 1.2;
        }
        .kop-alamat { 
            font-size: 7.5pt; 
            color: #000; 
            font-style: italic; 
            margin: 2px 0 0; 
        }

        /* ══════════ JUDUL DOKUMEN ══════════ */
        .doc-title { text-align: center; margin: 0 0 2px; }
        .doc-title h2 { 
            font-size: 10.5pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            text-decoration: underline; 
            margin: 0; 
            letter-spacing: 0.5px;
        }
        .doc-nomor { text-align: center; font-size: 8.5pt; margin: 2px 0 6px; }

        /* ══════════ EXECUTIVE KPI SUMMARY TABLE (FORMAL ORIGINAL) ══════════ */
        table.kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.kpi-table th, table.kpi-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }
        table.kpi-table th {
            background-color: #f1f5f9;
            font-size: 7.5pt;
            font-weight: bold;
            color: #000;
        }
        table.kpi-table td {
            font-size: 8.5pt;
            font-weight: bold;
            color: #000;
        }

        /* ══════════ DATA TABLE (FORMAL ORIGINAL BLACK & WHITE) ══════════ */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 7.2pt; 
            margin-top: 4px;
        }
        table.data-table th,
        table.data-table td { 
            border: 1px solid #000; 
            padding: 3.5px 3px; 
            text-align: center; 
            vertical-align: middle; 
            color: #000;
        }
        table.data-table th { 
            background-color: #f1f5f9; 
            color: #000; 
            font-weight: bold; 
            font-size: 7pt; 
        }
        table.data-table td.nama { 
            text-align: left; 
            padding-left: 4px; 
            font-size: 7.2pt; 
        }
        table.data-table td.jabatan { 
            text-align: left; 
            padding-left: 4px; 
            font-size: 6.8pt; 
            color: #000; 
        }
        table.data-table tr.even td { 
            background-color: #fafafa; 
        }

        /* ══════════ TTD TABLE ══════════ */
        .ttd-table { width: 100%; margin-top: 12px; page-break-inside: avoid; }
        .ttd-cell { width: 50%; text-align: center; font-size: 8pt; vertical-align: top; line-height: 1.3; color: #000; }
        .ttd-space { height: 48px; }
        .ttd-name { font-weight: bold; text-decoration: underline; font-size: 8.5pt; color: #000; }
        .ttd-nipd { font-size: 7.5pt; color: #000; }

        .footer-note { margin-top: 6px; font-size: 6.5pt; color: #333; font-style: italic; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo-tasikmalaya.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- KOP SURAT STANDAR PEMKAB TASIKMALAYA -->
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

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h2>LAPORAN EVALUASI & ANALITIK KEDISIPLINAN APARATUR DESA</h2>
    </div>
    <p class="doc-nomor">
        Periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong>
    </p>

    <!-- RINGKASAN EKSEKUTIF KPI KEDISIPLINAN -->
    <table class="kpi-table">
        <thead>
            <tr>
                <th>Indeks Kedisiplinan (IKK)</th>
                <th>Ketepatan Waktu</th>
                <th>Rata-rata Jam Kerja</th>
                <th>Total Keterlambatan</th>
                <th>Kepatuhan Piket</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $skorIKK }}% ({{ $skorIKK >= 90 ? 'Grade A' : ($skorIKK >= 80 ? 'Grade B' : 'Grade C') }})</td>
                <td>{{ $persenTepatWaktu }}%</td>
                <td>{{ $avgJamKerja }} Jam / Hari</td>
                <td>{{ $totalMenitTerlambat }} Menit</td>
                <td>{{ $piketRate }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- TABEL REKAPITULASI 14 PERANGKAT DESA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 18px;">No</th>
                <th style="width: 120px;">Nama Perangkat Desa</th>
                <th style="width: 95px;">Jabatan</th>
                <th style="width: 32px;">Tepat Waktu</th>
                <th style="width: 38px;">Terlambat</th>
                <th style="width: 32px;">Dinas Luar</th>
                <th style="width: 32px;">Izin/ Sakit</th>
                <th style="width: 25px;">Alpa</th>
                <th style="width: 35px;">Jam/ Hr</th>
                <th style="width: 35px;">Skor IKK</th>
                <th style="width: 55px;">Predikat</th>
                <th style="width: 85px;">Catatan Disiplin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employeeMatrix as $idx => $row)
                <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
                    <td>{{ $idx + 1 }}</td>
                    <td class="nama">
                        <strong>{{ $row['pegawai']->nama_lengkap }}</strong>
                        @if($row['pegawai']->nipd)
                            <div style="font-size: 6pt; color: #222;">NIPD: {{ $row['pegawai']->nipd }}</div>
                        @endif
                    </td>
                    <td class="jabatan">{{ $row['pegawai']->jabatan->nama_jabatan ?? 'Perangkat' }}</td>
                    <td><strong>{{ $row['hadir_tepat'] }}x</strong></td>
                    <td>
                        @if($row['hadir_terlambat'] > 0)
                            <strong>{{ $row['hadir_terlambat'] }}x</strong><br>
                            <span style="font-size: 5.8pt;">({{ $row['menit_terlambat'] }} m)</span>
                        @else
                            0
                        @endif
                    </td>
                    <td>{{ $row['dinas_luar'] > 0 ? $row['dinas_luar'] . 'x' : '—' }}</td>
                    <td>{{ $row['izin_sakit'] > 0 ? $row['izin_sakit'] . ' Hari' : '—' }}</td>
                    <td>
                        <strong>{{ $row['alpa'] > 0 ? $row['alpa'] . 'x' : '0' }}</strong>
                    </td>
                    <td>{{ $row['avg_jam_kerja'] }}</td>
                    <td><strong>{{ $row['skor'] }}%</strong></td>
                    <td style="font-size: 6.8pt; font-weight: bold;">
                        {{ $row['predikat'] }}
                    </td>
                    <td style="font-size: 6.5pt; text-align: left; padding-left: 3px;">
                        {{ $row['rekomendasi'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN RESMI -->
    <table class="ttd-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="ttd-cell">
                Mengetahui,<br>
                <strong>KEPALA DESA NANGTANG</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $kades?->nama_lengkap ?? 'KEPALA DESA NANGTANG' }}</div>
                <div class="ttd-nipd">{{ $kades?->nipd ? 'NIPD. ' . $kades->nipd : 'NIPD. —' }}</div>
            </td>
            <td class="ttd-cell">
                Nangtang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                <strong>SEKRETARIS DESA NANGTANG</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $sekdes?->nama_lengkap ?? 'SEKRETARIS DESA NANGTANG' }}</div>
                <div class="ttd-nipd">{{ $sekdes?->nipd ? 'NIPD. ' . $sekdes->nipd : 'NIPD. —' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        * Dokumen ini diterbitkan secara resmi melalui Sistem Informasi Kehadiran Aparatur Desa (Presence Desa Nangtang) berdasarkan Peraturan Pemerintah No. 94 tentang Disiplin Aparatur dan Permendagri.
    </div>
</body>
</html>
