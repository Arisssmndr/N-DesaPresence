<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        $kadesJabatan = Jabatan::where('kode_jabatan', 'KADES')->first();
        $sekdesJabatan = Jabatan::where('kode_jabatan', 'SEKDES')->first();
        $kaurJabatan = Jabatan::where('kode_jabatan', 'KAUR_UMUM')->first();

        // 1. Pegawai Kepala Desa
        $pegawaiKades = Pegawai::updateOrCreate(
            ['pin_fingerprint' => '001'],
            [
                'nipd' => '197501012005011001',
                'nik' => '3201010101750001',
                'nama_lengkap' => 'H. Ahmad Supriyadi, S.IP',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '1975-01-01',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $kadesJabatan->id,
                'kategori_pegawai' => 'perangkat_tetap',
                'shift_id' => 1,
                'no_hp' => '081234567890',
                'alamat' => 'Desa Nangtang RT 01 RW 02',
                'siltap_bruto' => 3000000.00,
                'status_aktif' => true,
            ]
        );

        // 2. Pegawai Sekretaris Desa (Admin)
        $pegawaiSekdes = Pegawai::updateOrCreate(
            ['pin_fingerprint' => '002'],
            [
                'nipd' => '198205122010012002',
                'nik' => '3201011205820002',
                'nama_lengkap' => 'Hj. Nurlaila Rahmawati, S.AP',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '1982-05-12',
                'jenis_kelamin' => 'P',
                'jabatan_id' => $sekdesJabatan->id,
                'kategori_pegawai' => 'perangkat_tetap',
                'shift_id' => 1,
                'no_hp' => '081987654321',
                'alamat' => 'Desa Nangtang RT 02 RW 01',
                'siltap_bruto' => 2500000.00,
                'status_aktif' => true,
            ]
        );

        // 3. User Admin Desa (Sekdes)
        User::updateOrCreate(
            ['username' => 'admin'git branch -m master main],
            [
                'pegawai_id' => $pegawaiSekdes->id,
                'name' => 'Admin Desa (Sekdes)',
                'email' => 'admin@desanangtang.go.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 4. User Kepala Desa
        User::updateOrCreate(
            ['username' => 'kades'],
            [
                'pegawai_id' => $pegawaiKades->id,
                'name' => 'Kepala Desa Nangtang',
                'email' => 'kades@desanangtang.go.id',
                'password' => Hash::make('kades123'),
                'role' => 'kepala_desa',
                'is_active' => true,
            ]
        );

        // 5. User Auditor Inspektorat
        User::updateOrCreate(
            ['username' => 'auditor'],
            [
                'pegawai_id' => null,
                'name' => 'Auditor Inspektorat',
                'email' => 'auditor@pemkab.go.id',
                'password' => Hash::make('auditor123'),
                'role' => 'auditor',
                'is_active' => true,
            ]
        );
    }
}
