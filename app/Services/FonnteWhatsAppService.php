<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteWhatsAppService
{
    private const API_ENDPOINT = 'https://api.fonnte.com/send';
    private const DEVICE_ENDPOINT = 'https://api.fonnte.com/device';

    public function __construct(private KonfigurasiWaService $configService) {}

    /**
     * Kirim satu pesan WhatsApp via Fonnte Gateway
     * 
     * @return array ['success' => bool, 'status' => string, 'message' => string, 'raw' => ?array, 'error' => ?string]
     */
    public function send(string $target, string $message, array $options = []): array
    {
        $apiKey = $this->configService->get('fonnte_api_key');

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

        // Tambahan opsi Fonnte jika ada (misal schedule, typing, delay)
        if (!empty($options['delay'])) {
            $payload['delay'] = (int) $options['delay'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])
            ->timeout(15)
            ->post(self::API_ENDPOINT, $payload);

            $result = $response->json();

            // Fonnte mengembalikan status: true / false di JSON
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
                'message' => 'Koneksi ke gateway Fonnte bermasalah: ' . $e->getMessage(),
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
            ->timeout(10)
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
                'message'   => 'Gagal menghubungi server Fonnte: ' . $e->getMessage(),
                'raw'       => null,
            ];
        }
    }
}
