<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShiftKerja;

class ShiftKerjaSeeder extends Seeder
{
    public function run(): void
    {
        ShiftKerja::updateOrCreate(
            ['id' => 1],
            [
                'nama_shift' => 'Shift Pagi Standard',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '15:30:00',
                'toleransi_menit' => 15,
                'is_active' => true,
            ]
        );

        ShiftKerja::updateOrCreate(
            ['id' => 2],
            [
                'nama_shift' => 'Shift Malam Linmas',
                'jam_masuk' => '20:00:00',
                'jam_pulang' => '06:00:00',
                'toleransi_menit' => 30,
                'is_active' => true,
            ]
        );
    }
}
