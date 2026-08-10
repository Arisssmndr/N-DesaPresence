<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Models\SuratPerintahTugas;
use App\Models\IzinSakit;
use App\Models\AuditLog;
use Carbon\Carbon;

class MarkAbsentJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $today = Carbon::today();
        $tanggal = $today->toDateString();

        // 1. If today is Sunday or Saturday or a registered Hari Libur, do not mark as Alpa
        if ($today->isWeekend()) {
            return;
        }

        $isHariLibur = HariLibur::where('tanggal', $tanggal)->exists();
        if ($isHariLibur) {
            return;
        }

        // 2. Fetch all active pegawais
        $pegawais = Pegawai::where('status_aktif', true)->get();
        $countAlpa = 0;

        foreach ($pegawais as $p) {
            $hasAttendance = Kehadiran::where('pegawai_id', $p->id)
                ->where('tanggal', $tanggal)
                ->exists();

            if (!$hasAttendance) {
                // Check if employee has active SPT
                $hasSPT = SuratPerintahTugas::where('pegawai_id', $p->id)
                    ->where('status', 'disetujui')
                    ->whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->exists();

                if ($hasSPT) {
                    Kehadiran::create([
                        'pegawai_id' => $p->id,
                        'tanggal' => $tanggal,
                        'status' => 'Dinas Luar',
                        'sumber_data' => 'fingerprint',
                        'keterangan' => 'Surat Perintah Tugas Aktif'
                    ]);
                    continue;
                }

                // Check if employee has approved Izin / Sakit
                $hasIzin = IzinSakit::where('pegawai_id', $p->id)
                    ->where('status', 'disetujui')
                    ->whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->first();

                if ($hasIzin) {
                    $statusIzin = str_contains($hasIzin->jenis, 'sakit') ? 'Sakit' : 'Izin';
                    Kehadiran::create([
                        'pegawai_id' => $p->id,
                        'tanggal' => $tanggal,
                        'status' => $statusIzin,
                        'sumber_data' => 'fingerprint',
                        'keterangan' => $hasIzin->keterangan ?? 'Izin Disetujui'
                    ]);
                    continue;
                }

                // Default: Mark as Alpa
                Kehadiran::create([
                    'pegawai_id' => $p->id,
                    'tanggal' => $tanggal,
                    'status' => 'Alpa',
                    'sumber_data' => 'fingerprint',
                    'keterangan' => 'Tidak melakukan scan presensi hingga akhir hari kerja'
                ]);

                $countAlpa++;
            }
        }

        AuditLog::create([
            'user_name' => 'System Scheduler',
            'role' => 'System',
            'aktivitas' => "Penandaan otomatis Alpa harian selesai: {$countAlpa} pegawai ditandai Alpa",
            'modul' => 'Scheduler',
        ]);
    }
}
