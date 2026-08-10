<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FingerprintIngestionService;
use Illuminate\Support\Facades\Log;

class SerialFingerprintListener extends Command
{
    protected $signature = 'fingerprint:listen 
                            {port=COM3 : Nama COM Port serial (contoh: COM3, COM4)} 
                            {--baud=9600 : Baud rate komunikasi serial}';

    protected $description = 'Mendengarkan data real-time transaksi tap jari dari mesin fingerprint via Serial COM Port';

    public function __construct(private FingerprintIngestionService $ingestionService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $port = strtoupper($this->argument('port'));

        $this->info("╔══════════════════════════════════════════════════════╗");
        $this->info("║      PRESENCE DESA — Serial Fingerprint Listener    ║");
        $this->info("║             Desa Nangtang — KKN 2025                 ║");
        $this->info("╚══════════════════════════════════════════════════════╝");
        $this->newLine();
        $this->info("Menghubungkan ke USB Serial Port: {$port}...");

        $fp = @fopen("\\\\.\\{$port}", "r+");

        if (!$fp) {
            $this->error("❌ Gagal membuka port {$port}.");
            $this->line("   Langkah Penanganan:");
            $this->line("   1. Pastikan Driver ZKTeco / CH340 / CP2102 sudah terinstal di Windows.");
            $this->line("   2. Cek Device Manager untuk memastikan nomor COM Port (misal COM3, COM4).");
            $this->line("   3. Pastikan port tidak sedang digunakan aplikasi lain (misal ZKTime).");
            Log::error("Presence Desa Listener: Gagal membuka COM Port {$port}");
            return Command::FAILURE;
        }

        $this->info("✅ Terhubung ke {$port}! Standby mendengarkan sinyal tap jari...");
        $this->line("   (Tekan Ctrl+C untuk menghentikan listener)");
        $this->newLine();

        $buffer = '';

        while (true) {
            $chunk = fread($fp, 256);

            if ($chunk !== false && $chunk !== '') {
                $buffer .= $chunk;

                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if (!empty($line)) {
                        $result = $this->ingestionService->ingest($line, 'serial_realtime');
                        $this->renderResult($result, $line);
                    }
                }
            }

            usleep(100000); // 0.1 detik CPU sleep efficiency
        }

        fclose($fp);
        return Command::SUCCESS;
    }

    private function renderResult(array $result, string $rawText): void
    {
        $time = now()->format('H:i:s');

        match ($result['status']) {
            'created' => $this->info("[{$time}] ✅ [{$result['nama']}] Scan {$result['jenis']} tercatat — ({$result['status_kehadiran']})"),
            'duplicate' => $this->warn("[{$time}] ⚠️  Duplikat scan diabaikan (PIN: {$result['pin']})"),
            'unknown_pin' => $this->warn("[{$time}] ❓ PIN tidak terdaftar: {$result['pin']}"),
            'invalid' => $this->error("[{$time}] ❌ Format tidak dikenali: {$rawText}"),
            default => $this->line("[{$time}] ℹ️  {$result['message']}"),
        };
    }
}
