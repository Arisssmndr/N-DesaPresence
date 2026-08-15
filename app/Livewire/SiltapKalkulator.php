<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\KonfigurasiSiltap;
use App\Models\RekapSiltap;
use App\Models\AuditLog;
use Carbon\Carbon;

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
        $pegawais = Pegawai::with('jabatan')->where('status_aktif', true)->get();
        $daysInMonth = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;

        foreach ($pegawais as $p) {
            $kehadirans = Kehadiran::where('pegawai_id', $p->id)
                ->whereYear('tanggal', $this->tahun)
                ->whereMonth('tanggal', $this->bulan)
                ->get();

            $totalHadir = $kehadirans->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat'])->count();
            $totalAlpa = $kehadirans->where('status', 'Alpa')->count();
            $totalIzin = $kehadirans->whereIn('status', ['Izin', 'Sakit'])->count();
            $totalDinasLuar = $kehadirans->where('status', 'Dinas Luar')->count();

            $config = KonfigurasiSiltap::where('jabatan_id', $p->jabatan_id)->first();
            $bruto = $p->siltap_bruto > 0 ? (float) $p->siltap_bruto : ($config?->nominal_siltap ?? 2025000);
            $potonganAlpaPerHari = $config?->nilai_potongan_alpa ?? 100000;

            $totalPotonganAlpa = $totalAlpa * $potonganAlpaPerHari;
            $neto = max(0, $bruto - $totalPotonganAlpa);

            RekapSiltap::updateOrCreate(
                ['pegawai_id' => $p->id, 'bulan' => $this->bulan, 'tahun' => $this->tahun],
                [
                    'total_hari_kerja' => $daysInMonth,
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

        session()->flash('success', "Rekapitulasi dan kalkulasi Siltap bulan {$this->bulan}/{$this->tahun} berhasil diproses.");
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
