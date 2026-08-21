<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            // Kepala Desa
            ['nama_jabatan' => 'Kepala Desa', 'kode_jabatan' => 'KADES', 'level_jabatan' => 1, 'deskripsi' => 'Kepala Pemerintahan Desa Nangtang'],
            
            // Perangkat Desa (Sekretariat & Pelaksana Teknis)
            ['nama_jabatan' => 'Sekretaris Desa', 'kode_jabatan' => 'SEKDES', 'level_jabatan' => 2, 'deskripsi' => 'Pimpinan Sekretariat Desa Nangtang'],
            ['nama_jabatan' => 'Kaur Keuangan', 'kode_jabatan' => 'KAUR_KEU', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Urusan Keuangan Desa'],
            ['nama_jabatan' => 'Kaur Perencanaan', 'kode_jabatan' => 'KAUR_REN', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Urusan Perencanaan'],
            ['nama_jabatan' => 'Kaur TU & Umum', 'kode_jabatan' => 'KAUR_TU', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Urusan Tata Usaha & Umum'],
            ['nama_jabatan' => 'Kasi Pemerintahan', 'kode_jabatan' => 'KASI_PEM', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Seksi Pemerintahan'],
            ['nama_jabatan' => 'Kasi Kesejahteraan', 'kode_jabatan' => 'KASI_KESRA', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Seksi Kesejahteraan'],
            ['nama_jabatan' => 'Kasi Pelayanan', 'kode_jabatan' => 'KASI_PEL', 'level_jabatan' => 3, 'deskripsi' => 'Kepala Seksi Pelayanan'],
            
            // Perangkat Desa (Pelaksana Kewilayahan / Kepala Dusun)
            ['nama_jabatan' => 'Kepala Kewilayahan Nangtang', 'kode_jabatan' => 'KADUS_NANGTANG', 'level_jabatan' => 4, 'deskripsi' => 'Kepala Dusun / Kewilayahan Nangtang'],
            ['nama_jabatan' => 'Kepala Kewilayahan Nangkabongkok', 'kode_jabatan' => 'KADUS_NANGKABONGKOK', 'level_jabatan' => 4, 'deskripsi' => 'Kepala Dusun / Kewilayahan Nangkabongkok'],
            ['nama_jabatan' => 'Kepala Kewilayahan Kawunglancar', 'kode_jabatan' => 'KADUS_KAWUNGLANCAR', 'level_jabatan' => 4, 'deskripsi' => 'Kepala Dusun / Kewilayahan Kawunglancar'],
            ['nama_jabatan' => 'Kepala Kewilayahan Mayana', 'kode_jabatan' => 'KADUS_MAYANA', 'level_jabatan' => 4, 'deskripsi' => 'Kepala Dusun / Kewilayahan Mayana'],
            
            // Staff Desa
            ['nama_jabatan' => 'Staff Desa', 'kode_jabatan' => 'STAFF_DESA', 'level_jabatan' => 5, 'deskripsi' => 'Staf Pembantu Administrasi Desa'],
        ];

        foreach ($jabatans as $j) {
            Jabatan::updateOrCreate(['kode_jabatan' => $j['kode_jabatan']], $j);
        }
    }
}
