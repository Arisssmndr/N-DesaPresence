<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AuditLog;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DatabaseBackupJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $backupDir = storage_path('backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $dbName = config('database.connections.mysql.database', 'db_kknpresencedesa');
        $user = config('database.connections.mysql.username', 'root');
        $pass = config('database.connections.mysql.password', '');
        $host = config('database.connections.mysql.host', '127.0.0.1');

        $fileName = 'backup_' . $dbName . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

        $passFlag = !empty($pass) ? "-p\"{$pass}\"" : '';
        $cmd = "mysqldump -h {$host} -u {$user} {$passFlag} {$dbName} > \"{$filePath}\"";

        @exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && File::exists($filePath)) {
            AuditLog::create([
                'user_name' => 'System Scheduler',
                'role' => 'System',
                'aktivitas' => "Backup database otomatis berhasil: {$fileName}",
                'modul' => 'Backup',
            ]);
        }
    }
}
