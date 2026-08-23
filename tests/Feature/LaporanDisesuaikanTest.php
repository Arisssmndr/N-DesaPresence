<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kehadiran;
use App\Models\AbsensiDisesuaikan;
use App\Livewire\LaporanDisesuaikanManager;
use Livewire\Livewire;
use Carbon\Carbon;

class LaporanDisesuaikanTest extends TestCase
{
    protected $adminUser;
    protected $kadesUser;
    protected $stafUser;
    protected $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'username' => 'admin_test_adj',
        ]);

        $this->kadesUser = User::where('role', 'kepala_desa')->first() ?? User::factory()->create([
            'role' => 'kepala_desa',
            'username' => 'kades_test_adj',
        ]);

        $this->pegawai = Pegawai::where('status_aktif', true)->first() ?? Pegawai::factory()->create([
            'nama_lengkap' => 'Budi Santoso',
            'status_aktif' => true,
        ]);

        $this->stafUser = User::where('pegawai_id', $this->pegawai->id)->first() ?? User::factory()->create([
            'role' => 'perangkat',
            'pegawai_id' => $this->pegawai->id,
            'username' => 'budi_staf_adj',
        ]);
    }

    public function test_admin_can_access_laporan_disesuaikan_page()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('laporan-disesuaikan.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Presensi Disesuaikan (Shadow Layer)');
    }

    public function test_kades_and_staff_cannot_access_laporan_disesuaikan_page()
    {
        $this->actingAs($this->kadesUser);
        $responseKades = $this->get(route('laporan-disesuaikan.index'));
        $responseKades->assertStatus(403);

        $this->actingAs($this->stafUser);
        $responseStaf = $this->get(route('laporan-disesuaikan.index'));
        $responseStaf->assertStatus(403);
    }

    public function test_adjusting_attendance_does_not_modify_pure_kehadirans_table()
    {
        $this->actingAs($this->adminUser);
        $testDate = Carbon::today()->subDays(5)->toDateString();

        // 1. Buat data kehadiran murni (Status: Sakit, tanpa jam masuk)
        Kehadiran::where('pegawai_id', $this->pegawai->id)->whereDate('tanggal', $testDate)->delete();
        AbsensiDisesuaikan::where('pegawai_id', $this->pegawai->id)->whereDate('tanggal', $testDate)->delete();

        $kehadiranMurni = Kehadiran::create([
            'pegawai_id'  => $this->pegawai->id,
            'tanggal'     => $testDate,
            'status'      => 'Sakit',
            'sumber_data' => 'manual_admin',
            'keterangan'  => 'Surat dokter demam',
        ]);

        // 2. Lakukan penyesuaian via Livewire LaporanDisesuaikanManager menjadi Hadir
        Livewire::test(LaporanDisesuaikanManager::class)
            ->call('bukaEdit', $this->pegawai->id, $testDate)
            ->set('editStatusDisesuaikan', 'Hadir')
            ->set('editJamMasuk', '08:00')
            ->set('editJamPulang', '15:30')
            ->call('simpanEdit');

        // 3. Verifikasi: Record di absensi_disesuaikans tercipta dengan status Hadir
        $adjRecord = AbsensiDisesuaikan::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', $testDate)
            ->first();

        $this->assertNotNull($adjRecord);
        $this->assertEquals('Sakit', $adjRecord->status_asli);
        $this->assertEquals('Hadir', $adjRecord->status_disesuaikan);

        // 4. Verifikasi KUNCI: Data murni di tabel kehadirans TETAP SAKIT & TIDAK BERUBAH SAMA SEKALI
        $kehadiranMurni->refresh();
        $this->assertEquals('Sakit', $kehadiranMurni->status);
        $this->assertEquals('Surat dokter demam', $kehadiranMurni->keterangan);
        $this->assertNull($kehadiranMurni->jam_masuk);
    }

    public function test_signature_borrowing_fetches_recent_signature_of_the_same_employee()
    {
        $this->actingAs($this->adminUser);
        $targetDate = Carbon::today()->subDays(2)->toDateString();
        $hMinus1 = Carbon::today()->subDays(3)->toDateString();

        // Buat data kehadiran 1 hari sebelum target dengan tanda tangan
        Kehadiran::where('pegawai_id', $this->pegawai->id)->whereDate('tanggal', $hMinus1)->delete();
        Kehadiran::create([
            'pegawai_id'          => $this->pegawai->id,
            'tanggal'             => $hMinus1,
            'status'              => 'Hadir',
            'jam_masuk'           => '07:45:00',
            'tanda_tangan_masuk'  => 'data:image/png;base64,sample_signature_budi_1234567890',
        ]);

        // Panggil helper pencarian tanda tangan
        $result = AbsensiDisesuaikan::cariTandaTanganPegawai($this->pegawai->id, $targetDate, 7);

        $this->assertNotNull($result);
        $this->assertEquals('data:image/png;base64,sample_signature_budi_1234567890', $result['signature']);
        $this->assertEquals($hMinus1, $result['date']);
    }

    public function test_all_four_pdf_endpoints_generate_successfully()
    {
        $this->actingAs($this->adminUser);
        $today = Carbon::today()->toDateString();
        $month = (int) date('m');
        $year = (int) date('Y');

        // 1. PDF Harian
        $resHarian = $this->get(route('laporan-disesuaikan.harian', ['tanggal' => $today]));
        $resHarian->assertStatus(200);
        $this->assertEquals('application/pdf', $resHarian->headers->get('content-type'));

        // 2. PDF Bulanan
        $resBulanan = $this->get(route('laporan-disesuaikan.bulanan', ['bulan' => $month, 'tahun' => $year]));
        $resBulanan->assertStatus(200);
        $this->assertEquals('application/pdf', $resBulanan->headers->get('content-type'));

        // 3. PDF Tahunan
        $resTahunan = $this->get(route('laporan-disesuaikan.tahunan', ['tahun' => $year]));
        $resTahunan->assertStatus(200);
        $this->assertEquals('application/pdf', $resTahunan->headers->get('content-type'));

        // 4. PDF Rentang Bebas
        $startDate = Carbon::today()->subDays(7)->toDateString();
        $resRentang = $this->get(route('laporan-disesuaikan.rentang', [
            'tanggal_mulai'   => $startDate,
            'tanggal_selesai' => $today,
        ]));
        $resRentang->assertStatus(200);
        $this->assertEquals('application/pdf', $resRentang->headers->get('content-type'));
    }

    public function test_weekend_adjusted_attendance_renders_as_hadir_in_pdf()
    {
        $this->actingAs($this->adminUser);
        // Tanggal 23 Agustus 2026 adalah hari Minggu (Weekend)
        $weekendSunday = '2026-08-23';

        AbsensiDisesuaikan::updateOrCreate(
            ['pegawai_id' => $this->pegawai->id, 'tanggal' => $weekendSunday],
            [
                'status_asli'        => 'Libur',
                'status_disesuaikan' => 'Hadir',
                'jam_masuk'          => '08:00:00',
                'jam_pulang'         => '15:30:00',
                'durasi_kerja_menit' => 450,
            ]
        );

        $response = $this->get(route('laporan-disesuaikan.harian', ['tanggal' => $weekendSunday]));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
