<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PengajuanAbsenLuar;
use App\Models\IzinSakit;
use App\Models\SuratPerintahTugas;

class AdminNotificationCenter extends Component
{
    public bool $isOpen = false;

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeDropdown()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        // 1. Pengajuan Absen Luar (Menunggu)
        $pengajuanLuars = PengajuanAbsenLuar::with(['pegawai.jabatan'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(6)
            ->get();

        // 2. Pengajuan Izin & Sakit (Menunggu)
        $izinSakits = IzinSakit::with(['pegawai.jabatan'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(6)
            ->get();

        // 3. SPT Kedinasan (Diajukan / Menunggu Approval)
        $spts = SuratPerintahTugas::with(['pegawai.jabatan'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(6)
            ->get();

        $totalPengajuanLuar = PengajuanAbsenLuar::where('status', 'menunggu')->count();
        $totalIzinSakit     = IzinSakit::where('status', 'menunggu')->count();
        $totalSpt           = SuratPerintahTugas::where('status', 'diajukan')->count();

        $totalCount = $totalPengajuanLuar + $totalIzinSakit + $totalSpt;

        return view('livewire.admin-notification-center', [
            'totalCount'         => $totalCount,
            'totalPengajuanLuar' => $totalPengajuanLuar,
            'totalIzinSakit'     => $totalIzinSakit,
            'totalSpt'           => $totalSpt,
            'pengajuanLuars'     => $pengajuanLuars,
            'izinSakits'         => $izinSakits,
            'spts'               => $spts,
        ]);
    }
}
