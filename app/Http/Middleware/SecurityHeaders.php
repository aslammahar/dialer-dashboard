<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Only set headers if they're not already set elsewhere (Apache/Nginx/Cloudflare/etc).
        $this->setIfMissing($response, 'X-Content-Type-Options', 'nosniff');
        $this->setIfMissing($response, 'X-Frame-Options', 'SAMEORIGIN');
        $this->setIfMissing($response, 'Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->setIfMissing(
            $response,
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()'
        );

        // HSTS (only makes sense on HTTPS)
        if ($request->isSecure() && $this->envBool('SECURITY_HEADERS_ENABLE_HSTS', true)) {
            $maxAge = (int) env('SECURITY_HEADERS_HSTS_MAX_AGE', 31536000); // 1 year
            $includeSubDomains = $this->envBool('SECURITY_HEADERS_HSTS_INCLUDE_SUBDOMAINS', true);
            $preload = $this->envBool('SECURITY_HEADERS_HSTS_PRELOAD', false);

            $hsts = "max-age={$maxAge}";
            if ($includeSubDomains) {
                $hsts .= '; includeSubDomains';
            }
            if ($preload) {
                $hsts .= '; preload';
            }

            $this->setIfMissing($response, 'Strict-Transport-Security', $hsts);
        }

        // CSP is powerful but can break pages if too strict. Keep it off by default.
        if ($this->envBool('SECURITY_HEADERS_ENABLE_CSP', false)) {
            $reportOnly = $this->envBool('SECURITY_HEADERS_CSP_REPORT_ONLY', true);

            // NOTE: This CSP is intentionally permissive because the app uses inline scripts/CDNs.
            // Tighten this later once you inventory script/style sources.
            $csp = implode('; ', [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "img-src 'self' data: https:",
                "font-src 'self' data: https:",
                "style-src 'self' 'unsafe-inline' https:",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:",
                "media-src 'self' data: blob: https: http:",
                "connect-src 'self' https: http:",
            ]);

            $headerName = $reportOnly ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
            $this->setIfMissing($response, $headerName, $csp);
        }

        return $response;
    }

    private function setIfMissing(Response $response, string $header, string $value): void
    {
        if (!$response->headers->has($header)) {
            $response->headers->set($header, $value);
        }
    }

    private function envBool(string $key, bool $default = false): bool
    {
        $value = env($key);
        if ($value === null) {
            return $default;
        }
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}

