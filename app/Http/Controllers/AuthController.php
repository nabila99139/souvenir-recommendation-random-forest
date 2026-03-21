<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        // Create new user with hashed password
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('auth.login')->with('success', 'Registration successful! Please login with your credentials.');
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

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->withInput();
        }

        // Verify password using hash
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Invalid password. Please try again.',
            ])->withInput();
        }

        // Store user info in session for verification
        Session::put('login_user_id', $user->id);

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
            // Login successful - authenticate the user
            $userId = Session::get('login_user_id');
            $user = User::find($userId);

            Session::forget('login_email');
            Session::forget('login_user_id');

            // Store authenticated user info
            Session::put('authenticated', true);
            Session::put('user_id', $userId);
            Session::put('user_email', $email);
            Session::put('user_name', $user->name);

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