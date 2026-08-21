<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('pin_fingerprint', 20)->unique()->nullable();
            $table->string('nipd', 30)->unique()->nullable();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->foreignId('jabatan_id')->constrained('jabatans')->restrictOnDelete();
            $table->enum('kategori_pegawai', ['perangkat_tetap', 'staf', 'bpd', 'kemasyarakatan'])->default('perangkat_tetap');
            $table->foreignId('shift_id')->nullable()->default(1)->constrained('shift_kerjas')->nullOnDelete();
            $table->string('no_hp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_profil', 255)->nullable();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->decimal('siltap_bruto', 15, 2)->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
