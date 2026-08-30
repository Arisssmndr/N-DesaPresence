<?php

namespace App\Services;

use App\Models\KonfigurasiWhatsApp;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Cache;

class KonfigurasiWaService
{
    private const CACHE_KEY = 'konfigurasi_whatsapp_all';
    private const CACHE_TTL = 3600; // 1 jam

    /**
     * Ambil nilai konfigurasi berdasarkan key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->getAllCached();

        if (isset($all[$key])) {
            return $all[$key];
        }

        $config = KonfigurasiWhatsApp::where('key', $key)->first();
        if ($config) {
            $val = $config->formatted_value;
            $this->clearCache();
            return $val !== null ? $val : $default;
        }

        return $default;
    }

    /**
     * Simpan nilai konfigurasi
     */
    public function set(string $key, mixed $value, ?string $keterangan = null): void
    {
        $config = KonfigurasiWhatsApp::firstOrNew(['key' => $key]);
        if ($keterangan) {
            $config->keterangan = $keterangan;
        }
        $config->setFormattedValue($value);
        $config->save();

        $this->clearCache();
    }

    /**
     * Cek apakah notifikasi WhatsApp aktif dan siap digunakan
     */
    public function isEnabled(): bool
    {
        $enabled = (bool) $this->get('wa_notifikasi_enabled', false);
        $apiKey = $this->get('fonnte_api_key');

        return $enabled && !empty($apiKey);
    }

    /**
     * Format nomor HP ke standar internasional Indonesia (628xxx)
     */
    public function formatNomorHp(?string $nomor): ?string
    {
        if (empty($nomor)) {
            return null;
        }

        // Hapus semua karakter non-numerik (+, -, spasi, dll)
        $clean = preg_replace('/[^0-9]/', '', trim($nomor));

        if (empty($clean)) {
            return null;
        }

        // Jika diawali '08', ubah ke '628'
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        } elseif (str_starts_with($clean, '62')) {
            // Sudah benar
        } else {
            $countryCode = (string) $this->get('wa_country_code', '62');
            $clean = $countryCode . $clean;
        }

        return $clean;
    }

    /**
     * Render pesan WhatsApp dari template untuk model Pengumuman
     */
    public function renderPesanPengumuman(Pengumuman $pengumuman, ?string $namaPenerima = null): string
    {
        $template = (string) $this->get('wa_template_pengumuman');

        if (empty($template)) {
            $template = "📢 *PENGUMUMAN DESA NANGTANG*\n\n📌 *Kategori:* {kategori}\n🏷️ *Perihal:* {judul}\n\n{isi}\n\n📅 *Berlaku s/d:* {berlaku_hingga}\n👤 *Diumumkan Oleh:* {pembuat}\n\n_Pesan otomatis N-DesaPresence Desa Nangtang_";
        }

        $kategoriIcon = match ($pengumuman->kategori) {
            'penting' => '🚨 PENTING / MENDESAK',
            'rapat' => '🏛️ RAPAT / MUSYAWARAH DESA',
            'kegiatan' => '📅 AGENDA KEGIATAN DESA',
            default => 'ℹ️ INFORMASI KEDINASAN',
        };

        $berlaku = $pengumuman->berlaku_hingga 
            ? $pengumuman->berlaku_hingga->translatedFormat('d F Y') 
            : 'Seterusnya / Hingga Dicabut';

        $pembuat = $pengumuman->pembuat ? $pengumuman->pembuat->name : 'Pemerintah Desa Nangtang';

        $replacements = [
            '{nama_penerima}' => $namaPenerima ?? 'Bapak/Ibu Perangkat Desa',
            '{kategori}' => $kategoriIcon,
            '{kategori_raw}' => strtoupper($pengumuman->kategori),
            '{judul}' => $pengumuman->judul,
            '{isi}' => $pengumuman->isi,
            '{berlaku_hingga}' => $berlaku,
            '{pembuat}' => $pembuat,
            '{tanggal}' => now()->translatedFormat('d F Y H:i') . ' WIB',
            '{desa}' => 'Desa Nangtang, Kec. Cigalontang, Kab. Tasikmalaya',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Bersihkan cache konfigurasi
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ambil seluruh konfigurasi dalam cache
     */
    private function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $configs = KonfigurasiWhatsApp::all();
            $result = [];
            foreach ($configs as $cfg) {
                $result[$cfg->key] = $cfg->formatted_value;
            }
            return $result;
        });
    }
}
