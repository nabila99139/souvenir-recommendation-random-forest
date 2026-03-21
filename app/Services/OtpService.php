<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Notifications\OtpNotification;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate a 6-digit random OTP code
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store OTP in database
     */
    public function storeOtp(string $email, string $code): void
    {
        OtpCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);
    }

    /**
     * Send OTP via Mail and Telegram
     */
    public function sendOtp(string $email, string $code): void
    {
        $otpCode = OtpCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otpCode) {
            $otpCode->notify(new OtpNotification($code));
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $email, string $code): bool
    {
        $otpCode = OtpCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        return $otpCode !== null;
    }
}