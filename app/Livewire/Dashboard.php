<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use App\Models\AuditLog;
use App\Services\KalenderNasionalService;
use Carbon\Carbon;

class Dashboard extends Component
{
    public bool $showLiveIndicator = true;
    public int $kalenderBulan;
    public int $kalenderTahun;

    public function mount()
    {
        $this->kalenderBulan = (int) date('m');
        $this->kalenderTahun = (int) date('Y');
    }

    public function nextMonth()
    {
        $dt = Carbon::createFromDate($this->kalenderTahun, $this->kalenderBulan, 1)->addMonth();
        $this->kalenderBulan = (int) $dt->format('m');
        $this->kalenderTahun = (int) $dt->format('Y');
    }

    public function prevMonth()
    {
        $dt = Carbon::createFromDate($this->kalenderTahun, $this->kalenderBulan, 1)->subMonth();
        $this->kalenderBulan = (int) $dt->format('m');
        $this->kalenderTahun = (int) $dt->format('Y');
    }

    public function resetToToday()
    {
        $this->kalenderBulan = (int) date('m');
        $this->kalenderTahun = (int) date('Y');
    }

    public function render(KalenderNasionalService $kalenderService)
    {
        $today = Carbon::today()->toDateString();
        $totalPegawai = Pegawai::where('status_aktif', true)->count();
        $kehadiranHariIni = Kehadiran::with('pegawai.jabatan')
            ->whereDate('tanggal', $today)
            ->get();

        $hadirCount = $kehadiranHariIni->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat', 'Dinas Luar'])->count();
        $izinCount = $kehadiranHariIni->where('status', 'Izin')->count();
        $sakitCount = $kehadiranHariIni->where('status', 'Sakit')->count();
        $alpaCount = $kehadiranHariIni->where('status', 'Alpa')->count();
        $belumMasukCount = max(0, $totalPegawai - $kehadiranHariIni->count());
        $persenHadir = $totalPegawai > 0 ? round(($hadirCount / $totalPegawai) * 100) : 0;

        // Data Kalender Nasional RI
        $kalenderData = $kalenderService->getKalenderBulan($this->kalenderTahun, $this->kalenderBulan);
        $liburMap = $kalenderData['libur'];
        $peringatanMap = $kalenderData['peringatan'];

        $firstDayOfMonth = Carbon::createFromDate($this->kalenderTahun, $this->kalenderBulan, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1 = Senin, 7 = Minggu

        $calendarGrid = [];
        // Padding hari sebelum tanggal 1
        for ($pad = 1; $pad < $startDayOfWeek; $pad++) {
            $calendarGrid[] = null;
        }

        // Tanggal 1 sampai akhir bulan
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateObj = Carbon::createFromDate($this->kalenderTahun, $this->kalenderBulan, $d);
            $dateStr = $dateObj->format('Y-m-d');
            $isWeekend = $dateObj->isWeekend();
            $isToday = ($dateStr === $today);

            $liburInfo = $liburMap[$dateStr] ?? null;
            $peringatanInfo = $peringatanMap[$dateStr] ?? null;

            $calendarGrid[] = [
                'day'            => $d,
                'date'           => $dateStr,
                'isToday'        => $isToday,
                'isWeekend'      => $isWeekend,
                'isLibur'        => $isWeekend || !empty($liburInfo),
                'liburInfo'      => $liburInfo,
                'peringatanInfo' => $peringatanInfo,
                'keterangan'     => $liburInfo['nama'] ?? ($peringatanInfo['nama'] ?? null),
            ];
        }

        // Agenda libur & peringatan bulan ini
        $agendaBulanIni = [];
        foreach ($liburMap as $dateStr => $info) {
            $agendaBulanIni[$dateStr] = [
                'tanggal' => $dateStr,
                'tgl'     => (int) substr($dateStr, 8, 2),
                'nama'    => $info['nama'],
                'tipe'    => $info['jenis'] === 'cuti_bersama' ? 'Cuti Bersama' : 'Libur Nasional',
                'badge'   => $info['jenis'] === 'cuti_bersama' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800',
            ];
        }

        foreach ($peringatanMap as $dateStr => $info) {
            if (!isset($agendaBulanIni[$dateStr])) {
                $agendaBulanIni[$dateStr] = [
                    'tanggal' => $dateStr,
                    'tgl'     => (int) substr($dateStr, 8, 2),
                    'nama'    => $info['nama'],
                    'tipe'    => 'Hari Besar RI',
                    'badge'   => 'bg-emerald-100 text-emerald-800',
                ];
            }
        }

        ksort($agendaBulanIni);

        return view('livewire.dashboard', [
            'statistik' => [
                'totalPegawai' => $totalPegawai,
                'hadir'        => $hadirCount,
                'izin'         => $izinCount,
                'sakit'        => $sakitCount,
                'alpa'         => $alpaCount,
                'belumMasuk'   => $belumMasukCount,
                'persenHadir'  => $persenHadir,
            ],
            'listAbsenHariIni' => $kehadiranHariIni->sortByDesc('updated_at')->take(15),
            'auditLogs'        => AuditLog::latest()->take(5)->get(),
            'calendarGrid'     => $calendarGrid,
            'namaBulanTahun'   => $firstDayOfMonth->translatedFormat('F Y'),
            'agendaBulanIni'   => array_values($agendaBulanIni),
        ])->layout('layouts.app', ['title' => 'Dashboard — Presence Desa']);
    }
}
