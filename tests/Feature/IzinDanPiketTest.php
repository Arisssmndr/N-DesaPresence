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

    public function test_admin_can_create_jadwal_piket_and_staff_can_sign()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(JadwalPiketManager::class)
            ->set('pegawai_id', $this->pegawai->id)
            ->set('tanggal_piket', Carbon::today()->toDateString())
            ->set('jam_mulai', '19:00')
            ->set('jam_selesai', '06:00')
            ->set('keterangan', 'Piket Jaga Malam Balai Desa')
            ->call('save');

        // Verifikasi jadwal piket dengan query model (cross-database compat)
        $piket = JadwalPiket::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal_piket', Carbon::today()->toDateString())
            ->where('status', 'terjadwal')
            ->first();
        $this->assertNotNull($piket, 'Jadwal piket berstatus terjadwal tidak ditemukan');

        // Staf signs attendance
        $this->actingAs($this->stafUser);

        $response = $this->post(route('staf.piket.absen'), [
            'piket_id'     => $piket->id,
            'tanda_tangan' => 'data:image/png;base64,sampleSignatureData',
        ]);

        $response->assertRedirect(route('staf.beranda'));

        $piket->refresh();
        $this->assertEquals('hadir', $piket->status);
        $this->assertNotNull($piket->waktu_absen);

        // Verifikasi kehadiran besok dengan whereDate (cross-database compat)
        $besok = Carbon::today()->addDay()->toDateString();
        $kehadiranBesok = Kehadiran::where('pegawai_id', $this->pegawai->id)
            ->whereDate('tanggal', $besok)
            ->where('status', 'Hadir')
            ->first();
        $this->assertNotNull($kehadiranBesok, 'Kehadiran Hadir untuk esok hari tidak ditemukan');
        $this->assertStringContainsString('Lepas Piket', $kehadiranBesok->keterangan);

    }
}
