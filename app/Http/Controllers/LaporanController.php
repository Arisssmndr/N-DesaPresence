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

    // =========================================================
    //  LAPORAN 4: EVALUASI & ANALITIK KEDISIPLINAN APARATUR (PDF KOP RESMI)
    // =========================================================
    public function laporanAnalitikPdf(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');
        $bulanInt = ($bulan !== null && $bulan !== '') ? (int) $bulan : null;

        // Base Query Kehadiran
        $baseQuery = Kehadiran::query()->whereYear('tanggal', $tahun);
        if ($bulanInt) {
            $baseQuery->whereMonth('tanggal', $bulanInt);
        }

        $allKehadiran = (clone $baseQuery)->get();
        $totalHadir = 0;
        $totalTepatWaktu = 0;
        $totalTerlambat = 0;
        $totalMenitTerlambat = 0;
        $totalDinasLuar = 0;
        $totalIzinSakit = 0;
        $totalAlpa = 0;
        $totalDurasiMenit = 0;
        $durasiCount = 0;

        foreach ($allKehadiran as $k) {
            $st = strtolower($k->status);
            if (in_array($st, ['hadir', 'tepat waktu', 'terlambat'])) {
                $totalHadir++;
                if ($k->durasi_kerja_menit > 0) {
                    $totalDurasiMenit += $k->durasi_kerja_menit;
                    $durasiCount++;
                }

                $menitTelat = $k->terlambat_menit;
                if ($k->jam_masuk && !$menitTelat) {
                    $jamClean = substr($k->jam_masuk, 0, 5);
                    if ($jamClean > '08:00') {
                        $masukC = Carbon::createFromTimeString($jamClean);
                        $standarC = Carbon::createFromTimeString('08:00');
                        $menitTelat = $standarC->diffInMinutes($masukC);
                    }
                }

                if ($menitTelat > 0) {
                    $totalTerlambat++;
                    $totalMenitTerlambat += $menitTelat;
                } else {
                    $totalTepatWaktu++;
                }
            } elseif ($st === 'dinas luar') {
                $totalDinasLuar++;
                $totalHadir++;
            } elseif (in_array($st, ['izin', 'sakit'])) {
                $totalIzinSakit++;
            } elseif ($st === 'alpa') {
                $totalAlpa++;
            }
        }

        $totalEntriValid = max(1, $totalHadir + $totalIzinSakit + $totalAlpa);
        $skorIKK = round(
            (($totalTepatWaktu * 100) + ($totalDinasLuar * 100) + ($totalTerlambat * 80) + ($totalIzinSakit * 70)) / $totalEntriValid,
            1
        );
        $persenTepatWaktu = $totalHadir > 0 ? round(($totalTepatWaktu + $totalDinasLuar) / $totalHadir * 100, 1) : 100;
        $avgJamKerja = $durasiCount > 0 ? round(($totalDurasiMenit / $durasiCount) / 60, 1) : 7.5;

        // Jadwal Piket
        $piketQuery = \App\Models\JadwalPiket::whereYear('tanggal_piket', $tahun);
        if ($bulanInt) {
            $piketQuery->whereMonth('tanggal_piket', $bulanInt);
        }
        $totalPiketJadwal = (clone $piketQuery)->count();
        $totalPiketHadir = (clone $piketQuery)->where('status', 'hadir')->count();
        $piketRate = $totalPiketJadwal > 0 ? round(($totalPiketHadir / $totalPiketJadwal) * 100, 1) : 100;

        // Matriks Pegawai
        $pegawais = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $employeeMatrix = [];
        foreach ($pegawais as $p) {
            $pQuery = Kehadiran::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun);
            if ($bulanInt) {
                $pQuery->whereMonth('tanggal', $bulanInt);
            }
            $pRecords = $pQuery->get();

            $pHadirTepat = 0;
            $pHadirTerlambat = 0;
            $pMenitTerlambat = 0;
            $pDinasLuar = 0;
            $pIzinSakit = 0;
            $pAlpa = 0;
            $pTotalMenitKerja = 0;
            $pCountDurasi = 0;

            foreach ($pRecords as $r) {
                $st = strtolower($r->status);
                if (in_array($st, ['hadir', 'tepat waktu', 'terlambat'])) {
                    if ($r->durasi_kerja_menit > 0) {
                        $pTotalMenitKerja += $r->durasi_kerja_menit;
                        $pCountDurasi++;
                    }

                    $telat = $r->terlambat_menit;
                    if ($r->jam_masuk && !$telat) {
                        $jamClean = substr($r->jam_masuk, 0, 5);
                        if ($jamClean > '08:00') {
                            $masukC = Carbon::createFromTimeString($jamClean);
                            $standarC = Carbon::createFromTimeString('08:00');
                            $telat = $standarC->diffInMinutes($masukC);
                        }
                    }

                    if ($telat > 0) {
                        $pHadirTerlambat++;
                        $pMenitTerlambat += $telat;
                    } else {
                        $pHadirTepat++;
                    }
                } elseif ($st === 'dinas luar') {
                    $pDinasLuar++;
                } elseif (in_array($st, ['izin', 'sakit'])) {
                    $pIzinSakit++;
                } elseif ($st === 'alpa') {
                    $pAlpa++;
                }
            }

            $pTotalEntries = max(1, $pHadirTepat + $pHadirTerlambat + $pDinasLuar + $pIzinSakit + $pAlpa);
            $pScore = round(
                (($pHadirTepat * 100) + ($pDinasLuar * 100) + ($pHadirTerlambat * 80) + ($pIzinSakit * 70)) / $pTotalEntries,
                1
            );

            if ($pScore >= 95 && $pAlpa === 0) {
                $predikat = 'Sangat Baik (A)';
                $rekomendasi = 'Kepatuhan Prima (Layak Insentif)';
            } elseif ($pScore >= 85 && $pAlpa === 0) {
                $predikat = 'Baik (B)';
                $rekomendasi = 'Disiplin Memenuhi Standar';
            } elseif ($pScore >= 75) {
                $predikat = 'Cukup (C)';
                $rekomendasi = 'Perlu Peningkatan Disiplin Waktu';
            } else {
                $predikat = 'Perlu Pembinaan (D)';
                $rekomendasi = 'Perlu Konseling / Pembinaan Kades';
            }

            $avgJamP = $pCountDurasi > 0 ? round(($pTotalMenitKerja / $pCountDurasi) / 60, 1) : 7.5;

            $employeeMatrix[] = [
                'pegawai'          => $p,
                'hadir_tepat'      => $pHadirTepat,
                'hadir_terlambat'  => $pHadirTerlambat,
                'menit_terlambat'  => $pMenitTerlambat,
                'dinas_luar'       => $pDinasLuar,
                'izin_sakit'       => $pIzinSakit,
                'alpa'             => $pAlpa,
                'total_kehadiran'  => $pHadirTepat + $pHadirTerlambat + $pDinasLuar,
                'avg_jam_kerja'    => $avgJamP,
                'skor'             => $pScore,
                'predikat'         => $predikat,
                'rekomendasi'      => $rekomendasi,
            ];
        }

        $namaBulan = $bulanInt ? Carbon::create()->month($bulanInt)->translatedFormat('F') : 'Setahun Penuh';
        $nomorLaporan = sprintf('800.1/%s/PEM-DS/%s/%d', $bulanInt ? str_pad($bulanInt, 2, '0', STR_PAD_LEFT) : 'THN', date('m'), $tahun);
        $kades  = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KADES'))->first();
        $sekdes = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'SEKDES'))->first();

        $pdf = Pdf::loadView('reports.laporan-analitik-pdf', compact(
            'tahun', 'namaBulan', 'bulanInt', 'nomorLaporan',
            'skorIKK', 'persenTepatWaktu', 'avgJamKerja', 'totalMenitTerlambat', 'totalTerlambat', 'totalHadir',
            'totalPiketHadir', 'totalPiketJadwal', 'piketRate',
            'employeeMatrix', 'kades', 'sekdes'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Analitik_Kedisiplinan_{$namaBulan}_{$tahun}.pdf";
        return $pdf->stream($filename);
    }
}
