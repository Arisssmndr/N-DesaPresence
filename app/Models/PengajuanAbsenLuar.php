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
        'instansi_pengundang',
        'nomor_surat_tugas',
        'deskripsi',
        'foto_lokasi',
        'file_dokumen',
        'latitude',
        'longitude',
        'alamat_gps',
        'sumber_koordinat',
        'akurasi_gps_meter',
        'tanda_tangan',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected $casts = [
        'tanggal'            => 'date',
        'diproses_pada'      => 'datetime',
        'akurasi_gps_meter'  => 'integer',
        'latitude'           => 'decimal:8',
        'longitude'          => 'decimal:8',
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
            'dinas_luar_undangan'    => 'Dinas Luar (Undangan)',
            'dinas_luar_pengajuan'   => 'Dinas Luar (Pengajuan Mandiri)',
            'dinas_luar_surat_tugas' => 'Dinas Luar (Surat Tugas)',
            'kegiatan_sosial'        => 'Kegiatan Sosial',
            'dinas_luar'             => 'Dinas Luar Resmi',
            default                  => ucwords(str_replace('_', ' ', $this->jenis)),
        };
    }

    public function getJenisBadgeClassAttribute(): string
    {
        return 'bg-slate-100 text-slate-800 border-slate-200';
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'Menunggu',
            'disetujui'  => 'Disetujui',
            'ditolak'    => 'Ditolak',
            default      => $this->status,
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'bg-amber-50 text-amber-900 border-amber-300',
            'disetujui'  => 'bg-emerald-50 text-[#064E3B] border-emerald-300',
            'ditolak'    => 'bg-rose-50 text-rose-700 border-rose-300 font-bold',
            default      => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function getLabelSumberKoordinatAttribute(): string
    {
        return match ($this->sumber_koordinat) {
            'gps'             => 'GPS Fisik Presisi' . ($this->akurasi_gps_meter ? ' (±' . $this->akurasi_gps_meter . 'm)' : ''),
            'ip_geolocation'  => 'Estimasi Jaringan IP',
            'manual'          => 'Input Manual Terverifikasi',
            default           => 'GPS',
        };
    }

    public function getBadgeSumberKoordinatAttribute(): string
    {
        return match ($this->sumber_koordinat) {
            'gps'             => 'bg-emerald-50 text-emerald-800 border-emerald-300',
            'ip_geolocation'  => 'bg-blue-50 text-blue-800 border-blue-300',
            'manual'          => 'bg-purple-50 text-purple-800 border-purple-300',
            default           => 'bg-slate-100 text-slate-800 border-slate-200',
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

