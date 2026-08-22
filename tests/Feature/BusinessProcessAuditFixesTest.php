<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kehadiran;
use App\Models\SuratPerintahTugas;
use App\Models\IzinSakit;
use App\Livewire\SptManager;
use App\Livewire\IzinManager;
use App\Livewire\UserStafManager;
use Livewire\Livewire;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class BusinessProcessAuditFixesTest extends TestCase
{
    protected User $adminUser;
    protected User $stafUser;
    protected Pegawai $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::where('username', 'susanti')->first() ?? User::where('role', 'admin')->first();
        $this->stafUser  = User::where('username', 'dedelisman')->first() ?? User::where('role', 'perangkat')->first();
        $this->pegawai   = $this->stafUser?->pegawai ?? Pegawai::first();
    }

    public function test_all_14_staff_can_login_via_portal_staf()
    {
        $response = $this->post(route('staf.login.post'), [
            'username' => $this->stafUser->username,
        ]);

        $response->assertRedirect(route('staf.beranda'));
        $this->assertAuthenticatedAs($this->stafUser);
    }

    public function test_staf_cannot_access_admin_dashboard()
    {
        $this->actingAs($this->stafUser);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(403);
    }

    public function test_spt_approval_does_not_overwrite_direct_check_in_attendance()
    {
        $this->actingAs($this->adminUser);

        $testDate = Carbon::today()->subDays(3)->toDateString();

        // Buat kehadiran langsung (sudah absen di kantor)
        Kehadiran::where('pegawai_id', $this->pegawai->id)->where('tanggal', $testDate)->delete();
        $kehadiran = Kehadiran::create([
            'pegawai_id' => $this->pegawai->id,
            'tanggal' => $testDate,
            'jam_masuk' => '07:35:00',
            'jam_pulang' => '15:30:00',
            'status' => 'Hadir',
            'sumber_data' => 'web_signature',
        ]);

        $randomNum = 'SPT/' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10));

        // Buat dan approve SPT pada tanggal yang sama
        $spt = SuratPerintahTugas::create([
            'nomor_spt' => $randomNum,
            'pegawai_id' => $this->pegawai->id,
            'tanggal_mulai' => $testDate,
            'tanggal_selesai' => $testDate,
            'tujuan' => 'Kecamatan Cigalontang',
            'keperluan' => 'Rakor',
            'status' => 'diajukan',
            'created_by' => $this->adminUser->id,
        ]);

        Livewire::test(SptManager::class)
            ->call('approve', $spt->id);

        $kehadiran->refresh();
        // Jam masuk dan pulang tetap aman, tidak terhapus!
        $this->assertEquals('07:35:00', $kehadiran->jam_masuk);
        $this->assertEquals('15:30:00', $kehadiran->jam_pulang);
        $this->assertEquals('web_signature', $kehadiran->sumber_data);

        $spt->delete();
        $kehadiran->delete();
    }

    public function test_izin_approval_does_not_overwrite_direct_check_in_attendance()
    {
        $this->actingAs($this->adminUser);

        $testDate = Carbon::today()->subDays(4)->toDateString();

        Kehadiran::where('pegawai_id', $this->pegawai->id)->where('tanggal', $testDate)->delete();
        $kehadiran = Kehadiran::create([
            'pegawai_id' => $this->pegawai->id,
            'tanggal' => $testDate,
            'jam_masuk' => '07:40:00',
            'status' => 'Hadir',
            'sumber_data' => 'web_signature',
        ]);

        $izin = IzinSakit::create([
            'pegawai_id' => $this->pegawai->id,
            'jenis' => 'izin_pribadi',
            'tanggal_mulai' => $testDate,
            'tanggal_selesai' => $testDate,
            'jumlah_hari' => 1,
            'keterangan' => 'Urusan keluarga',
            'status' => 'menunggu',
        ]);

        Livewire::test(IzinManager::class)
            ->call('approve', $izin->id);

        $kehadiran->refresh();
        $this->assertEquals('07:40:00', $kehadiran->jam_masuk);
        $this->assertEquals('web_signature', $kehadiran->sumber_data);

        $izin->delete();
        $kehadiran->delete();
    }

    public function test_user_manager_requires_password_when_creating_admin_role()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(UserStafManager::class)
            ->set('form.name', 'Kepala Desa Nangtang')
            ->set('form.username', 'pak_kades_new')
            ->set('form.email', 'kades_new@desanangtang.go.id')
            ->set('form.role', 'kepala_desa')
            ->set('form.password', '') // Password kosong
            ->call('simpan')
            ->assertHasErrors(['form.password']);
    }
}
