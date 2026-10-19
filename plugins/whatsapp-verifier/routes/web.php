<?php

use Illuminate\Support\Facades\Route;
use Plugins\WhatsappVerifier\Http\Controllers\WhatsappOtpController;

Route::middleware(['web'])->prefix('api/plugins/whatsapp-verifier')->group(function () {
    Route::post('/send-otp', [WhatsappOtpController::class, 'sendOtp'])->name('plugins.whatsapp-verifier.send-otp');
    Route::post('/verify-otp', [WhatsappOtpController::class, 'verifyOtp'])->name('plugins.whatsapp-verifier.verify-otp');
});
