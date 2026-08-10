<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HariLibur;

class HariLiburSeeder extends Seeder
{
    public function run(): void
    {
        $liburs = [
            ['tanggal' => '2026-01-01', 'nama_hari_libur' => 'Tahun Baru 2026 Masehi', 'jenis' => 'nasional'],
            ['tanggal' => '2026-03-20', 'nama_hari_libur' => 'Hari Raya Idul Fitri 1447 H', 'jenis' => 'nasional'],
            ['tanggal' => '2026-03-21', 'nama_hari_libur' => 'Hari Raya Idul Fitri 1447 H (Hari Kedua)', 'jenis' => 'nasional'],
            ['tanggal' => '2026-08-17', 'nama_hari_libur' => 'Hari Kemerdekaan Republik Indonesia', 'jenis' => 'nasional'],
            ['tanggal' => '2026-12-25', 'nama_hari_libur' => 'Hari Raya Natal', 'jenis' => 'nasional'],
        ];

        foreach ($liburs as $l) {
            HariLibur::updateOrCreate(['tanggal' => $l['tanggal']], $l);
        }
    }
}
