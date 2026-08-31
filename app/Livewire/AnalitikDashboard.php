<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Models\IzinSakit;
use App\Models\PengajuanAbsenLuar;
use App\Models\JadwalPiket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalitikDashboard extends Component
{
    public int $selectedYear;
    public string $selectedMonth = ''; // '' = Setahun Penuh, '1'-'12' = Bulan Spesifik
    public string $searchPegawai = '';
    public string $filterKategori = 'all'; // 'all', 'prima', 'warning', 'alpa'

    public function mount()
    {
        $this->selectedYear = (int) date('Y');
        $this->selectedMonth = (string) (int) date('n'); // Default to current month
    }

    public function setMonth(string $month)
    {
        $this->selectedMonth = $month;
    }

    public function setYear(int $year)
    {
        $this->selectedYear = $year;
    }

    public function render()
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;

        // Base Query untuk Kehadiran
        $baseQuery = Kehadiran::query()->whereYear('tanggal', $year);
        if ($month) {
            $baseQuery->whereMonth('tanggal', $month);
        }

        // ═════════════════════════════════════════════════════════════════════
        // 1. STATISTIK UTAMA / KPI KEDISIPLINAN NASIONAL
        // ═════════════════════════════════════════════════════════════════════
        $allKehadiran = (clone $baseQuery)->get();
        $totalRecord = $allKehadiran->count();

        $totalHadir = 0;
        $totalTepatWaktu = 0;
        $totalTerlambat = 0;
        $totalMenitTerlambat = 0;
        $totalDinasLuar = 0;
        $totalIzinSakit = 0;
        $totalAlpa = 0;
        $totalDurasiMenit = 0;
        $durasiCount = 0;

        // Distribusi Jam Masuk
        $earlyCount = 0;   // < 07:30
        $onTimeCount = 0;  // 07:30 - 08:00
        $graceCount = 0;   // 08:01 - 08:15
        $lateCount = 0;    // > 08:15

        foreach ($allKehadiran as $k) {
            $st = strtolower($k->status);

            if (in_array($st, ['hadir', 'tepat waktu', 'terlambat'])) {
                $totalHadir++;

                if ($k->durasi_kerja_menit > 0) {
                    $totalDurasiMenit += $k->durasi_kerja_menit;
                    $durasiCount++;
                }

                $menitTelat = $k->terlambat_menit;
                if ($k->jam_masuk) {
                    $jamClean = substr($k->jam_masuk, 0, 5);
                    if ($jamClean < '07:30') {
                        $earlyCount++;
                    } elseif ($jamClean <= '08:00') {
                        $onTimeCount++;
                    } elseif ($jamClean <= '08:15') {
                        $graceCount++;
                    } else {
                        $lateCount++;
                    }

                    if (!$menitTelat && $jamClean > '08:00') {
                        $masukC = Carbon::createFromTimeString($jamClean);
                        $standarC = Carbon::createFromTimeString('08:00');
                        if ($masukC->greaterThan($standarC)) {
                            $menitTelat = $standarC->diffInMinutes($masukC);
                        }
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
                $onTimeCount++;
            } elseif (in_array($st, ['izin', 'sakit'])) {
                $totalIzinSakit++;
            } elseif ($st === 'alpa') {
                $totalAlpa++;
            }
        }

        // Skor Indeks Kedisiplinan Kerja (IKK)
        $totalEntriValid = max(1, $totalHadir + $totalIzinSakit + $totalAlpa);
        $skorIKK = round(
            (($totalTepatWaktu * 100) + ($totalDinasLuar * 100) + ($totalTerlambat * 80) + ($totalIzinSakit * 70)) / $totalEntriValid,
            1
        );

        $persenTepatWaktu = $totalHadir > 0 ? round(($totalTepatWaktu + $totalDinasLuar) / $totalHadir * 100, 1) : 100;
        $avgJamKerja = $durasiCount > 0 ? round(($totalDurasiMenit / $durasiCount) / 60, 1) : 7.5;

        // ═════════════════════════════════════════════════════════════════════
        // 2. TREN BULANAN KINERJA (12 BULAN)
        // ═════════════════════════════════════════════════════════════════════
        $monthlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create()->month($m)->translatedFormat('M');
            $mHadir = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar'])->count();
            $mIzin = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->whereIn('status', ['Izin', 'Sakit'])->count();
            $mAlpa = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('status', 'Alpa')->count();

            $mTotal = max(1, $mHadir + $mIzin + $mAlpa);
            $mPersen = round(($mHadir / $mTotal) * 100, 1);

            $monthlyStats[] = [
                'bulan_num' => $m,
                'bulan'     => $monthName,
                'hadir'     => $mHadir,
                'izin'      => $mIzin,
                'alpa'      => $mAlpa,
                'persen'    => $mPersen,
            ];
        }

        // ═════════════════════════════════════════════════════════════════════
        // 3. MATRIKS EVALUASI KEDISIPLINAN INDIVIDU SELURUH PEGAWAI (14 ORANG)
        // ═════════════════════════════════════════════════════════════════════
        $pegawais = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->when($this->searchPegawai, function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->searchPegawai . '%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        $employeeMatrix = [];
        foreach ($pegawais as $p) {
            $pQuery = Kehadiran::where('pegawai_id', $p->id)->whereYear('tanggal', $year);
            if ($month) {
                $pQuery->whereMonth('tanggal', $month);
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

            // Predikat Mutu
            if ($pScore >= 95 && $pAlpa === 0) {
                $predikat = 'Sangat Baik (A)';
                $predikatClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                $rekomendasi = 'Kepatuhan Prima (Layak Insentif/Reward)';
            } elseif ($pScore >= 85 && $pAlpa === 0) {
                $predikat = 'Baik (B)';
                $predikatClass = 'bg-teal-100 text-teal-800 border-teal-300';
                $rekomendasi = 'Disiplin Memenuhi Standar';
            } elseif ($pScore >= 75) {
                $predikat = 'Cukup (C)';
                $predikatClass = 'bg-amber-100 text-amber-800 border-amber-300';
                $rekomendasi = 'Perlu Peningkatan Ketepatan Jam Masuk';
            } else {
                $predikat = 'Perlu Pembinaan (D)';
                $predikatClass = 'bg-rose-100 text-rose-800 border-rose-300';
                $rekomendasi = 'Perlu Pembinaan Disiplin / Konseling Kades';
            }

            $avgJamP = $pCountDurasi > 0 ? round(($pTotalMenitKerja / $pCountDurasi) / 60, 1) : 7.5;

            $item = [
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
                'grade'            => $pScore >= 95 && $pAlpa === 0 ? 'A' : ($pScore >= 85 && $pAlpa === 0 ? 'B' : ($pScore >= 75 ? 'C' : 'D')),
                'predikat'         => $predikat,
                'predikat_class'   => $predikatClass,
                'rekomendasi'      => $rekomendasi,
            ];

            $allEmployeeMatrix[] = $item;
        }

        // Urutkan matriks keseluruhan berdasarkan Skor tertinggi
        usort($allEmployeeMatrix, fn($a, $b) => $b['skor'] <=> $a['skor']);

        // Top Performers & Warning List DIHITUNG DARI SELURUH PEGAWAI (tidak terpengaruh filter tabel)
        $topPerformers = array_slice(array_filter($allEmployeeMatrix, fn($e) => $e['alpa'] === 0 && $e['skor'] >= 85), 0, 3);
        $warningList   = array_values(array_filter($allEmployeeMatrix, fn($e) => $e['alpa'] > 0 || $e['menit_terlambat'] > 30 || $e['skor'] < 75));

        // Filter KHUSUS untuk Baris Tabel Kinerja
        $employeeMatrix = array_values(array_filter($allEmployeeMatrix, function ($row) {
            if ($this->filterKategori === 'grade_a') {
                return $row['grade'] === 'A';
            }
            if ($this->filterKategori === 'grade_b') {
                return $row['grade'] === 'B';
            }
            if ($this->filterKategori === 'grade_c') {
                return $row['grade'] === 'C';
            }
            if ($this->filterKategori === 'grade_d') {
                return $row['grade'] === 'D';
            }
            return true; // 'all'
        }));

        // ═════════════════════════════════════════════════════════════════════
        // 4. KEPATUHAN PIKET PELAYANAN DESA
        // ═════════════════════════════════════════════════════════════════════
        $piketQuery = JadwalPiket::whereYear('tanggal_piket', $year);
        if ($month) {
            $piketQuery->whereMonth('tanggal_piket', $month);
        }
        $totalPiketJadwal = (clone $piketQuery)->count();
        $totalPiketHadir = (clone $piketQuery)->where('status', 'hadir')->count();
        $piketRate = $totalPiketJadwal > 0 ? round(($totalPiketHadir / $totalPiketJadwal) * 100, 1) : 100;

        return view('livewire.analitik-dashboard', [
            'totalRecord'         => $totalRecord,
            'totalHadir'          => $totalHadir,
            'totalTepatWaktu'     => $totalTepatWaktu,
            'totalTerlambat'      => $totalTerlambat,
            'totalMenitTerlambat' => $totalMenitTerlambat,
            'totalDinasLuar'      => $totalDinasLuar,
            'totalIzinSakit'      => $totalIzinSakit,
            'totalAlpa'           => $totalAlpa,
            'skorIKK'             => $skorIKK,
            'persenTepatWaktu'    => $persenTepatWaktu,
            'avgJamKerja'         => $avgJamKerja,
            'earlyCount'          => $earlyCount,
            'onTimeCount'         => $onTimeCount,
            'graceCount'          => $graceCount,
            'lateCount'           => $lateCount,
            'monthlyStats'        => $monthlyStats,
            'employeeMatrix'      => $employeeMatrix,
            'topPerformers'       => $topPerformers,
            'warningList'         => $warningList,
            'totalPiketJadwal'    => $totalPiketJadwal,
            'totalPiketHadir'     => $totalPiketHadir,
            'piketRate'           => $piketRate,
            'monthName'           => $month ? Carbon::create()->month($month)->translatedFormat('F') : 'Setahun Penuh',
        ])->layout('layouts.app', ['title' => 'Analitik Kedisiplinan & Kinerja Aparatur — Presence Desa']);
    }
}
