<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CacheOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;


class AuthController extends Controller
{
    protected CacheOtpService $otpService;

    public function __construct(CacheOtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration (stores pending user in cache, sends OTP)
     */
    public function register(Request $request)
    {
        Log::info('Registration attempt started', ['email' => $request->email, 'role' => $request->role]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:users,email',
            'password' => 'required|string|min:6|max:50|confirmed',
            'role' => 'required|in:customer,seller', // Only Customer or Seller allowed for public registration
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name must not exceed 255 characters',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email address must not exceed 255 characters',
            'email.regex' => 'Please enter a valid email format',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password must not exceed 50 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'role.required' => 'Please select an account type',
            'role.in' => 'Invalid account type selected',
        ]);

        try {
            // Generate OTP
            $otp = $this->otpService->generateOtp();

            // Determine admin status based on role
            $isAdmin = false;

            // Store pending user data in cache (not in database)
            $this->otpService->storePendingUser(
                email: $request->email,
                name: $request->name,
                hashedPassword: Hash::make($request->password),
                otp: $otp,
                role: $request->role,
                isAdmin: $isAdmin
            );

            // Store OTP in cache
            $this->otpService->storeOtp($request->email, $otp);

            // Send OTP email
            $emailSent = $this->sendOtpEmail($request->email, $request->name, $otp);

            // Store email in session for verification page
            session()->put('verification_email', $request->email);
            session()->put('verification_type', 'registration');
            session()->put('registration_role', $request->role);

            Log::info('Pending registration stored and OTP sent', [
                'email' => $request->email,
                'name' => $request->name,
                'role' => $request->role,
                'email_sent' => $emailSent,
                'timestamp' => now()
            ]);

            $message = $emailSent
                ? 'Verification code has been sent to your email! Please check your inbox and spam folder.'
                : 'We could not send the verification email. Please try again.';

            return redirect()->route('auth.verify')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email,
                'role' => $request->role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Handle login request and send OTP
     */
    public function login(Request $request)
    {
        Log::info('Login attempt started', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => 'required|string|min:6|max:50',
        ], [
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email address must not exceed 255 characters',
            'email.regex' => 'Please enter a valid email format',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password must not exceed 50 characters',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('Login failed - user not found', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->withInput();
        }

        // Verify password using hash
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Login failed - invalid password', ['email' => $request->email, 'user_id' => $user->id]);
            return back()->withErrors([
                'password' => 'Invalid password. Please try again.',
            ])->withInput();
        }

        Log::info('Login credentials verified', ['email' => $request->email, 'user_id' => $user->id]);

        // Store user ID in session for verification
        session()->put('login_user_id', $user->id);

        // Generate and store OTP for the user's email
        $otp = $this->otpService->generateOtp();
        $this->otpService->storeOtp($request->email, $otp);

        // Send OTP to the user's email
        $emailSent = $this->sendOtpEmail($request->email, $user->name, $otp);

        // Store email in session for verification
        session()->put('verification_email', $request->email);
        session()->put('verification_type', 'login');

        // Provide immediate feedback about email dispatch
        $message = $emailSent
            ? 'OTP code has been sent to your email! Please check your inbox and spam folder.'
            : 'We could not send the OTP email. Please try again.';

        Log::info('Login redirect to OTP verification', [
            'email' => $request->email,
            'email_sent' => $emailSent,
            'timestamp' => now()
        ]);

        return redirect()->route('auth.verify')->with('success', $message);
    }

    /**
     * Show OTP verification form
     */
    public function showVerify()
    {
        $email = session()->get('verification_email');
        $verificationType = session()->get('verification_type');

        if (!$email) {
            return redirect()->route('auth.login')->with('error', 'Session expired. Please try again.');
        }

        return view('auth.verify', compact('email', 'verificationType'));
    }

    /**
     * Verify OTP code and complete registration or login
     */
    public function verify(Request $request)
    {
        Log::info('OTP verification attempt', ['code' => $request->code]);

        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Verification code is required',
            'code.size' => 'Verification code must be 6 digits',
        ]);

        $email = session()->get('verification_email');
        $verificationType = session()->get('verification_type');

        if (!$email) {
            Log::warning('OTP verification failed - session expired', ['code' => $request->code]);
            return back()->withErrors([
                'code' => 'Session expired. Please start over.',
            ]);
        }

        // Verify OTP using cache service
        $verificationResult = $this->otpService->verifyOtp($email, $request->code);

        if (!$verificationResult['success']) {
            return back()->withErrors([
                'code' => $verificationResult['message'],
            ]);
        }

        // Based on verification type, complete the flow
        if ($verificationType === 'registration') {
            return $this->completeRegistration($email);
        } elseif ($verificationType === 'login') {
            return $this->completeLogin($email);
        }

