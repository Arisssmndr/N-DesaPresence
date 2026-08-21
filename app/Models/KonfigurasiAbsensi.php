<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cache;

class KonfigurasiAbsensi extends Model
{
    protected $table = 'konfigurasi_absensis';

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    public static function getNilai(string $kunci, string $default = ''): string
    {
        return Cache::remember("config_absensi_{$kunci}", 3600, function () use ($kunci, $default) {
            $config = static::where('kunci', $kunci)->first();
            return $config ? $config->nilai : $default;
        });
    }

    public static function getJadwal(): array
    {
        return Cache::remember('config_absensi_jadwal_all', 3600, function () {
            return [
                'jam_masuk_mulai'   => static::where('kunci', 'jam_masuk_mulai')->value('nilai') ?? '06:00',
                'jam_masuk_selesai' => static::where('kunci', 'jam_masuk_selesai')->value('nilai') ?? '11:00',
                'jam_pulang_mulai'  => static::where('kunci', 'jam_pulang_mulai')->value('nilai') ?? '14:00',
                'jam_pulang_selesai'=> static::where('kunci', 'jam_pulang_selesai')->value('nilai') ?? '18:00',
            ];
        });
    }

    public static function setNilai(string $kunci, string $nilai, ?string $keterangan = null): void
    {
        static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai, 'keterangan' => $keterangan]
        );
        Cache::forget("config_absensi_{$kunci}");
        Cache::forget('config_absensi_jadwal_all');
    }
}
