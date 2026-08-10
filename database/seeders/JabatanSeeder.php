<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['nama_jabatan' => 'Kepala Desa', 'kode_jabatan' => 'KADES', 'level_jabatan' => 1, 'deskripsi' => 'Pemimpin Pemerintah Desa Nangtang'],
            ['nama_jabatan' => 'Sekretaris Desa', 'kode_jabatan' => 'SEKDES', 'level_jabatan' => 2, 'deskripsi' => 'Pimpinan Sekretariat Desa'],
            ['nama_jabatan' => 'Kaur Umum & Perencanaan', 'kode_jabatan' => 'KAUR_UMUM', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Urusan Umum dan Perencanaan'],
            ['nama_jabatan' => 'Kaur Keuangan', 'kode_jabatan' => 'KAUR_KEU', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Urusan Keuangan Desa'],
            ['nama_jabatan' => 'Kasi Pemerintahan', 'kode_jabatan' => 'KASI_PEM', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Seksi Pemerintahan'],
            ['nama_jabatan' => 'Kasi Kesejahteraan & Pelayanan', 'kode_jabatan' => 'KASI_KESRA', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Seksi Kesra dan Pelayanan'],
            ['nama_jabatan' => 'Kepala Dusun I', 'kode_jabatan' => 'KADUS_1', 'level_jabatan' => 4, 'deskripsi' => 'Kepala Dusun Wilayah I'],
            ['nama_jabatan' => 'Kepala Dusun II', 'kode_jabatan' => 'KADUS_2', 'level_jabatan' => 4, 'deskripsi' => 'Kepala Dusun Wilayah II'],
            ['nama_jabatan' => 'Staf Administrasi', 'kode_jabatan' => 'STAF_ADM', 'level_jabatan' => 5, 'deskripsi' => 'Staf Pembantu Desa'],
        ];

        foreach ($jabatans as $j) {
            Jabatan::updateOrCreate(['kode_jabatan' => $j['kode_jabatan']], $j);
        }
    }
}
