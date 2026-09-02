<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            // Ubah kategori menjadi string agar fleksibel mendukung 10+ kategori
            $table->string('kategori', 50)->default('informasi')->change();
            
            // Tambahkan kolom pegawai_ids untuk menyimpan array ID pegawai terpilih (jika mode individual)
            if (!Schema::hasColumn('pengumuman', 'pegawai_ids')) {
                $table->json('pegawai_ids')->nullable()->after('target_penerima');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            if (Schema::hasColumn('pengumuman', 'pegawai_ids')) {
                $table->dropColumn('pegawai_ids');
            }
        });
    }
};
