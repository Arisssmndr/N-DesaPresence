<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Pengumuman;
use App\Models\WaNotifikasiLog;
use App\Services\FonnteWhatsAppService;
use App\Services\KonfigurasiWaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KirimWaNotifikasiPengumumanJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    public function __construct(
        public int $pengumumanId,
        public ?int $pegawaiId,
        public ?int $userId,
        public string $nomorHp,
        public string $namaPenerima
    ) {}

    public function handle(
        FonnteWhatsAppService $waService,
        KonfigurasiWaService $configService
    ): void {
        $pengumuman = Pengumuman::find($this->pengumumanId);

        if (!$pengumuman) {
            Log::info("Pengumuman #{$this->pengumumanId} tidak ditemukan, membatalkan pengiriman WA.");
            return;
        }

        // Cari atau buat log notifikasi
        $log = WaNotifikasiLog::firstOrNew([
            'pengumuman_id' => $this->pengumumanId,
            'no_hp'         => $this->nomorHp,
        ]);

        $log->pegawai_id    = $this->pegawaiId;
        $log->user_id       = $this->userId;
        $log->nama_penerima = $this->namaPenerima;
        $log->percobaan     = ($log->percobaan ?? 0) + 1;

        // Render pesan pengumuman
        $pesan = $configService->renderPesanPengumuman($pengumuman, $this->namaPenerima);
        $log->pesan = $pesan;

        // Kirim langsung via Fonnte Gateway
        $sendResult = $waService->send($this->nomorHp, $pesan);

        $log->status        = $sendResult['status'] ?? 'gagal';
        $log->response_raw  = is_array($sendResult['raw']) ? json_encode($sendResult['raw']) : (string) ($sendResult['raw'] ?? '');
        $log->error_message = $sendResult['error'] ?? null;

        if ($sendResult['success']) {
            $log->terkirim_pada = Carbon::now();
        }

        $log->save();

        // Rekalkulasi status agregat pengumuman
        $totalTerkirim = WaNotifikasiLog::where('pengumuman_id', $this->pengumumanId)->where('status', 'terkirim')->count();
        $totalGagal    = WaNotifikasiLog::where('pengumuman_id', $this->pengumumanId)->where('status', 'gagal')->count();

        $pengumuman->update([
            'total_wa_terkirim' => $totalTerkirim,
            'total_wa_gagal'    => $totalGagal,
            'wa_terkirim_at'    => $totalTerkirim > 0 ? Carbon::now() : $pengumuman->wa_terkirim_at,
        ]);
    }
}
