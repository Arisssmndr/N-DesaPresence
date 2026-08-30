<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // =========================================================
    //  LAPORAN 1: REKAP PRESENSI HARIAN
    // =========================================================
    public function laporanHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $dt = Carbon::parse($tanggal);

        $pegawais = Pegawai::with(['jabatan', 'kehadirans' => function ($q) use ($tanggal) {
            $q->where('tanggal', $tanggal);
        }])->where('status_aktif', true)->orderBy('nama_lengkap')->get();

        $hariLiburs = HariLibur::where('tanggal', $tanggal)->exists();
        $isWeekend   = $dt->isWeekend();

        $rekap = ['hadir' => 0, 'alpa' => 0, 'izin' => 0, 'sakit' => 0, 'libur' => 0];

        foreach ($pegawais as $p) {
            $k = $p->kehadirans->first();
            if ($k) {
                // Jika ada data kehadiran aktual, catat statusnya
                match ($k->status) {
                    'Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar' => $rekap['hadir']++,
                    'Izin'                                            => $rekap['izin']++,
                    'Sakit'                                           => $rekap['sakit']++,
                    default                                           => $rekap['alpa']++,
                };
            } elseif ($isWeekend || $hariLiburs) {
                $rekap['libur']++;
            } else {
                $rekap['alpa']++;
            }
        }

        $kades  = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KADES'))->first();
        $sekdes = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'SEKDES'))->first();
        $nomorLaporan = sprintf('001/PRES-HRN/%s/%s', $dt->format('m'), $dt->format('Y'));

        $pdf = Pdf::loadView('reports.laporan-harian-pdf', compact(
            'tanggal', 'dt', 'pegawais', 'rekap', 'isWeekend', 'hariLiburs',
            'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Harian_Presensi_{$dt->format('d-m-Y')}.pdf";
        return $pdf->stream($filename);
    }

    // =========================================================
    //  LAPORAN 2: REKAP PRESENSI BULANAN (BATCH LOADED — NO N+1)
    // =========================================================
    public function laporanBulanan(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $carbonBulan  = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth  = $carbonBulan->daysInMonth;
        $namaBulan    = $carbonBulan->translatedFormat('F');

        $pegawais   = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $pegawaiIds = $pegawais->pluck('id')->toArray();

        // 1 Query untuk hari libur bulan ini
        $hariLiburs = HariLibur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->flip()
            ->toArray();

        // 1 Query untuk semua kehadiran semua pegawai bulan ini
        $semuaKehadiran = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->groupBy('pegawai_id');

        $matrix  = [];
        $summary = [];

        $todayStr = Carbon::today()->toDateString();

        foreach ($pegawais as $p) {
            $kehadiranMap = $semuaKehadiran->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));
            $pSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
                $dt = Carbon::createFromDate($tahun, $bulan, $d);

                $isWeekend = $dt->isWeekend();
                $isHoliday = isset($hariLiburs[$dateStr]) || $isWeekend;
                $isFuture = ($dateStr > $todayStr);
                $isToday = ($dateStr === $todayStr);

                if (isset($kehadiranMap[$dateStr])) {
                    $code = match ($kehadiranMap[$dateStr]->status) {
                        'Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar' => 'H',
                        'Izin'                                            => 'I',
                        'Sakit'                                           => 'S',
                        default                                           => 'A',
                    };
                } elseif ($isHoliday) {
                    $code = 'L';
                } elseif ($isFuture || $isToday) {
                    $code = '-';
                } else {
                    $code = 'A';
                }

                $matrix[$p->id][$d] = $code;
                if (isset($pSummary[$code])) {
                    $pSummary[$code]++;
                }
            }

            $totalHariKerja = $daysInMonth - $pSummary['L'];
            $totalHadir     = $pSummary['H'];
            $pSummary['persen'] = $totalHariKerja > 0
                ? round(($totalHadir / $totalHariKerja) * 100, 1)
                : 0;
            $summary[$p->id] = $pSummary;
        }

        $kades  = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KADES'))->first();
        $sekdes = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'SEKDES'))->first();
        $nomorLaporan = sprintf('002/PRES-BLN/%02d/%d', $bulan, $tahun);

        $pdf = Pdf::loadView('reports.laporan-bulanan-pdf', compact(
            'bulan', 'tahun', 'namaBulan', 'daysInMonth', 'pegawais',
            'matrix', 'summary', 'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'landscape');

        $filename = "Laporan_Bulanan_Presensi_{$namaBulan}_{$tahun}.pdf";
        return $pdf->stream($filename);
    }

    // =========================================================
    //  LAPORAN 3: REKAP PRESENSI TAHUNAN (BATCH LOADED — 2 QUERIES TOTAL)
    // =========================================================
    public function laporanTahunan(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));

        $pegawais     = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $pegawaiIds   = $pegawais->pluck('id')->toArray();

        $namaBulanArr = [];
        for ($m = 1; $m <= 12; $m++) {
            $namaBulanArr[$m] = Carbon::createFromDate($tahun, $m, 1)->translatedFormat('M');
        }

        // Query 1: Ambil semua hari libur dalam 1 tahun penuh
        $hariLiburSet = HariLibur::whereYear('tanggal', $tahun)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->flip()
            ->toArray();

        // Query 2: Ambil SEMUA data kehadiran tahun ini untuk semua pegawai sekaligus
        $semuaKehadiran = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->groupBy(['pegawai_id', function ($item) {
                return (int) Carbon::parse($item->tanggal)->format('m');
            }]);

        // Pre-calculate hari kerja per bulan di tahun tersebut
        $hariKerjaPerBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $carbonBulan = Carbon::createFromDate($tahun, $m, 1);
            $daysInMonth = $carbonBulan->daysInMonth;
            $mLibur = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $tahun, $m, $d);
                $dt = Carbon::createFromDate($tahun, $m, $d);
                if ($dt->isWeekend() || isset($hariLiburSet[$dateStr])) {
                    $mLibur++;
                }
            }
            $hariKerjaPerBulan[$m] = $daysInMonth - $mLibur;
        }

        $dataRekap = [];
        foreach ($pegawais as $p) {
            $rowData = [];
            $totalHadir = 0;
            $totalAlpa  = 0;
            $totalIzin  = 0;
            $totalSakit = 0;
            $totalHariKerja = 0;

            for ($m = 1; $m <= 12; $m++) {
                $kehadiranBulan = $semuaKehadiran->get($p->id, collect())->get($m, collect());

                $mHadir = $kehadiranBulan->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar'])->count();
                $mAlpa  = $kehadiranBulan->where('status', 'Alpa')->count();
                $mIzin  = $kehadiranBulan->where('status', 'Izin')->count();
                $mSakit = $kehadiranBulan->where('status', 'Sakit')->count();

                $mHariKerja = $hariKerjaPerBulan[$m];
                $rowData[$m] = [
                    'hadir'       => $mHadir,
                    'alpa'        => $mAlpa,
                    'izin'        => $mIzin,
                    'sakit'       => $mSakit,
                    'hari_kerja'  => $mHariKerja,
                    'persen'      => $mHariKerja > 0 ? round(($mHadir / $mHariKerja) * 100, 0) : 0,
                ];

                $totalHadir     += $mHadir;
                $totalAlpa      += $mAlpa;
                $totalIzin      += $mIzin;
                $totalSakit     += $mSakit;
                $totalHariKerja += $mHariKerja;
            }

            $dataRekap[$p->id] = [
                'per_bulan'      => $rowData,
                'total_hadir'    => $totalHadir,
                'total_alpa'     => $totalAlpa,
                'total_izin'     => $totalIzin,
                'total_sakit'    => $totalSakit,
                'total_hk'       => $totalHariKerja,
                'persen_tahunan' => $totalHariKerja > 0 ? round(($totalHadir / $totalHariKerja) * 100, 1) : 0,
            ];
        }

        $kades  = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KADES'))->first();
        $sekdes = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'SEKDES'))->first();
        $nomorLaporan = sprintf('003/PRES-THN/%d', $tahun);

        $pdf = Pdf::loadView('reports.laporan-tahunan-pdf', compact(
            'tahun', 'pegawais', 'namaBulanArr', 'dataRekap', 'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'landscape');

        $filename = "Laporan_Tahunan_Presensi_{$tahun}.pdf";
        return $pdf->stream($filename);
    }
}
