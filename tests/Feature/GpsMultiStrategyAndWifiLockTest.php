<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\PengajuanAbsenLuar;
use App\Models\KonfigurasiWifi;
use App\Services\AbsensiSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GpsMultiStrategyAndWifiLockTest extends TestCase
{
    public function test_wifi_diagnosis_service_returns_complete_details()
    {
        $service = app(AbsensiSignatureService::class);

        // Pastikan ada wifi aktif
        KonfigurasiWifi::updateOrCreate(
            ['ip_address' => '192.168.1.0/24'],
            ['nama_jaringan' => 'WiFi Kantor Desa Nangtang', 'is_active' => true]
        );

        $validDiagnosis = $service->getWifiDiagnosis('192.168.1.50');
        $this->assertTrue($validDiagnosis['is_valid']);
        $this->assertEquals('192.168.1.50', $validDiagnosis['client_ip']);
        $this->assertEquals('WiFi Kantor Desa Nangtang', $validDiagnosis['matched_network']);
        $this->assertNull($validDiagnosis['rejection_reason']);

        $invalidDiagnosis = $service->getWifiDiagnosis('114.122.5.88'); // IP Data Seluler Telkomsel
        $this->assertFalse($invalidDiagnosis['is_valid']);
        $this->assertNotNull($invalidDiagnosis['rejection_reason']);
        $this->assertStringContainsString('114.122.5.88', $invalidDiagnosis['rejection_reason']);
    }

    public function test_absen_luar_rejects_brazil_or_foreign_coordinates()
    {
        Storage::fake('public');
        $user = User::first();
        $this->assertNotNull($user);

        // Koordinat Brazil (Lat -14.235, Lng -51.925)
        $response = $this->actingAs($user)
            ->post(route('staf.ajukan.store'), [
                'tanggal'           => now()->toDateString(),
                'jenis'             => 'dinas_luar_pengajuan',
                'judul'             => 'Kegiatan di Luar Indonesia',
                'deskripsi'         => 'Test koordinat luar negeri',
                'latitude'          => -14.2350000,
                'longitude'         => -51.9250000,
                'sumber_koordinat'  => 'gps',
                'tanda_tangan'      => 'data:image/png;base64,' . base64_encode(str_repeat('S', 100)),
            ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_absen_luar_accepts_valid_indonesian_coordinates_with_metadata()
    {
        Storage::fake('public');
        $user = User::first();
        $this->assertNotNull($user);
        $pegawai = $user->pegawai;

        // Bersihkan data hari ini
        PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->delete();

        $fotoLokasi = UploadedFile::fake()->image('bukti.jpg', 300, 300);

        // Koordinat Tasikmalaya, Jawa Barat, Indonesia
        $response = $this->actingAs($user)
            ->post(route('staf.ajukan.store'), [
                'tanggal'           => now()->toDateString(),
                'jenis'             => 'dinas_luar_pengajuan',
                'judul'             => 'Rapat Koordinasi Kecamatan',
                'deskripsi'         => 'Menghadiri rapat koordinasi tingkat kecamatan',
                'foto_lokasi'       => $fotoLokasi,
                'latitude'          => -7.3481234,
                'longitude'         => 108.1234567,
                'alamat_gps'        => 'Desa Nangtang, Kec. Cigalontang, Kab. Tasikmalaya',
                'sumber_koordinat'  => 'gps',
                'akurasi_gps_meter' => 18,
                'tanda_tangan'      => 'data:image/png;base64,' . base64_encode(str_repeat('S', 100)),
            ]);

        $response->assertRedirect(route('staf.riwayat', ['tab' => 'absen_luar']));

        $pengajuan = PengajuanAbsenLuar::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $this->assertNotNull($pengajuan);
        $this->assertEquals('gps', $pengajuan->sumber_koordinat);
        $this->assertEquals(18, $pengajuan->akurasi_gps_meter);
        $this->assertStringContainsString('GPS Fisik Presisi', $pengajuan->label_sumber_koordinat);
    }
}
