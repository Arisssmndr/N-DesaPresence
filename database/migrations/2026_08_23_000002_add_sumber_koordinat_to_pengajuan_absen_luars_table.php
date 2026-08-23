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
            $table->enum('sumber_koordinat', ['gps', 'ip_geolocation', 'manual'])->default('gps')->after('alamat_gps');
            $table->integer('akurasi_gps_meter')->nullable()->after('sumber_koordinat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_absen_luars', function (Blueprint $table) {
            $table->dropColumn(['sumber_koordinat', 'akurasi_gps_meter']);
        });
    }
};
