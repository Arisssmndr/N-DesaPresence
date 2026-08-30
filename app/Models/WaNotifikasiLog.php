<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaNotifikasiLog extends Model
{
    protected $table = 'wa_notifikasi_logs';

    protected $fillable = [
        'pengumuman_id',
        'pegawai_id',
        'user_id',
        'no_hp',
        'nama_penerima',
        'pesan',
        'status',
        'percobaan',
        'response_raw',
        'error_message',
        'terkirim_pada',
    ];

    protected $casts = [
        'percobaan' => 'integer',
        'terkirim_pada' => 'datetime',
    ];

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
