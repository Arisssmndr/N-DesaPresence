<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\IzinSakit;
use App\Models\JadwalPiket;
use App\Models\Kehadiran;
use App\Livewire\IzinManager;
use App\Livewire\JadwalPiketManager;
use Livewire\Livewire;
use Carbon\Carbon;

class IzinDanPiketTest extends TestCase
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

    public function test_staf_can_submit_izin_and_sakit_without_attachment()
    {
        $this->actingAs($this->stafUser);

        // 1. Submit Izin
        $responseIzin = $this->post(route('staf.izin.store'), [
            'kategori'        => 'izin',
            'jenis_detail'    => 'izin_pribadi',
            'tanggal_mulai'   => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'keterangan'      => 'Ada keperluan keluarga mendesak',
        ]);

        $responseIzin->assertRedirect(route('staf.izin'));
        $this->assertDatabaseHas('izin_sakits', [
            'pegawai_id' => $this->pegawai->id,
            'jenis'      => 'izin_pribadi',
            'status'     => 'menunggu',
        ]);

        // 2. Submit Sakit
        $responseSakit = $this->post(route('staf.izin.store'), [
            'kategori'        => 'sakit',
            'tanggal_mulai'   => Carbon::tomorrow()->toDateString(),
            'tanggal_selesai' => Carbon::tomorrow()->toDateString(),
            'keterangan'      => 'Demam tinggi dan istirahat',
        ]);

        $responseSakit->assertRedirect(route('staf.izin'));
        $this->assertDatabaseHas('izin_sakits', [
            'pegawai_id' => $this->pegawai->id,
            'jenis'      => 'sakit_tanpa_surat',
            'status'     => 'menunggu',
        ]);
    }

    public function test_admin_can_approve_izin_and_updates_kehadiran_status()
    {
        Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->delete();

        $izin = IzinSakit::create([
            'pegawai_id'      => $this->pegawai->id,
            'jenis'           => 'izin_pribadi',
            'tanggal_mulai'   => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'jumlah_hari'     => 1,
            'keterangan'      => 'Urusan keluarga',
            'status'          => 'menunggu',
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(IzinManager::class)
            ->call('approve', $izin->id);

        $izin->refresh();
        $this->assertEquals('disetujui', $izin->status);

        // Verifikasi kehadiran dengan query model (cross-database compat: SQLite vs MySQL)
        $kehadiran = Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->where('status', 'Izin')
            ->first();
        $this->assertNotNull($kehadiran, 'Kehadiran berstatus Izin tidak ditemukan untuk hari ini');
    }

    public function test_admin_can_create_jadwal_piket_and_staff_can_sign_masuk_and_pulang()
    {
        $this->actingAs($this->adminUser);

        $tglPiket = Carbon::create(2026, 9, 1);
        $tglPiketStr = $tglPiket->toDateString();
        $tglBesokStr = Carbon::create(2026, 9, 2)->toDateString();

        Livewire::test(JadwalPiketManager::class)
            ->set('pegawai_id', $this->pegawai->id)
            ->set('tanggal_piket', $tglPiketStr)
            ->set('jam_mulai', '19:00')
            ->set('jam_selesai', '06:00')
            ->set('keterangan', 'Piket Jaga Malam Balai Desa')
            ->call('save');

        $piket = JadwalPiket::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal_piket', $tglPiketStr)
            ->where('status', 'terjadwal')
            ->first();
        $this->assertNotNull($piket, 'Jadwal piket berstatus terjadwal tidak ditemukan');

        $this->actingAs($this->stafUser);

        // 1. Coba absen masuk sebelum jam mulai (tgl 1 Sep jam 15:00) -> ditolak
        Carbon::setTestNow(Carbon::create(2026, 9, 1, 15, 0, 0));
        $respEarly = $this->post(route('staf.piket.absen'), [
            'piket_id'     => $piket->id,
            'tipe'         => 'masuk',
            'tanda_tangan' => 'data:image/png;base64,sampleSignatureMasukData1234567890',
        ]);
        $respEarly->assertSessionHas('error');
        $piket->refresh();
        $this->assertNull($piket->waktu_absen);

        // 2. Absen masuk tepat waktu (tgl 1 Sep jam 19:30) -> berhasil
        Carbon::setTestNow(Carbon::create(2026, 9, 1, 19, 30, 0));
        $responseMasuk = $this->post(route('staf.piket.absen'), [
            'piket_id'     => $piket->id,
            'tipe'         => 'masuk',
            'tanda_tangan' => 'data:image/png;base64,sampleSignatureMasukData1234567890',
        ]);
        $responseMasuk->assertRedirect(route('staf.beranda'));
        $piket->refresh();
        $this->assertEquals('sedang_piket', $piket->status);
        $this->assertNotNull($piket->waktu_absen);

        // 3. Coba absen pulang sebelum jam selesai (tgl 1 Sep jam 23:00 malam) -> ditolak
        Carbon::setTestNow(Carbon::create(2026, 9, 1, 23, 0, 0));
        $respEarlyPulang = $this->post(route('staf.piket.absen'), [
            'piket_id'     => $piket->id,
            'tipe'         => 'pulang',
            'tanda_tangan' => 'data:image/png;base64,sampleSignaturePulangData1234567890',
        ]);
        $respEarlyPulang->assertSessionHas('error');
        $piket->refresh();
        $this->assertNull($piket->waktu_pulang);

        // 4. Absen pulang saat jam selesai tiba (tgl 2 Sep jam 06:15) -> berhasil
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 6, 15, 0));
        $respPulang = $this->post(route('staf.piket.absen'), [
            'piket_id'     => $piket->id,
            'tipe'         => 'pulang',
            'tanda_tangan' => 'data:image/png;base64,sampleSignaturePulangData1234567890',
        ]);
        $respPulang->assertRedirect(route('staf.beranda'));
        $piket->refresh();
        $this->assertEquals('hadir', $piket->status);
        $this->assertNotNull($piket->waktu_pulang);

        // Verifikasi kehadiran esok hari (2 Sep) otomatis tercatat sebagai "Hadir / Lepas Piket"
        $kehadiranBesok = Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', $tglBesokStr)
            ->where('status', 'Hadir')
            ->first();
        $this->assertNotNull($kehadiranBesok, 'Kehadiran Hadir untuk esok hari tidak ditemukan');
        $this->assertStringContainsString('Lepas Piket', $kehadiranBesok->keterangan);

        Carbon::setTestNow(); // Reset time
    }

    public function test_admin_cannot_create_jadwal_piket_for_female_staff()
    {
        $femalePegawai = Pegawai::where('jenis_kelamin', 'P')->first();
        $this->assertNotNull($femalePegawai, 'Pegawai berjenis kelamin perempuan harus tersedia dari seeder');

        $this->actingAs($this->adminUser);

        Livewire::test(JadwalPiketManager::class)
            ->set('pegawai_id', $femalePegawai->id)
            ->set('tanggal_piket', Carbon::today()->toDateString())
            ->set('jam_mulai', '19:00')
            ->set('jam_selesai', '06:00')
            ->set('keterangan', 'Piket Jaga Malam Balai Desa')
            ->call('save')
            ->assertHasErrors(['pegawai_id']);

        $this->assertDatabaseMissing('jadwal_pikets', [
            'pegawai_id' => $femalePegawai->id,
            'tanggal_piket' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_jadwal_piket_manager_only_lists_male_staff()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(JadwalPiketManager::class);
        $pegawais = $component->viewData('pegawais');

        $this->assertNotEmpty($pegawais);
        foreach ($pegawais as $p) {
            $this->assertEquals('L', $p->jenis_kelamin, "Pegawai {$p->nama_lengkap} harus berjenis kelamin laki-laki");
        }
    }



    public function test_admin_can_generate_pola_jadwal_desa_with_masa_aktif()
    {
        $this->actingAs($this->adminUser);

        $startDate = Carbon::today()->startOfMonth()->toDateString();
        $endDate   = Carbon::today()->endOfMonth()->toDateString();

        Livewire::test(JadwalPiketManager::class)
            ->call('openPolaModal')
            ->set('polaDurasi', '1_bulan')
            ->set('polaTanggalMulai', $startDate)
            ->set('polaOpsiKonflik', 'replace')
            ->set('polaJamMulai', '19:00')
            ->set('polaJamSelesai', '06:00')
            ->call('generatePolaJadwalDesa');

        $pikets = JadwalPiket::with('pegawai')
            ->whereDate('tanggal_piket', '>=', $startDate)
            ->whereDate('tanggal_piket', '<=', $endDate)
            ->get();

        $this->assertNotEmpty($pikets);

        // Pastikan seluruh staf yang terjadwal berjenis kelamin laki-laki
        foreach ($pikets as $p) {
            $this->assertEquals('L', $p->pegawai->jenis_kelamin);
        }

        // Cek hari Senin memiliki 2 staf (Dede Sumirna & Rukanda)
        $seninPiket = $pikets->filter(fn($p) => $p->tanggal_piket->dayOfWeek === 1)->groupBy(fn($p) => $p->tanggal_piket->toDateString())->first();
        if ($seninPiket) {
            $this->assertCount(2, $seninPiket, 'Hari Senin harus memiliki 2 staf yang dijadwalkan bersamaan');
        }
    }

    public function test_admin_can_generate_dummy_mingguan()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(JadwalPiketManager::class)
            ->call('generateDummyMingguan');

        $startOfWeek = Carbon::today()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek   = Carbon::today()->startOfWeek(Carbon::MONDAY)->addDays(6)->toDateString();

        $count = JadwalPiket::whereDate('tanggal_piket', '>=', $startOfWeek)
            ->whereDate('tanggal_piket', '<=', $endOfWeek)
            ->count();
        $this->assertGreaterThanOrEqual(7, $count);
    }

    public function test_admin_can_edit_and_delete_jadwal_piket()
    {
        $this->actingAs($this->adminUser);

        $malePegawai = Pegawai::where('jenis_kelamin', 'L')->first();

        // 1. Buat Jadwal
        $piket = JadwalPiket::create([
            'pegawai_id'    => $malePegawai->id,
            'tanggal_piket' => Carbon::today()->addDays(10)->toDateString(),
            'jam_mulai'     => '19:00:00',
            'jam_selesai'   => '06:00:00',
            'keterangan'    => 'Piket Sebelum Diedit',
            'status'        => 'terjadwal',
            'created_by'    => $this->adminUser->id,
        ]);

        // 2. Edit Jadwal via Livewire
        Livewire::test(JadwalPiketManager::class)
            ->call('openEditModal', $piket->id)
            ->set('keterangan', 'Piket Posko Utama Setelah Diedit')
            ->call('save');

        $piket->refresh();
        $this->assertEquals('Piket Posko Utama Setelah Diedit', $piket->keterangan);

        // 3. Delete Jadwal via Livewire
        Livewire::test(JadwalPiketManager::class)
            ->call('delete', $piket->id);

        $this->assertDatabaseMissing('jadwal_pikets', ['id' => $piket->id]);
    }

    public function test_admin_can_delete_selected_and_kosongkan_bulan_ini()
    {
        $this->actingAs($this->adminUser);

        $malePegawai = Pegawai::where('jenis_kelamin', 'L')->first();
        $year = 2026;
        $month = 9;

        $piket1 = JadwalPiket::create([
            'pegawai_id'    => $malePegawai->id,
            'tanggal_piket' => '2026-09-02',
            'jam_mulai'     => '19:00:00',
            'jam_selesai'   => '06:00:00',
            'keterangan'    => 'Piket Hari 1',
            'status'        => 'terjadwal',
            'created_by'    => $this->adminUser->id,
        ]);

        $piket2 = JadwalPiket::create([
            'pegawai_id'    => $malePegawai->id,
            'tanggal_piket' => '2026-09-10',
            'jam_mulai'     => '19:00:00',
            'jam_selesai'   => '06:00:00',
            'keterangan'    => 'Piket Hari 2',
            'status'        => 'terjadwal',
            'created_by'    => $this->adminUser->id,
        ]);

        // Hapus terpilih via Livewire
        Livewire::test(JadwalPiketManager::class)
            ->set('bulan', $month)
            ->set('tahun', $year)
            ->set('selectedPiketIds', [(string) $piket1->id])
            ->call('deleteSelected');

        $this->assertFalse(JadwalPiket::where('id', $piket1->id)->exists());
        $this->assertTrue(JadwalPiket::where('id', $piket2->id)->exists());

        // Kosongkan seluruh bulan
        Livewire::test(JadwalPiketManager::class)
            ->set('bulan', $month)
            ->set('tahun', $year)
            ->call('kosongkanBulanIni');

        $this->assertFalse(JadwalPiket::where('id', $piket2->id)->exists());
    }

    public function test_staff_who_checked_in_cannot_submit_absen_luar_or_izin_on_same_day()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $today = Carbon::today()->toDateString();

        // 1. Simulasikan pegawai sudah absen masuk langsung
        Kehadiran::updateOrCreate(
            ['pegawai_id' => $this->pegawai->id, 'tanggal' => $today],
            [
                'jam_masuk' => '07:45:00',
                'status'    => 'Hadir',
                'sumber_data' => 'web_signature'
            ]
        );

        $this->actingAs($this->stafUser);

        // 2. Coba ajukan Absen Luar pada hari yang sama -> DITOLAK
        $foto = \Illuminate\Http\UploadedFile::fake()->image('lokasi.jpg');
        $responseAbsenLuar = $this->post(route('staf.ajukan.store'), [
            'tanggal'      => $today,
            'jenis'        => 'dinas_luar_pengajuan',
            'judul'        => 'Rapat Koordinasi Kecamatan',
            'deskripsi'    => 'Menghadiri rapat di kantor camat',
            'foto_lokasi'  => $foto,
            'latitude'     => -7.350000,
            'longitude'    => 108.200000,
            'tanda_tangan' => str_repeat('A', 120),
        ]);

        $responseAbsenLuar->assertSessionHas('error');

        // 3. Coba ajukan Izin pada hari yang sama -> DITOLAK
        $responseIzin = $this->post(route('staf.izin.store'), [
            'kategori'        => 'izin',
            'jenis_detail'    => 'izin_pribadi',
            'tanggal_mulai'   => $today,
            'tanggal_selesai' => $today,
            'keterangan'      => 'Izin keperluan pribadi mendadak',
        ]);

        $responseIzin->assertSessionHas('error');
    }

    public function test_staff_with_active_izin_period_cannot_check_in_or_submit_absen_luar()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $startDate = Carbon::today()->subDays(2)->toDateString();
        $endDate = Carbon::today()->addDays(2)->toDateString();

        // Bersihkan kehadiran eksisting
        Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->delete();

        // 1. Buat izin aktif (mencakup hari ini)
        $izin = IzinSakit::create([
            'pegawai_id'      => $this->pegawai->id,
            'jenis'           => 'sakit_dengan_surat',
            'tanggal_mulai'   => $startDate,
            'tanggal_selesai' => $endDate,
            'jumlah_hari'     => 5,
            'keterangan'      => 'Sakit tifus rawat jalan',
            'status'          => 'disetujui',
            'diproses_oleh'   => $this->adminUser->id,
        ]);

        $this->actingAs($this->stafUser);

        // 2. Akses lembar absen masuk -> DITOLAK
        $responseHalaman = $this->get(route('staf.absen.form', 'masuk'));
        $responseHalaman->assertRedirect(route('staf.beranda'));
        $responseHalaman->assertSessionHas('error');

        // 3. Submit absen langsung via API -> DITOLAK (403)
        $responseSubmit = $this->postJson(route('staf.absen.submit'), [
            'jenis'        => 'masuk',
            'tanda_tangan' => str_repeat('B', 120),
        ]);
        $responseSubmit->assertStatus(403);

        // 4. Submit pengajuan absen luar di tengah periode izin -> DITOLAK
        $foto = \Illuminate\Http\UploadedFile::fake()->image('lokasi.jpg');
        $responseAbsenLuar = $this->post(route('staf.ajukan.store'), [
            'tanggal'      => Carbon::today()->toDateString(),
            'jenis'        => 'dinas_luar_pengajuan',
            'judul'        => 'Tugas luar saat izin',
            'deskripsi'    => 'Deskripsi tugas',
            'foto_lokasi'  => $foto,
            'latitude'     => -7.350000,
            'longitude'    => 108.200000,
            'tanda_tangan' => str_repeat('C', 120),
        ]);
        $responseAbsenLuar->assertSessionHas('error');

        // 5. Admin mencoba override manual pada tanggal izin -> DITOLAK dengan error validasi
        $this->actingAs($this->adminUser);

        Livewire::test(IzinManager::class)
            ->set('manual_pegawai_id', $this->pegawai->id)
            ->set('manual_tanggal', Carbon::today()->toDateString())
            ->set('manual_status', 'Hadir')
            ->set('manual_keterangan', 'Mencoba override di tanggal sakit')
            ->call('saveManualAttendance')
            ->assertHasErrors(['manual_tanggal']);
    }

    public function test_admin_can_record_direct_manual_attendance_in_izin_manager()
    {
        $targetDateMulai = Carbon::today()->subDays(5)->toDateString();
        $targetDateSelesai = Carbon::today()->subDays(3)->toDateString();

        // Bersihkan data tanggal tersebut
        IzinSakit::where('pegawai_id', $this->pegawai->id)->whereDate('tanggal_mulai', '<=', $targetDateSelesai)->whereDate('tanggal_selesai', '>=', $targetDateMulai)->delete();
        Kehadiran::where('pegawai_id', $this->pegawai->id)->whereBetween('tanggal', [$targetDateMulai, $targetDateSelesai])->delete();

        $this->actingAs($this->adminUser);

        // Test Input Sakit Multi-Hari (3 hari: H-5 s/d H-3)
        Livewire::test(IzinManager::class)
            ->set('manual_pegawai_id', $this->pegawai->id)
            ->set('manual_tanggal_mulai', $targetDateMulai)
            ->set('manual_tanggal_selesai', $targetDateSelesai)
            ->set('manual_status', 'Sakit')
            ->set('manual_keterangan', 'Sakit demam rawat jalan 3 hari')
            ->call('saveManualAttendance')
            ->assertHasNoErrors();

        // Verifikasi seluruh 3 hari langsung tercatat di Kehadiran berstatus Sakit
        $kehadirans = Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', '>=', $targetDateMulai)
            ->whereDate('tanggal', '<=', $targetDateSelesai)
            ->get();

        $this->assertCount(3, $kehadirans);
        foreach ($kehadirans as $k) {
            $this->assertEquals('Sakit', $k->status);
            $this->assertEquals('manual_admin', $k->sumber_data);
            $this->assertEquals($this->adminUser->id, $k->diverifikasi_oleh);
        }
    }
}

