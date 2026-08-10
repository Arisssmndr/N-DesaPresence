<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SpjReportController extends Controller
{
    public function downloadPdf(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $pegawais = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $hariLiburs = HariLibur::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->pluck('tanggal')->map(fn($t) => $t->format('Y-m-d'))->toArray();

        $matrix = [];
        $summary = [];

        foreach ($pegawais as $p) {
            $kehadirans = Kehadiran::where('pegawai_id', $p->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->get()
                ->keyBy(fn($k) => $k->tanggal->format('Y-m-d'));

            $pSummary = ['H' => 0, 'T' => 0, 'A' => 0, 'I' => 0, 'D' => 0, 'L' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
                $dt = Carbon::createFromDate($tahun, $bulan, $d);

                if (isset($kehadirans[$dateStr])) {
                    $status = $kehadirans[$dateStr]->status;
                    $code = match ($status) {
                        'Tepat Waktu' => 'H',
                        'Terlambat' => 'T',
                        'Izin', 'Sakit' => 'I',
                        'Dinas Luar' => 'D',
                        default => 'A',
                    };
                } elseif ($dt->isWeekend() || in_array($dateStr, $hariLiburs)) {
                    $code = 'L';
                } else {
                    $code = 'A';
                }

                $matrix[$p->id][$d] = $code;
                $pSummary[$code] = ($pSummary[$code] ?? 0) + 1;
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
}
