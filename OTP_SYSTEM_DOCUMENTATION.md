# Email OTP Verification System - Complete Documentation

## Overview

This document describes a robust Email OTP (One-Time Password) Verification System for Laravel 11 using the cache driver instead of Redis. This system provides secure user registration and login flow with rate limiting and proper security measures.

## Architecture

```
┌─────────────┐
│  User       │
└──────┬──────┘
       │ Register/Login
       ▼
┌─────────────────────────┐
│  AuthController        │
│  - register()          │  Stores pending data in cache
│  - login()             │  Sends OTP via email
│  - verify()            │  Verifies OTP code
│  - resend()            │  Resends with rate limiting
└──────┬──────────────────┘
       │
       ▼
┌─────────────────────────┐
│  CacheOtpService       │
│  - generateOtp()       │  Generates 6-digit OTP
│  - storeOtp()          │  Stores in cache (2 min expiry)
│  - verifyOtp()         │  Verifies with 3 attempts logic
│  - canResendOtp()      │  Rate limiting checks
│  - storePendingUser()  │  Stores registration data
│  - removePendingUser() │ Cleans up after verification
└──────┬──────────────────┘
       │
       ▼
┌─────────────────────────┐
│  Laravel Cache         │
│  - otp:{email}         │  OTP code with expiry
│  - pending:{email}     │  Registration data
│  - rate_limit:{email}  │  Resend attempts count
│  - failed:{email}      │  Failed verification attempts
│  - last_resend:{email} │  Last resend timestamp
└─────────────────────────┘
```

## Security Features

### 1. OTP Expiry (2 Minutes)
- OTP codes expire after 2 minutes for optimal security
- Prevents replay attacks while giving users enough time

### 2. Resend Cooldown (60 Seconds)
- Users must wait 60 seconds before requesting another OTP
- Prevents email bombing attacks
- Configured via `CacheOtpService::RESEND_COOLDOWN_SECONDS`

### 3. Rate Limiting (Max 3 Resends/Hour)
- Maximum 3 resend attempts per hour per email
- Prevents abuse while allowing legitimate retries
- Configured via `CacheOtpService::MAX_RESENDS_PER_HOUR`

### 4. Failed Attempts Limit (3 Attempts)
- OTP is invalidated after 3 failed verification attempts
- Prevents brute force attacks
- Resets when new OTP is generated
- Configured via `CacheOtpService::MAX_FAILED_ATTEMPTS`

### 5. Pending User Storage
- Registration data stored in cache (not database)
- Keeps users table clean from fake/unverified emails
- Data expires after 10 minutes
- Moved to users table only after successful verification

## Configuration

All timing and security settings are configurable in `CacheOtpService`:

```php
// CacheOtpService constants
private const OTP_EXPIRY_MINUTES = 2;           // OTP expires in 2 minutes
private const RESEND_COOLDOWN_SECONDS = 60;      // Can resend after 60 seconds
private const MAX_RESENDS_PER_HOUR = 3;          // Max 3 resends per hour
private const MAX_FAILED_ATTEMPTS = 3;           // Max 3 failed attempts
private const PENDING_USER_EXPIRY_MINUTES = 10;  // Pending user expires in 10 minutes
```

## Cache Keys Structure

The system uses the following cache keys:

| Key Pattern | Purpose | Expiry |
|-------------|---------|--------|
| `otp:{email}` | Stores OTP code with expiry timestamp | 2 minutes |
| `pending_user:{email}` | Stores pending registration data | 10 minutes |
| `rate_limit:{email}` | Counts resend attempts | 1 hour |
| `rate_limit:{email}_data` | Stores rate limit metadata | 1 hour |
| `failed_attempts:{email}` | Counts failed verification attempts | 5 minutes |
| `last_resend:{email}` | Last resend timestamp | 60 seconds |

## Data Flow

### Registration Flow

1. **User submits registration form**
   ```
   POST /register
   - name, email, password, password_confirmation
   ```

2. **System stores pending data in cache**
   - User data (name, email, hashed password) stored as `pending_user:{email}`
   - No database record created yet
   - Prevents fake emails in users table

3. **OTP generated and stored**
   - 6-digit numeric code generated
   - Stored in cache as `otp:{email}` with 2-minute expiry

4. **Email sent with OTP**
   - Email dispatched with verification code
   - User receives and enters code

5. **User submits OTP**
   ```
   POST /verify
   - code: "123456"
   ```

6. **System verifies OTP**
   - Checks if code matches
   - Checks if expired
   - Tracks failed attempts (max 3)

7. **If valid:**
   - Data moved from cache to `users` table
   - User automatically logged in
   - Pending data removed from cache
   - OTP invalidated

8. **If invalid:**
   - User shown error message
   - Failed attempts incremented
   - After 3 attempts: OTP invalidated

