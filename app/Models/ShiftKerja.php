<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftKerja extends Model
{
    protected $table = 'shift_kerjas';
    protected $fillable = ['nama_shift', 'jam_masuk', 'jam_pulang', 'toleransi_menit', 'is_active'];

    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'shift_id');
    }
}
