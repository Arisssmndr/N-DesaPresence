<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class JadwalPiket extends Model
{
    protected $fillable = [
        'pegawai_id',
        'tanggal_piket',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'status',
        'tanda_tangan',
        'waktu_absen',
        'ip_absen',
        'waktu_pulang',
        'tanda_tangan_pulang',
        'ip_pulang',
        'created_by',
    ];

    protected $casts = [
        'tanggal_piket' => 'date',
        'waktu_absen'   => 'datetime',
        'waktu_pulang'  => 'datetime',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isHMin1(): bool
    {
        return Carbon::parse($this->tanggal_piket)->isTomorrow();
    }

    public function isHariPiket(): bool
    {
        return Carbon::parse($this->tanggal_piket)->isToday();
    }

    public function isHariLepasPiket(): bool
    {
        return Carbon::parse($this->tanggal_piket)->isYesterday();
    }

    /**
     * Menghitung tanggal & jam mulai piket sebagai objek Carbon
     */
    public function getWaktuMulaiDatetimeAttribute(): Carbon
    {
        $jam = strlen($this->jam_mulai) === 5 ? $this->jam_mulai . ':00' : $this->jam_mulai;
        return Carbon::parse($this->tanggal_piket->toDateString() . ' ' . $jam);
    }

    /**
     * Menghitung tanggal & jam selesai piket sebagai objek Carbon
     * Jika jam selesai <= jam mulai (misal 06:00 <= 19:00), berarti piket lintas malam (+1 hari)
     */
    public function getWaktuSelesaiDatetimeAttribute(): Carbon
    {
        $jamMulai = strlen($this->jam_mulai) === 5 ? $this->jam_mulai . ':00' : $this->jam_mulai;
        $jamSelesai = strlen($this->jam_selesai) === 5 ? $this->jam_selesai . ':00' : $this->jam_selesai;

        $dtSelesai = Carbon::parse($this->tanggal_piket->toDateString() . ' ' . $jamSelesai);
        $dtMulai = Carbon::parse($this->tanggal_piket->toDateString() . ' ' . $jamMulai);

        if ($dtSelesai->lte($dtMulai)) {
            $dtSelesai->addDay();
        }

        return $dtSelesai;
    }

    /**
     * Apakah piket ini melewati tengah malam (lintas hari)
     */
    public function isLintasHari(): bool
    {
        return Carbon::parse($this->jam_selesai)->lte(Carbon::parse($this->jam_mulai));
    }

    /**
     * Cek apakah staf sudah melakukan absen hadir/masuk piket
     */
    public function isSudahMasuk(): bool
    {
        return !is_null($this->waktu_absen) || !is_null($this->tanda_tangan);
    }

    /**
     * Cek apakah staf sudah melakukan absen pulang piket
     */
    public function isSudahPulang(): bool
    {
        return !is_null($this->waktu_pulang) || !is_null($this->tanda_tangan_pulang);
    }

    /**
     * Cek apakah piket sudah selesai secara lengkap (sudah masuk DAN sudah pulang)
     */
    public function isSelesaiLengkap(): bool
    {
        return $this->isSudahMasuk() && $this->isSudahPulang();
    }

    /**
     * Cek apakah jam mulai piket telah tiba
     */
    public function isWaktuMasukTiba(?Carbon $now = null): bool
    {
        $check = $now ?? now();
        return $check->gte($this->waktu_mulai_datetime);
    }

    /**
     * Cek apakah jam selesai piket telah tiba / terlewati
     */
    public function isWaktuPulangTiba(?Carbon $now = null): bool
    {
        $check = $now ?? now();
        return $check->gte($this->waktu_selesai_datetime);
    }

    /**
     * Cek apakah tombol/akses Absen Masuk piket aktif
     */
    public function isBisaAbsenMasuk(?Carbon $now = null): bool
    {
        return !$this->isSudahMasuk() && $this->isWaktuMasukTiba($now);
    }

    /**
     * Cek apakah tombol/akses Absen Pulang piket aktif
     */
    public function isBisaAbsenPulang(?Carbon $now = null): bool
    {
        return $this->isSudahMasuk() && !$this->isSudahPulang() && $this->isWaktuPulangTiba($now);
    }
}
