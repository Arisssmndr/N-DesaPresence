<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_absen_luars', function (Blueprint $table) {
            $table->id();

            // Pengaju
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Data Pengajuan
            $table->date('tanggal'); // Tanggal absensi yang dimohonkan
            $table->enum('jenis', [
                'kegiatan_sosial',
                'dinas_luar',
                'dinas_luar_undangan',
                'dinas_luar_pengajuan',
                'dinas_luar_surat_tugas',
            ]);
            $table->string('judul', 150);  // Ringkasan singkat
            $table->text('deskripsi');     // Keterangan lengkap kegiatan/tujuan

            // Detail Jenis Khusus
            $table->string('instansi_pengundang', 255)->nullable(); // untuk dinas_luar_undangan
            $table->string('nomor_surat_tugas', 100)->nullable();   // untuk dinas_luar_surat_tugas

            // Bukti
            $table->string('foto_lokasi', 500)->nullable();    // Upload foto (kegiatan_sosial)
            $table->string('file_dokumen', 500)->nullable();   // Upload surat (dinas_luar)
            $table->longText('tanda_tangan')->nullable();      // Base64 canvas TTD

            // Status Approval (Admin Only)
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();          // Alasan tolak / keterangan setuju
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_pada')->nullable();

            $table->timestamps();

            // Satu staf hanya bisa punya 1 pengajuan per tanggal
            $table->unique(['pegawai_id', 'tanggal'], 'unique_pengajuan_per_hari');
        });

        // Tambahkan nilai 'pengajuan_luar' ke enum sumber_data pada tabel kehadirans (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kehadirans MODIFY COLUMN sumber_data ENUM('fingerprint', 'manual_admin', 'import_file', 'web_signature', 'pengajuan_luar') DEFAULT 'web_signature'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_absen_luars');

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kehadirans MODIFY COLUMN sumber_data ENUM('fingerprint', 'manual_admin', 'import_file', 'web_signature') DEFAULT 'web_signature'");
        }
    }
};
