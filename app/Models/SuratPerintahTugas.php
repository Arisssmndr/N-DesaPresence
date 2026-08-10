<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPerintahTugas extends Model
{
    protected $table = 'surat_perintah_tugas';
    protected $fillable = [
        'nomor_spt', 'pegawai_id', 'tanggal_mulai', 'tanggal_selesai', 'tujuan',
        'keperluan', 'file_undangan', 'file_bukti_kegiatan', 'anggaran', 'status',
        'disetujui_oleh', 'tanggal_persetujuan', 'catatan_penolakan', 'created_by'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'anggaran' => 'decimal:2',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function persetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
