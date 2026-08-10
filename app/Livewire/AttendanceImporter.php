<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\FingerprintIngestionService;
use App\Models\AuditLog;

class AttendanceImporter extends Component
{
    use WithFileUploads;

    public $logFile;
    public bool $isProcessing = false;
    public array $importSummary = [];

    public function import(FingerprintIngestionService $ingestionService)
    {
        $this->validate([
            'logFile' => 'required|file|max:10240', // 10MB max
        ]);

        $this->isProcessing = true;
        $path = $this->logFile->getRealPath();
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $totalLines = count($lines);
        $successCount = 0;
        $duplicateCount = 0;
        $unknownPinCount = 0;
        $invalidCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $result = $ingestionService->ingest($line, 'import_file');

            match ($result['status']) {
                'created' => $successCount++,
                'duplicate' => $duplicateCount++,
                'unknown_pin' => $unknownPinCount++,
                default => $invalidCount++,
            };
        }

        $this->importSummary = [
            'total' => $totalLines,
            'success' => $successCount,
            'duplicate' => $duplicateCount,
            'unknown_pin' => $unknownPinCount,
            'invalid' => $invalidCount,
        ];

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Import file log presensi: {$totalLines} baris diproses ({$successCount} berhasil, {$duplicateCount} duplikat)",
            'modul' => 'Import Absensi',
        ]);

        session()->flash('success', "Import file log presensi selesai! Total: {$totalLines} baris ({$successCount} berhasil, {$duplicateCount} duplikat).");
        $this->isProcessing = false;
        $this->reset('logFile');
    }

    public function render()
    {
        return view('livewire.attendance-importer')
            ->layout('layouts.app', ['title' => 'Import Log Presensi — Presence Desa']);
    }
}
