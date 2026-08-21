<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Models\KonfigurasiSiltap;
use App\Models\RekapSiltap;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SiltapKalkulator extends Component
{
    public int $bulan = 8;
    public int $tahun = 2026;

    public function mount()
    {
        $this->bulan = (int) date('m');
        $this->tahun = (int) date('Y');
    }

    public function generateRekap()
    {
        $currentPeriod = Carbon::now()->startOfMonth();
        $targetPeriod = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();

        if ($targetPeriod->greaterThan($currentPeriod)) {
            $err = 'Tidak dapat mengkalkulasi Siltap untuk bulan di masa mendatang.';
            session()->flash('error', $err);
            $this->dispatch('notify', message: $err, type: 'error');
            return;
        }

        DB::transaction(function () {
            $pegawais = Pegawai::with('jabatan')->where('status_aktif', true)->get();
            $pegawaiIds = $pegawais->pluck('id')->toArray();

            $carbonBulan = Carbon::createFromDate($this->tahun, $this->bulan, 1);
            $daysInMonth = $carbonBulan->daysInMonth;

            $hariLiburs = HariLibur::whereYear('tanggal', $this->tahun)
                ->whereMonth('tanggal', $this->bulan)
                ->pluck('tanggal')
                ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
                ->flip()
                ->toArray();

            $totalHariKerja = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dt = Carbon::createFromDate($this->tahun, $this->bulan, $d);
                $dateStr = $dt->format('Y-m-d');
                if (!$dt->isWeekend() && !isset($hariLiburs[$dateStr])) {
                    $totalHariKerja++;
                }
            }

            // Batch pre-fetch all Siltap configs & Kehadiran records
            $configMap = KonfigurasiSiltap::all()->keyBy('jabatan_id');
            $kehadiranMap = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
                ->whereYear('tanggal', $this->tahun)
                ->whereMonth('tanggal', $this->bulan)
                ->get()
                ->groupBy('pegawai_id');

            foreach ($pegawais as $p) {
                $kehadirans = $kehadiranMap->get($p->id, collect());

                $totalHadir = $kehadirans->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat'])->count();
                $totalAlpa = $kehadirans->where('status', 'Alpa')->count();
                $totalIzin = $kehadirans->whereIn('status', ['Izin', 'Sakit'])->count();
                $totalDinasLuar = $kehadirans->where('status', 'Dinas Luar')->count();

                $config = $configMap->get($p->jabatan_id);
                $bruto = $p->siltap_bruto > 0 ? (float) $p->siltap_bruto : ($config?->nominal_siltap ?? 2025000);
                $potonganAlpaPerHari = $config?->nilai_potongan_alpa ?? 100000;

                $totalPotonganAlpa = $totalAlpa * $potonganAlpaPerHari;
                $neto = max(0, $bruto - $totalPotonganAlpa);

                RekapSiltap::updateOrCreate(
                    ['pegawai_id' => $p->id, 'bulan' => $this->bulan, 'tahun' => $this->tahun],
                    [
                        'total_hari_kerja' => $totalHariKerja,
                        'total_hadir' => $totalHadir,
                        'total_terlambat' => 0,
                        'total_alpa' => $totalAlpa,
                        'total_izin' => $totalIzin,
                        'total_dinas_luar' => $totalDinasLuar,
                        'total_menit_terlambat' => 0,
                        'siltap_bruto' => $bruto,
                        'potongan_alpa' => $totalPotonganAlpa,
                        'potongan_terlambat' => 0,
                        'siltap_neto' => $neto,
                        'status' => 'draft',
                    ]
                );
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Kalkulasi ulang Rekap Siltap bulan {$this->bulan}/{$this->tahun}",
                'modul' => 'Kalkulasi Siltap',
            ]);
        });

        $msg = "Rekapitulasi dan kalkulasi Siltap bulan {$this->bulan}/{$this->tahun} berhasil diproses.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
        $this->dispatch('refresh-notifications');
    }

    public function render()
    {
        $rekaps = RekapSiltap::with(['pegawai.jabatan'])
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->get();

        return view('livewire.siltap-kalkulator', [
            'rekaps' => $rekaps,
        ])->layout('layouts.app', ['title' => 'Kalkulasi Siltap — Presence Desa']);
    }
}
