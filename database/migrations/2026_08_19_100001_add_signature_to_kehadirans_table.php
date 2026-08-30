<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->longText('tanda_tangan_masuk')->nullable()->after('keterangan');
            $table->longText('tanda_tangan_pulang')->nullable()->after('tanda_tangan_masuk');
            $table->string('ip_absensi_masuk', 45)->nullable()->after('tanda_tangan_pulang');
            $table->string('ip_absensi_pulang', 45)->nullable()->after('ip_absensi_masuk');
        });

        // Update enum sumber_data to include 'web_signature' (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kehadirans MODIFY COLUMN sumber_data ENUM('fingerprint', 'manual_admin', 'import_file', 'web_signature') DEFAULT 'web_signature'");
        }
    }

    public function down(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropColumn(['tanda_tangan_masuk', 'tanda_tangan_pulang', 'ip_absensi_masuk', 'ip_absensi_pulang']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kehadirans MODIFY COLUMN sumber_data ENUM('fingerprint', 'manual_admin', 'import_file') DEFAULT 'fingerprint'");
        }
    }
};
