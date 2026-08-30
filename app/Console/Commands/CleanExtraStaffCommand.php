<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\User;

class CleanExtraStaffCommand extends Command
{
    protected $signature = 'desa:clean-extra-staff';
    protected $description = 'Bersihkan pegawai dan user yang bukan 14 data resmi Desa Nangtang';

    public function handle(): int
    {
        $officialNiks = [
            '3206270107660001', // Daday Daryat
            '3206272909120014', // Susanti
            '3206270309050012', // Mela Marsela
            '3206270309050011', // Heri Ginanjar
            '3206270207880001', // Dede Sumirna
            '3206270309052289', // Dadah Jubaedah
            '3206270309051338', // Apip Mansur
            '3206272805120009', // Yayan Taryana
            '3206270503111371', // Zailani Rahmat
            '3206270503110211', // Rukanda
            '3206270309051231', // Abun Suparman
            '3206272203160011', // Dedi Suherman
            '3206272405930001', // Dede Lisman
            '3206274906980001', // Anggi Widiyani
        ];

        $officialUsernames = [
            'dadaydaryat',
            'susanti',
            'melamarsela',
            'heriginanjar',
            'dedesumirna',
            'dadahjubaedah',
            'apipmansur',
            'yayantaryana',
            'zailanirahmat',
            'rukanda',
            'abunsuparman',
            'dedisuherman',
            'dedelisman',
            'anggiwidiyani',
        ];

        $this->info('=== STATUS DATA PEGAWAI SEBELUMNYA ===');
        $this->line('Total Pegawai di DB: ' . Pegawai::count());
        foreach (Pegawai::with('jabatan')->get() as $p) {
            $this->line("- [ID {$p->id}] {$p->nama_lengkap} (NIK: {$p->nik}, Jabatan: " . ($p->jabatan->nama_jabatan ?? '-') . ")");
        }

        // Hapus pegawai yang bukan dari 14 data resmi
        $extraPegawais = Pegawai::whereNotIn('nik', $officialNiks)->get();
        foreach ($extraPegawais as $ep) {
            $this->warn("Menghapus extra pegawai: {$ep->nama_lengkap} (NIK: {$ep->nik})");
            $ep->delete();
        }

        // Dapatkan ID akun resmi Susanti (admin) dan Daday Daryat (kades)
        $susantiUser = User::where('username', 'susanti')->first();
        $dadayUser   = User::where('username', 'dadaydaryat')->first();

        // Hapus user yang bukan dari 14 user resmi
        $extraUsers = User::whereNotIn('username', $officialUsernames)->get();
        foreach ($extraUsers as $eu) {
            $this->warn("Menghapus extra user: {$eu->name} (@{$eu->username})");
            
            // Re-assign relasi foreign key jika ada
            $targetUserId = ($eu->role === 'kepala_desa' ? $dadayUser?->id : $susantiUser?->id);
            if ($targetUserId) {
                \Illuminate\Support\Facades\DB::table('pengumuman')->where('dibuat_oleh', $eu->id)->update(['dibuat_oleh' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('surat_perintah_tugas')->where('created_by', $eu->id)->update(['created_by' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('surat_perintah_tugas')->where('disetujui_oleh', $eu->id)->update(['disetujui_oleh' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('izin_sakits')->where('diproses_oleh', $eu->id)->update(['diproses_oleh' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('kehadirans')->where('diverifikasi_oleh', $eu->id)->update(['diverifikasi_oleh' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('pengajuan_absen_luars')->where('diproses_oleh', $eu->id)->update(['diproses_oleh' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('pengajuan_absen_luars')->where('user_id', $eu->id)->update(['user_id' => $targetUserId]);
                \Illuminate\Support\Facades\DB::table('audit_logs')->where('user_id', $eu->id)->update(['user_id' => $targetUserId]);
            }

            $eu->delete();
        }

        // Bersihkan data dummy WiFi
        \App\Models\KonfigurasiWifi::where('nama_jaringan', 'like', 'Test%')->delete();
        $wifiDesa = \App\Models\KonfigurasiWifi::where('nama_jaringan', 'like', '%WiFi Kantor Desa%')->first();
        if ($wifiDesa) {
            $wifiDesa->update(['is_active' => true]);
            \App\Models\KonfigurasiWifi::where('id', '!=', $wifiDesa->id)->update(['is_active' => false]);
        }

        $this->info('');
        $this->info('=== DATA 14 PEGAWAI RESMI DESA NANGTANG ===');
        $this->line('Total Pegawai Resmi: ' . Pegawai::count());
        foreach (Pegawai::with('jabatan')->orderBy('id')->get() as $idx => $p) {
            $this->line(($idx + 1) . ". {$p->nama_lengkap} — " . ($p->jabatan->nama_jabatan ?? '-') . " (NIK: {$p->nik})");
        }

        $this->info('');
        $this->info('=== DATA AKUN USER RESMI ===');
        $this->line('Total User Resmi: ' . User::count());
        foreach (User::orderBy('id')->get() as $u) {
            $this->line("- @{$u->username} ({$u->name}) [Role: {$u->role}]");
        }

        return Command::SUCCESS;
    }
}

