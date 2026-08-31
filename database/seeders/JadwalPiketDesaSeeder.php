<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\JadwalPiket;
use Carbon\Carbon;

class JadwalPiketDesaSeeder extends Seeder
{
    public function run(): void
    {
        $findId = function (string $name) {
            return Pegawai::where('nama_lengkap', 'like', "%{$name}%")->value('id');
        };

        // Pemetaan staf resmi desa sesuai jadwal baku
        $polaDesa = [
            1 => array_values(array_filter([$findId('DEDE SUMIRNA'), $findId('RUKANDA')])), // Senin
            2 => array_values(array_filter([$findId('YAYAN TARYANA'), $findId('DEDE LISMAN')])), // Selasa
            3 => array_values(array_filter([$findId('APIP MANSUR')])), // Rabu
            4 => array_values(array_filter([$findId('DEDI SUHERMAN')])), // Kamis
            5 => array_values(array_filter([$findId('ABUN SUPARMAN')])), // Jumat
            6 => array_values(array_filter([$findId('HERI GINANJAR')])), // Sabtu
            0 => array_values(array_filter([$findId('ZAILANI RAHMAT')])), // Minggu
        ];

        $start = Carbon::today()->startOfYear();
        $end   = Carbon::today()->endOfYear();

        $current = $start->copy();
        $totalCreated = 0;

        while ($current->lte($end)) {
            $dayOfWeek = $current->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $stafIds = $polaDesa[$dayOfWeek] ?? [];
            $dateStr = $current->toDateString();

            foreach ($stafIds as $stafId) {
                if (!$stafId) continue;

                JadwalPiket::updateOrCreate(
                    [
                        'pegawai_id'    => $stafId,
                        'tanggal_piket' => $dateStr,
                    ],
                    [
                        'jam_mulai'   => '19:00:00',
                        'jam_selesai' => '06:00:00',
                        'keterangan'  => 'Piket Jaga Malam Balai Desa',
                        'status'      => 'terjadwal',
                        'created_by'  => 1,
                    ]
                );
                $totalCreated++;
            }

            $current->addDay();
        }

        if (isset($this->command)) {
            $this->command->info("Jadwal piket baku desa berhasil dibuat sebanyak {$totalCreated} penugasan dari {$start->format('d/m/Y')} s/d {$end->format('d/m/Y')}.");
        }
    }
}
