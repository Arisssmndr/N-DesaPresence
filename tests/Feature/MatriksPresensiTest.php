<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Livewire\MatriksPresensi;
use Livewire\Livewire;
use Carbon\Carbon;

class MatriksPresensiTest extends TestCase
{
    public function test_future_dates_are_not_marked_as_alpa()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 20, 10, 0, 0));

        $user = \App\Models\User::first();
        if ($user) {
            $this->actingAs($user);
        }

        $test = Livewire::test(MatriksPresensi::class)
            ->set('bulan', 8)
            ->set('tahun', 2026);

        $test->assertViewHas('matrix', function ($matrix) {
            $this->assertNotEmpty($matrix);

            $firstPegawaiId = array_key_first($matrix);
            $pegawaiMatrix = $matrix[$firstPegawaiId];

            // Tanggal 31 Agustus 2026 (masa depan jika hari ini 20 Agustus)
            // Hari Senin, 31 Agustus 2026 -> harus '-' (bukan 'A')
            $this->assertEquals('-', $pegawaiMatrix[31]);

            // Tanggal 23 Agustus 2026 (Hari Minggu) -> harus 'L'
            $this->assertEquals('L', $pegawaiMatrix[23]);

            // Tanggal 17 Agustus 2026 (HUT RI / Libur Nasional) -> harus 'L'
            $this->assertEquals('L', $pegawaiMatrix[17]);

            return true;
        });

        Carbon::setTestNow(); // Reset time
    }
}
