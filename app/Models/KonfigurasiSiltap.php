<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonfigurasiSiltap extends Model
{
    protected $table = 'konfigurasi_siltaps';
    protected $fillable = [
        'jabatan_id', 'nominal_siltap', 'nominal_tunjangan',
        'nilai_potongan_alpa', 'nilai_potongan_terlambat',
        'berlaku_mulai', 'berlaku_selesai'
    ];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'berlaku_selesai' => 'date',
        'nominal_siltap' => 'decimal:2',
        'nominal_tunjangan' => 'decimal:2',
        'nilai_potongan_alpa' => 'decimal:2',
        'nilai_potongan_terlambat' => 'decimal:2',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
}
