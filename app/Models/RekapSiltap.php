<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapSiltap extends Model
{
    protected $table = 'rekap_siltaps';
    protected $fillable = [
        'pegawai_id', 'bulan', 'tahun', 'total_hari_kerja', 'total_hadir',
        'total_terlambat', 'total_alpa', 'total_izin', 'total_dinas_luar',
        'total_menit_terlambat', 'siltap_bruto', 'potongan_alpa',
        'potongan_terlambat', 'siltap_neto', 'status', 'disetujui_oleh'
    ];

    protected $casts = [
        'siltap_bruto' => 'decimal:2',
        'potongan_alpa' => 'decimal:2',
        'potongan_terlambat' => 'decimal:2',
        'siltap_neto' => 'decimal:2',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
