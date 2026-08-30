<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'kategori',
        'is_pinned',
        'kirim_wa',
        'target_penerima',
        'berlaku_hingga',
        'wa_terkirim_at',
        'total_wa_terkirim',
        'total_wa_gagal',
        'dibuat_oleh',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'kirim_wa' => 'boolean',
        'berlaku_hingga' => 'date',
        'wa_terkirim_at' => 'datetime',
        'total_wa_terkirim' => 'integer',
        'total_wa_gagal' => 'integer',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function waLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WaNotifikasiLog::class, 'pengumuman_id');
    }

    public function getTargetPenerimaLabelAttribute(): string
    {
        return match ($this->target_penerima) {
            'semua' => 'Seluruh Perangkat & Staf',
            'perangkat_tetap' => 'Perangkat Desa Tetap',
            'staf' => 'Staf / Honorer Desa',
            'bpd' => 'Badan Permusyawaratan Desa (BPD)',
            'kemasyarakatan' => 'Lembaga Kemasyarakatan Desa',
            default => 'Semua',
        };
    }
}
