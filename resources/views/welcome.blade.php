<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="welcome-card">
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold text-primary">
                            <i class="fas fa-link me-3"></i>URL Shortener
                        </h1>
                        <p class="lead text-muted">Professional URL shortening service with advanced features</p>
                    </div>
                    
                    <div class="row mb-5">
                        <div class="col-md-4 text-center">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5>Secure & Private</h5>
                            <p class="text-muted">Role-based access control with advanced security features</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5>Advanced Analytics</h5>
                            <p class="text-muted">Track clicks and performance with detailed statistics</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Team Collaboration</h5>
                            <p class="text-muted">Multiple roles for different team members and permissions</p>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="d-grid gap-2 d-md-block">
                            @if(Route::has('login'))
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 me-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </a>
                                    @if(Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5">
                                            <i class="fas fa-user-plus me-2"></i>Register
                                        </a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-muted">
                                <strong>Demo Accounts:</strong><br>
                                SuperAdmin: superadmin@example.com<br>
                                Admin: admin@example.com<br>
                                Password for all: password123
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>