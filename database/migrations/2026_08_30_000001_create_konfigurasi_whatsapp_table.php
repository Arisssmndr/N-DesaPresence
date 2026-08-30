<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->text('value')->nullable();
            $table->string('tipe', 20)->default('string'); // string, boolean, integer, text, encrypted
            $table->string('keterangan', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed data default konfigurasi WhatsApp Fonnte
        $defaultConfigs = [
            [
                'key' => 'wa_gateway_provider',
                'value' => 'fonnte',
                'tipe' => 'string',
                'keterangan' => 'Provider WhatsApp Gateway (Fonnte)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'fonnte_api_key',
                'value' => null,
                'tipe' => 'encrypted',
                'keterangan' => 'Token API Fonnte Resmi (dikelola admin)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'fonnte_sender_number',
                'value' => null,
                'tipe' => 'string',
                'keterangan' => 'Nomor WhatsApp Sender Terdaftar di Fonnte (contoh: 6281234567890)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_notifikasi_enabled',
                'value' => '0',
                'tipe' => 'boolean',
                'keterangan' => 'Master Sakelar Aktif/Nonaktif Pengiriman WhatsApp Otomatis',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_template_pengumuman',
                'value' => "📢 *PENGUMUMAN RESMI PEMERINTAH DESA NANGTANG*\n*N-DesaPresence Notification System*\n\n📌 *Kategori:* {kategori}\n🏷️ *Perihal:* {judul}\n\n{isi}\n\n📅 *Berlaku s/d:* {berlaku_hingga}\n👤 *Diumumkan Oleh:* {pembuat}\n\n--------------------------------------------\n_Pesan otomatis dikirim melalui Sistem N-DesaPresence Desa Nangtang (KKN 0226 LP3I Tasikmalaya © 2026)_",
                'tipe' => 'text',
                'keterangan' => 'Template Pesan WhatsApp untuk Pengumuman Desa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wa_country_code',
                'value' => '62',
                'tipe' => 'string',
                'keterangan' => 'Default Country Code Nomor WhatsApp (62)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('konfigurasi_whatsapp')->insert($defaultConfigs);
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_whatsapp');
    }
};
