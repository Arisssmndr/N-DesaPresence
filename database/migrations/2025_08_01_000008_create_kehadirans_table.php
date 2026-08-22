<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->integer('durasi_kerja_menit')->default(0);
            $table->integer('terlambat_menit')->default(0);
            // Versi lengkap enum mencakup semua nilai yang ditambahkan migration selanjutnya.
            // Alternatif MODIFY COLUMN (MySQL-only) sekarang hanya berjalan jika driver = mysql.
            $table->enum('status', ['Hadir', 'Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa', 'Libur'])->default('Hadir');
            $table->enum('sumber_data', ['fingerprint', 'manual_admin', 'import_file', 'web_signature', 'pengajuan_luar'])->default('web_signature');
            $table->text('keterangan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal'], 'unique_daily_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
