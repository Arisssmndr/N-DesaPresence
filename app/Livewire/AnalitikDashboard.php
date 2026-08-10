<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use Carbon\Carbon;

class AnalitikDashboard extends Component
{
    public function render()
    {
        $year = date('Y');

        $monthlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create()->month($m)->translatedFormat('M');
            $hadir = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('status', 'Tepat Waktu')->count();
            $terlambat = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('status', 'Terlambat')->count();
            $alpa = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('status', 'Alpa')->count();

            $monthlyStats[] = [
                'bulan' => $monthName,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'alpa' => $alpa,
            ];
        }

        // Top 5 Disciplined Employees (most Tepat Waktu)
        $topDisciplined = Pegawai::withCount(['kehadirans as tepat_count' => function ($q) use ($year) {
            $q->whereYear('tanggal', $year)->where('status', 'Tepat Waktu');
        }])
        ->orderByDesc('tepat_count')
        ->take(5)
        ->get();

        // Top 5 Most Late Employees
        $topLate = Pegawai::withCount(['kehadirans as terlambat_count' => function ($q) use ($year) {
            $q->whereYear('tanggal', $year)->where('status', 'Terlambat');
        }])
        ->orderByDesc('terlambat_count')
        ->take(5)
        ->get();

        return view('livewire.analitik-dashboard', [
            'monthlyStats' => $monthlyStats,
            'topDisciplined' => $topDisciplined,
            'topLate' => $topLate,
        ])->layout('layouts.app', ['title' => 'Analitik Presensi — Presence Desa']);
    }
}
