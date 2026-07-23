<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check Authorization header (Bearer token format)
            $authHeader = $request->header('Authorization');
            $apiKey = null;
            
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                // Extract token from "Bearer {token}"
                $apiKey = substr($authHeader, 7);
            } else {
                // Fallback to X-API-Key header or query parameter for backward compatibility
                $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
            }
            
            $validApiKey = env('RECORDING_API_KEY', 'your-secret-api-key-here');

            if (!$apiKey || $apiKey !== $validApiKey) {
                Log::warning('API key validation failed', [
                    'has_api_key' => !empty($apiKey),
                    'api_key_length' => $apiKey ? strlen($apiKey) : 0,
                    'valid_key_set' => !empty($validApiKey),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or missing API key'
                ], 401);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('VerifyApiKey middleware error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication error: ' . $e->getMessage()
            ], 500);
        }
    }
}
