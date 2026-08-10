<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAbsensi extends Model
{
    protected $table = 'log_absensis';
    protected $fillable = ['pin_fingerprint', 'waktu_scan', 'metode_ingest', 'raw_data', 'is_processed'];

    protected $casts = [
        'waktu_scan' => 'datetime',
        'is_processed' => 'boolean',
    ];
}
