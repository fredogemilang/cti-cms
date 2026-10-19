<?php

namespace Plugins\WhatsappVerifier\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Plugins\WhatsappVerifier\Services\WhatsappGatewayService;

class WhatsappOtpController extends Controller
{
    /**
     * Send OTP to visitor's WhatsApp number.
     */
    public function sendOtp(Request $request, WhatsappGatewayService $service): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string|max:100',
        ]);

        $rawPhone = (string) $request->input('phone');
        $normalizedPhone = $service->normalizePhone($rawPhone);

        if (! $service->isValidPhone($rawPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid Indonesian phone number (10 to 14 digits).',
            ], 422);
        }

        // Rate limit by IP & Phone: max 5 requests per 10 minutes
        $rateLimitKey = 'wa_otp_send:'.$request->ip().':'.$normalizedPhone;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return response()->json([
                'success' => false,
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        // Check cooldown (e.g. 60s)
        $cooldownKey = "wa_otp_cooldown:{$normalizedPhone}";
        if ($service->cache()->has($cooldownKey)) {
            $remaining = $service->cache()->get($cooldownKey) - time();
            if ($remaining > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$remaining} seconds before requesting a new code.",
                    'cooldown' => $remaining,
                ], 429);
            }
        }

        RateLimiter::hit($rateLimitKey, 600);

        $cooldownSeconds = (int) setting('wa_verifier_cooldown', 60);
        if ($cooldownSeconds < 10) {
            $cooldownSeconds = 60;
        }

        // Generate & store OTP
        $otp = $service->generateOtp();
        $service->storeOtp($normalizedPhone, $otp);

        // Set cooldown timestamp
        $service->cache()->put($cooldownKey, time() + $cooldownSeconds, now()->addSeconds($cooldownSeconds));

        // Send via WhatsApp
        $name = $request->input('name') ?: 'Customer';
        $sendResult = $service->sendOtp($normalizedPhone, $otp, $name);

        if (! $sendResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $sendResult['message'] ?? 'Failed to send OTP message.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to WhatsApp.',
            'phone' => $normalizedPhone,
            'cooldown' => $cooldownSeconds,
        ]);
    }

    /**
     * Verify input OTP code.
     */
    public function verifyOtp(Request $request, WhatsappGatewayService $service): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $rawPhone = (string) $request->input('phone');
        $normalizedPhone = $service->normalizePhone($rawPhone);
        $inputOtp = (string) $request->input('otp');

        // Check attempts: max 5 verification attempts per phone in 10 minutes
        $verifyLimitKey = 'wa_otp_verify_attempt:'.$normalizedPhone;
        if (RateLimiter::tooManyAttempts($verifyLimitKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many failed verification attempts. Please request a new code.',
            ], 429);
        }

        $result = $service->verifyOtp($normalizedPhone, $inputOtp);

        if (! $result['success']) {
            RateLimiter::hit($verifyLimitKey, 600);

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        RateLimiter::clear($verifyLimitKey);

        return response()->json([
            'success' => true,
            'token' => $result['token'],
            'phone' => $normalizedPhone,
            'message' => 'Phone number verified successfully.',
        ]);
    }
}
