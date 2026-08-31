<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AbsensiDisesuaikan extends Model
{
    protected $table = 'absensi_disesuaikans';

    protected $fillable = [
        'kehadiran_id',
        'pegawai_id',
        'tanggal',
        'status_asli',
        'status_disesuaikan',
        'jam_masuk',
        'jam_pulang',
        'durasi_kerja_menit',
        'terlambat_menit',
        'tanda_tangan_disesuaikan',
        'sumber_tanda_tangan',
        'tanggal_sumber_ttd',
        'keterangan',
        'dibuat_oleh',
        'diubah_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_sumber_ttd' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function kehadiran(): BelongsTo
    {
        return $this->belongsTo(Kehadiran::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function getJamMasukFormatAttribute(): ?string
    {
        return $this->jam_masuk ? substr($this->jam_masuk, 0, 5) : null;
    }

    public function getJamPulangFormatAttribute(): ?string
    {
        return $this->jam_pulang ? substr($this->jam_pulang, 0, 5) : null;
    }

    public function getTandaTanganSrcAttribute(): ?string
    {
        if (!$this->tanda_tangan_disesuaikan) {
            return null;
        }
        return str_starts_with($this->tanda_tangan_disesuaikan, 'data:')
            ? $this->tanda_tangan_disesuaikan
            : Storage::url($this->tanda_tangan_disesuaikan);
    }

    public function getPdfTandaTanganAttribute(): ?string
    {
        if (!$this->tanda_tangan_disesuaikan) {
            return null;
        }
        if (str_starts_with($this->tanda_tangan_disesuaikan, 'data:')) {
            return $this->tanda_tangan_disesuaikan;
        }
        $fullPath = storage_path('app/public/' . $this->tanda_tangan_disesuaikan);
        if (file_exists($fullPath)) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'png';
            $data = @file_get_contents($fullPath);
            return $data ? 'data:image/' . $type . ';base64,' . base64_encode($data) : null;
        }
        return null;
    }

    public function getLabelSumberTtdAttribute(): string
    {
        if (!$this->tanda_tangan_disesuaikan) {
            return 'Belum Ada TTD';
        }

        if ($this->sumber_tanda_tangan === 'asli') {
            return 'Tanda Tangan Asli';
        }

        if (str_starts_with($this->sumber_tanda_tangan ?? '', 'pinjam') && $this->tanggal_sumber_ttd) {
            $tgl = Carbon::parse($this->tanggal_sumber_ttd)->translatedFormat('d M Y');
            return "Pinjam dari {$tgl}";
        }

        return 'Tanda Tangan Disesuaikan';
    }

    /**
     * Cari tanda tangan terdekat untuk pegawai yang sama (sampai maxHari ke belakang atau ke depan).
     */
    public static function cariTandaTanganPegawai(int $pegawaiId, string $targetTanggal, int $maxHari = 7): ?array
    {
        // 0. Cek master spesimen tanda tangan resmi di profil pegawai terlebih dahulu
        $pegawai = Pegawai::find($pegawaiId);
        if ($pegawai && $pegawai->tanda_tangan) {
            return [
                'signature' => $pegawai->tanda_tangan,
                'source'    => 'master_resmi',
                'date'      => $targetTanggal,
            ];
        }

        $targetDt = Carbon::parse($targetTanggal);

        // 1. Prioritaskan cari ke belakang (H-1 s/d H-7)
        for ($i = 1; $i <= $maxHari; $i++) {
            $checkDate = $targetDt->copy()->subDays($i)->toDateString();
            
            // Cek di data kehadiran asli terlebih dahulu
            $kehadiran = Kehadiran::where('pegawai_id', $pegawaiId)
                ->whereDate('tanggal', $checkDate)
                ->where(function ($q) {
                    $q->whereNotNull('tanda_tangan_masuk')
                      ->orWhereNotNull('tanda_tangan_pulang');
                })
                ->first();

            if ($kehadiran) {
                $ttd = $kehadiran->tanda_tangan_masuk ?: $kehadiran->tanda_tangan_pulang;
                if ($ttd) {
                    return [
                        'signature' => $ttd,
                        'source'    => "pinjam_H-{$i}",
                        'date'      => $checkDate,
                    ];
                }
            }

            // Cek di data disesuaikan jika ada
            $disesuaikan = static::where('pegawai_id', $pegawaiId)
                ->whereDate('tanggal', $checkDate)
                ->whereNotNull('tanda_tangan_disesuaikan')
                ->first();

            if ($disesuaikan && $disesuaikan->tanda_tangan_disesuaikan) {
                return [
                    'signature' => $disesuaikan->tanda_tangan_disesuaikan,
                    'source'    => "pinjam_H-{$i}",
                    'date'      => $checkDate,
                ];
            }
        }

        // 2. Jika dalam rentang mundur belum ketemu, cari tanda tangan apapun yang pernah ada dari pegawai tersebut
        $latestRecord = Kehadiran::where('pegawai_id', $pegawaiId)
            ->where(function ($q) {
                $q->whereNotNull('tanda_tangan_masuk')
                  ->orWhereNotNull('tanda_tangan_pulang');
            })
            ->latest('tanggal')
            ->first();

        if ($latestRecord) {
            $ttd = $latestRecord->tanda_tangan_masuk ?: $latestRecord->tanda_tangan_pulang;
            if ($ttd) {
                $tglStr = is_string($latestRecord->tanggal) ? substr($latestRecord->tanggal, 0, 10) : $latestRecord->tanggal->format('Y-m-d');
                return [
                    'signature' => $ttd,
                    'source'    => 'pinjam_arsip',
                    'date'      => $tglStr,
                ];
            }
        }

        return null;
    }
}
