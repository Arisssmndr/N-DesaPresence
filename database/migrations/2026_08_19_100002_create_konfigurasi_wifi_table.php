<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_wifi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jaringan', 100); // contoh: "WiFi Kantor Desa Nangtang"
            $table->string('ip_address', 45);     // IPv4 atau IPv6, bisa range: 192.168.1.0/24
            $table->string('keterangan', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed data awal: izinkan localhost untuk keperluan testing/development
        DB::table('konfigurasi_wifi')->insert([
            [
                'nama_jaringan' => 'Localhost (Development)',
                'ip_address'    => '127.0.0.1',
                'keterangan'    => 'IP lokal untuk development & testing. Nonaktifkan di production.',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama_jaringan' => 'WiFi Kantor Desa Nangtang',
                'ip_address'    => '192.168.1.0/24',
                'keterangan'    => 'Range IP jaringan WiFi kantor desa. Sesuaikan dengan IP aktual.',
                'is_active'     => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_wifi');
    }
};
