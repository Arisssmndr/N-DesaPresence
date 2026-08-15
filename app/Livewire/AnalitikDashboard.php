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
            $hadir = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat'])->count();
            $alpa = Kehadiran::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('status', 'Alpa')->count();

            $monthlyStats[] = [
                'bulan' => $monthName,
                'hadir' => $hadir,
                'alpa' => $alpa,
            ];
        }

        // Top 5 Disciplined Employees (most Hadir)
        $topDisciplined = Pegawai::withCount(['kehadirans as hadir_count' => function ($q) use ($year) {
            $q->whereYear('tanggal', $year)->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat']);
        }])
        ->orderByDesc('hadir_count')
        ->take(5)
        ->get();

        return view('livewire.analitik-dashboard', [
            'monthlyStats' => $monthlyStats,
            'topDisciplined' => $topDisciplined,
        ])->layout('layouts.app', ['title' => 'Analitik Presensi — Presence Desa']);
    }
}
