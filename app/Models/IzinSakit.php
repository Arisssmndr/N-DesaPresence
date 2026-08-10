<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinSakit extends Model
{
    protected $table = 'izin_sakits';
    protected $fillable = [
        'pegawai_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari',
        'keterangan', 'file_lampiran', 'status', 'diproses_oleh', 'catatan_admin'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pemproses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
