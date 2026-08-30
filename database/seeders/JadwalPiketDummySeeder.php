<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\JadwalPiket;
use Carbon\Carbon;

class JadwalPiketDummySeeder extends Seeder
{
    public function run(): void
    {
        $malePegawais = Pegawai::where('status_aktif', true)
            ->where('jenis_kelamin', 'L')
            ->orderBy('nama_lengkap')
            ->get();

        if ($malePegawais->isEmpty()) {
            $this->command->warn('Tidak ditemukan staf laki-laki aktif untuk membuat data dummy piket.');
            return;
        }

        $startDate = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $stafCount = $malePegawais->count();

        for ($i = 0; $i < 7; $i++) {
            $tgl = $startDate->copy()->addDays($i);
            $pegawai = $malePegawais[$i % $stafCount];

            JadwalPiket::updateOrCreate(
                ['tanggal_piket' => $tgl->toDateString()],
                [
                    'pegawai_id'  => $pegawai->id,
                    'jam_mulai'   => '19:00:00',
                    'jam_selesai' => '06:00:00',
                    'keterangan'  => 'Piket Jaga Malam Balai Desa',
                    'status'      => 'terjadwal',
                    'created_by'  => 1,
                ]
            );
        }

        $this->command->info("Data dummy jadwal piket 1 minggu ({$startDate->format('d/m/Y')} s/d {$startDate->copy()->addDays(6)->format('d/m/Y')}) berhasil dibuat.");
    }
}
