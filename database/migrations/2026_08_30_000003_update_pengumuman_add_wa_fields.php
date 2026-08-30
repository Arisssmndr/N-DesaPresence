<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->boolean('kirim_wa')->default(false)->after('is_pinned');
            $table->string('target_penerima', 30)->default('semua')->after('kirim_wa'); // 'semua', 'perangkat_tetap', 'staf', 'bpd', 'kemasyarakatan'
            $table->timestamp('wa_terkirim_at')->nullable()->after('berlaku_hingga');
            $table->unsignedSmallInteger('total_wa_terkirim')->default(0)->after('wa_terkirim_at');
            $table->unsignedSmallInteger('total_wa_gagal')->default(0)->after('total_wa_terkirim');
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropColumn([
                'kirim_wa',
                'target_penerima',
                'wa_terkirim_at',
                'total_wa_terkirim',
                'total_wa_gagal',
            ]);
        });
    }
};
