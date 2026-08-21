<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanAbsenLuar extends Model
{
    protected $table = 'pengajuan_absen_luars';

    protected $fillable = [
        'pegawai_id',
        'user_id',
        'tanggal',
        'jenis',
        'judul',
        'deskripsi',
        'foto_lokasi',
        'file_dokumen',
        'latitude',
        'longitude',
        'alamat_gps',
        'tanda_tangan',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'diproses_pada' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diprosesoleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getLabelJenisAttribute(): string
    {
        return match ($this->jenis) {
            'kegiatan_sosial' => 'Kegiatan Sosial',
            'dinas_luar'      => 'Dinas Luar Resmi',
            default           => $this->jenis,
        };
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'Menunggu Persetujuan',
            'disetujui'  => 'Disetujui',
            'ditolak'    => 'Ditolak',
            default      => $this->status,
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'bg-amber-100 text-amber-800 border-amber-300',
            'disetujui'  => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'ditolak'    => 'bg-red-100 text-red-800 border-red-300',
            default      => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getTandaTanganSrcAttribute(): ?string
    {
        if (!$this->tanda_tangan) {
            return null;
        }
        return str_starts_with($this->tanda_tangan, 'data:')
            ? $this->tanda_tangan
            : \Illuminate\Support\Facades\Storage::url($this->tanda_tangan);
    }
}
