<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE kehadirans MODIFY COLUMN status ENUM('Hadir', 'Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa', 'Libur') DEFAULT 'Hadir'");
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE kehadirans MODIFY COLUMN status ENUM('Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa', 'Libur') DEFAULT 'Alpa'");
    }
};
