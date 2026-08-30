<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WifiAccessLog extends Model
{
    protected $table = 'wifi_access_logs';

    protected $fillable = [
        'client_ip',
        'pegawai_id',
        'jenis_aksi',
        'hasil',
        'alasan_ditolak',
        'matched_wifi',
        'user_agent',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function scopeDitolak($query)
    {
        return $query->where('hasil', 'ditolak');
    }

    public function scopeDiizinkan($query)
    {
        return $query->where('hasil', 'diizinkan');
    }

    public function getLabelJenisAksiAttribute(): string
    {
        return match ($this->jenis_aksi) {
            'portal_akses'       => 'Akses Portal Absensi',
            'absen_masuk'        => 'Absen Masuk (Tanda Tangan)',
            'absen_pulang'       => 'Absen Pulang (Tanda Tangan)',
            'wifi_status_check'  => 'Cek Status Jaringan (Polling)',
            default              => ucwords(str_replace('_', ' ', $this->jenis_aksi)),
        };
    }
}
