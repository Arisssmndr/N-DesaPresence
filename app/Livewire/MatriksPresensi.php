<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
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

    public function render()
    {
        $daysInMonth = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;
        $pegawais = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $hariLiburs = HariLibur::whereYear('tanggal', $this->tahun)->whereMonth('tanggal', $this->bulan)->pluck('tanggal')->map(fn($t) => $t->format('Y-m-d'))->toArray();

        $attendanceMatrix = [];
        $summary = [];

        foreach ($pegawais as $p) {
            $kehadirans = Kehadiran::where('pegawai_id', $p->id)
                ->whereYear('tanggal', $this->tahun)
                ->whereMonth('tanggal', $this->bulan)
                ->get()
                ->keyBy(fn($k) => $k->tanggal->format('Y-m-d'));

            $pSummary = ['H' => 0, 'A' => 0, 'I' => 0, 'D' => 0, 'L' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf("%04d-%02d-%02d", $this->tahun, $this->bulan, $d);
                $dt = Carbon::createFromDate($this->tahun, $this->bulan, $d);

                if (isset($kehadirans[$dateStr])) {
                    $status = $kehadirans[$dateStr]->status;
                    $code = match ($status) {
                        'Hadir', 'Tepat Waktu', 'Terlambat' => 'H',
                        'Izin', 'Sakit' => 'I',
                        'Dinas Luar' => 'D',
                        default => 'A',
                    };
                } elseif ($dt->isWeekend() || in_array($dateStr, $hariLiburs)) {
                    $code = 'L';
                } else {
                    $code = 'A';
                }

                $attendanceMatrix[$p->id][$d] = $code;
                $pSummary[$code] = ($pSummary[$code] ?? 0) + 1;
            }

            $summary[$p->id] = $pSummary;
        }

        return view('livewire.matriks-presensi', [
            'daysInMonth' => $daysInMonth,
            'pegawais' => $pegawais,
            'matrix' => $attendanceMatrix,
            'summary' => $summary,
        ])->layout('layouts.app', ['title' => 'Buku Matriks Presensi — Presence Desa']);
    }
}
