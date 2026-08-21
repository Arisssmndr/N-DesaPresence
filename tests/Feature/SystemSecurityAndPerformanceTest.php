<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\AbsensiSignatureService;
use App\Models\KonfigurasiAbsensi;

class SystemSecurityAndPerformanceTest extends TestCase
{
    public function test_user_role_enum_methods()
    {
        $this->assertEquals('admin', UserRole::ADMIN->value);
        $this->assertEquals('kepala_desa', UserRole::KEPALA_DESA->value);
        $this->assertEquals('perangkat', UserRole::PERANGKAT->value);
        $this->assertEquals('auditor', UserRole::AUDITOR->value);
        $this->assertEquals('staf', UserRole::STAF->value);

        $this->assertContains('admin', UserRole::values());
        $this->assertContains('perangkat', UserRole::values());
    }

    public function test_signature_service_cidr_and_ip_validation()
    {
        $service = new AbsensiSignatureService();

        // Exact match
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('ipInCidr');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($service, '192.168.1.50', '192.168.1.0/24'));
        $this->assertFalse($method->invoke($service, '10.0.0.1', '192.168.1.0/24'));
        $this->assertTrue($method->invoke($service, '127.0.0.1', '127.0.0.1'));
    }

    public function test_konfigurasi_absensi_get_jadwal()
    {
        $jadwal = KonfigurasiAbsensi::getJadwal();
        $this->assertArrayHasKey('jam_masuk_mulai', $jadwal);
        $this->assertArrayHasKey('jam_masuk_selesai', $jadwal);
        $this->assertArrayHasKey('jam_pulang_mulai', $jadwal);
        $this->assertArrayHasKey('jam_pulang_selesai', $jadwal);
    }
}
