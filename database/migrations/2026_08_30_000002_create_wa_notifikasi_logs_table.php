<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_notifikasi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_id')->nullable()->constrained('pengumuman')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_hp', 25);
            $table->string('nama_penerima', 100)->nullable();
            $table->text('pesan');
            $table->enum('status', ['pending', 'terkirim', 'gagal', 'dilewati'])->default('pending');
            $table->unsignedTinyInteger('percobaan')->default(0);
            $table->text('response_raw')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('terkirim_pada')->nullable();
            $table->timestamps();

            $table->index(['pengumuman_id', 'status']);
            $table->index('no_hp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_notifikasi_logs');
    }
};
