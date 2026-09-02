<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;

class ApiClientMiddleware
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
        $apiKey = $request->header('X-Api-Key');
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        if (!$apiKey) {
            return response()->json([
                'status' => false,
                'message' => 'Header X-Api-Key diperlukan.',
            ], 401);
        }

        $client = ApiClient::where('api_key', $apiKey)->first();
        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'API Key tidak valid atau tidak ditemukan.',
            ], 401);
        }

        if (!$client->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'API Client ini sedang nonaktif. Silakan hubungi administrator.',
            ], 403);
        }

        if (!$timestamp || !is_numeric($timestamp)) {
            return response()->json([
                'status' => false,
                'message' => 'Header X-Timestamp (Unix timestamp dalam detik) diperlukan dan harus berupa angka.',
            ], 401);
        }

        // Cek toleransi waktu (maksimal 5 menit / 300 detik) dari sekarang
        if (abs(time() - (int)$timestamp) > 300) {
            return response()->json([
                'status' => false,
                'message' => 'Request kedaluwarsa atau selisih waktu terlalu besar (maksimal toleransi 5 menit / 300 detik dari waktu server).',
                'server_timestamp' => time(),
            ], 401);
        }

        if (!$signature) {
            return response()->json([
                'status' => false,
                'message' => 'Header X-Signature diperlukan.',
            ], 401);
        }

        // Dua kemungkinan string to sign untuk kompatibilitas penuh:
        // 1. METHOD:PATH:TIMESTAMP (contoh: GET:api/client/v1/absensi:1784191830)
        // 2. API_KEY:TIMESTAMP (contoh: abcdef123456...:1784191830)
        $stringToSign = strtoupper($request->method()) . ':' . $request->path() . ':' . $timestamp;
        $stringToSignFallback = $apiKey . ':' . $timestamp;

        $expectedSignature = hash_hmac('sha256', $stringToSign, $client->secret_key);
        $expectedSignatureFallback = hash_hmac('sha256', $stringToSignFallback, $client->secret_key);

        if (!hash_equals($expectedSignature, $signature) && !hash_equals($expectedSignatureFallback, $signature)) {
            return response()->json([
                'status' => false,
                'message' => 'X-Signature tidak valid.',
                'hint' => 'Gunakan hash HMAC SHA256 dari string (METHOD:PATH:TIMESTAMP atau API_KEY:TIMESTAMP) menggunakan Secret Key Anda.',
            ], 401);
        }

        // Update terakhir digunakan
        $client->last_used_at = now();
        $client->save();

        $request->attributes->set('api_client', $client);

        return $next($request);
    }
}
