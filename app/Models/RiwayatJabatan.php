<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatJabatan extends Model
{
    public $timestamps = false;
    protected $table = 'riwayat_jabatans';
    protected $fillable = [
        'pegawai_id', 'jabatan_id', 'mulai_menjabat', 'selesai_menjabat',
        'sk_nomor', 'keterangan', 'created_at'
    ];

    protected $casts = [
        'mulai_menjabat' => 'date',
        'selesai_menjabat' => 'date',
        'created_at' => 'datetime',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
}
