<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KonfigurasiAbsensi;

class KonfigurasiAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'kunci' => 'jam_masuk_mulai',
                'nilai' => '06:00',
                'keterangan' => 'Batas awal jam absensi masuk',
            ],
            [
                'kunci' => 'jam_masuk_selesai',
                'nilai' => '11:00',
                'keterangan' => 'Batas akhir jam absensi masuk',
            ],
            [
                'kunci' => 'jam_pulang_mulai',
                'nilai' => '14:00',
                'keterangan' => 'Batas awal jam absensi pulang',
            ],
            [
                'kunci' => 'jam_pulang_selesai',
                'nilai' => '18:00',
                'keterangan' => 'Batas akhir jam absensi pulang',
            ],
        ];

        foreach ($configs as $cfg) {
            KonfigurasiAbsensi::updateOrCreate(
                ['kunci' => $cfg['kunci']],
                $cfg
            );
        }
    }
}
