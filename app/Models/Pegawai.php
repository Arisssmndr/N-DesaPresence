<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pegawai extends Model
{
    protected $fillable = [
        'pin_fingerprint', 'nipd', 'nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'jabatan_id', 'kategori_pegawai', 'shift_id', 'no_hp', 'alamat',
        'foto_profil', 'periode_mulai', 'periode_akhir', 'siltap_bruto', 'status_aktif'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
        'status_aktif' => 'boolean',
        'siltap_bruto' => 'decimal:2',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function shiftKerja(): BelongsTo
    {
        return $this->belongsTo(ShiftKerja::class, 'shift_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function kehadirans(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function suratPerintahTugas(): HasMany
    {
        return $this->hasMany(SuratPerintahTugas::class);
    }

    public function izinSakits(): HasMany
    {
        return $this->hasMany(IzinSakit::class);
    }

    public function riwayatJabatans(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function pengajuanAbsenLuars(): HasMany
    {
        return $this->hasMany(PengajuanAbsenLuar::class);
    }

    public function jadwalPikets(): HasMany
    {
        return $this->hasMany(JadwalPiket::class);
    }
}
