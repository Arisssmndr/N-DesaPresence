<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Models\IzinSakit;
use App\Models\SuratPerintahTugas;
use App\Services\KalenderNasionalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SpjReportController extends Controller
{
    public function downloadPdf(Request $request, KalenderNasionalService $kalenderService)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $firstDay = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $namaBulan = $firstDay->translatedFormat('F');
        $todayStr = Carbon::today()->toDateString();

        $pegawais = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get();

        $startMonth = sprintf('%04d-%02d-01', $tahun, $bulan);
        $endMonth = sprintf('%04d-%02d-%02d', $tahun, $bulan, $daysInMonth);

        // Ambil hari libur DB & API
        $hariLibursDb = HariLibur::whereBetween('tanggal', [$startMonth, $endMonth])
            ->get()
            ->mapWithKeys(fn($h) => [(is_string($h->tanggal) ? substr($h->tanggal, 0, 10) : $h->tanggal->format('Y-m-d')) => $h->nama_hari_libur])
            ->toArray();

        $kalenderData = $kalenderService->getKalenderBulan($tahun, $bulan);
        foreach ($kalenderData['libur'] as $tglStr => $info) {
            if (!isset($hariLibursDb[$tglStr])) {
                $hariLibursDb[$tglStr] = $info['nama'];
            }
        }

        $semuaKehadiran = Kehadiran::whereBetween('tanggal', [$startMonth, $endMonth])
            ->get()
            ->groupBy('pegawai_id');

        $semuaIzin = IzinSakit::where('status', 'disetujui')
            ->where(function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('tanggal_mulai', [$startMonth, $endMonth])
                  ->orWhereBetween('tanggal_selesai', [$startMonth, $endMonth]);
            })
            ->get()
            ->groupBy('pegawai_id');

        $semuaSpt = SuratPerintahTugas::where('status', 'disetujui')
            ->where(function ($q) use ($startMonth, $endMonth) {
                $q->whereBetween('tanggal_mulai', [$startMonth, $endMonth])
                  ->orWhereBetween('tanggal_selesai', [$startMonth, $endMonth]);
            })
            ->get()
            ->groupBy('pegawai_id');

        $matrix = [];
        $summary = [];

        foreach ($pegawais as $p) {
            $kehadirans = $semuaKehadiran->get($p->id, collect())->keyBy(fn($k) => (is_string($k->tanggal) ? substr($k->tanggal, 0, 10) : $k->tanggal->format('Y-m-d')));
            $izinList = $semuaIzin->get($p->id, collect());
            $sptList = $semuaSpt->get($p->id, collect());

            $pSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
                $dt = Carbon::createFromDate($tahun, $bulan, $d);

                $isWeekend = $dt->isWeekend();
                $isHoliday = isset($hariLibursDb[$dateStr]) || $isWeekend;
                $isFuture = ($dateStr > $todayStr);
                $isToday = ($dateStr === $todayStr);

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
                    $code = '-';
                } else {
                    $code = 'A';
                }

                $matrix[$p->id][$d] = $code;
                if (isset($pSummary[$code])) {
                    $pSummary[$code]++;
                }
            }

            $summary[$p->id] = $pSummary;
        }

        $kades = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KADES'))->first();
        $sekdes = Pegawai::whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'SEKDES'))->first();

        $pdf = Pdf::loadView('reports.spj-pdf', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'daysInMonth' => $daysInMonth,
            'pegawais' => $pegawais,
            'matrix' => $matrix,
            'summary' => $summary,
            'kades' => $kades,
            'sekdes' => $sekdes,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("SPJ_Presensi_Desa_Nangtang_{$namaBulan}_{$tahun}.pdf");
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
