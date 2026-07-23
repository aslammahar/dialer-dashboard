<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeviceIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = $this->allowedDeviceIps();

        if ($allowedIps === []) {
            Log::error('HikVision allowed_device_ips is empty. Check ALLOWED_FINGER_DEVICE_IPS in .env and run php artisan config:clear');
        }

        if (! in_array($request->ip(), $allowedIps, true)) {
            Log::info('HikVision device IP rejected', [
                'ip' => $request->ip(),
                'allowed_count' => count($allowedIps),
            ]);

            abort(403, 'Unauthorized IP: ' . $request->ip());
        }

        return $next($request);
    }

    /**
     * @return list<string>
     */
    private function allowedDeviceIps(): array
    {
        $ips = config('hikvision.allowed_device_ips', []);

        if ($ips !== []) {
            return $ips;
        }

        // Works when config is not cached (local dev). When config is cached with empty
        // values, run: php artisan config:clear && php artisan config:cache
        if (! app()->configurationIsCached()) {
            return array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env('ALLOWED_FINGER_DEVICE_IPS', ''))
            )));
        }

        return [];
    }
}
