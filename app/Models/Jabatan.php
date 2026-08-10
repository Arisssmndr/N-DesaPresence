<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $fillable = ['nama_jabatan', 'kode_jabatan', 'level_jabatan', 'deskripsi'];

    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
}
