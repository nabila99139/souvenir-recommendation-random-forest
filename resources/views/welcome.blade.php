<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souvenir Recommendation System</title>
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
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .header {
            padding: 20px 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-buttons {
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: white;
            color: #667eea;
            border: 2px solid white;
        }
        .btn-primary:hover {
            background: transparent;
            color: white;
        }
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        .btn-secondary:hover {
            background: white;
            color: #667eea;
        }
        .hero {
            padding: 120px 0 60px;
            text-align: center;
            color: white;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .features {
            background: white;
            padding: 80px 0;
            margin-top: 40px;
        }
        .features-title {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 60px;
            color: #333;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .feature-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .feature-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        .how-it-works {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .how-it-works-title {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 60px;
            color: #333;
        }
        .steps-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 30px;
        }
        .step {
            text-align: center;
            flex: 1;
            min-width: 250px;
        }
        .step-number {
            width: 60px;
            height: 60px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
        .step h4 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }
        .step p {
            color: #666;
            line-height: 1.6;
        }
        .cta-section {
            padding: 80px 0;
            text-align: center;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .cta-section h2 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .cta-section p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 40px 0;
            text-align: center;
        }
        .footer p {
            opacity: 0.7;
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            .nav-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .steps-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="{{ route('welcome') }}" class="logo">
                <i class="bi bi-gift-fill"></i>
                Souvenir Recommendation
            </a>
            <div class="nav-buttons">
                <a href="{{ route('auth.login') }}" class="btn btn-secondary">Login</a>
                <a href="{{ route('auth.register') }}" class="btn btn-primary">Register</a>
            </div>

            <!-- Admin Access Link (Hidden, accessible directly) -->
            <a href="/admin" class="absolute -right-20 opacity-0 hover:opacity-0 transition" title="Admin Panel">
                <i class="fas fa-shield-alt"></i>
            </a>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Find Perfect Souvenirs<br>for Every Occasion</h1>
            <p>Our intelligent recommendation system helps you discover the perfect souvenirs based on your preferences, budget, and recipient. Let AI guide you to memorable gifts.</p>
            <div class="hero-buttons">
                <a href="{{ route('auth.register') }}" class="btn btn-primary">Get Started Free</a>
                <a href="{{ route('auth.login') }}" class="btn btn-secondary">Already Have an Account</a>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="features-title">Why Choose Our System?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h3>AI-Powered Recommendations</h3>
                    <p>Our advanced machine learning algorithm analyzes your preferences to suggest the most suitable souvenirs for any occasion.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h3>Budget-Friendly Options</h3>
                    <p>Specify your budget range and we'll find the perfect souvenirs that match your financial constraints without compromising quality.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3>Personalized Suggestions</h3>
                    <p>Whether for family, colleagues, or partners, get tailored recommendations based on who you're buying for.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-bookmark"></i>
                    </div>
                    <h3>Save Your Favorites</h3>
                    <p>Create a personalized collection of favorite souvenirs and easily access them whenever you need gift ideas.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-grid"></i>
                    </div>
                    <h3>Extensive Catalog</h3>
                    <p>Browse through a wide variety of souvenirs from different categories, styles, and price ranges.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h3>Instant Results</h3>
                    <p>Get personalized recommendations in seconds. No waiting, just quick and accurate suggestions for your needs.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <h2 class="how-it-works-title">How It Works</h2>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Create Account</h4>
                    <p>Sign up for free and access our powerful recommendation system.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Enter Preferences</h4>
                    <p>Tell us about your budget, age, status, and gift recipient.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Get Recommendations</h4>
                    <p>Receive personalized souvenir suggestions based on your inputs.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Choose Perfect Gift</h4>
                    <p>Select the perfect souvenir and make memorable gifts.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2>Ready to Find Perfect Souvenirs?</h2>
            <p>Join thousands of users who have discovered amazing gifts through our system.</p>
            <div class="hero-buttons">
                <a href="{{ route('auth.register') }}" class="btn btn-primary">Create Free Account</a>
                <a href="{{ route('auth.login') }}" class="btn btn-secondary">Login to Continue</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Souvenir Recommendation System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>