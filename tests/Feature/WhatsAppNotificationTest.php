<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Pengumuman;
use App\Models\KonfigurasiWhatsApp;
use App\Models\WaNotifikasiLog;
use App\Services\KonfigurasiWaService;
use App\Services\FonnteWhatsAppService;
use App\Jobs\KirimWaNotifikasiPengumumanJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use App\Livewire\KonfigurasiWhatsAppManager;
use App\Livewire\PengumumanManager;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Pegawai $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $jabatan = Jabatan::firstOrCreate(
            ['kode_jabatan' => 'SEKDES'],
            [
                'nama_jabatan'  => 'Sekretaris Desa',
                'level_jabatan' => 2,
            ]
        );

        $this->pegawai = Pegawai::firstOrCreate(
            ['nik' => '3206123456780001'],
            [
                'nama_lengkap'     => 'Budi Santoso',
                'jabatan_id'       => $jabatan->id,
                'kategori_pegawai' => 'perangkat_tetap',
                'no_hp'            => '081234567890',
                'status_aktif'     => true,
            ]
        );

        $this->admin = User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'name'       => 'Administrator Test',
                'password'   => bcrypt('password'),
                'role'       => 'admin',
                'pegawai_id' => $this->pegawai->id,
                'is_active'  => true,
            ]
        );

        // Global default mock for all Fonnte endpoints to prevent external network calls during testing
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'target' => ['6281234567890'],
                'detail' => 'success',
            ], 200),
            'https://api.fonnte.com/device' => Http::response([
                'status'        => true,
                'device_status' => 'connect',
                'device'        => '081322575473',
                'quota'         => '1000',
            ], 200),
            'https://api.fonnte.com/get-devices' => Http::response([
                'status' => true,
                'data'   => [
                    [
                        'device' => '6281322575473',
                        'name'   => 'WA Desa',
                        'status' => 'connect',
                        'token'  => 'device_tok_123',
                    ]
                ],
                'devices'   => 1,
                'connected' => 1,
            ], 200),
            'https://api.fonnte.com/add-device' => Http::response([
                'status' => true,
                'token'  => 'new_device_token_xyz',
                'detail' => 'device added',
            ], 200),
            'https://api.fonnte.com/qr' => Http::response([
                'status' => true,
                'url'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...',
            ], 200),
            'https://api.fonnte.com/delete-device' => Http::response([
                'status' => true,
                'detail' => 'device deleted',
            ], 200),
        ]);
    }

    public function test_konfigurasi_wa_service_format_nomor_hp()
    {
        $service = app(KonfigurasiWaService::class);

        $this->assertEquals('6281234567890', $service->formatNomorHp('0812-3456-7890'));
        $this->assertEquals('6281234567890', $service->formatNomorHp('+62 812 3456 7890'));
        $this->assertEquals('6281234567890', $service->formatNomorHp('81234567890'));
        $this->assertNull($service->formatNomorHp(''));
    }

    public function test_konfigurasi_wa_service_encryption()
    {
        $service = app(KonfigurasiWaService::class);
        $rawToken = 'sample_fonnte_secret_token_12345';

        $service->set('fonnte_api_key', $rawToken);

        $config = KonfigurasiWhatsApp::where('key', 'fonnte_api_key')->first();
        $this->assertNotEquals($rawToken, $config->value); // Tersimpan terenkripsi
        $this->assertEquals($rawToken, $service->get('fonnte_api_key')); // Diambil ter-decrypt
    }

    public function test_fonnte_whatsapp_service_send_success()
    {
        $configService = app(KonfigurasiWaService::class);
        $configService->set('fonnte_api_key', 'valid_token');
        $configService->set('wa_notifikasi_enabled', '1');

        $waService = app(FonnteWhatsAppService::class);
        $result = $waService->send('081234567890', 'Uji coba notifikasi');

        $this->assertTrue($result['success']);
        $this->assertEquals('terkirim', $result['status']);
    }

    public function test_fonnte_whatsapp_service_remote_device_management()
    {
        $configService = app(KonfigurasiWaService::class);
        $configService->set('fonnte_account_token', 'master_account_token_abc');

        $waService = app(FonnteWhatsAppService::class);

        // 1. Get Devices List
        $devList = $waService->getDevicesList();
        $this->assertTrue($devList['success']);
        $this->assertCount(1, $devList['data']);

        // 2. Add Device
        $addRes = $waService->addDevice('WA Baru', '081299998888');
        $this->assertTrue($addRes['success']);
        $this->assertEquals('new_device_token_xyz', $addRes['token']);

        // 3. Get QR
        $qrRes = $waService->getQrCode('081299998888', 'new_device_token_xyz');
        $this->assertTrue($qrRes['success']);
        $this->assertNotEmpty($qrRes['url']);
    }

    public function test_konfigurasi_wa_manager_livewire()
    {
        $this->actingAs($this->admin);

        Livewire::test(KonfigurasiWhatsAppManager::class)
            ->set('form.fonnte_account_token', 'master_token_account_xyz')
            ->call('saveAccountToken')
            ->assertDispatched('notify')
            ->call('setActiveDevice', '081322575473', 'device_token_aktif_123')
            ->assertDispatched('notify');

        $configService = app(KonfigurasiWaService::class);
        $this->assertEquals('master_token_account_xyz', $configService->get('fonnte_account_token'));
        $this->assertEquals('device_token_aktif_123', $configService->get('fonnte_api_key'));
        $this->assertEquals('081322575473', $configService->get('fonnte_sender_number'));
        $this->assertTrue($configService->isEnabled());
    }

    public function test_pengumuman_manager_create_and_dispatch_wa()
    {
        $configService = app(KonfigurasiWaService::class);
        $configService->set('fonnte_api_key', 'valid_token_123');
        $configService->set('wa_notifikasi_enabled', '1');

        $this->actingAs($this->admin);

        Livewire::test(PengumumanManager::class)
            ->set('judul', 'Rapat Koordinasi Pilkades 2026')
            ->set('isi', 'Diharapkan seluruh perangkat desa hadir di aula kantor desa.')
            ->set('kategori', 'rapat')
            ->set('mode_target', 'semua')
            ->set('kirim_wa', true)
            ->call('save')
            ->assertDispatched('notify');

        $pengumuman = Pengumuman::first();
        $this->assertNotNull($pengumuman);
        $this->assertEquals('Rapat Koordinasi Pilkades 2026', $pengumuman->judul);
        $this->assertEquals('rapat', $pengumuman->kategori);
        $this->assertTrue($pengumuman->kirim_wa);
        $this->assertGreaterThanOrEqual(1, $pengumuman->total_wa_terkirim);

        $log = WaNotifikasiLog::where('pengumuman_id', $pengumuman->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('terkirim', $log->status);
    }

    public function test_pengumuman_manager_create_with_individual_recipients()
    {
        $configService = app(KonfigurasiWaService::class);
        $configService->set('fonnte_api_key', 'valid_token_123');
        $configService->set('wa_notifikasi_enabled', '1');

        $this->actingAs($this->admin);

        Livewire::test(PengumumanManager::class)
            ->set('judul', 'Instruksi Khusus Sekretariat')
            ->set('isi', 'Harap lengkapi berkas SPJ sebelum jam 12 siang.')
            ->set('kategori', 'arahan')
            ->set('mode_target', 'individual')
            ->set('selected_pegawai_ids', [(string) $this->pegawai->id])
            ->set('kirim_wa', true)
            ->call('save')
            ->assertDispatched('notify');

        $pengumuman = Pengumuman::latest()->first();
        $this->assertNotNull($pengumuman);
        $this->assertEquals('arahan', $pengumuman->kategori);
        $this->assertIsArray($pengumuman->pegawai_ids);
        $this->assertContains($this->pegawai->id, $pengumuman->pegawai_ids);
        $this->assertEquals('1 Pegawai Terpilih', $pengumuman->target_penerima_label);
        $this->assertEquals(1, $pengumuman->total_wa_terkirim);

        $log = WaNotifikasiLog::where('pengumuman_id', $pengumuman->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('terkirim', $log->status);
    }
}
