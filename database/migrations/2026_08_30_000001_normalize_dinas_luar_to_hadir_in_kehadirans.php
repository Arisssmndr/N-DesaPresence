<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update semua record lama di kehadirans yang berstatus 'Dinas Luar' menjadi 'Hadir'
        DB::table('kehadirans')
            ->where('status', 'Dinas Luar')
            ->orWhere('status', 'dinas luar')
            ->orWhere('status', 'dinas_luar')
            ->update([
                'status' => 'Hadir',
            ]);

        // 2. Update di absensi_disesuaikans jika ada
        if (Schema::hasTable('absensi_disesuaikans')) {
            DB::table('absensi_disesuaikans')
                ->where('status_disesuaikan', 'Dinas Luar')
                ->orWhere('status_disesuaikan', 'dinas luar')
                ->update([
                    'status_disesuaikan' => 'Hadir',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed as Dinas Luar is canonically Hadir
    }
};
