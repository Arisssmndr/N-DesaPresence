<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            $table->dateTime('waktu_pulang')->nullable()->after('waktu_absen');
            $table->longText('tanda_tangan_pulang')->nullable()->after('tanda_tangan');
            $table->string('ip_pulang', 45)->nullable()->after('ip_absen');
        });

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `jadwal_pikets` MODIFY `status` ENUM('terjadwal', 'sedang_piket', 'hadir', 'lepas_piket', 'batal') DEFAULT 'terjadwal'");
        } catch (\Throwable $e) {
            // Ignore for SQLite or test environments
        }
    }

    public function down(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            $table->dropColumn(['waktu_pulang', 'tanda_tangan_pulang', 'ip_pulang']);
        });
    }
};
