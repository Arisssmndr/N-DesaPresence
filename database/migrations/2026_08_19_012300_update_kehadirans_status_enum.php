<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * SQLite (used in testing) does not support ALTER TABLE ... MODIFY COLUMN.
     * This migration only runs on MySQL/MariaDB.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kehadirans MODIFY COLUMN status ENUM('Hadir', 'Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa', 'Libur') DEFAULT 'Hadir'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kehadirans MODIFY COLUMN status ENUM('Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa', 'Libur') DEFAULT 'Alpa'");
        }
    }
};
