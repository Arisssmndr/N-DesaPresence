<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\PengajuanAbsenLuar;
use App\Models\Kehadiran;
use App\Livewire\PengajuanAbsenManager;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DinasLuarKategoriTest extends TestCase
{
    protected User $user;
    protected Pegawai $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::first() ?? User::factory()->create(['role' => 'Admin']);
        $this->pegawai = Pegawai::first() ?? Pegawai::create([
            'user_id' => $this->user->id,
            'nama_lengkap' => 'Asep Saepuloh',
            'nik' => '3206123456780001',
            'nip_desa' => '199001012020011001',
            'jabatan_id' => Jabatan::first()?->id ?? 1,
            'status_pegawai' => 'Perangkat Desa',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);
    }

    public function test_can_submit_dinas_luar_undangan()
    {
        $this->actingAs($this->user);

        // Hapus pengajuan hari ini jika ada
        PengajuanAbsenLuar::where('pegawai_id', $this->pegawai->id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->delete();

        $file = UploadedFile::fake()->create('surat_undangan.pdf', 500, 'application/pdf');

        $response = $this->post(route('staf.ajukan.store'), [
            'tanggal'              => Carbon::today()->toDateString(),
            'jenis'                => 'dinas_luar_undangan',
            'instansi_pengundang'  => 'Kantor Kecamatan Cigalontang',
            'judul'                => 'Rapat Koordinasi PATEN & Pemerintahan',
            'deskripsi'            => 'Menghadiri rakor pembahasan program pembangunan dan pelayanan administrasi terpadu kecamatan.',
            'file_dokumen'         => $file,
            'latitude'             => -7.3456789,
            'longitude'            => 108.1234567,
            'alamat_gps'           => 'Kecamatan Cigalontang',
            'tanda_tangan'         => 'data:image/png;base64,' . base64_encode('dummy_signature_content_long_enough_to_pass_validation_1234567890'),
        ]);

        $response->assertRedirect(route('staf.riwayat.pengajuan'));

        $this->assertDatabaseHas('pengajuan_absen_luars', [
            'pegawai_id'          => $this->pegawai->id,
            'jenis'               => 'dinas_luar_undangan',
            'instansi_pengundang' => 'Kantor Kecamatan Cigalontang',
            'judul'               => 'Rapat Koordinasi PATEN & Pemerintahan',
            'status'              => 'menunggu',
        ]);
    }

    public function test_can_submit_dinas_luar_surat_tugas()
    {
        $this->actingAs($this->user);

        $testDate = Carbon::yesterday()->toDateString();
        PengajuanAbsenLuar::where('pegawai_id', $this->pegawai->id)
            ->where('tanggal', $testDate)
            ->delete();

        $file = UploadedFile::fake()->create('spt_kades.pdf', 500, 'application/pdf');

        $response = $this->post(route('staf.ajukan.store'), [
            'tanggal'            => $testDate,
            'jenis'              => 'dinas_luar_surat_tugas',
            'nomor_surat_tugas'  => '090/045/SPT/Pemdes/2026',
            'judul'              => 'Peninjauan Lapangan Program PTSL',
            'deskripsi'          => 'Melaksanakan peninjauan dan pengukuran batas tanah program PTSL.',
            'file_dokumen'       => $file,
            'latitude'           => -7.3456789,
            'longitude'          => 108.1234567,
            'alamat_gps'         => 'Dusun Kawunglancar',
            'tanda_tangan'       => 'data:image/png;base64,' . base64_encode('dummy_signature_content_long_enough_to_pass_validation_1234567890'),
        ]);

        $response->assertRedirect(route('staf.riwayat.pengajuan'));

        $this->assertDatabaseHas('pengajuan_absen_luars', [
            'pegawai_id'        => $this->pegawai->id,
            'jenis'             => 'dinas_luar_surat_tugas',
            'nomor_surat_tugas' => '090/045/SPT/Pemdes/2026',
            'judul'             => 'Peninjauan Lapangan Program PTSL',
        ]);
    }

    public function test_manager_can_approve_dinas_luar_and_create_kehadiran()
    {
        $this->actingAs($this->user);

        $testDate = Carbon::today()->subDays(2)->toDateString();
        PengajuanAbsenLuar::where('pegawai_id', $this->pegawai->id)
            ->where('tanggal', $testDate)
            ->delete();
        Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->where('tanggal', $testDate)
            ->delete();

        $pengajuan = PengajuanAbsenLuar::create([
            'pegawai_id'          => $this->pegawai->id,
            'user_id'             => $this->user->id,
            'tanggal'             => $testDate,
            'jenis'               => 'dinas_luar_undangan',
            'instansi_pengundang' => 'DPMD Kabupaten Tasikmalaya',
            'judul'               => 'Bimtek Siskeudes',
            'deskripsi'           => 'Bimtek pengelolaan keuangan desa berbasis Siskeudes online.',
            'latitude'            => -7.3456789,
            'longitude'           => 108.1234567,
            'status'              => 'menunggu',
        ]);

        Livewire::test(PengajuanAbsenManager::class)
            ->call('konfirmasiSetujui', $pengajuan->id)
            ->call('setujui')
            ->assertDispatched('notify');

        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);

        $this->assertDatabaseHas('kehadirans', [
            'pegawai_id'  => $this->pegawai->id,
            'tanggal'     => $testDate,
            'status'      => 'Dinas Luar',
            'sumber_data' => 'pengajuan_luar',
        ]);

        $kehadiran = Kehadiran::where('pegawai_id', $this->pegawai->id)->where('tanggal', $testDate)->first();
        $this->assertStringContainsString('Dinas Luar (Undangan)', $kehadiran->keterangan);
        $this->assertStringContainsString('DPMD Kabupaten Tasikmalaya', $kehadiran->keterangan);
    }
}
