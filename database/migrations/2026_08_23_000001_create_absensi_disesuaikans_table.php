<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_disesuaikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kehadiran_id')->nullable()->constrained('kehadirans')->nullOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('status_asli', 50)->default('Alpa');
            $table->string('status_disesuaikan', 50)->default('Hadir');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->integer('durasi_kerja_menit')->default(0);
            $table->integer('terlambat_menit')->default(0);
            $table->longText('tanda_tangan_disesuaikan')->nullable();
            $table->string('sumber_tanda_tangan', 50)->nullable()->comment('asli, pinjam_H-1, pinjam_H-2, pinjam_H-3, pinjam_H-7, manual');
            $table->date('tanggal_sumber_ttd')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal'], 'absensi_disesuaikan_unique');
            $table->index(['tanggal', 'status_disesuaikan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_disesuaikans');
    }
};
