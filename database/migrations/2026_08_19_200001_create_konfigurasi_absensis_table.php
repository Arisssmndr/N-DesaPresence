<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 50)->unique();
            $table->string('nilai', 255);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_absensis');
    }
};
