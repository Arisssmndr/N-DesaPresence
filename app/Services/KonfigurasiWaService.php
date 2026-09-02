<?php

namespace App\Services;

use App\Models\KonfigurasiWhatsApp;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Cache;

class KonfigurasiWaService
{
    private const CACHE_KEY = 'konfigurasi_whatsapp_all';
    private const DEVICES_CACHE_KEY = 'konfigurasi_whatsapp_devices_cache';
    private const CACHE_TTL = 86400; // 24 jam

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
     * Ambil daftar perangkat yang tersimpan di cache lokal
     */
    public function getCachedDevices(): array
    {
        $cached = Cache::get(self::DEVICES_CACHE_KEY);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $fromDb = $this->get('cached_devices_list');
        if (is_array($fromDb) && !empty($fromDb)) {
            Cache::put(self::DEVICES_CACHE_KEY, $fromDb, self::CACHE_TTL);
            return $fromDb;
        }

        return [];
    }

    /**
     * Simpan daftar perangkat ke cache lokal & database
     */
    public function setCachedDevices(array $devices): void
    {
        Cache::put(self::DEVICES_CACHE_KEY, $devices, self::CACHE_TTL);
        $this->set('cached_devices_list', $devices, 'Cache daftar perangkat Fonnte WhatsApp');
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
     * Render pesan WhatsApp resmi untuk model Pengumuman (Bersih, rapi, langsung dari isi)
     */
    public function renderPesanPengumuman(Pengumuman $pengumuman, ?string $namaPenerima = null): string
    {
        $kategoriIcon = $pengumuman->kategori_icon . ' ' . strtoupper($pengumuman->kategori_label);
        $salam = $namaPenerima ? "Yth. *{$namaPenerima}*,\n\n" : "";

        $berlaku = $pengumuman->berlaku_hingga 
            ? "\n\n📅 *Berlaku s/d:* " . $pengumuman->berlaku_hingga->translatedFormat('d F Y')
            : '';

        $pembuat = $pengumuman->pembuat ? $pengumuman->pembuat->name : 'Pemerintah Desa Nangtang';

        return "📢 *PENGUMUMAN RESMI PEMERINTAH DESA NANGTANG*\n"
            . "────────────────────────────\n"
            . "📌 *Kategori:* {$kategoriIcon}\n"
            . "🏷️ *Perihal:* *{$pengumuman->judul}*\n\n"
            . "{$salam}"
            . "{$pengumuman->isi}"
            . "{$berlaku}\n\n"
            . "👤 *Diumumkan Oleh:* {$pembuat}\n"
            . "🕒 *" . now()->translatedFormat('d M Y, H:i') . " WIB*\n"
            . "────────────────────────────\n"
            . "_Pesan otomatis Sistem N-DesaPresence Desa Nangtang_";
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
