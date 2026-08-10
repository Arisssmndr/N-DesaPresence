<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnknownScan extends Model
{
    public $timestamps = false;
    protected $fillable = ['pin_fingerprint', 'waktu_scan', 'keterangan', 'created_at'];

    protected $casts = [
        'waktu_scan' => 'datetime',
        'created_at' => 'datetime',
    ];
}
