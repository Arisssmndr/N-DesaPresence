<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'kategori',
        'is_pinned',
        'kirim_wa',
        'target_penerima',
        'pegawai_ids',
        'berlaku_hingga',
        'wa_terkirim_at',
        'total_wa_terkirim',
        'total_wa_gagal',
        'dibuat_oleh',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'kirim_wa' => 'boolean',
        'pegawai_ids' => 'array',
        'berlaku_hingga' => 'date',
        'wa_terkirim_at' => 'datetime',
        'total_wa_terkirim' => 'integer',
        'total_wa_gagal' => 'integer',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function waLogs(): HasMany
    {
        return $this->hasMany(WaNotifikasiLog::class, 'pengumuman_id');
    }

    public static function kategoriList(): array
    {
        return [
            'informasi'     => [
                'label'      => 'Informasi Umum', 
                'type'       => 'info', 
                'badge'      => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                'border_bar' => 'border-l-4 border-l-emerald-600',
                'accent_bg'  => 'bg-emerald-50/60',
            ],
            'rapat'         => [
                'label'      => 'Undangan Rapat', 
                'type'       => 'meeting', 
                'badge'      => 'bg-blue-50 text-blue-800 border-blue-300',
                'border_bar' => 'border-l-4 border-l-blue-600',
                'accent_bg'  => 'bg-blue-50/60',
            ],
            'kegiatan'      => [
                'label'      => 'Kegiatan Desa', 
                'type'       => 'event', 
                'badge'      => 'bg-indigo-50 text-indigo-800 border-indigo-300',
                'border_bar' => 'border-l-4 border-l-indigo-600',
                'accent_bg'  => 'bg-indigo-50/60',
            ],
            'penting'       => [
                'label'      => 'Penting / Mendesak', 
                'type'       => 'urgent', 
                'badge'      => 'bg-rose-50 text-rose-800 border-rose-300',
                'border_bar' => 'border-l-4 border-l-rose-600',
                'accent_bg'  => 'bg-rose-50/60',
            ],
            'arahan'        => [
                'label'      => 'Arahan & Instruksi Kerja', 
                'type'       => 'directive', 
                'badge'      => 'bg-teal-50 text-teal-800 border-teal-300',
                'border_bar' => 'border-l-4 border-l-teal-600',
                'accent_bg'  => 'bg-teal-50/60',
            ],
            'keuangan'      => [
                'label'      => 'Keuangan & Siltap', 
                'type'       => 'finance', 
                'badge'      => 'bg-amber-50 text-amber-900 border-amber-300',
                'border_bar' => 'border-l-4 border-l-amber-500',
                'accent_bg'  => 'bg-amber-50/60',
            ],
            'administrasi'  => [
                'label'      => 'Administrasi & Laporan', 
                'type'       => 'admin', 
                'badge'      => 'bg-slate-100 text-slate-800 border-slate-300',
                'border_bar' => 'border-l-4 border-l-slate-600',
                'accent_bg'  => 'bg-slate-50/80',
            ],
            'libur'         => [
                'label'      => 'Hari Libur & Cuti Bersama', 
                'type'       => 'holiday', 
                'badge'      => 'bg-purple-50 text-purple-800 border-purple-300',
                'border_bar' => 'border-l-4 border-l-purple-600',
                'accent_bg'  => 'bg-purple-50/60',
            ],
            'kesehatan'     => [
                'label'      => 'Kesehatan & Posyandu', 
                'type'       => 'health', 
                'badge'      => 'bg-pink-50 text-pink-800 border-pink-300',
                'border_bar' => 'border-l-4 border-l-pink-600',
                'accent_bg'  => 'bg-pink-50/60',
            ],
            'lainnya'       => [
                'label'      => 'Lain-lain / Tambahan', 
                'type'       => 'other', 
                'badge'      => 'bg-[#FAF6F0] text-[#064E3B] border-[#C9A84C]/50',
                'border_bar' => 'border-l-4 border-l-[#C9A84C]',
                'accent_bg'  => 'bg-[#FAF6F0]/80',
            ],
        ];
    }

    public function getKategoriLabelAttribute(): string
    {
        $list = self::kategoriList();
        return $list[$this->kategori]['label'] ?? ucfirst($this->kategori);
    }

    public function getKategoriBadgeAttribute(): string
    {
        $list = self::kategoriList();
        return $list[$this->kategori]['badge'] ?? 'bg-slate-100 text-slate-800 border-slate-200';
    }

    public function getKategoriBorderBarAttribute(): string
    {
        $list = self::kategoriList();
        return $list[$this->kategori]['border_bar'] ?? 'border-l-4 border-l-slate-400';
    }

    public function getKategoriAccentBgAttribute(): string
    {
        $list = self::kategoriList();
        return $list[$this->kategori]['accent_bg'] ?? 'bg-slate-50/50';
    }

    public function getTargetPenerimaLabelAttribute(): string
    {
        if (!empty($this->pegawai_ids) && is_array($this->pegawai_ids)) {
            $count = count($this->pegawai_ids);
            return "{$count} Pegawai Terpilih";
        }

        return match ($this->target_penerima) {
            'semua' => 'Seluruh Perangkat & Staf',
            'perangkat_tetap' => 'Perangkat Desa Tetap',
            'staf' => 'Staf / Honorer Desa',
            'bpd' => 'Badan Permusyawaratan Desa (BPD)',
            'kemasyarakatan' => 'Lembaga Kemasyarakatan Desa',
            default => 'Semua',
        };
    }
}