### Login Flow

1. **User submits login form**
   ```
   POST /login
   - email, password
   ```

2. **System validates credentials**
   - Checks if user exists in database
   - Verifies password hash

3. **OTP generated and stored**
   - 6-digit code generated
   - Stored in cache as `otp:{email}`

4. **Email sent with OTP**

5. **User submits OTP**
   - Verified against cached code
   - On success: User authenticated and session created

### Resend Flow

1. **User requests resend**
   ```
   POST /resend
   - email
   ```

2. **System checks rate limits**
   - Checks 60-second cooldown
   - Checks hourly limit (max 3)

3. **If allowed:**
   - New OTP generated
   - Old OTP invalidated
   - Email sent
   - Rate limit counter incremented

4. **If blocked:**
   - User shown appropriate error message
   - Time remaining displayed

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/register` | Show registration form |
| POST | `/register` | Submit registration (sends OTP) |
| GET | `/login` | Show login form |
| POST | `/login` | Submit login (sends OTP) |
| GET | `/verify` | Show OTP verification form |
| POST | `/verify` | Verify OTP code |
| POST | `/resend` | Resend OTP code |
| POST | `/logout` | Logout user |

## Testing the System

### Clear Rate Limits (Development Only)

To clear rate limit data for testing:

```php
// In controller (remove in production)
public function clearRateLimit(Request $request)
{
    $email = $request->email;
    $this->otpService->clearRateLimitData($email);
    return back()->with('success', 'Rate limit data cleared');
}
```

### Testing Scenarios

1. **Normal Registration**
   - Fill form → Receive OTP → Enter code → Create account

2. **Expired OTP**
   - Wait 2+ minutes → Try to verify → See error

3. **Wrong Code**
   - Enter wrong code 3 times → OTP invalidated → Must request new

4. **Resend Rate Limiting**
   - Resend immediately → See 60-second wait
   - Resend 4 times in hour → See hourly limit error

5. **Pending User Expiry**
   - Start registration → Wait 10+ minutes → Data expired

## Security Best Practices

### 1. OTP Generation
```php
// 6-digit numeric OTP
$code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
```

### 2. OTP Invalidation Strategy
- **Immediate deletion after successful verification** ✓
- **After 3 failed attempts** (not immediate) ✓
- Rationale: Typos happen, but brute force must be prevented

### 3. Rate Limiting
- **60-second cooldown**: Prevents spam
- **3 resends/hour**: Prevents email bombing
- **3 failed attempts**: Prevents brute force

### 4. Data Persistence
- **Cache only**: No database entries for unverified users
- **Automatic cleanup**: Cache expiry handles cleanup
- **Move on verification**: Only valid users reach database

### 5. Session Management
- Verification email stored in session
- User ID stored in session (login flow)
- All verification data cleared after completion

## Troubleshooting

### "Please wait X seconds before requesting another OTP"
- You've recently requested an OTP
- Wait for the cooldown period (60 seconds)

### "You have exceeded the maximum resends per hour"
- You've requested too many OTPs
- Wait for the hourly limit to reset

### "Too many failed attempts. Please request a new OTP code"
- You've entered wrong OTP 3 times
- Request a new code

### "Invalid or expired OTP code"
- OTP has expired (2 minutes)
- Request a new code

### "Registration data expired. Please try again."
- Pending data expired (10 minutes)
- Start registration process again

## Migration from Old System

If migrating from the old database-based system:

1. Remove `OtpCode` model and migration (if not needed elsewhere)
2. Update `AuthController` to use `CacheOtpService`
3. Update `OtpMail` to handle different user formats
4. Update views to use new verification flow
5. Remove any Redis-specific configurations
6. Configure cache driver in `.env`:

```env
CACHE_DRIVER=file  # or array, database, etc.
```

## Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure mail driver (SMTP, SendGrid, etc.)
- [ ] Remove `clearRateLimit()` method from controller
- [ ] Set appropriate cache driver (Redis recommended for production)
- [ ] Monitor cache usage and clear old keys if needed
- [ ] Test all security features
- [ ] Review and adjust timing constants if needed
- [ ] Set up logging and monitoring

## Future Enhancements

Potential improvements for future versions:

1. **SMS OTP**: Add support for SMS verification
2. **TOTP**: Support for authenticator apps
3. **Biometric**: Fingerprint/Face ID integration
4. **Multi-factor**: Combine OTP with other factors
5. **Analytics**: Track OTP success/failure rates
6. **Admin Dashboard**: View OTP usage statistics

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review cache: `php artisan cache:clear` to reset
3. Test email: Use mailpit or mailhog for local testing

---

**Version**: 1.0.0
**Last Updated**: 2025-03-26
**Laravel Version**: 11.x
**PHP Version**: 8.2+