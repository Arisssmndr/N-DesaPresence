<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Models\AbsensiDisesuaikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanDisesuaikanController extends Controller
{
    // =========================================================
    //  LAPORAN 1: REKAP PRESENSI DISESUAIKAN HARIAN
    // =========================================================
    public function laporanHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $dt = Carbon::parse($tanggal);

        $pegawais = Pegawai::with(['jabatan'])->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $pegawaiIds = $pegawais->pluck('id')->toArray();

        $hariLiburs = HariLibur::whereDate('tanggal', $tanggal)->exists();
        $isWeekend   = $dt->isWeekend();

        // Ambil data disesuaikan hari ini
        $disesuaikanMap = AbsensiDisesuaikan::whereIn('pegawai_id', $pegawaiIds)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('pegawai_id');

        // Ambil data kehadiran murni hari ini
        $kehadiranMap = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('pegawai_id');

        $rekap = ['hadir' => 0, 'alpa' => 0, 'izin' => 0, 'sakit' => 0, 'libur' => 0];

        foreach ($pegawais as $p) {
            $adj = $disesuaikanMap->get($p->id);
            $ori = $kehadiranMap->get($p->id);

            // Prioritaskan data disesuaikan, fallback ke data murni
            $resolved = $adj ?: $ori;
            $p->resolved_kehadiran = $resolved;

            if ($resolved) {
                $status = $resolved->status_disesuaikan ?? $resolved->status;
                match ($status) {
                    'Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar' => $rekap['hadir']++,
                    'Izin'                                            => $rekap['izin']++,
                    'Sakit'                                           => $rekap['sakit']++,
                    'Libur'                                           => null,
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
        $nomorLaporan = sprintf('001/PRES-HRN-ADJ/%s/%s', $dt->format('m'), $dt->format('Y'));

        $pdf = Pdf::loadView('reports.laporan-disesuaikan-harian-pdf', compact(
            'tanggal', 'dt', 'pegawais', 'rekap', 'isWeekend', 'hariLiburs',
            'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Disesuaikan_Harian_Presensi_{$dt->format('d-m-Y')}.pdf";
        return $pdf->stream($filename);
    }

    // =========================================================
    //  LAPORAN 2: REKAP PRESENSI DISESUAIKAN BULANAN
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

        // Hari libur bulan ini
        $hariLiburs = HariLibur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->flip()
            ->toArray();

        // Data disesuaikan bulan ini
        $semuaDisesuaikan = AbsensiDisesuaikan::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->groupBy('pegawai_id');

        // Data murni bulan ini
        $semuaKehadiran = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->groupBy('pegawai_id');

        $matrix  = [];
        $summary = [];

        $todayStr = Carbon::today()->toDateString();

        foreach ($pegawais as $p) {
            $adjMap = $semuaDisesuaikan->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));
            $oriMap = $semuaKehadiran->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));

            $pSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
                $dt = Carbon::createFromDate($tahun, $bulan, $d);

                $isWeekend = $dt->isWeekend();
                $isHoliday = isset($hariLiburs[$dateStr]) || $isWeekend;
                $isFuture = ($dateStr > $todayStr);
                $isToday = ($dateStr === $todayStr);

                $record = $adjMap->get($dateStr) ?: $oriMap->get($dateStr);

                if ($record) {
                    $status = $record->status_disesuaikan ?? $record->status;
                    $code = match ($status) {
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
        $nomorLaporan = sprintf('002/PRES-BLN-ADJ/%02d/%d', $bulan, $tahun);

        $pdf = Pdf::loadView('reports.laporan-disesuaikan-bulanan-pdf', compact(
            'bulan', 'tahun', 'namaBulan', 'daysInMonth', 'pegawais',
            'matrix', 'summary', 'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'landscape');

        $filename = "Laporan_Disesuaikan_Bulanan_Presensi_{$namaBulan}_{$tahun}.pdf";
        return $pdf->stream($filename);
    }

    // =========================================================
    //  LAPORAN 3: REKAP PRESENSI DISESUAIKAN TAHUNAN
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

        // Hari libur tahun ini
        $hariLiburSet = HariLibur::whereYear('tanggal', $tahun)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->flip()
            ->toArray();

        // Data disesuaikan tahun ini
        $semuaDisesuaikan = AbsensiDisesuaikan::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->groupBy('pegawai_id');

        // Data murni tahun ini
        $semuaKehadiran = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->groupBy('pegawai_id');

        // Pre-calculate hari kerja per bulan
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
            $adjByDate = $semuaDisesuaikan->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));
            $oriByDate = $semuaKehadiran->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));

            $rowData = [];
            $totalHadir = 0;
            $totalAlpa  = 0;
            $totalIzin  = 0;
            $totalSakit = 0;
            $totalDinas = 0;
            $totalHariKerja = 0;

            for ($m = 1; $m <= 12; $m++) {
                $carbonBulan = Carbon::createFromDate($tahun, $m, 1);
                $daysInM = $carbonBulan->daysInMonth;

                $mHadir = 0;
                $mAlpa  = 0;
                $mIzin  = 0;
                $mSakit = 0;

                for ($d = 1; $d <= $daysInM; $d++) {
                    $dStr = sprintf("%04d-%02d-%02d", $tahun, $m, $d);
                    $dt = Carbon::createFromDate($tahun, $m, $d);
                    $isWk = $dt->isWeekend();
                    $isHoli = isset($hariLiburSet[$dStr]) || $isWk;

                    $rec = $adjByDate->get($dStr) ?: $oriByDate->get($dStr);

                    if ($rec) {
                        $st = $rec->status_disesuaikan ?? $rec->status;
                        match ($st) {
                            'Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar' => $mHadir++,
                            'Izin'                                            => $mIzin++,
                            'Sakit'                                           => $mSakit++,
                            'Alpa'                                            => $mAlpa++,
                            default                                           => null,
                        };
                    } elseif (!$isHoli) {
                        $mAlpa++;
                    }
                }

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
        $nomorLaporan = sprintf('003/PRES-THN-ADJ/%d', $tahun);

        $pdf = Pdf::loadView('reports.laporan-disesuaikan-tahunan-pdf', compact(
            'tahun', 'pegawais', 'namaBulanArr', 'dataRekap', 'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'landscape');

        $filename = "Laporan_Disesuaikan_Tahunan_Presensi_{$tahun}.pdf";
        return $pdf->stream($filename);
    }

    // =========================================================
    //  LAPORAN 4: REKAP PRESENSI DISESUAIKAN RENTANG TANGGAL
    // =========================================================
    public function laporanRentang(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggalSelesai = $request->input('tanggal_selesai', date('Y-m-d'));

        if ($tanggalMulai > $tanggalSelesai) {
            $temp = $tanggalMulai;
            $tanggalMulai = $tanggalSelesai;
            $tanggalSelesai = $temp;
        }

        $startDt = Carbon::parse($tanggalMulai);
        $endDt = Carbon::parse($tanggalSelesai);

        $dateRange = [];
        $curr = $startDt->copy();
        while ($curr->lte($endDt)) {
            $dateRange[] = $curr->toDateString();
            $curr->addDay();
        }

        $pegawais   = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $pegawaiIds = $pegawais->pluck('id')->toArray();

        // Hari libur dalam rentang
        $hariLiburSet = HariLibur::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->flip()
            ->toArray();

        // Data disesuaikan dalam rentang
        $semuaDisesuaikan = AbsensiDisesuaikan::whereIn('pegawai_id', $pegawaiIds)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->groupBy('pegawai_id');

        // Data murni dalam rentang
        $semuaKehadiran = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->groupBy('pegawai_id');

        $totalHariKerjaPeriode = 0;
        foreach ($dateRange as $dStr) {
            $dt = Carbon::parse($dStr);
            if (!$dt->isWeekend() && !isset($hariLiburSet[$dStr])) {
                $totalHariKerjaPeriode++;
            }
        }

        $matrix  = [];
        $summary = [];
        $todayStr = Carbon::today()->toDateString();

        foreach ($pegawais as $p) {
            $adjMap = $semuaDisesuaikan->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));
            $oriMap = $semuaKehadiran->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));

            $pSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0];

            foreach ($dateRange as $dateStr) {
                $dt = Carbon::parse($dateStr);
                $isWeekend = $dt->isWeekend();
                $isHoliday = isset($hariLiburSet[$dateStr]) || $isWeekend;
                $isFuture = ($dateStr > $todayStr);
                $isToday = ($dateStr === $todayStr);

                $record = $adjMap->get($dateStr) ?: $oriMap->get($dateStr);

                if ($record) {
                    $status = $record->status_disesuaikan ?? $record->status;
                    $code = match ($status) {
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

                $matrix[$p->id][$dateStr] = $code;
                if (isset($pSummary[$code])) {
                    $pSummary[$code]++;
                }
            }

            $totalHadir = $pSummary['H'];
            $pSummary['persen'] = $totalHariKerjaPeriode > 0
                ? round(($totalHadir / $totalHariKerjaPeriode) * 100, 1)
                : 0;
            $summary[$p->id] = $pSummary;
        }

        $kades  = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KADES'))->first();
        $sekdes = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'SEKDES'))->first();
        $nomorLaporan = sprintf('004/PRES-RNT-ADJ/%s-%s', Carbon::parse($tanggalMulai)->format('dmy'), Carbon::parse($tanggalSelesai)->format('dmy'));

        $pdf = Pdf::loadView('reports.laporan-disesuaikan-rentang-pdf', compact(
            'tanggalMulai', 'tanggalSelesai', 'dateRange', 'totalHariKerjaPeriode',
            'pegawais', 'matrix', 'summary', 'kades', 'sekdes', 'nomorLaporan'
        ))->setPaper('a4', 'landscape');

        $filename = "Laporan_Disesuaikan_Rentang_{$tanggalMulai}_sd_{$tanggalSelesai}.pdf";
        return $pdf->stream($filename);
    }
}
