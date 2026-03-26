# Quick Fix: Clear Rate Limits

## Your Current Issue

You're seeing: "Please wait 3754 seconds before requesting another OTP"

This means you have a pending rate limit from the old system. Here's how to fix it:

## Solution 1: Clear Cache (Recommended)

Run this command in your project root:

```bash
php artisan cache:clear
```

This will clear all cached rate limits and OTP data.

## Solution 2: Clear Specific Rate Limit

If you want to clear rate limits for a specific email only, you can:

1. **Add a temporary route** (in `routes/web.php`):
   ```php
   Route::post('/clear-rate-limit', [AuthController::class, 'clearRateLimit'])->name('clear.rate.limit');
   ```

2. **Visit the URL with your email**:
   ```
   POST /clear-rate-limit?email=your@email.com
   ```

3. **After testing, remove this route** from production

## Solution 3: Manual Cache Clearing

Create a simple PHP script:

```php
<?php
// File: clear_rate_limit.php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'your@email.com'; // Change this to your email

$cacheKeys = [
    'otp:' . $email,
    'pending_user:' . $email,
    'rate_limit:' . $email,
    'rate_limit:' . $email . '_data',
    'failed_attempts:' . $email,
    'last_resend:' . $email,
];

foreach ($cacheKeys as $key) {
    \Illuminate\Support\Facades\Cache::forget($key);
    echo "Cleared: $key\n";
}

echo "All rate limits cleared for: $email\n";
```

Run it:
```bash
php clear_rate_limit.php
```

## Prevention for Future Testing

### Development Mode

In your `.env` file, you can set:

```env
CACHE_DRIVER=array
```

The `array` cache driver doesn't persist between requests, which is great for development but **NOT** for production.

### Testing the New System

After clearing the cache, test the new system:

1. **Register flow**:
   - Visit `/register`
   - Fill in details
   - You'll receive an OTP (check your email/mailpit)
   - Enter the OTP within 2 minutes
   - Your account will be created

2. **Test rate limiting**:
   - Try to resend immediately → Should show 60-second wait
   - Wait 60 seconds → Can resend
   - Resend 3 times → Should show hourly limit

3. **Test wrong OTP**:
   - Enter wrong code 3 times → Should invalidate OTP
   - Request new code → Should work

## Testing Email Locally

For local development, I recommend using **Mailpit** or **Mailhog**:

### Install Mailpit (Recommended)

```bash
# Windows (using Chocolatey)
choco install mailpit

# Or download from https://github.com/axllent/mailpit/releases

# Run Mailpit
mailpit
```

Then update your `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Mailpit will be available at: `http://localhost:8025`

## Verify Your Cache Driver

Check your current cache driver in `.env`:

```bash
# Should be one of these (NOT redis for your requirement)
CACHE_DRIVER=file      # Files in storage/framework/cache
CACHE_DRIVER=array     # In-memory (dev only)
CACHE_DRIVER=database  # Store in database table
```

## Quick Test Command

To test if the system is working:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Test the registration flow
php artisan serve
```

Then visit: `http://localhost:8000/register`

---

## Summary

**To fix your immediate issue:**
```bash
php artisan cache:clear
```

**To prevent this in development:**
```env
CACHE_DRIVER=array
```

**To test email locally:**
Use Mailpit at `http://localhost:8025`

---

**Need more help?** Check the full documentation in `OTP_SYSTEM_DOCUMENTATION.md`