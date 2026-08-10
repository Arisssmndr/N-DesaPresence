<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_siltaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            $table->decimal('nominal_siltap', 15, 2);
            $table->decimal('nominal_tunjangan', 15, 2)->default(0);
            $table->decimal('nilai_potongan_alpa', 15, 2)->default(0);
            $table->decimal('nilai_potongan_terlambat', 15, 2)->default(0);
            $table->date('berlaku_mulai');
            $table->date('berlaku_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_siltaps');
    }
};
