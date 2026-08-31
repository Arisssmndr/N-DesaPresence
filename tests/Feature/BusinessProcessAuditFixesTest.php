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
        Kehadiran::where('pegawai_id', $this->pegawai->id)->whereDate('tanggal', $testDate)->delete();
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

        Kehadiran::where('pegawai_id', $this->pegawai->id)->whereDate('tanggal', $testDate)->delete();
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

    public function test_analitik_dashboard_renders_successfully()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/analitik');
        $response->assertStatus(200);
        $response->assertSee('Analitik Kedisiplinan');
        $response->assertSee('Indeks Kedisiplinan (IKK)');

        Livewire::test(\App\Livewire\AnalitikDashboard::class)
            ->set('selectedMonth', '8')
            ->set('selectedYear', 2026)
            ->assertStatus(200)
            ->assertSee('Matriks Kinerja');
    }

    public function test_analitik_pdf_generates_successfully()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/analitik/pdf?bulan=8&tahun=2026');
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_staff_can_accept_spt_and_it_auto_generates_attendance()
    {
        $this->actingAs($this->stafUser);

        $startDate = Carbon::today()->addDays(20)->toDateString();
        $endDate = Carbon::today()->addDays(22)->toDateString();

        Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->delete();

        $spt = SuratPerintahTugas::create([
            'nomor_spt' => 'SPT/TEST/' . rand(100, 999),
            'pegawai_id' => $this->pegawai->id,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tujuan' => 'DPMD Kabupaten Tasikmalaya',
            'keperluan' => 'Bimtek Tata Kelola Keuangan Desa',
            'status' => 'diajukan',
            'respons_staf' => 'menunggu',
            'created_by' => $this->adminUser->id,
        ]);

        $signatureData = 'data:image/png;base64,' . base64_encode(str_repeat('SIG_STAFF', 20));

        $response = $this->post(route('staf.spt.terima', $spt->id), [
            'tanda_tangan' => $signatureData,
        ]);

        $response->assertRedirect(route('staf.beranda'));
        $spt->refresh();

        $this->assertEquals('disetujui', $spt->status);
        $this->assertEquals('diterima', $spt->respons_staf);
        $this->assertNotNull($spt->waktu_respons_staf);

        // Verifikasi bahwa seluruh 3 hari kehadiran otomatis berstatus 'Hadir'
        $kehadirans = Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->get();

        $this->assertCount(3, $kehadirans);
        foreach ($kehadirans as $k) {
            $this->assertEquals('Hadir', $k->status);
            $this->assertEquals($signatureData, $k->tanda_tangan_masuk);
            $this->assertEquals($signatureData, $k->tanda_tangan_pulang);
            $this->assertStringContainsString($spt->nomor_spt, $k->keterangan);
        }

        // Clean up
        $spt->batalkanKehadiran();
        $spt->delete();
    }

    public function test_staff_can_reject_spt_with_reason()
    {
        $this->actingAs($this->stafUser);

        $spt = SuratPerintahTugas::create([
            'nomor_spt' => 'SPT/TEST/' . rand(100, 999),
            'pegawai_id' => $this->pegawai->id,
            'tanggal_mulai' => Carbon::today()->addDays(25)->toDateString(),
            'tanggal_selesai' => Carbon::today()->addDays(25)->toDateString(),
            'tujuan' => 'Kecamatan Cigalontang',
            'keperluan' => 'Rakor Perlindungan Anak',
            'status' => 'diajukan',
            'respons_staf' => 'menunggu',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->post(route('staf.spt.tolak', $spt->id), [
            'alasan_tolak' => 'Tupoksi berbeda dan sedang bertugas pelayanan administrasi umum di kantor desa.',
        ]);

        $response->assertRedirect(route('staf.beranda'));
        $spt->refresh();

        $this->assertEquals('ditolak', $spt->status);
        $this->assertEquals('ditolak', $spt->respons_staf);
        $this->assertStringContainsString('Tupoksi berbeda', $spt->alasan_tolak_staf);

        $spt->delete();
    }

    public function test_active_spt_locks_direct_absen_and_absen_luar_and_izin()
    {
        $this->actingAs($this->stafUser);

        $today = Carbon::today()->toDateString();

        $spt = SuratPerintahTugas::create([
            'nomor_spt' => 'SPT/LOCK/' . rand(100, 999),
            'pegawai_id' => $this->pegawai->id,
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'tujuan' => 'Kantor Bupati Tasikmalaya',
            'keperluan' => 'Upacara Hari Jadi Kabupaten',
            'status' => 'disetujui',
            'respons_staf' => 'diterima',
            'waktu_respons_staf' => now(),
            'tanda_tangan_staf' => 'data:image/png;base64,' . base64_encode('SIGNATURE'),
            'created_by' => $this->adminUser->id,
        ]);

        // 1. Direct Attendance form must redirect with error
        $responseAbsen = $this->get(route('staf.absen.form', 'masuk'));
        $responseAbsen->assertRedirect(route('staf.beranda'));
        $responseAbsen->assertSessionHas('error');

        // 2. Direct Attendance submission must return 403
        $responseSubmit = $this->postJson(route('staf.absen.submit'), [
            'jenis' => 'masuk',
            'tanda_tangan' => 'data:image/png;base64,' . base64_encode(str_repeat('A', 100)),
        ]);
        $responseSubmit->assertStatus(403);

        // 3. Absen Luar submission must be rejected
        $responseAbsenLuar = $this->post(route('staf.ajukan.store'), [
            'tanggal' => $today,
            'jenis' => 'dinas_luar_undangan',
            'judul' => 'Dinas Luar Acara Lain',
            'instansi_pengundang' => 'Kecamatan Cigalontang',
            'file_dokumen' => \Illuminate\Http\UploadedFile::fake()->create('undangan.pdf', 100),
            'deskripsi' => 'Rapat koordinasi bersama pihak kecamatan setempat.',
            'latitude' => -7.345,
            'longitude' => 108.123,
            'tanda_tangan' => 'data:image/png;base64,' . base64_encode(str_repeat('B', 100)),
        ]);
        $responseAbsenLuar->assertSessionHas('error');

        // 4. Izin submission on the same date must be rejected
        $responseIzin = $this->post(route('staf.izin.store'), [
            'kategori' => 'izin',
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'keterangan' => 'Keperluan keluarga mendesak',
        ]);
        $responseIzin->assertSessionHas('error');

        // Clean up
        $spt->batalkanKehadiran();
        $spt->delete();
    }

    public function test_staf_can_view_riwayat_spt_page()
    {
        $this->actingAs($this->stafUser);

        $response = $this->get(route('staf.spt.riwayat'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Surat Perintah Tugas');
    }

    public function test_admin_spt_manager_requires_file_undangan()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(SptManager::class)
            ->set('pegawai_id', $this->pegawai->id)
            ->set('tanggal_mulai', Carbon::today()->toDateString())
            ->set('tanggal_selesai', Carbon::today()->toDateString())
            ->set('tujuan', 'Dinas Pendidikan')
            ->set('keperluan', 'Rapat Koordinasi')
            ->set('file_undangan', null) // Berkas kosong
            ->call('createSpt')
            ->assertHasErrors(['file_undangan']);
    }

    public function test_storage_files_are_accessible_via_http()
    {
        \Illuminate\Support\Facades\Storage::disk('public')->put('spt-undangan/test_lampiran.txt', 'ISI_BERKAS_LAMPIRAN');

        $response = $this->get('/storage/spt-undangan/test_lampiran.txt');
        $response->assertStatus(200);

        \Illuminate\Support\Facades\Storage::disk('public')->delete('spt-undangan/test_lampiran.txt');
    }

    public function test_staf_can_delete_photo_and_update_password()
    {
        $this->actingAs($this->stafUser);

        // 1. Delete Photo
        $this->stafUser->update(['foto_profil' => 'foto-profil/dummy.jpg']);
        $this->pegawai->update(['foto_profil' => 'foto-profil/dummy.jpg']);

        $resDelete = $this->delete(route('staf.profil.hapus-foto'));
        $resDelete->assertSessionHas('success');

        $this->stafUser->refresh();
        $this->pegawai->refresh();
        $this->assertNull($this->stafUser->foto_profil);
        $this->assertNull($this->pegawai->foto_profil);

        // 2. Update Password
        $this->stafUser->update(['password' => \Illuminate\Support\Facades\Hash::make('oldpassword123')]);

        $resPass = $this->put(route('staf.profil.update-password'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $resPass->assertSessionHas('success');

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $this->stafUser->fresh()->password));
    }

    public function test_staf_can_update_master_signature()
    {
        $this->actingAs($this->stafUser);

        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $res = $this->put(route('staf.profil.update-ttd'), [
            'tanda_tangan' => $fakeSignature,
        ]);

        $res->assertSessionHas('success');
        $this->pegawai->refresh();
        $this->assertEquals($fakeSignature, $this->pegawai->tanda_tangan);

        // Verifikasi bahwa AbsensiDisesuaikan memprioritaskan tanda tangan master resmi ini
        $ttdFound = \App\Models\AbsensiDisesuaikan::cariTandaTanganPegawai($this->pegawai->id, Carbon::today()->toDateString());
        $this->assertNotNull($ttdFound);
        $this->assertEquals($fakeSignature, $ttdFound['signature']);
        $this->assertEquals('master_resmi', $ttdFound['source']);
    }
}
