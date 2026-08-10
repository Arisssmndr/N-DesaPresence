<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Kehadiran;
use App\Models\LogAbsensi;
use Carbon\Carbon;

class UpdateJamPulangJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $today = Carbon::today()->toDateString();

        $kehadirans = Kehadiran::with('pegawai')
            ->where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->get();

        foreach ($kehadirans as $k) {
            $lastScan = LogAbsensi::where('pin_fingerprint', $k->pegawai->pin_fingerprint)
                ->whereDate('waktu_scan', $today)
                ->orderByDesc('waktu_scan')
                ->first();

            if ($lastScan) {
                $jamScanTerakhir = Carbon::parse($lastScan->waktu_scan)->format('H:i:s');
                if ($jamScanTerakhir !== $k->jam_masuk && $jamScanTerakhir !== $k->jam_pulang) {
                    $k->update([
                        'jam_pulang' => $jamScanTerakhir,
                        'durasi_kerja_menit' => Carbon::parse($k->jam_masuk)->diffInMinutes(Carbon::parse($lastScan->waktu_scan)),
                    ]);
                }
            }
        }
    }
}
