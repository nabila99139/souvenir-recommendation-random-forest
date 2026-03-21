<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\Request;
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
     * Handle login request and send OTP
     */
    public function login(Request $request)
    {
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

        // Dummy password validation (for testing purposes)
        $dummyPassword = 'password';

        if ($request->password !== $dummyPassword) {
            return back()->withErrors([
                'password' => 'Invalid password. For testing, please use: password',
            ])->withInput();
        }

        // Generate and store OTP for the user's email
        $code = $this->otpService->generateOtp();
        $this->otpService->storeOtp($request->email, $code);

        // Send OTP to the user's email address
        $this->otpService->sendOtp($request->email, $code);

        // Store email in session for verification
        Session::put('login_email', $request->email);

        return redirect()->route('auth.verify')->with('success', 'OTP code has been sent to your email!');
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
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = Session::get('login_email');

        if (!$email) {
            return back()->withErrors([
                'code' => 'Session expired. Please login again.',
            ]);
        }

        if ($this->otpService->verifyOtp($email, $request->code)) {
            // Login successful
            Session::forget('login_email');
            Session::put('authenticated', true);
            Session::put('user_email', $email);

            return redirect()->route('home')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'code' => 'Invalid or expired OTP code.',
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        Session::forget('authenticated');
        Session::forget('user_email');

        return redirect()->route('auth.login')->with('success', 'Logged out successfully!');
    }
}