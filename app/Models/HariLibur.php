<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'hari_liburs';
    protected $fillable = ['tanggal', 'nama_hari_libur', 'jenis'];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
