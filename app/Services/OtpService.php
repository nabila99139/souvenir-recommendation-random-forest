<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate a 6-digit random OTP code
     */
    public function generateOtp(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::info('OTP generated', ['code' => $code, 'timestamp' => now()]);
        return $code;
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

        Log::info('OTP stored in database', [
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5)->toDateTimeString()
        ]);
    }

    /**
     * Send OTP via Queueable Mail
     *
     * @param string $email
     * @param string $code
     * @return bool Returns true if email was dispatched to queue
     */
    public function sendOtp(string $email, string $code): bool
    {
        Log::info('Attempting to send OTP', ['email' => $email, 'code' => $code]);

        $otpCode = OtpCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otpCode) {
            // Get user for email
            $user = User::where('email', $email)->first();

            if ($user) {
                try {
                    // Dispatch email to queue for async processing
                    Mail::to($user->email)->queue(new OtpMail($user, $code));

                    Log::info('OTP email dispatched to queue', [
                        'email' => $email,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'code' => $code,
                        'timestamp' => now()
                    ]);

                    return true;
                } catch (\Exception $e) {
                    Log::error('Failed to dispatch OTP email', [
                        'email' => $email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return false;
                }
            } else {
                Log::warning('User not found for OTP email', ['email' => $email]);
            }
        } else {
            Log::warning('OTP code not found or expired', ['email' => $email, 'code' => $code]);
        }

        return false;
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $email, string $code): bool
    {
        Log::info('Attempting to verify OTP', ['email' => $email, 'code' => $code]);

        $otpCode = OtpCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        $isValid = $otpCode !== null;

        Log::info('OTP verification result', [
            'email' => $email,
            'code' => $code,
            'is_valid' => $isValid,
            'timestamp' => now()
        ]);

        return $isValid;
    }
}