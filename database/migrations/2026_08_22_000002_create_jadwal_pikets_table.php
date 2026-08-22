<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->date('tanggal_piket');
            $table->time('jam_mulai')->default('19:00:00');
            $table->time('jam_selesai')->default('06:00:00');
            $table->string('keterangan')->default('Piket Jaga Malam Balai Desa');
            $table->enum('status', ['terjadwal', 'hadir', 'lepas_piket', 'batal'])->default('terjadwal');
            $table->longText('tanda_tangan')->nullable();
            $table->dateTime('waktu_absen')->nullable();
            $table->string('ip_absen', 45)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['pegawai_id', 'tanggal_piket']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pikets');
    }
};
