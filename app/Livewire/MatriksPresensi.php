<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Models\IzinSakit;
use App\Models\SuratPerintahTugas;
use App\Services\KalenderNasionalService;
use Carbon\Carbon;

class MatriksPresensi extends Component
{
    public int $bulan = 8;
    public int $tahun = 2026;

    public function mount()
    {
        $this->bulan = (int) date('m');
        $this->tahun = (int) date('Y');
    }

    public function render(KalenderNasionalService $kalenderService)
    {
        $firstDay = Carbon::createFromDate($this->tahun, $this->bulan, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $todayStr = Carbon::today()->toDateString();

        $pegawais = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get();

        $startMonth = sprintf('%04d-%02d-01', $this->tahun, $this->bulan);
        $endMonth = sprintf('%04d-%02d-%02d', $this->tahun, $this->bulan, $daysInMonth);

        // 1. Ambil Hari Libur dari Database
        $hariLibursDb = HariLibur::whereBetween('tanggal', [$startMonth, $endMonth])
            ->get()
            ->mapWithKeys(function ($h) {
                $tglStr = is_string($h->tanggal) ? substr($h->tanggal, 0, 10) : $h->tanggal->format('Y-m-d');
                return [$tglStr => $h->nama_hari_libur];
            })
            ->toArray();

        // 2. Gabungkan dengan Hari Libur Resmi dari Kalender Nasional Service (API Caching)
        $kalenderData = $kalenderService->getKalenderBulan($this->tahun, $this->bulan);
        foreach ($kalenderData['libur'] as $tglStr => $info) {
            if (!isset($hariLibursDb[$tglStr])) {
                $hariLibursDb[$tglStr] = $info['nama'];
            }
        }

        // 3. Ambil semua Data Kehadiran pegawai bulan ini dari Database
        $semuaKehadiran = Kehadiran::whereBetween('tanggal', [$startMonth, $endMonth])
            ->get()
            ->groupBy('pegawai_id');

        // 4. Ambil data Izin/Sakit yang disetujui
        $semuaIzin = IzinSakit::where('status', 'disetujui')
            ->where(function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('tanggal_mulai', [$startMonth, $endMonth])
                  ->orWhereBetween('tanggal_selesai', [$startMonth, $endMonth]);
            })
            ->get()
            ->groupBy('pegawai_id');

        // 5. Ambil data SPT (Dinas Luar) yang disetujui
        $semuaSpt = SuratPerintahTugas::where('status', 'disetujui')
            ->where(function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('tanggal_mulai', [$startMonth, $endMonth])
                  ->orWhereBetween('tanggal_selesai', [$startMonth, $endMonth]);
            })
            ->get()
            ->groupBy('pegawai_id');

        $attendanceMatrix = [];
        $summary = [];

        foreach ($pegawais as $p) {
            $kehadirans = $semuaKehadiran->get($p->id, collect())->keyBy(function ($k) {
                return is_string($k->tanggal) ? substr($k->tanggal, 0, 10) : $k->tanggal->format('Y-m-d');
            });

            $izinList = $semuaIzin->get($p->id, collect());
            $sptList = $semuaSpt->get($p->id, collect());

            $pSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $this->tahun, $this->bulan, $d);
                $dt = Carbon::createFromDate($this->tahun, $this->bulan, $d);

                $isWeekend = $dt->isWeekend();
                $isHoliday = isset($hariLibursDb[$dateStr]) || $isWeekend;
                $isFuture = ($dateStr > $todayStr);
                $isToday = ($dateStr === $todayStr);

                // Cek data kehadiran database terlebih dahulu
                if (isset($kehadirans[$dateStr])) {
                    $status = $kehadirans[$dateStr]->status;
                    $code = match ($status) {
                        'Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar' => 'H',
                        'Izin'                                            => 'I',
                        'Sakit'                                           => 'S',
                        default                                           => 'A',
                    };
                } elseif ($izinObj = $this->getApprovedIzin($izinList, $dateStr)) {
                    $code = str_contains(strtolower($izinObj->jenis ?? ''), 'sakit') ? 'S' : 'I';
                } elseif ($this->checkApprovedSpt($sptList, $dateStr)) {
                    $code = 'H';
                } elseif ($isHoliday) {
                    $code = 'L';
                } elseif ($isFuture || $isToday) {
                    // Tanggal belum terjadi / hari ini belum scan presensi
                    $code = '-';
                } else {
                    // Hari kerja lampau yang tidak memiliki scan presensi
                    $code = 'A';
                }

                $attendanceMatrix[$p->id][$d] = $code;

                // Hanya hitung ke summary jika bukan hari belum berjalan (-)
                if (isset($pSummary[$code])) {
                    $pSummary[$code]++;
                }
            }

            $summary[$p->id] = $pSummary;
        }

        return view('livewire.matriks-presensi', [
            'daysInMonth' => $daysInMonth,
            'pegawais'    => $pegawais,
            'matrix'      => $attendanceMatrix,
            'summary'     => $summary,
            'todayStr'    => $todayStr,
        ])->layout('layouts.app', ['title' => 'Buku Matriks Presensi — Presence Desa']);
    }

    private function getApprovedIzin($izinList, string $dateStr): ?object
    {
        foreach ($izinList as $izin) {
            $mulai = is_string($izin->tanggal_mulai) ? substr($izin->tanggal_mulai, 0, 10) : $izin->tanggal_mulai->format('Y-m-d');
            $selesai = is_string($izin->tanggal_selesai) ? substr($izin->tanggal_selesai, 0, 10) : $izin->tanggal_selesai->format('Y-m-d');
            if ($dateStr >= $mulai && $dateStr <= $selesai) {
                return $izin;
            }
        }
        return null;
    }

    private function checkApprovedSpt($sptList, string $dateStr): bool
    {
        foreach ($sptList as $spt) {
            $mulai = is_string($spt->tanggal_mulai) ? substr($spt->tanggal_mulai, 0, 10) : $spt->tanggal_mulai->format('Y-m-d');
            $selesai = is_string($spt->tanggal_selesai) ? substr($spt->tanggal_selesai, 0, 10) : $spt->tanggal_selesai->format('Y-m-d');
            if ($dateStr >= $mulai && $dateStr <= $selesai) {
                return true;
            }
        }
        return false;
    }
}
