<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Robust OTP Service using Laravel Cache
 *
 * Features:
 * - 2-minute OTP expiry
 * - 60-second resend cooldown
 * - Rate limiting (max 3 resends per hour)
 * - 3 failed attempts before invalidation
 * - Pending user storage in cache
 */
class CacheOtpService
{
    // Cache keys
    private const OTP_KEY_PREFIX = 'otp:';
    private const PENDING_USER_PREFIX = 'pending_user:';
    private const RATE_LIMIT_PREFIX = 'rate_limit:';
    private const FAILED_ATTEMPTS_PREFIX = 'failed_attempts:';
    private const LAST_RESEND_PREFIX = 'last_resend:';

    // Configuration
    private const OTP_EXPIRY_MINUTES = 2;           // OTP expires in 2 minutes
    private const RESEND_COOLDOWN_SECONDS = 60;      // Can resend after 60 seconds
    private const MAX_RESENDS_PER_HOUR = 3;          // Max 3 resends per hour
    private const MAX_FAILED_ATTEMPTS = 3;           // Max 3 failed attempts
    private const PENDING_USER_EXPIRY_MINUTES = 10;  // Pending user expires in 10 minutes

    /**
     * Generate a 6-digit numeric OTP code
     */
    public function generateOtp(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::info('OTP generated', ['code' => $code, 'timestamp' => now()]);
        return $code;
    }

    /**
     * Store OTP in cache with expiry
     */
    public function storeOtp(string $email, string $code): bool
    {
        $cacheKey = self::OTP_KEY_PREFIX . $email;
        $expiry = now()->addMinutes(self::OTP_EXPIRY_MINUTES);

        // Reset failed attempts when storing new OTP
        $this->resetFailedAttempts($email);

        Cache::put($cacheKey, [
            'code' => $code,
            'email' => $email,
            'created_at' => now(),
            'expires_at' => $expiry,
        ], self::OTP_EXPIRY_MINUTES * 60); // Convert to seconds

        Log::info('OTP stored in cache', [
            'email' => $email,
            'code' => $code,
            'expires_at' => $expiry->toDateTimeString(),
        ]);

        return true;
    }

    /**
     * Store pending user registration data in cache
     */
    public function storePendingUser(
        string $email,
        string $name,
        string $hashedPassword,
        string $otp,
        ?string $role = 'admin',
        ?bool $isAdmin = true
    ): bool {
        $cacheKey = self::PENDING_USER_PREFIX . $email;

        $pendingUserData = [
            'email' => $email,
            'name' => $name,
            'password' => $hashedPassword,
            'otp' => $otp,
            'role' => $role,
            'is_admin' => $isAdmin,
            'authorized_by' => null,
            'created_at' => now(),
        ];

        Cache::put($cacheKey, $pendingUserData, self::PENDING_USER_EXPIRY_MINUTES * 60);

        Log::info('Pending user stored in cache', [
            'email' => $email,
            'name' => $name,
            'role' => $role,
            'expires_at' => now()->addMinutes(self::PENDING_USER_EXPIRY_MINUTES)->toDateTimeString(),
        ]);

        return true;
    }

    /**
     * Get pending user data from cache
     */
    public function getPendingUser(string $email): ?array
    {
        $cacheKey = self::PENDING_USER_PREFIX . $email;
        $pendingUser = Cache::get($cacheKey);

        if ($pendingUser) {
            Log::info('Pending user retrieved from cache', ['email' => $email]);
        } else {
            Log::warning('Pending user not found in cache', ['email' => $email]);
        }

        return $pendingUser;
    }

    /**
     * Remove pending user from cache
     */
    public function removePendingUser(string $email): bool
    {
        $cacheKey = self::PENDING_USER_PREFIX . $email;
        Cache::forget($cacheKey);

        Log::info('Pending user removed from cache', ['email' => $email]);
        return true;
    }

    /**
     * Verify OTP code
     * Returns array with success status and message
     */
    public function verifyOtp(string $email, string $code): array
    {
        $cacheKey = self::OTP_KEY_PREFIX . $email;
        $otpData = Cache::get($cacheKey);

        // Check if OTP exists
        if (!$otpData) {
            Log::warning('OTP verification failed - code not found or expired', [
                'email' => $email,
                'code' => $code,
            ]);
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP code. Please request a new code.',
            ];
        }

        // Check if code matches
        if ($otpData['code'] !== $code) {
            // Increment failed attempts
            $this->incrementFailedAttempts($email);
            $failedAttempts = $this->getFailedAttempts($email);

            Log::warning('OTP verification failed - wrong code', [
                'email' => $email,
                'provided_code' => $code,
                'expected_code' => $otpData['code'],
                'failed_attempts' => $failedAttempts,
            ]);

            // Check if should invalidate OTP
            if ($failedAttempts >= self::MAX_FAILED_ATTEMPTS) {
                $this->invalidateOtp($email);
                return [
                    'success' => false,
                    'message' => 'Too many failed attempts. Please request a new OTP code.',
                ];
            }

            $remainingAttempts = self::MAX_FAILED_ATTEMPTS - $failedAttempts;
            return [
                'success' => false,
                'message' => "Invalid OTP code. {$remainingAttempts} attempt(s) remaining.",
            ];
        }

