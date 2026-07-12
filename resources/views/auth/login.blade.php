<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Transaksi</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Google Font Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
        }

        .login-header {
            background-color: #0f172a;
            color: #fff;
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #0d9488;
        }

        .login-body {
            padding: 40px 35px;
        }

        .form-control-premium {
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.3s;
        }

        .form-control-premium:focus {
            background-color: #fff;
            border-color: #0d9488;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
        }

        .btn-premium {
            background-color: #0d9488;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s;
        }

        .btn-premium:hover {
            background-color: #0f766e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
            color: #fff;
        }

        /* Demo credentials box */
        .demo-box {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            margin-top: 25px;
            border: 1px dashed #cbd5e1;
        }

        .demo-user-badge {
            cursor: pointer;
            padding: 6px 12px;
            margin: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 30px;
            background-color: #cbd5e1;
            color: #1e293b;
            display: inline-block;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .demo-user-badge:hover {
            background-color: #0d9488;
            color: #fff;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-wallet2 fs-1 mb-2 d-block text-teal" style="color: #0d9488;"></i>
            <h4 class="mb-0 fw-bold">EXPENSE SYSTEM</h4>
            <p class="mb-0 text-muted small mt-1">Sistem Pengajuan Transaksi Pengeluaran</p>
        </div>
        
        <div class="login-body">
            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="alert alert-success border-0 mb-4 small" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-muted small">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="email" name="email" class="form-control form-control-premium border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold text-muted small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="password" name="password" class="form-control form-control-premium border-start-0 @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                        <label class="form-check-label text-muted small" for="remember_me">
                            Ingat Saya
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none text-teal small" style="color: #0d9488;">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-premium btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sistem
                    </button>
                </div>
            </form>

            <!-- Quick Login Demo Users -->
            <div class="demo-box">
                <div class="fw-bold small text-muted text-uppercase mb-2 text-center"><i class="bi bi-magic me-1"></i> Klik untuk Auto-Fill (Demo Account)</div>
                <div class="text-center">
                    <span class="demo-user-badge" data-email="staff@example.com" data-role="Staff">Staff</span>
                    <span class="demo-user-badge" data-email="supervisor@example.com" data-role="Supervisor">Supervisor</span>
                    <span class="demo-user-badge" data-email="manager@example.com" data-role="Manager">Manager</span>
                    <span class="demo-user-badge" data-email="director@example.com" data-role="Director">Director</span>
                    <span class="demo-user-badge" data-email="finance@example.com" data-role="Finance">Finance</span>
                </div>
                <div class="text-center mt-2 small text-muted" style="font-size: 0.7rem;">Password default: <strong>password</strong></div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-Fill Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const badges = document.querySelectorAll('.demo-user-badge');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            badges.forEach(badge => {
                badge.addEventListener('click', function () {
                    const email = this.getAttribute('data-email');
                    emailInput.value = email;
                    passwordInput.value = 'password';
                    
                    // Add subtle glow effect to form fields
                    emailInput.focus();
                    setTimeout(() => {
                        passwordInput.focus();
                    }, 200);
                });
            });
        });
    </script>
</body>
</html>
