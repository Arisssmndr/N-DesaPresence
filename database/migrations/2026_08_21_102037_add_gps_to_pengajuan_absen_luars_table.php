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
        Schema::table('pengajuan_absen_luars', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('file_dokumen');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('alamat_gps', 255)->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_absen_luars', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'alamat_gps']);
        });
    }
};
