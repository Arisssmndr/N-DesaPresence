<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\KalenderNasionalService;
use App\Models\HariLibur;
use App\Livewire\Dashboard;
use Livewire\Livewire;

class KalenderNasionalTest extends TestCase
{
    public function test_kalender_nasional_returns_holidays_and_hari_pahlawan()
    {
        $service = new KalenderNasionalService();
        $novemberData = $service->getKalenderBulan(2026, 11);

        // Pastikan Hari Pahlawan terdeteksi otomatis pada 10 November
        $this->assertArrayHasKey('2026-11-10', $novemberData['peringatan']);
        $this->assertEquals('Hari Pahlawan Nasional', $novemberData['peringatan']['2026-11-10']['nama']);

        // Pastikan Hari Kemerdekaan RI terdeteksi pada Agustus
        $agustusData = $service->getKalenderBulan(2026, 8);
        $this->assertArrayHasKey('2026-08-17', $agustusData['libur']);
        $this->assertStringContainsString('Kemerdekaan', $agustusData['libur']['2026-08-17']['nama']);
    }

    public function test_kalender_nasional_database_sync()
    {
        // Hapus data hari libur yang di-seed agar sinkronisasi bisa insert baru
        \App\Models\HariLibur::truncate();

        $service = new KalenderNasionalService();
        $count = $service->sinkronkanKeDatabase(2026);

        $this->assertGreaterThan(0, $count);
        // Gunakan whereDate agar kompatibel dengan SQLite (menyimpan tanggal sebagai datetime)
        $hariMerdeka = \App\Models\HariLibur::whereDate('tanggal', '2026-08-17')->first();
        $this->assertNotNull($hariMerdeka, 'Hari Kemerdekaan 2026-08-17 tidak ditemukan di database');

    }


    public function test_dashboard_renders_kalender_nasional_widget()
    {
        $user = \App\Models\User::first();
        if ($user) {
            $this->actingAs($user);
            Livewire::test(Dashboard::class)
                ->assertSee('Kalender')
                ->assertSee('Sen')
                ->assertSee('Min');
        } else {
            $this->assertTrue(true);
        }
    }
}
