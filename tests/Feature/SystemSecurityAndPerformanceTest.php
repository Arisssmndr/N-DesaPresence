<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\KonfigurasiWifi;
use App\Services\AbsensiSignatureService;
use App\Models\KonfigurasiAbsensi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SystemSecurityAndPerformanceTest extends TestCase
{
    // ─── Test: User Role Enum ─────────────────────────────────────────────────

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

    // ─── Test: CIDR & IP Validation (Unit) ───────────────────────────────────

    public function test_signature_service_cidr_and_ip_validation()
    {
        $service = new AbsensiSignatureService();

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('ipInCidr');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($service, '192.168.1.50', '192.168.1.0/24'));
        $this->assertFalse($method->invoke($service, '10.0.0.1', '192.168.1.0/24'));
        $this->assertTrue($method->invoke($service, '127.0.0.1', '127.0.0.1'));
    }

    public function test_ip_validation_wildcard_subnet()
    {
        $service = new AbsensiSignatureService();

        // Isolasi: nonaktifkan semua WiFi lain agar tidak mengganggu pengujian
        KonfigurasiWifi::where('is_active', true)->update(['is_active' => false]);

        $wifi = KonfigurasiWifi::create([
            'nama_jaringan' => 'Test Subnet Wildcard',
            'ip_address'    => '192.168.0.*',
            'is_active'     => true,
        ]);

        // IP yang masuk subnet → harus lolos
        $this->assertTrue($service->validasiIpWifi('192.168.0.1'));
        $this->assertTrue($service->validasiIpWifi('192.168.0.100'));
        $this->assertTrue($service->validasiIpWifi('192.168.0.254'));

        // IP yang bukan subnet → harus ditolak
        $this->assertFalse($service->validasiIpWifi('192.168.1.1'));
        $this->assertFalse($service->validasiIpWifi('10.0.0.1'));
        $this->assertFalse($service->validasiIpWifi('8.8.8.8'));

        // Bersihkan & aktifkan kembali
        $wifi->delete();
        KonfigurasiWifi::where('is_active', false)->update(['is_active' => true]);
    }

    public function test_ip_validation_cidr_notation()
    {
        $service = new AbsensiSignatureService();

        // Isolasi: nonaktifkan semua WiFi lain agar tidak mengganggu pengujian
        KonfigurasiWifi::where('is_active', true)->update(['is_active' => false]);

        $wifi = KonfigurasiWifi::create([
            'nama_jaringan' => 'Test CIDR',
            'ip_address'    => '10.141.135.0/24',
            'is_active'     => true,
        ]);

        $this->assertTrue($service->validasiIpWifi('10.141.135.1'));
        $this->assertTrue($service->validasiIpWifi('10.141.135.189'));
        $this->assertFalse($service->validasiIpWifi('10.141.136.1'));
        $this->assertFalse($service->validasiIpWifi('192.168.0.1'));

        // Bersihkan & aktifkan kembali
        $wifi->delete();
        KonfigurasiWifi::where('is_active', false)->update(['is_active' => true]);
    }

    // ─── Test: Absen Langsung — DITOLAK jika bukan WiFi desa ─────────────────

    public function test_absen_langsung_ditolak_jika_ip_tidak_terdaftar()
    {
        // Hapus semua konfigurasi WiFi aktif supaya tidak ada yang cocok
        KonfigurasiWifi::where('is_active', true)->update(['is_active' => false]);

        // Gunakan user yang sudah ada di DB (tidak buat baru agar tidak error kolom username)
        $user = User::first();
        $this->assertNotNull($user, 'Tidak ada user di database test. Jalankan php artisan db:seed terlebih dahulu.');

        // Request dari IP acak (bukan WiFi desa)
        $response = $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])  // IP Google DNS
            ->postJson(route('staf.absen.submit'), [
                'jenis'        => 'masuk',
                'tanda_tangan' => 'data:image/png;base64,' . base64_encode(str_repeat('A', 100)),
            ]);

        // Harus ditolak 403
        $response->assertStatus(403);
        $response->assertJson(['status' => 'error']);

        // Aktifkan kembali
        KonfigurasiWifi::where('is_active', false)->update(['is_active' => true]);
    }

    public function test_absen_langsung_diterima_jika_ip_terdaftar()
    {
        $wifi = KonfigurasiWifi::create([
            'nama_jaringan' => 'Test WiFi Authorized',
            'ip_address'    => '192.168.99.1',
            'is_active'     => true,
        ]);

        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '192.168.99.1'])
            ->postJson(route('staf.absen.submit'), [
                'jenis'        => 'masuk',
                'tanda_tangan' => 'data:image/png;base64,' . base64_encode(str_repeat('X', 100)),
            ]);

        // Harus diterima (200/422 = sudah absen) — bukan 403
        $this->assertNotEquals(403, $response->status());

        $wifi->delete();
    }

    // ─── Test: IP Spoofing via X-Forwarded-For HARUS DIABAIKAN ───────────────

    public function test_ip_spoofing_via_x_forwarded_for_diabaikan_di_staf_portal()
    {
        // Pastikan tidak ada WiFi yang terdaftar untuk IP palsu
        KonfigurasiWifi::where('is_active', true)->update(['is_active' => false]);

        $user = User::first();
        $this->assertNotNull($user);

        // Penyerang mencoba spoof IP dengan header X-Forwarded-For: 192.168.0.1
        // tetapi IP asli (REMOTE_ADDR) adalah 8.8.8.8 — bukan WiFi desa
        $response = $this->actingAs($user)
            ->withHeaders(['X-Forwarded-For' => '192.168.0.1'])  // IP palsu
            ->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])    // IP asli
            ->postJson(route('staf.absen.submit'), [
                'jenis'        => 'masuk',
                'tanda_tangan' => 'data:image/png;base64,' . base64_encode(str_repeat('A', 100)),
            ]);

        // Harus TETAP ditolak 403 karena sistem menggunakan $request->ip()
        // (yang dihitung dari TrustedProxies, bukan manual X-Forwarded-For)
        $response->assertStatus(403);

        KonfigurasiWifi::where('is_active', false)->update(['is_active' => true]);
    }

    // ─── Test: Absen Luar — BEBAS dari WiFi mana saja ────────────────────────

    public function test_absen_luar_bisa_submit_dari_ip_manapun()
    {
        Storage::fake('public');

        $user    = User::first();
        $this->assertNotNull($user);
        $pegawai = $user->pegawai;
        $this->assertNotNull($pegawai, 'User pertama tidak memiliki data pegawai.');

        // Hapus pengajuan hari kemarin jika ada
        \App\Models\PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->where('tanggal', now()->subDay()->toDateString())
            ->delete();

        // dinas_luar_pengajuan mensyaratkan foto_lokasi — buat fake image
        $fotoLokasi = UploadedFile::fake()->image('bukti_lokasi.jpg', 400, 300);

        // Submit dari IP luar (bukan WiFi desa) — HARUS BERHASIL karena absen luar bebas WiFi
        $response = $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])  // IP Google — bukan WiFi desa
            ->post(route('staf.ajukan.store'), [
                'tanggal'      => now()->subDay()->toDateString(),
                'jenis'        => 'dinas_luar_pengajuan',
                'judul'        => 'Koordinasi Program BUMDes',
                'deskripsi'    => 'Menghadiri rapat pembentukan BUMDes bersama pendamping desa dari kecamatan setempat.',
                'foto_lokasi'  => $fotoLokasi,
                'latitude'     => -7.3456789,
                'longitude'    => 108.1234567,
                'alamat_gps'   => 'Kecamatan Cigalontang, Tasikmalaya',
                'tanda_tangan' => 'data:image/png;base64,' . base64_encode(str_repeat('S', 100)),
            ]);

        // Harus berhasil redirect ke riwayat pengajuan — bukan 403 dan bukan kembali ke form
        $response->assertRedirect(route('staf.riwayat', ['tab' => 'absen_luar']));
    }

    // ─── Test: Konfigurasi Jadwal Absensi ─────────────────────────────────────

    public function test_konfigurasi_absensi_get_jadwal()
    {
        $jadwal = KonfigurasiAbsensi::getJadwal();
        $this->assertArrayHasKey('jam_masuk_mulai', $jadwal);
        $this->assertArrayHasKey('jam_masuk_selesai', $jadwal);
        $this->assertArrayHasKey('jam_pulang_mulai', $jadwal);
        $this->assertArrayHasKey('jam_pulang_selesai', $jadwal);
    }
}
