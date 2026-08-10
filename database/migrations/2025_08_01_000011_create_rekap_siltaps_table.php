<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_siltaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->tinyInteger('bulan');
            $table->year('tahun');
            $table->integer('total_hari_kerja')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->integer('total_terlambat')->default(0);
            $table->integer('total_alpa')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_dinas_luar')->default(0);
            $table->integer('total_menit_terlambat')->default(0);
            $table->decimal('siltap_bruto', 15, 2)->default(0);
            $table->decimal('potongan_alpa', 15, 2)->default(0);
            $table->decimal('potongan_terlambat', 15, 2)->default(0);
            $table->decimal('siltap_neto', 15, 2)->default(0);
            $table->enum('status', ['draft', 'final', 'disetujui'])->default('draft');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pegawai_id', 'bulan', 'tahun'], 'unique_rekap_siltap');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_siltaps');
    }
};
