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
        Schema::create('wifi_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('client_ip', 45);
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('jenis_aksi', 30); // portal_akses, absen_masuk, absen_pulang, wifi_status_check
            $table->enum('hasil', ['diizinkan', 'ditolak']);
            $table->string('alasan_ditolak', 255)->nullable();
            $table->string('matched_wifi', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for high performance querying & analytics
            $table->index(['client_ip', 'created_at']);
            $table->index(['hasil', 'created_at']);
            $table->index(['jenis_aksi', 'hasil']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_access_logs');
    }
};
