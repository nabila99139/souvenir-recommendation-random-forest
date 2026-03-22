<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
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
     * Handle user registration
     */
    public function register(Request $request)
    {
        Log::info('Registration attempt started', ['email' => $request->email]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:users,email',
            'password' => 'required|string|min:6|max:50|confirmed',
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
        ]);

        try {
            // Create new user with hashed password and admin privileges
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin', // Make all users admin
                'is_admin' => true,
                'cid' => null, // No company association (root admin)
                'sid' => null, // No site association (root admin)
                'authorized_by' => null, // System authorized
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'timestamp' => now()
            ]);

            return redirect()->route('auth.login')->with('success', 'Registration successful! Please login with your credentials.');
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email,
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

        // Store user info in session for verification
        Session::put('login_user_id', $user->id);

        // Generate and store OTP for the user's email
        $code = $this->otpService->generateOtp();
        $this->otpService->storeOtp($request->email, $code);

        // Send OTP to the user's email address asynchronously via queue
        $emailSent = $this->otpService->sendOtp($request->email, $code);

        // Store email in session for verification
        Session::put('login_email', $request->email);

        // Provide immediate feedback about email dispatch
        $message = $emailSent
            ? 'OTP code has been dispatched to your email! Please check your inbox and spam folder.'
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
        $email = Session::get('login_email');

        if (!$email) {
            return redirect()->route('auth.login');
        }

        return view('auth.verify', compact('email'));
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
    {
        Log::info('OTP verification attempt', ['code' => $request->code]);

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = Session::get('login_email');

        if (!$email) {
            Log::warning('OTP verification failed - session expired', ['code' => $request->code]);
            return back()->withErrors([
                'code' => 'Session expired. Please login again.',
            ]);
        }

        if ($this->otpService->verifyOtp($email, $request->code)) {
            // Login successful - authenticate the user
            $userId = Session::get('login_user_id');
            $user = User::find($userId);

            Session::forget('login_email');
            Session::forget('login_user_id');

            // Store authenticated user info including admin status
            Session::put('authenticated', true);
            Session::put('user_id', $userId);
            Session::put('user_email', $email);
            Session::put('user_name', $user->name);
            Session::put('user_role', $user->role);
            Session::put('is_admin', $user->is_admin);
            Session::put('is_root_admin', $user->isRootAdmin());

            Log::info('User successfully authenticated via OTP', [
                'user_id' => $userId,
                'email' => $email,
                'name' => $user->name,
                'timestamp' => now()
            ]);

            return redirect()->route('home')->with('success', 'Welcome back! You can now explore our souvenir recommendations.');
        }

        Log::warning('OTP verification failed - invalid or expired code', [
            'email' => $email,
            'code' => $request->code,
            'timestamp' => now()
        ]);

        return back()->withErrors([
            'code' => 'Invalid or expired OTP code.',
        ]);
    }

    /**
     * Resend OTP code
     */
    public function resend(Request $request)
    {
        Log::info('OTP resend request', ['email' => $request->email]);

        // Rate limiting: Check if user recently requested OTP (60 seconds)
        $lastResend = Session::get('last_otp_resend');
        $canResend = !$lastResend || (now()->diffInSeconds($lastResend) >= 60);

        if (!$canResend) {
            $remainingTime = 60 - now()->diffInSeconds($lastResend);
            Log::warning('OTP resend blocked by rate limit', [
                'email' => $request->email,
                'remaining_time' => $remainingTime,
                'last_resend' => $lastResend
            ]);
            return back()->withErrors([
                'email' => "Please wait {$remainingTime} seconds before requesting another OTP.",
            ]);
        }

        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email not found in our system.',
        ]);

        Log::info('OTP resend request validated', ['email' => $request->email]);

        // Generate and store new OTP
        $code = $this->otpService->generateOtp();
        $this->otpService->storeOtp($request->email, $code);

        // Send OTP to queue
        $emailSent = $this->otpService->sendOtp($request->email, $code);

        // Update session
        Session::put('login_email', $request->email);
        Session::put('last_otp_resend', now()->toDateTimeString());

        $message = $emailSent
            ? 'New OTP code has been dispatched to your email! Please check your inbox.'
            : 'We could not send OTP email. Please try again later.';

        Log::info('OTP resend completed', [
            'email' => $request->email,
            'email_sent' => $emailSent,
            'timestamp' => now()
        ]);

        return back()->with('success', $message);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $userId = Session::get('user_id');
        $email = Session::get('user_email');

        Log::info('User logout', [
            'user_id' => $userId,
            'email' => $email,
            'timestamp' => now()
        ]);

        Session::forget('authenticated');
        Session::forget('user_id');
        Session::forget('user_email');
        Session::forget('user_name');
        Session::forget('user_role');
        Session::forget('is_admin');
        Session::forget('is_root_admin');

        return redirect()->route('auth.login')->with('success', 'Logged out successfully!');
    }
}