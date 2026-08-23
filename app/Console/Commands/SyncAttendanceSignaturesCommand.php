<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kehadiran;
use App\Models\PengajuanAbsenLuar;
use App\Models\JadwalPiket;
use Carbon\Carbon;

class SyncAttendanceSignaturesCommand extends Command
{
    protected $signature = 'desa:sync-signatures';
    protected $description = 'Sinkronisasi tanda tangan presensi digital dari pengajuan dan absensi asli';

    public function handle(): int
    {
        $this->info('=== SINKRONISASI TANDA TANGAN KEHADIRAN (DATA ASLI) ===');
        $kehadirans = Kehadiran::with('pegawai')->orderByDesc('tanggal')->get();

        $syncedCount = 0;

        foreach ($kehadirans as $k) {
            $nama = $k->pegawai->nama_lengkap ?? 'Unknown';
            $tgl  = $k->tanggal->toDateString();

            // Kasus 1: Dari Pengajuan Absen Luar yang punya TTD asli pemohon
            if (($k->status === 'Dinas Luar' || $k->sumber_data === 'pengajuan_luar') && !$k->tanda_tangan_masuk) {
                $pengajuan = PengajuanAbsenLuar::where('pegawai_id', $k->pegawai_id)
                    ->whereDate('tanggal', $tgl)
                    ->whereNotNull('tanda_tangan')
                    ->first();

                if ($pengajuan && $pengajuan->tanda_tangan) {
                    $k->tanda_tangan_masuk = $pengajuan->tanda_tangan;
                    if (!$k->jam_masuk && $pengajuan->created_at) {
                        $k->jam_masuk = $pengajuan->created_at->format('H:i:s');
                    }
                    $k->save();
                    $this->info(" -> Berhasil menautkan TTD asli dari Pengajuan Absen Luar untuk {$nama} ({$tgl})");
                    $syncedCount++;
                }
            }

            // Kasus 2: Dari Lepas Piket yang punya TTD asli dari form piket
            if (str_contains($k->keterangan ?? '', 'Lepas Piket') && !$k->tanda_tangan_masuk) {
                $piket = JadwalPiket::where('pegawai_id', $k->pegawai_id)
                    ->whereDate('tanggal_piket', Carbon::parse($tgl)->subDay()->toDateString())
                    ->whereNotNull('tanda_tangan')
                    ->first();

                if ($piket && $piket->tanda_tangan) {
                    $k->tanda_tangan_masuk = $piket->tanda_tangan;
                    if (!$k->jam_masuk && $piket->waktu_absen) {
                        $k->jam_masuk = $piket->waktu_absen->format('H:i:s');
                    }
                    $k->save();
                    $this->info(" -> Berhasil menautkan TTD asli dari form Piket untuk {$nama} ({$tgl})");
                    $syncedCount++;
                }
            }
        }

        $this->info("Selesai! {$syncedCount} record kehadiran berhasil ditautkan ke tanda tangan asli.");
        return Command::SUCCESS;
    }
}