        // Check if expired
        if (now()->gt($otpData['expires_at'])) {
            Log::warning('OTP verification failed - code expired', [
                'email' => $email,
                'code' => $code,
                'expired_at' => $otpData['expires_at'],
            ]);
            $this->invalidateOtp($email);
            return [
                'success' => false,
                'message' => 'OTP code has expired. Please request a new code.',
            ];
        }

        // OTP is valid - invalidate it immediately after successful verification
        $this->invalidateOtp($email);
        $this->resetFailedAttempts($email);

        Log::info('OTP verified successfully', [
            'email' => $email,
            'code' => $code,
            'timestamp' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'OTP verified successfully.',
        ];
    }

    /**
     * Check if user can resend OTP (rate limiting)
     */
    public function canResendOtp(string $email, ?string $ip = null): array
    {
        // Check cooldown (60 seconds)
        $lastResendKey = self::LAST_RESEND_PREFIX . $email;
        $lastResend = Cache::get($lastResendKey);

        if ($lastResend) {
            $secondsSinceLastResend = now()->diffInSeconds($lastResend);
            $remainingCooldown = max(0, self::RESEND_COOLDOWN_SECONDS - $secondsSinceLastResend);

            if ($remainingCooldown > 0) {
                Log::info('OTP resend blocked by cooldown', [
                    'email' => $email,
                    'remaining_seconds' => $remainingCooldown,
                ]);
                return [
                    'can_resend' => false,
                    'message' => "Please wait {$remainingCooldown} seconds before requesting another OTP.",
                    'remaining_seconds' => $remainingCooldown,
                ];
            }
        }

        // Check hourly rate limit (max 3 per hour)
        $rateLimitKey = self::RATE_LIMIT_PREFIX . $email;
        $resendCount = Cache::get($rateLimitKey, 0);

        if ($resendCount >= self::MAX_RESENDS_PER_HOUR) {
            // Calculate time until reset (1 hour window)
            $rateLimitData = Cache::get($rateLimitKey . '_data');
            if ($rateLimitData) {
                $secondsUntilReset = now()->diffInSeconds($rateLimitData['reset_at']);
                $minutesUntilReset = ceil($secondsUntilReset / 60);

                Log::warning('OTP resend blocked by rate limit', [
                    'email' => $email,
                    'resend_count' => $resendCount,
                    'minutes_until_reset' => $minutesUntilReset,
                ]);

                return [
                    'can_resend' => false,
                    'message' => "You have exceeded the maximum resends per hour. Please try again in {$minutesUntilReset} minutes.",
                    'minutes_until_reset' => $minutesUntilReset,
                ];
            }
        }

        return [
            'can_resend' => true,
            'message' => 'OTP can be resent.',
        ];
    }

    /**
     * Record resend attempt
     */
    public function recordResendAttempt(string $email): void
    {
        // Update last resend time (for cooldown)
        $lastResendKey = self::LAST_RESEND_PREFIX . $email;
        Cache::put($lastResendKey, now(), self::RESEND_COOLDOWN_SECONDS);

        // Update rate limit counter
        $rateLimitKey = self::RATE_LIMIT_PREFIX . $email;
        $resendCount = Cache::get($rateLimitKey, 0);

        Cache::put($rateLimitKey, $resendCount + 1, 3600); // 1 hour expiry
        Cache::put($rateLimitKey . '_data', [
            'count' => $resendCount + 1,
            'reset_at' => now()->addHour(),
        ], 3600);

        Log::info('OTP resend attempt recorded', [
            'email' => $email,
            'total_resends' => $resendCount + 1,
        ]);
    }

    /**
     * Increment failed attempts
     */
    private function incrementFailedAttempts(string $email): void
    {
        $cacheKey = self::FAILED_ATTEMPTS_PREFIX . $email;
        $attempts = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $attempts + 1, 300); // Expire in 5 minutes
    }

    /**
     * Get failed attempts count
     */
    private function getFailedAttempts(string $email): int
    {
        $cacheKey = self::FAILED_ATTEMPTS_PREFIX . $email;
        return Cache::get($cacheKey, 0);
    }

    /**
     * Reset failed attempts
     */
    private function resetFailedAttempts(string $email): void
    {
        $cacheKey = self::FAILED_ATTEMPTS_PREFIX . $email;
        Cache::forget($cacheKey);
    }

    /**
     * Invalidate OTP (delete from cache)
     */
    private function invalidateOtp(string $email): void
    {
        $cacheKey = self::OTP_KEY_PREFIX . $email;
        Cache::forget($cacheKey);
        Log::info('OTP invalidated', ['email' => $email]);
    }

    /**
     * Clear all rate limit data for testing/dev purposes
     */
    public function clearRateLimitData(string $email): void
    {
        Cache::forget(self::LAST_RESEND_PREFIX . $email);
        Cache::forget(self::RATE_LIMIT_PREFIX . $email);
        Cache::forget(self::RATE_LIMIT_PREFIX . $email . '_data');
        Cache::forget(self::FAILED_ATTEMPTS_PREFIX . $email);

        Log::info('Rate limit data cleared', ['email' => $email]);
    }
}