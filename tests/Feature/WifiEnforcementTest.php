<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\KonfigurasiWifi;
use App\Models\WifiAccessLog;
use App\Models\PengajuanAbsenLuar;
use App\Services\AbsensiSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class WifiEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache & fake storage
        Cache::flush();
        Storage::fake('public');

        // Truncate existing seed WiFi to ensure clean single active state
        KonfigurasiWifi::truncate();

        // Setup 1 active WiFi router for testing
        KonfigurasiWifi::create([
            'nama_jaringan' => 'WiFi Kantor Desa Nangtang',
            'ip_address'    => '192.168.1.0/24',
            'keterangan'    => 'Router Utama Kantor Desa',
            'is_active'     => true,
        ]);
    }

    public function test_wifi_status_endpoint_returns_valid_for_whitelisted_ip(): void
    {
        $jabatan = Jabatan::first() ?? Jabatan::create(['kode_jabatan' => 'TEST_JAB_W1', 'nama_jabatan' => 'Staf Test', 'kategori' => 'perangkat_tetap']);
        $pegawai = Pegawai::create([
            'nama_lengkap'     => 'Staf WiFi Test',
            'nik'              => '3206123456780091',
            'jabatan_id'       => $jabatan->id,
            'kategori_pegawai' => 'perangkat_tetap',
            'status_aktif'     => true,
            'no_hp'            => '081234567899',
        ]);
        $user = User::create([
            'name'       => 'Staf WiFi Test',
            'username'   => 'stafwifi',
            'role'       => 'perangkat',
            'pegawai_id' => $pegawai->id,
        ]);

        $response = $this->actingAs($user)->get('/staf/wifi-status', [
            'REMOTE_ADDR' => '192.168.1.45',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid'           => true,
            'client_ip'       => '192.168.1.45',
            'matched_network' => 'WiFi Kantor Desa Nangtang',
        ]);
    }

    public function test_wifi_status_endpoint_returns_invalid_for_cellular_or_external_ip(): void
    {
        $jabatan = Jabatan::first() ?? Jabatan::create(['kode_jabatan' => 'TEST_JAB_W2', 'nama_jabatan' => 'Staf Test 2', 'kategori' => 'perangkat_tetap']);
        $pegawai = Pegawai::create([
            'nama_lengkap'     => 'Staf WiFi Test 2',
            'nik'              => '3206123456780092',
            'jabatan_id'       => $jabatan->id,
            'kategori_pegawai' => 'perangkat_tetap',
            'status_aktif'     => true,
            'no_hp'            => '081234567898',
        ]);
        $user = User::create([
            'name'       => 'Staf WiFi Test 2',
            'username'   => 'stafwifi2',
            'role'       => 'perangkat',
            'pegawai_id' => $pegawai->id,
        ]);

        $response = $this->actingAs($user)->get('/staf/wifi-status', [
            'REMOTE_ADDR' => '36.85.12.99', // External Telkomsel/Indosat IP
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid'     => false,
            'client_ip' => '36.85.12.99',
        ]);
    }

    public function test_direct_attendance_rejected_with_403_when_using_external_network(): void
    {
        $jabatan = Jabatan::first() ?? Jabatan::create(['kode_jabatan' => 'TEST_JAB_1', 'nama_jabatan' => 'Kaur Keuangan', 'kategori' => 'perangkat_tetap']);
        $pegawai = Pegawai::create([
            'nama_lengkap'     => 'Budi Santoso',
            'nik'              => '3206123456780001',
            'jabatan_id'       => $jabatan->id,
            'kategori_pegawai' => 'perangkat_tetap',
            'status_aktif'     => true,
            'no_hp'            => '081234567890',
        ]);
        $user = User::create([
            'name'       => 'Budi Santoso',
            'username'   => 'budi',
            'role'       => 'perangkat',
            'pegawai_id' => $pegawai->id,
        ]);

        $fakeSignature = 'data:image/png;base64,' . base64_encode('fake_signature_png_data_content_test_1234567890123456789012345678901234567890');

        $response = $this->actingAs($user)->postJson('/staf/absen/submit', [
            'jenis'        => 'masuk',
            'tanda_tangan' => $fakeSignature,
        ], [
            'REMOTE_ADDR' => '114.124.8.99', // Cellular Data IP
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'ip'     => '114.124.8.99',
        ]);

        // Verify rejected audit log was recorded
        $this->assertDatabaseHas('wifi_access_logs', [
            'client_ip'  => '114.124.8.99',
            'pegawai_id' => $pegawai->id,
            'jenis_aksi' => 'absen_masuk',
            'hasil'      => 'ditolak',
        ]);
    }

    public function test_direct_attendance_accepted_when_connected_to_village_wifi(): void
    {
        $jabatan = Jabatan::first() ?? Jabatan::create(['kode_jabatan' => 'TEST_JAB_2', 'nama_jabatan' => 'Sekretaris Desa', 'kategori' => 'perangkat_tetap']);
        $pegawai = Pegawai::create([
            'nama_lengkap'     => 'Asep Sunandar',
            'nik'              => '3206123456780002',
            'jabatan_id'       => $jabatan->id,
            'kategori_pegawai' => 'perangkat_tetap',
            'status_aktif'     => true,
            'no_hp'            => '081234567891',
        ]);
        $user = User::create([
            'name'       => 'Asep Sunandar',
            'username'   => 'asep',
            'role'       => 'perangkat',
            'pegawai_id' => $pegawai->id,
        ]);

        $fakeSignature = 'data:image/png;base64,' . base64_encode('fake_signature_png_data_content_test_1234567890123456789012345678901234567890');

        $response = $this->actingAs($user)->postJson('/staf/absen/submit', [
            'jenis'        => 'masuk',
            'tanda_tangan' => $fakeSignature,
        ], [
            'REMOTE_ADDR' => '192.168.1.105', // Connected to village WiFi subnet
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'berhasil',
            'jenis'  => 'masuk',
        ]);

        // Verify success audit log was recorded
        $this->assertDatabaseHas('wifi_access_logs', [
            'client_ip'  => '192.168.1.105',
            'pegawai_id' => $pegawai->id,
            'jenis_aksi' => 'absen_masuk',
            'hasil'      => 'diizinkan',
        ]);
    }

    public function test_pengajuan_absen_luar_works_freely_from_any_network(): void
    {
        $jabatan = Jabatan::first() ?? Jabatan::create(['kode_jabatan' => 'TEST_JAB_3', 'nama_jabatan' => 'Kasi Pemerintahan', 'kategori' => 'perangkat_tetap']);
        $pegawai = Pegawai::create([
            'nama_lengkap'     => 'Dedi Suryadi',
            'nik'              => '3206123456780003',
            'jabatan_id'       => $jabatan->id,
            'kategori_pegawai' => 'perangkat_tetap',
            'status_aktif'     => true,
            'no_hp'            => '081234567892',
        ]);

        $user = User::create([
            'name'       => 'Dedi Suryadi',
            'username'   => 'dedi',
            'role'       => 'perangkat',
            'pegawai_id' => $pegawai->id,
        ]);

        $fakeSignature = 'data:image/png;base64,' . base64_encode('fake_signature_png_data_content_test_1234567890123456789012345678901234567890');
        $fakeFoto = UploadedFile::fake()->image('bukti_kegiatan.jpg');

        // Submitting from cellular data IP in external city/district
        $response = $this->actingAs($user)->post('/staf/ajukan-absen', [
            'tanggal'             => now()->toDateString(),
            'jenis'               => 'dinas_luar_pengajuan',
            'judul'               => 'Rapat Koordinasi di Kantor Kecamatan Cigalontang',
            'deskripsi'           => 'Menghadiri rapat persiapan Musrenbang tingkat kecamatan.',
            'foto_lokasi'         => $fakeFoto,
            'latitude'            => -7.3321,
            'longitude'           => 108.1234,
            'alamat_gps'          => 'Kantor Kecamatan Cigalontang, Kab. Tasikmalaya',
            'sumber_koordinat'    => 'gps',
            'tanda_tangan'        => $fakeSignature,
        ], [
            'REMOTE_ADDR' => '180.252.160.45', // Cellular Data IP (Telkomsel)
        ]);

        $response->assertRedirect(route('staf.riwayat', ['tab' => 'absen_luar']));

        $this->assertDatabaseHas('pengajuan_absen_luars', [
            'pegawai_id' => $pegawai->id,
            'judul'      => 'Rapat Koordinasi di Kantor Kecamatan Cigalontang',
            'status'     => 'menunggu',
        ]);
    }

    public function test_cache_is_invalidated_when_wifi_config_changes(): void
    {
        $service = app(AbsensiSignatureService::class);

        // Warm up cache
        $initialWifi = $service->getDaftarWifiAktif();
        $this->assertCount(1, $initialWifi);

        // Add new IP and activate it
        KonfigurasiWifi::where('is_active', true)->update(['is_active' => false]);
        KonfigurasiWifi::create([
            'nama_jaringan' => 'Router Baru WiFi Kantor Desa',
            'ip_address'    => '10.10.10.0/24',
            'is_active'     => true,
        ]);

        $service->invalidateWifiCache();

        $updatedWifi = $service->getDaftarWifiAktif();
        $this->assertCount(1, $updatedWifi);
        $this->assertEquals('10.10.10.0/24', $updatedWifi->first()->ip_address);
    }
}
