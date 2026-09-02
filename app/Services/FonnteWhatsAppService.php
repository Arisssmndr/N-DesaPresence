<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteWhatsAppService
{
    private const API_ENDPOINT = 'https://api.fonnte.com/send';
    private const DEVICE_ENDPOINT = 'https://api.fonnte.com/device';
    private const ADD_DEVICE_ENDPOINT = 'https://api.fonnte.com/add-device';
    private const QR_ENDPOINT = 'https://api.fonnte.com/qr';
    private const GET_DEVICES_ENDPOINT = 'https://api.fonnte.com/get-devices';
    private const DELETE_DEVICE_ENDPOINT = 'https://api.fonnte.com/delete-device';
    private const DISCONNECT_ENDPOINT = 'https://api.fonnte.com/disconnect';

    public function __construct(private KonfigurasiWaService $configService) {}

    /**
     * Kirim satu pesan WhatsApp via Fonnte Gateway
     */
    public function send(string $target, string $message, array $options = []): array
    {
        $apiKey = $options['api_key'] ?? $this->configService->get('fonnte_api_key');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'status'  => 'gagal',
                'message' => 'API Token Fonnte belum dikonfigurasi di Panel Admin.',
                'raw'     => null,
                'error'   => 'Missing Fonnte API Key',
            ];
        }

        $formattedTarget = $this->configService->formatNomorHp($target);

        if (empty($formattedTarget)) {
            return [
                'success' => false,
                'status'  => 'dilewati',
                'message' => 'Nomor tujuan tidak valid atau kosong.',
                'raw'     => null,
                'error'   => 'Invalid destination number',
            ];
        }

        $payload = [
            'target'      => $formattedTarget,
            'message'     => $message,
            'countryCode' => (string) $this->configService->get('wa_country_code', '62'),
        ];

        if (!empty($options['delay'])) {
            $payload['delay'] = (int) $options['delay'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])
            ->timeout(6)
            ->post(self::API_ENDPOINT, $payload);

            $result = $response->json();

            $isSuccess = $response->successful() && ($result['status'] ?? false) === true;

            if ($isSuccess) {
                return [
                    'success' => true,
                    'status'  => 'terkirim',
                    'message' => 'Pesan WhatsApp berhasil dikirim.',
                    'raw'     => $result,
                    'error'   => null,
                ];
            }

            $errorMessage = $result['reason'] ?? $result['message'] ?? ('HTTP Error: ' . $response->status());

            Log::warning('Fonnte WhatsApp Dispatch Failed', [
                'target'  => $formattedTarget,
                'error'   => $errorMessage,
                'payload' => $payload,
                'res'     => $result,
            ]);

            return [
                'success' => false,
                'status'  => 'gagal',
                'message' => 'Gagal mengirim pesan: ' . $errorMessage,
                'raw'     => $result,
                'error'   => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('Fonnte WhatsApp Exception', [
                'target' => $formattedTarget,
                'error'  => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => 'gagal',
                'message' => 'Koneksi ke gateway Fonnte timeout: ' . $e->getMessage(),
                'raw'     => null,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status koneksi perangkat WhatsApp di Fonnte
     */
    public function checkDeviceStatus(?string $customApiKey = null): array
    {
        $apiKey = $customApiKey ?: $this->configService->get('fonnte_api_key');

        if (empty($apiKey)) {
            return [
                'success'   => false,
                'connected' => false,
                'message'   => 'Token API Fonnte belum diisi.',
                'raw'       => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])
            ->timeout(5)
            ->post(self::DEVICE_ENDPOINT);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                $deviceStatus = strtolower($result['device_status'] ?? '');
                $isConnected = in_array($deviceStatus, ['connect', 'connected']);

                return [
                    'success'       => true,
                    'connected'     => $isConnected,
                    'device_status' => $result['device_status'] ?? 'unknown',
                    'device'        => $result['device'] ?? null,
                    'name'          => $result['name'] ?? null,
                    'package'       => $result['package'] ?? 'Free / Regular',
                    'quota'         => $result['quota'] ?? '-',
                    'messages'      => $result['messages'] ?? '-',
                    'expired'       => $result['expired'] ?? '-',
                    'message'       => $isConnected ? 'Perangkat WhatsApp Terhubung & Siap Kirim' : 'Perangkat Terputus/Perlu Scan QR di Fonnte',
                    'raw'           => $result,
                ];
            }

            return [
                'success'   => false,
                'connected' => false,
                'message'   => $result['reason'] ?? $result['message'] ?? 'Gagal memeriksa status perangkat.',
                'raw'       => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success'   => false,
                'connected' => false,
                'message'   => 'Gagal menghubungi server Fonnte (Timeout): ' . $e->getMessage(),
                'raw'       => null,
            ];
        }
    }

    /**
     * Ambil list seluruh device yang terdaftar di akun Fonnte via Account Token (Dengan Cache Fallback)
     */
    public function getDevicesList(?string $customAccountToken = null): array
    {
        $accountToken = $customAccountToken ?: $this->configService->get('fonnte_account_token');

        if (empty($accountToken)) {
            return [
                'success' => false,
                'message' => 'Account Token Fonnte belum diatur. Masukkan Account Token terlebih dahulu.',
                'data'    => [],
            ];
        }

        $cachedDevices = $this->configService->getCachedDevices();

        try {
            $response = Http::withHeaders([
                'Authorization' => $accountToken,
            ])
            ->timeout(8)
            ->post(self::GET_DEVICES_ENDPOINT);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false) === true) {
                $devices = $result['data'] ?? [];
                
                // Simpan ke cache lokal agar tidak pernah hilang saat refresh
                $this->configService->setCachedDevices($devices);

                return [
                    'success'   => true,
                    'is_cached' => false,
                    'data'      => $devices,
                    'devices'   => $result['devices'] ?? count($devices),
                    'connected' => $result['connected'] ?? 0,
                    'messages'  => $result['messages'] ?? 0,
                    'raw'       => $result,
                ];
            }

            // Jika API gagal tapi ada cache lokal, gunakan cache
            if (!empty($cachedDevices)) {
                return [
                    'success'   => true,
                    'is_cached' => true,
                    'data'      => $cachedDevices,
                    'devices'   => count($cachedDevices),
                    'message'   => 'Menampilkan data perangkat dari penyimpanan lokal.',
                ];
            }

            return [
                'success' => false,
                'message' => $result['reason'] ?? $result['message'] ?? 'Gagal memuat daftar perangkat dari Fonnte.',
                'data'    => [],
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            // Jika request timeout / error tapi ada cache lokal, tetap tampilkan perangkat!
            if (!empty($cachedDevices)) {
                return [
                    'success'   => true,
                    'is_cached' => true,
                    'data'      => $cachedDevices,
                    'devices'   => count($cachedDevices),
                    'message'   => 'Menampilkan data perangkat tersimpan (Server Fonnte sedang sibuk).',
                ];
            }

            return [
                'success' => false,
                'message' => 'Koneksi ke Fonnte sedang lambat: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    /**
     * Tambah Device Baru ke Akun Fonnte via Account Token
     */
    public function addDevice(string $name, string $deviceNumber, ?string $customAccountToken = null): array
    {
        $accountToken = $customAccountToken ?: $this->configService->get('fonnte_account_token');

        if (empty($accountToken)) {
            return [
                'success' => false,
                'message' => 'Account Token Fonnte belum diatur.',
            ];
        }

        $formatted = $this->configService->formatNomorHp($deviceNumber) ?? $deviceNumber;

        try {
            $response = Http::withHeaders([
                'Authorization' => $accountToken,
            ])
            ->timeout(8)
            ->post(self::ADD_DEVICE_ENDPOINT, [
                'name'     => $name,
                'device'   => $formatted,
                'autoread' => true,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false) === true) {
                return [
                    'success' => true,
                    'token'   => $result['token'] ?? null,
                    'message' => $result['detail'] ?? 'Device berhasil ditambahkan ke Fonnte.',
                    'device'  => $formatted,
                    'name'    => $name,
                    'raw'     => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['reason'] ?? $result['message'] ?? 'Gagal menambahkan device ke Fonnte.',
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke Fonnte timeout: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil QR Code Base64 untuk Scan WhatsApp.
     */
    public function getQrCode(string $whatsappNumber, ?string $deviceToken = null, ?string $customAccountToken = null): array
    {
        $accountToken = $customAccountToken ?: $this->configService->get('fonnte_account_token');
        $tokenToUse = $deviceToken ?: $this->configService->get('fonnte_api_key') ?: $accountToken;

        if (empty($tokenToUse)) {
            return [
                'success' => false,
                'message' => 'Token API untuk perangkat ini belum tersedia.',
            ];
        }

        $formatted = $this->configService->formatNomorHp($whatsappNumber) ?? $whatsappNumber;

        // Coba request QR pertama dengan Device Token
        try {
            $response = Http::withHeaders([
                'Authorization' => $tokenToUse,
            ])
            ->timeout(8)
            ->post(self::QR_ENDPOINT, [
                'type'     => 'qr',
                'whatsapp' => $formatted,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false) === true && !empty($result['url'])) {
                return [
                    'success' => true,
                    'url'     => $result['url'],
                    'message' => 'QR Code berhasil dimuat. Silakan scan dengan aplikasi WhatsApp.',
                    'raw'     => $result,
                ];
            }

            // Fallback dengan Account Token jika ada
            if (!empty($accountToken) && $accountToken !== $tokenToUse) {
                $fallbackResponse = Http::withHeaders([
                    'Authorization' => $accountToken,
                ])
                ->timeout(6)
                ->post(self::QR_ENDPOINT, [
                    'type'     => 'qr',
                    'whatsapp' => $formatted,
                ]);

                $fallbackResult = $fallbackResponse->json();
                if ($fallbackResponse->successful() && ($fallbackResult['status'] ?? false) === true && !empty($fallbackResult['url'])) {
                    return [
                        'success' => true,
                        'url'     => $fallbackResult['url'],
                        'message' => 'QR Code berhasil dimuat.',
                        'raw'     => $fallbackResult,
                    ];
                }
            }

            $reason = $result['reason'] ?? $result['message'] ?? 'Gagal mengambil QR Code dari Fonnte.';
            
            if (str_contains(strtolower($reason), 'already connected') || str_contains(strtolower($reason), 'free device')) {
                $reason = 'Akun Fonnte Free hanya mengizinkan 1 perangkat terhubung. Putuskan/disconnect perangkat lain yang sedang aktif terlebih dahulu di Fonnte.';
            }

            return [
                'success' => false,
                'message' => $reason,
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke Fonnte timeout: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Hapus Device dari Fonnte
     */
    public function deleteDevice(string $deviceNumber, ?string $deviceToken = null, ?string $customAccountToken = null): array
    {
        $accountToken = $customAccountToken ?: $this->configService->get('fonnte_account_token');
        $tokenToUse = $deviceToken ?: $accountToken ?: $this->configService->get('fonnte_api_key');

        if (empty($tokenToUse)) {
            return [
                'success' => true,
                'message' => 'Perangkat dihapus dari daftar lokal.',
            ];
        }

        $formatted = $this->configService->formatNomorHp($deviceNumber) ?? $deviceNumber;

        try {
            $response = Http::withHeaders([
                'Authorization' => $tokenToUse,
            ])
            ->timeout(5)
            ->post(self::DELETE_DEVICE_ENDPOINT, [
                'device' => $formatted,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false) === true) {
                return [
                    'success' => true,
                    'message' => 'Perangkat WhatsApp berhasil dihapus dari Fonnte.',
                    'raw'     => $result,
                ];
            }

            // Fallback dengan Account Token
            if (!empty($accountToken) && $accountToken !== $tokenToUse) {
                $fallback = Http::withHeaders([
                    'Authorization' => $accountToken,
                ])
                ->timeout(4)
                ->post(self::DELETE_DEVICE_ENDPOINT, [
                    'device' => $formatted,
                ]);

                $fallbackRes = $fallback->json();
                if ($fallback->successful() && ($fallbackRes['status'] ?? false) === true) {
                    return [
                        'success' => true,
                        'message' => 'Perangkat WhatsApp berhasil dihapus dari Fonnte.',
                        'raw'     => $fallbackRes,
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Perangkat dihapus dari sistem.',
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => true,
                'message' => 'Perangkat dihapus dari sistem (Fonnte background sync).',
            ];
        }
    }

    /**
     * Putuskan koneksi WhatsApp (Disconnect)
     */
    public function disconnectDevice(string $deviceNumber, ?string $deviceToken = null, ?string $customAccountToken = null): array
    {
        $accountToken = $customAccountToken ?: $this->configService->get('fonnte_account_token');
        $tokenToUse = $deviceToken ?: $accountToken ?: $this->configService->get('fonnte_api_key');

        if (empty($tokenToUse)) {
            return [
                'success' => false,
                'message' => 'Token API belum diatur.',
            ];
        }

        $formatted = $this->configService->formatNomorHp($deviceNumber) ?? $deviceNumber;

        try {
            $response = Http::withHeaders([
                'Authorization' => $tokenToUse,
            ])
            ->timeout(5)
            ->post(self::DISCONNECT_ENDPOINT, [
                'device' => $formatted,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false) === true) {
                return [
                    'success' => true,
                    'message' => 'Perangkat WhatsApp berhasil diputuskan (disconnected).',
                    'raw'     => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['reason'] ?? $result['message'] ?? 'Gagal memutuskan koneksi WhatsApp.',
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke Fonnte timeout: ' . $e->getMessage(),
            ];
        }
    }
}
