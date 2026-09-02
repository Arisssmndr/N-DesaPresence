<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert default config for Fonnte Account Token (Master Token)
        if (!DB::table('konfigurasi_whatsapp')->where('key', 'fonnte_account_token')->exists()) {
            DB::table('konfigurasi_whatsapp')->insert([
                'key'        => 'fonnte_account_token',
                'value'      => null,
                'tipe'       => 'encrypted',
                'keterangan' => 'Master Account Token Fonnte (untuk kelola Device & QR Code)',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('konfigurasi_whatsapp')->where('key', 'fonnte_account_token')->delete();
    }
};
