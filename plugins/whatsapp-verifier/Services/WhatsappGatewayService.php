<?php

namespace Plugins\WhatsappVerifier\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappGatewayService
{
    /**
     * Default JWT Token from Helios Open API.
     */
    public const DEFAULT_JWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJfdHlwZSI6InNlY3JldF9rZXlfdG9rZW4iLCJfaWQiOjkyLCJfdXNlcm5hbWUiOiJhZG1pbmlzX2hpbm0iLCJfcm9sZV9pZCI6MSwiX3RlbmFudF9pZCI6NDcsImp0aSI6IjgwNjA0MjZjLWNkZTQtNDZlZi05MWY5LTllN2U0YzY0NzQ4MCIsImlhdCI6MTY5MDI2ODEwMH0.to5zmP33Ap2onWkMYMu8Gn2MgzQM2SFD7YQl_ib_okI';

    /**
     * Normalize Indonesian phone number to E.164 standard without '+' (e.g. 628123456789).
     */
    public function normalizePhone(string $phone): string
    {
        // Strip non-digits
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '628'.substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Validate Indonesian phone number format (typically 10 to 14 digits starting with 628).
     */
    public function isValidPhone(string $phone): bool
    {
        $normalized = $this->normalizePhone($phone);

        return (bool) preg_match('/^62[0-9]{8,12}$/', $normalized);
    }

    /**
     * Generate a 4-digit random OTP.
     */
    public function generateOtp(): string
    {
        return strval(mt_rand(1000, 9999));
    }

    /**
     * Get persistent cache store for WhatsApp OTP and tokens.
     */
    public function cache(): \Illuminate\Contracts\Cache\Repository
    {
        $default = config('cache.default');
        if ($default === 'array') {
            if (\Illuminate\Support\Facades\Schema::hasTable('cache')) {
                return Cache::store('database');
            }

            return Cache::store('file');
        }

        return Cache::store();
    }

    /**
     * Store OTP in cache with expiration.
     */
    public function storeOtp(string $normalizedPhone, string $otp): void
    {
        $expiryMinutes = (int) setting('wa_verifier_otp_expiry', 5);
        if ($expiryMinutes < 1) {
            $expiryMinutes = 5;
        }

        $this->cache()->put("wa_otp:{$normalizedPhone}", $otp, now()->addMinutes($expiryMinutes));
    }

    /**
     * Send OTP via Helios WhatsApp Gateway (or mock in simulation mode).
     *
     * @return array{success: bool, message?: string}
     */
    public function sendOtp(string $normalizedPhone, string $otp, string $name = 'Customer'): array
    {
        $isSimulation = (bool) setting('wa_verifier_simulation_mode', false);

        if ($isSimulation) {
            Log::info("[WhatsApp Verifier SIMULATION] OTP sent to {$normalizedPhone}: {$otp} (User: {$name})");

            return [
                'success' => true,
                'message' => 'OTP sent in simulation mode (check server log).',
            ];
        }

        $url = (string) setting('wa_verifier_gateway_url', 'https://kouventa-becore.helios.id/openApi/chat/sends');
        $token = (string) setting('wa_verifier_jwt_token', self::DEFAULT_JWT);
        $templateCode = (string) setting('wa_verifier_template_code', '1627');
        $otpMessage = (string) setting('wa_verifier_otp_message', 'Use the following OTP to complete your Sign Up procedures.');

        $payload = [
            'code' => $templateCode,
            'phone' => $normalizedPhone,
            'parameters' => [
                $name ?: 'Customer',
                $otpMessage,
                $otp,
            ],
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $token,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                return ['success' => true];
            }

            Log::error('Helios WhatsApp Gateway Error', [
                'phone' => $normalizedPhone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message: '.($response->json('message') ?? 'HTTP '.$response->status()),
            ];
        } catch (\Throwable $e) {
            Log::error('Helios WhatsApp Gateway Exception: '.$e->getMessage(), [
                'phone' => $normalizedPhone,
            ]);

            return [
                'success' => false,
                'message' => 'Connection to WhatsApp Gateway failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Verify input OTP against stored OTP.
     *
     * @return array{success: bool, token?: string, message?: string}
     */
    public function verifyOtp(string $normalizedPhone, string $inputOtp): array
    {
        $storedOtp = $this->cache()->get("wa_otp:{$normalizedPhone}");

        if (empty($storedOtp)) {
            return [
                'success' => false,
                'message' => 'Verification code expired or not found. Please request a new code.',
            ];
        }

        if (trim($inputOtp) !== trim($storedOtp)) {
            return [
                'success' => false,
                'message' => 'Invalid verification code. Please check and try again.',
            ];
        }

        // OTP matched, consume it
        $this->cache()->forget("wa_otp:{$normalizedPhone}");

        // Generate tamper-proof signed token valid for 45 minutes
        $token = hash_hmac('sha256', "wa_verified:{$normalizedPhone}:".config('app.key'), config('app.key'));
        $this->cache()->put("wa_verified_token:{$token}", $normalizedPhone, now()->addMinutes(45));

        if (session()->isStarted()) {
            session()->put("wa_verified_{$normalizedPhone}", $token);
        }

        return [
            'success' => true,
            'token' => $token,
            'message' => 'Phone number successfully verified.',
        ];
    }

    /**
     * Check if a given phone number has been verified.
     */
    public function isPhoneVerified(string $phone, ?string $token = null, $request = null): bool
    {
        $normalized = $this->normalizePhone($phone);
        if (empty($normalized)) {
            return false;
        }

        // 1. Check signed HMAC token
        if (! empty($token)) {
            $cachedPhone = $this->cache()->get("wa_verified_token:{$token}");
            if ($cachedPhone && $cachedPhone === $normalized) {
                return true;
            }
        }

        // 2. Check session if active
        if ($request && $request->hasSession()) {
            $sessionToken = $request->session()->get("wa_verified_{$normalized}");
            if (! empty($sessionToken)) {
                $cachedPhone = $this->cache()->get("wa_verified_token:{$sessionToken}");
                if ($cachedPhone && $cachedPhone === $normalized) {
                    return true;
                }
            }
        }

        return false;
    }
}
