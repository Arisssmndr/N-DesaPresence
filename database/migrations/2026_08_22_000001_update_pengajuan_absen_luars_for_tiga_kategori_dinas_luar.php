<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom jenis menjadi string(50) agar mendukung semua jenis kategori dinas luar
        // Catatan: SQLite tidak mendukung ->change(), hanya dijalankan di MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            Schema::table('pengajuan_absen_luars', function (Blueprint $table) {
                $table->string('jenis', 50)->change();
            });
        }

        Schema::table('pengajuan_absen_luars', function (Blueprint $table) {
            // Tambahkan kolom instansi_pengundang untuk Dinas Luar Undangan
            if (!Schema::hasColumn('pengajuan_absen_luars', 'instansi_pengundang')) {
                $table->string('instansi_pengundang', 150)->nullable()->after('judul');
            }

            // Tambahkan kolom nomor_surat_tugas untuk Dinas Luar Surat Tugas (SPT)
            if (!Schema::hasColumn('pengajuan_absen_luars', 'nomor_surat_tugas')) {
                $table->string('nomor_surat_tugas', 100)->nullable()->after('instansi_pengundang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_absen_luars', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_absen_luars', 'instansi_pengundang')) {
                $table->dropColumn('instansi_pengundang');
            }
            if (Schema::hasColumn('pengajuan_absen_luars', 'nomor_surat_tugas')) {
                $table->dropColumn('nomor_surat_tugas');
            }
        });
    }
};
