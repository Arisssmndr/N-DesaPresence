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
        'respons_staf', 'alasan_tolak_staf', 'waktu_respons_staf', 'tanda_tangan_staf',
        'disetujui_oleh', 'tanggal_persetujuan', 'catatan_penolakan', 'created_by'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_persetujuan' => 'datetime',
        'waktu_respons_staf' => 'datetime',
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

    public function isMenungguRespons(): bool
    {
        return $this->respons_staf === 'menunggu';
    }

    public function isDiterima(): bool
    {
        return $this->respons_staf === 'diterima';
    }

    public function isDitolak(): bool
    {
        return $this->respons_staf === 'ditolak' || $this->status === 'ditolak';
    }

    public function isAktifPadaTanggal(string $date): bool
    {
        if ($this->status !== 'disetujui' || $this->respons_staf !== 'diterima') {
            return false;
        }

        $d = \Carbon\Carbon::parse($date);
        return $d->betweenIncluded($this->tanggal_mulai, $this->tanggal_selesai);
    }

    public function terapkanKehadiran(?string $signature = null, ?int $verifiedBy = null): void
    {
        $start = \Carbon\Carbon::parse($this->tanggal_mulai)->startOfDay();
        $end = \Carbon\Carbon::parse($this->tanggal_selesai)->startOfDay();
        $verifierId = $verifiedBy ?? $this->disetujui_oleh ?? $this->created_by ?? 1;
        $keteranganSpt = $this->nomor_spt 
            ? "Dinas Luar SPT: {$this->nomor_spt} ({$this->tujuan})" 
            : "Dinas Luar SPT: {$this->tujuan}";
        $ttd = $signature ?? $this->tanda_tangan_staf;

        while ($start->lte($end)) {
            $dateStr = $start->toDateString();
            $existing = \App\Models\Kehadiran::where('pegawai_id', $this->pegawai_id)
                ->whereDate('tanggal', $dateStr)
                ->first();

            if (!$existing) {
                \App\Models\Kehadiran::create([
                    'pegawai_id'          => $this->pegawai_id,
                    'tanggal'             => $dateStr,
                    'status'              => 'Hadir',
                    'jam_masuk'           => '07:30:00',
                    'jam_pulang'          => '15:30:00',
                    'tanda_tangan_masuk'  => $ttd,
                    'tanda_tangan_pulang' => $ttd,
                    'sumber_data'         => 'manual_admin',
                    'diverifikasi_oleh'   => $verifierId,
                    'keterangan'          => $keteranganSpt,
                ]);
            } elseif (!$existing->jam_masuk || $existing->sumber_data === 'manual_admin') {
                $existing->update([
                    'status'              => 'Hadir',
                    'jam_masuk'           => $existing->jam_masuk ?? '07:30:00',
                    'jam_pulang'          => $existing->jam_pulang ?? '15:30:00',
                    'tanda_tangan_masuk'  => $ttd ?: $existing->tanda_tangan_masuk,
                    'tanda_tangan_pulang' => $ttd ?: $existing->tanda_tangan_pulang,
                    'sumber_data'         => 'manual_admin',
                    'diverifikasi_oleh'   => $verifierId,
                    'keterangan'          => $keteranganSpt,
                ]);
            }
            $start->addDay();
        }
    }

    public function batalkanKehadiran(): void
    {
        $start = \Carbon\Carbon::parse($this->tanggal_mulai)->startOfDay();
        $end = \Carbon\Carbon::parse($this->tanggal_selesai)->startOfDay();

        while ($start->lte($end)) {
            $dateStr = $start->toDateString();
            $query = \App\Models\Kehadiran::where('pegawai_id', $this->pegawai_id)
                ->whereDate('tanggal', $dateStr)
                ->where('sumber_data', 'manual_admin');

            if ($this->nomor_spt) {
                $query->where('keterangan', 'like', "%{$this->nomor_spt}%");
            } else {
                $query->where('keterangan', 'like', "%{$this->tujuan}%");
            }

            $query->delete();
            $start->addDay();
        }
    }
}
