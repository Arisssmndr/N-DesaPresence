<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Models\AuditLog;
use App\Models\Pengumuman;
use Carbon\Carbon;

class Dashboard extends Component
{
    public bool $showLiveIndicator = true;
    public ?string $lastTapInfo = null;

    protected $listeners = ['scanReceived' => 'handleScanReceived'];

    public function handleScanReceived($data)
    {
        $this->lastTapInfo = $data['nama'] ?? 'Tap sidik jari baru diterima';
        $this->dispatch('show-toast', message: $this->lastTapInfo);
    }

    public function render()
    {
        $today = Carbon::today()->toDateString();
        $totalPegawai = Pegawai::where('status_aktif', true)->count();
        $kehadiranHariIni = Kehadiran::where('tanggal', $today)->get();

        $hadirCount = $kehadiranHariIni->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat'])->count();
        $izinSakitCount = $kehadiranHariIni->whereIn('status', ['Izin', 'Sakit'])->count();
        $dinasLuarCount = $kehadiranHariIni->where('status', 'Dinas Luar')->count();
        $alpaCount = $kehadiranHariIni->where('status', 'Alpa')->count();
        $belumMasukCount = max(0, $totalPegawai - $kehadiranHariIni->count());
        $persenHadir = $totalPegawai > 0 ? round(($hadirCount / $totalPegawai) * 100) : 0;

        return view('livewire.dashboard', [
            'statistik' => [
                'totalPegawai' => $totalPegawai,
                'hadir' => $hadirCount,
                'izinSakit' => $izinSakitCount,
                'dinasLuar' => $dinasLuarCount,
                'alpa' => $alpaCount,
                'belumMasuk' => $belumMasukCount,
                'persenHadir' => $persenHadir,
            ],
            'listAbsenHariIni' => Kehadiran::with('pegawai.jabatan')
                ->where('tanggal', $today)
                ->latest('updated_at')
                ->take(15)
                ->get(),
            'auditLogs' => AuditLog::latest()->take(5)->get(),
            'pengumumans' => Pengumuman::where('is_pinned', true)
                ->orWhere(fn($q) => $q->whereDate('berlaku_hingga', '>=', $today))
                ->orderByDesc('is_pinned')
                ->latest()
                ->take(3)
                ->get(),
            'matrixDays' => range(1, Carbon::now()->daysInMonth),
        ])->layout('layouts.app', ['title' => 'Dashboard — Presence Desa']);
    }
}
