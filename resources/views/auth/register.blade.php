<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #5568d3;
        }
        .alert {
            padding: 12px;
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 5px;
            color: #c33;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .success {
            background: #efe;
            border-color: #cfc;
            color: #3c3;
        }
        .password-container {
            position: relative;
        }
        .password-container input {
            padding-right: 40px;
        }
        .eye-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            color: #666;
            transition: color 0.3s;
            display: inline-block;
        }
        .eye-toggle:hover {
            color: #667eea;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Register</h1>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="alert success">
                {{ session('success') }}
            </div>
        @endif

        <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 14px;">
            Create your account to access the system
        </p>

        <form method="POST" action="{{ route('auth.register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="John Doe"
                    value="{{ old('name') }}"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    required
                >
                <small style="color: #888; font-size: 12px;">Enter a valid email address (e.g., user@gmail.com)</small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >
                    <i class="bi bi-eye eye-toggle" id="togglePassword"></i>
                </div>
                <small style="color: #888; font-size: 12px;">Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <div class="password-container">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="••••••••"
                        required
                    >
                    <i class="bi bi-eye eye-toggle" id="togglePasswordConfirm"></i>
                </div>
                <small style="color: #888; font-size: 12px;">Must match password above</small>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <a href="{{ route('auth.login') }}" class="login-link">Already have an account? Login</a>
    </div>

    <script>
        // Password field toggle
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');

        toggleIcon.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'text') {
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        });

        // Password confirmation field toggle
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const toggleIconConfirm = document.getElementById('togglePasswordConfirm');

        toggleIconConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);

            if (type === 'text') {
                toggleIconConfirm.classList.remove('bi-eye');
                toggleIconConfirm.classList.add('bi-eye-slash');
            } else {
                toggleIconConfirm.classList.remove('bi-eye-slash');
                toggleIconConfirm.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>