<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    protected $fillable = [
        'pegawai_id', 'tanggal', 'jam_masuk', 'jam_pulang', 'durasi_kerja_menit',
        'terlambat_menit', 'status', 'sumber_data', 'keterangan', 'diverifikasi_oleh',
        'tanda_tangan_masuk', 'tanda_tangan_pulang', 'ip_absensi_masuk', 'ip_absensi_pulang',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function getJamMasukFormatAttribute(): ?string
    {
        return $this->jam_masuk ? substr($this->jam_masuk, 0, 5) : null;
    }

    public function getJamPulangFormatAttribute(): ?string
    {
        return $this->jam_pulang ? substr($this->jam_pulang, 0, 5) : null;
    }

    public function getTandaTanganMasukSrcAttribute(): ?string
    {
        if (!$this->tanda_tangan_masuk) {
            return null;
        }
        if (str_starts_with($this->tanda_tangan_masuk, 'data:')) {
            return $this->tanda_tangan_masuk;
        }
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->tanda_tangan_masuk)) {
            return \Illuminate\Support\Facades\Storage::url($this->tanda_tangan_masuk);
        }
        return null;
    }

    public function getTandaTanganPulangSrcAttribute(): ?string
    {
        if (!$this->tanda_tangan_pulang) {
            return null;
        }
        if (str_starts_with($this->tanda_tangan_pulang, 'data:')) {
            return $this->tanda_tangan_pulang;
        }
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->tanda_tangan_pulang)) {
            return \Illuminate\Support\Facades\Storage::url($this->tanda_tangan_pulang);
        }
        return null;
    }

    public function getPdfTandaTanganMasukAttribute(): ?string
    {
        if (!$this->tanda_tangan_masuk) {
            return null;
        }
        if (str_starts_with($this->tanda_tangan_masuk, 'data:')) {
            return $this->tanda_tangan_masuk;
        }
        $fullPath = storage_path('app/public/' . $this->tanda_tangan_masuk);
        if (file_exists($fullPath)) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'png';
            $data = @file_get_contents($fullPath);
            return $data ? 'data:image/' . $type . ';base64,' . base64_encode($data) : null;
        }
        return null;
    }

    public function getPdfTandaTanganPulangAttribute(): ?string
    {
        if (!$this->tanda_tangan_pulang) {
            return null;
        }
        if (str_starts_with($this->tanda_tangan_pulang, 'data:')) {
            return $this->tanda_tangan_pulang;
        }
        $fullPath = storage_path('app/public/' . $this->tanda_tangan_pulang);
        if (file_exists($fullPath)) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'png';
            $data = @file_get_contents($fullPath);
            return $data ? 'data:image/' . $type . ';base64,' . base64_encode($data) : null;
        }
        return null;
    }
}
