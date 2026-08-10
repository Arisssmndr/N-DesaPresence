<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPJ Presensi Desa Nangtang — {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9pt; color: #111; line-height: 1.2; }
        .kop-surat { text-align: center; border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 12px; }
        .kop-surat h2 { font-size: 13pt; margin: 0; font-weight: bold; text-transform: uppercase; }
        .kop-surat h3 { font-size: 11pt; margin: 2px 0; font-weight: bold; text-transform: uppercase; }
        .kop-surat p { font-size: 8pt; margin: 0; font-style: italic; }

        .title-section { text-align: center; margin-bottom: 12px; }
        .title-section h4 { font-size: 11pt; margin: 0; text-decoration: underline; text-transform: uppercase; }
        .title-section p { font-size: 9pt; margin: 2px 0 0 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 8pt; }
        table.data-table th, table.data-table td { border: 1px solid #333; padding: 4px 2px; text-align: center; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; }
        table.data-table td.nama { text-align: left; padding-left: 6px; }

        .code-H { background-color: #d1fae5; font-weight: bold; color: #065f46; }
        .code-T { background-color: #fef3c7; font-weight: bold; color: #92400e; }
        .code-A { background-color: #fee2e2; font-weight: bold; color: #991b1b; }
        .code-I { background-color: #f3e8ff; font-weight: bold; color: #6b21a8; }
        .code-D { background-color: #dbeafe; font-weight: bold; color: #1e40af; }
        .code-L { background-color: #e5e7eb; color: #4b5563; }

        .ttd-section { margin-top: 24px; width: 100%; font-size: 9pt; }
        .ttd-box { width: 45%; float: left; text-align: center; }
        .ttd-box-right { width: 45%; float: right; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi Pemdes Nangtang -->
    <div class="kop-surat">
        <h2>PEMERINTAH KABUPATEN TASIKMALAYA</h2>
        <h3>KECAMATAN CIGALONTANG — PEMERINTAH DESA NANGTANG</h3>
        <p>Jalan Raya Desa Nangtang Kode Pos 46463 — Email: pemdes@desanangtang.go.id</p>
    </div>

    <!-- Title Section -->
    <div class="title-section">
        <h4>LAPORAN REKAPITULASI PRESENSI PERANGKAT DESA (SPJ)</h4>
        <p>Bulan: <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>
    </div>

    <!-- Table Matriks -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 140px;">Nama Perangkat Desa</th>
                <th rowspan="2" style="width: 90px;">Jabatan</th>
                <th colspan="{{ $daysInMonth }}">Tanggal</th>
                <th colspan="5">Rekapitulasi</th>
            </tr>
            <tr>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <th style="width: 14px;">{{ $d }}</th>
                @endfor
                <th style="width: 16px;">H</th>
                <th style="width: 16px;">T</th>
                <th style="width: 16px;">I</th>
                <th style="width: 16px;">D</th>
                <th style="width: 16px;">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $idx => $p)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="nama"><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td style="text-align: left; padding-left: 4px;">{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php $code = $matrix[$p->id][$d] ?? 'A'; @endphp
                        <td class="code-{{ $code }}">{{ $code }}</td>
                    @endfor
                    <td style="font-weight: bold; background-color: #ecfdf5;">{{ $summary[$p->id]['H'] ?? 0 }}</td>
                    <td style="font-weight: bold; background-color: #fffbeb;">{{ $summary[$p->id]['T'] ?? 0 }}</td>
                    <td style="font-weight: bold; background-color: #faf5ff;">{{ $summary[$p->id]['I'] ?? 0 }}</td>
                    <td style="font-weight: bold; background-color: #eff6ff;">{{ $summary[$p->id]['D'] ?? 0 }}</td>
                    <td style="font-weight: bold; background-color: #fef2f2;">{{ $summary[$p->id]['A'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 7.5pt; font-style: italic; margin-top: 6px;">Keterangan: H = Hadir Tepat Waktu, T = Terlambat, I = Izin/Sakit, D = Dinas Luar (SPT), A = Alpa/Tanpa Keterangan, L = Libur/Akhir Pekan.</p>

    <!-- Lembar Tanda Tangan Resmi Kades & Sekdes -->
    <div class="ttd-section">
        <div class="ttd-box">
            <p>Mengetahui/Menyetujui,<br><strong>KEPALA DESA NANGTANG</strong></p>
            <br><br><br>
            <p><u><strong>{{ $kades->nama_lengkap ?? 'H. AHMAD SUPRIYADI, S.IP' }}</strong></u><br>NIPD: {{ $kades->nipd ?? '-' }}</p>
        </div>

        <div class="ttd-box-right">
            <p>Nangtang, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><strong>SEKRETARIS DESA NANGTANG</strong></p>
            <br><br><br>
            <p><u><strong>{{ $sekdes->nama_lengkap ?? 'HJ. NURLAILA RAHMAWATI, S.AP' }}</strong></u><br>NIPD: {{ $sekdes->nipd ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
