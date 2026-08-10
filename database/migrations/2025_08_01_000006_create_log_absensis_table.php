<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('pin_fingerprint', 20);
            $table->dateTime('waktu_scan');
            $table->enum('metode_ingest', ['serial_realtime', 'import_file', 'manual_admin'])->default('serial_realtime');
            $table->text('raw_data')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();

            $table->unique(['pin_fingerprint', 'waktu_scan'], 'unique_raw_scan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_absensis');
    }
};
