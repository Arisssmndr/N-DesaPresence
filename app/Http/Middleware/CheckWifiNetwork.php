<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AbsensiSignatureService;
use Symfony\Component\HttpFoundation\Response;

class CheckWifiNetwork
{
    public function __construct(private AbsensiSignatureService $wifiService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $this->wifiService->resolveClientIp($request);

        if (!$this->wifiService->validasiIpWifi($clientIp)) {
            // Jika request AJAX/API, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Akses ditolak. Absensi hanya dapat dilakukan dari jaringan WiFi Desa Nangtang.',
                    'ip'      => $clientIp,
                ], 403);
            }

            // Jika request browser biasa, tampilkan halaman error khusus
            return response()->view('errors.wifi-blocked', [
                'clientIp' => $clientIp,
            ], 403);
        }

        return $next($request);
    }
}
