<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class JadwalPiket extends Model
{
    protected $fillable = [
        'pegawai_id',
        'tanggal_piket',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'status',
        'tanda_tangan',
        'waktu_absen',
        'ip_absen',
        'created_by',
    ];

    protected $casts = [
        'tanggal_piket' => 'date',
        'waktu_absen'   => 'datetime',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isHMin1(): bool
    {
        return Carbon::parse($this->tanggal_piket)->isTomorrow();
    }

    public function isHariPiket(): bool
    {
        return Carbon::parse($this->tanggal_piket)->isToday();
    }

    public function isHariLepasPiket(): bool
    {
        return Carbon::parse($this->tanggal_piket)->isYesterday();
    }
}
