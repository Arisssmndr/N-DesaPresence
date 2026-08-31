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
        Schema::table('surat_perintah_tugas', function (Blueprint $table) {
            $table->enum('respons_staf', ['menunggu', 'diterima', 'ditolak'])->default('menunggu')->after('status');
            $table->text('alasan_tolak_staf')->nullable()->after('respons_staf');
            $table->timestamp('waktu_respons_staf')->nullable()->after('alasan_tolak_staf');
            $table->longText('tanda_tangan_staf')->nullable()->after('waktu_respons_staf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_perintah_tugas', function (Blueprint $table) {
            $table->dropColumn(['respons_staf', 'alasan_tolak_staf', 'waktu_respons_staf', 'tanda_tangan_staf']);
        });
    }
};
