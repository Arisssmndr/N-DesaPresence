<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\KonfigurasiSiltap;

class KonfigurasiSiltapSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = Jabatan::all();

        foreach ($jabatans as $j) {
            $siltap = match ($j->kode_jabatan) {
                'KADES' => 3000000,
                'SEKDES' => 2500000,
                default => 2025000,
            };

            KonfigurasiSiltap::updateOrCreate(
                ['jabatan_id' => $j->id],
                [
                    'nominal_siltap' => $siltap,
                    'nominal_tunjangan' => 250000,
                    'nilai_potongan_alpa' => 100000,
                    'nilai_potongan_terlambat' => 10000,
                    'berlaku_mulai' => '2025-01-01',
                ]
            );
        }
    }
}