        return back()->withErrors(['error' => 'Invalid verification flow.']);
    }

    /**
     * Complete registration by moving data from cache to users table
     */
    private function completeRegistration(string $email)
    {
        Log::info('Completing registration', ['email' => $email]);

        // Get pending user data from cache
        $pendingUser = $this->otpService->getPendingUser($email);

        if (!$pendingUser) {
            Log::error('Pending user data not found in cache', ['email' => $email]);
            return back()->withErrors(['error' => 'Registration data expired. Please try again.']);
        }

        try {
            // Create user in database
            $user = User::create([
                'name' => $pendingUser['name'],
                'email' => $pendingUser['email'],
                'password' => $pendingUser['password'], // Already hashed
                'role' => $pendingUser['role'],
                'is_admin' => $pendingUser['is_admin'],
                'authorized_by' => $pendingUser['authorized_by'],
            ]);

            // Remove pending user from cache
            $this->otpService->removePendingUser($email);

            // Clear verification session data
            session()->forget(['verification_email', 'verification_type']);

            // Auto-login the user
            $this->authenticateUser($user);

            Log::info('User registered successfully via OTP verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
                'timestamp' => now()
            ]);

            // Redirect to appropriate dashboard based on role
            $dashboardRoute = $user->getDashboardRoute();
            $welcomeMessage = match($user->role) {
                User::ROLE_CUSTOMER => 'Registration successful! Welcome to your customer dashboard.',
                User::ROLE_SELLER => 'Registration successful! Welcome to your seller dashboard.',
                User::ROLE_ROOT => 'Registration successful! Welcome, Admin.',
                default => 'Registration successful! Welcome to our system.',
            };

            return redirect()->route($dashboardRoute)->with('success', $welcomeMessage);
        } catch (\Exception $e) {
            Log::error('Failed to complete registration', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Complete login by authenticating the user
     */
    private function completeLogin(string $email)
    {
        Log::info('Completing login', ['email' => $email]);

        $userId = session()->get('login_user_id');
        $user = User::find($userId);

        if (!$user) {
            Log::error('User not found during login completion', ['user_id' => $userId]);
            return back()->withErrors(['error' => 'Session expired. Please try again.']);
        }

        // Clear verification session data
        session()->forget(['verification_email', 'verification_type', 'login_user_id']);

        // Authenticate the user
        $this->authenticateUser($user);

        Log::info('User successfully logged in via OTP verification', [
            'user_id' => $user->id,
            'email' => $email,
            'name' => $user->name,
            'role' => $user->role,
            'timestamp' => now()
        ]);

        // Redirect to appropriate dashboard based on role
        $dashboardRoute = $user->getDashboardRoute();
        $welcomeMessage = match($user->role) {
            User::ROLE_ROOT => 'Welcome back, Admin! Full system control is now at your fingertips.',
            User::ROLE_CUSTOMER => 'Welcome back! You can now explore our souvenir recommendations.',
            User::ROLE_SELLER => 'Welcome back! Manage your souvenir business and track customer views.',
            default => 'Welcome back! You can now explore our souvenir recommendations.',
        };
        //     User::ROLE_SELLER => 'Welcome back! Manage your souvenir business.',
        //     User::ROLE_ROOT => 'Welcome back, Admin! System is under your control.',
        //     default => 'Welcome back! You can now explore our system.',
        // };

        return redirect()->route($dashboardRoute)->with('success', $welcomeMessage);
    }

    /**
     * Authenticate user and set session data
     */
    private function authenticateUser(User $user): void
    {
        // Use Laravel's built-in Auth system
        Auth::login($user);

        // Additional session data for views
        session()->put('user_role', $user->role);
        session()->put('is_root', $user->isRoot());
        session()->put('is_customer', $user->isCustomer());
        session()->put('is_seller', $user->isSeller());
        session()->put('is_admin', $user->is_admin);
        session()->put('is_root_admin', $user->isRootAdmin());
    }

    /**
     * Resend OTP code
     */
    public function resend(Request $request)
    {
        Log::info('OTP resend request', ['email' => $request->email]);

        $email = $request->email;

        // Validate email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user exists (for login) or get pending user (for registration)
        $user = User::where('email', $email)->first();
        $pendingUser = $this->otpService->getPendingUser($email);

        if (!$user && !$pendingUser) {
            Log::warning('OTP resend failed - user/pending user not found', ['email' => $email]);
            return back()->withErrors([
                'email' => 'Email not found in our system. Please register or login with a valid email.',
            ]);
        }

        // Check rate limiting
        $rateLimitCheck = $this->otpService->canResendOtp($email, $request->ip());

        if (!$rateLimitCheck['can_resend']) {
            return back()->withErrors([
                'email' => $rateLimitCheck['message'],
            ]);
        }

        try {
            // Generate and store new OTP
            $otp = $this->otpService->generateOtp();
            $this->otpService->storeOtp($email, $otp);

            // Record resend attempt
            $this->otpService->recordResendAttempt($email);

            // Determine name for email
            $name = $user ? $user->name : ($pendingUser['name'] ?? 'User');

            // Send OTP email
            $emailSent = $this->sendOtpEmail($email, $name, $otp);

            // Update session
            session()->put('verification_email', $email);

            $message = $emailSent
                ? 'New OTP code has been sent to your email! Please check your inbox.'
                : 'We could not send OTP email. Please try again later.';

            Log::info('OTP resend completed', [
                'email' => $email,
                'email_sent' => $emailSent,
                'timestamp' => now()
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('OTP resend failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to resend OTP. Please try again.']);
        }
    }

    /**
     * Send OTP email
     */
    private function sendOtpEmail(string $email, string $name, string $otp): bool
    {
        try {
            Mail::to($email)->send(new OtpMail($name, $otp));

            Log::info('OTP email sent successfully', [
                'email' => $email,
                'name' => $name,
                'otp' => $otp,
                'timestamp' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $userId = Auth::id();
        $email = Auth::user() ? Auth::user()->email : null;

        Log::info('User logout', [
            'user_id' => $userId,
            'email' => $email,
            'timestamp' => now()
        ]);

        // Use Laravel's built-in Auth logout
        Auth::logout();

        return redirect()->route('auth.login')->with('success', 'Logged out successfully!');
    }

    /**
     * Clear rate limit data (for testing/dev purposes)
     * Remove this in production
     */
    public function clearRateLimit(Request $request)
    {
        $email = $request->email;

        if (!$email) {
            return back()->withErrors(['error' => 'Email is required']);
        }

        $this->otpService->clearRateLimitData($email);

        return back()->with('success', 'Rate limit data cleared for ' . $email);
    }
}