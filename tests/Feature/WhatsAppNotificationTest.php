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
use Illuminate\Support\Facades\Queue;
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

        // Seed basic configs
        $this->artisan('migrate');

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
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'target' => ['6281234567890'],
                'detail' => 'success',
            ], 200),
        ]);

        $configService = app(KonfigurasiWaService::class);
        $configService->set('fonnte_api_key', 'valid_token');
        $configService->set('wa_notifikasi_enabled', '1');

        $waService = app(FonnteWhatsAppService::class);
        $result = $waService->send('081234567890', 'Uji coba notifikasi');

        $this->assertTrue($result['success']);
        $this->assertEquals('terkirim', $result['status']);
    }

    public function test_konfigurasi_wa_manager_livewire()
    {
        $this->actingAs($this->admin);

        Livewire::test(KonfigurasiWhatsAppManager::class)
            ->set('form.fonnte_api_key', 'my_new_fonnte_token_123')
            ->set('form.wa_notifikasi_enabled', true)
            ->call('simpan')
            ->assertDispatched('notify');

        $configService = app(KonfigurasiWaService::class);
        $this->assertEquals('my_new_fonnte_token_123', $configService->get('fonnte_api_key'));
        $this->assertTrue($configService->isEnabled());
    }

    public function test_pengumuman_manager_create_and_dispatch_wa()
    {
        Queue::fake();
        $this->actingAs($this->admin);

        Livewire::test(PengumumanManager::class)
            ->set('judul', 'Rapat Koordinasi Pilkades 2026')
            ->set('isi', 'Diharapkan seluruh perangkat desa hadir di aula kantor desa.')
            ->set('kategori', 'rapat')
            ->set('target_penerima', 'semua')
            ->set('kirim_wa', true)
            ->call('save')
            ->assertDispatched('notify');

        $pengumuman = Pengumuman::first();
        $this->assertNotNull($pengumuman);
        $this->assertEquals('Rapat Koordinasi Pilkades 2026', $pengumuman->judul);
        $this->assertTrue($pengumuman->kirim_wa);

        // Pastikan job antrian ter-dispatch ke seluruh pegawai ber-no HP
        $expectedCount = Pegawai::where('status_aktif', true)->whereNotNull('no_hp')->where('no_hp', '!=', '')->count();
        Queue::assertPushed(KirimWaNotifikasiPengumumanJob::class, $expectedCount);
    }
}
