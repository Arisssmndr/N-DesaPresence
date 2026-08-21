<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiWifi extends Model
{
    protected $table = 'konfigurasi_wifi';

    protected $fillable = [
        'nama_jaringan',
        'ip_address',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk hanya ambil konfigurasi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
