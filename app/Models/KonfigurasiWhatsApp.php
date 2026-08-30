<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class KonfigurasiWhatsApp extends Model
{
    protected $table = 'konfigurasi_whatsapp';

    protected $fillable = [
        'key',
        'value',
        'tipe',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Ambil nilai konfigurasi dengan auto-decrypt jika bertipe encrypted
     */
    public function getFormattedValueAttribute(): mixed
    {
        if (empty($this->value)) {
            return null;
        }

        if ($this->tipe === 'encrypted') {
            try {
                return Crypt::decryptString($this->value);
            } catch (\Exception $e) {
                // Fallback jika belum terenkripsi (plain)
                return $this->value;
            }
        }

        if ($this->tipe === 'boolean') {
            return in_array(strtolower((string) $this->value), ['1', 'true', 'yes', 'on']);
        }

        if ($this->tipe === 'integer') {
            return (int) $this->value;
        }

        return $this->value;
    }

    /**
     * Simpan nilai konfigurasi dengan auto-encrypt jika bertipe encrypted
     */
    public function setFormattedValue(mixed $val): void
    {
        if ($val === null || $val === '') {
            $this->value = null;
            return;
        }

        if ($this->tipe === 'encrypted') {
            $this->value = Crypt::encryptString((string) $val);
            return;
        }

        if ($this->tipe === 'boolean') {
            $this->value = $val ? '1' : '0';
            return;
        }

        $this->value = (string) $val;
    }
}
