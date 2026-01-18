<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Visitor Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --pertamina-blue: #0033a0;
            --pertamina-red: #e4002b;
            --pertamina-green: #00a859;
            --pertamina-light-blue: #0066b3;
            --pertamina-dark-blue: #00205b;
            --pertamina-orange: #ff6b00;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        
        .login-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Left Panel - Info & Branding */
        .info-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--pertamina-dark-blue) 0%, var(--pertamina-blue) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .info-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%230066b3' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-circle {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-circle i {
            font-size: 3.5rem;
            color: white;
        }
        
        .system-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        
        .system-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
            margin-bottom: 30px;
        }
        
        .features-container {
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(5px);
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--pertamina-green) 0%, var(--pertamina-light-blue) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .feature-text h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .feature-text p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--pertamina-green);
            display: block;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .info-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            opacity: 0.7;
            text-align: center;
        }
        
        /* Right Panel - Login Form */
        .login-panel {
            flex: 0.8;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 60px;
            position: relative;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--pertamina-dark-blue);
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .role-badges {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .role-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .role-badge.admin {
            background: rgba(228, 0, 43, 0.1);
            color: var(--pertamina-red);
        }
        
        .role-badge.security {
            background: rgba(0, 51, 160, 0.1);
            color: var(--pertamina-blue);
        }
        
        .role-badge.internal {
            background: rgba(0, 168, 89, 0.1);
            color: var(--pertamina-green);
        }
        
        .login-card {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--pertamina-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 160, 0.15);
        }
        
        .input-group-text {
            background-color: white;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: var(--pertamina-blue);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--pertamina-blue);
        }
        
        .btn-pertamina {
            background: linear-gradient(135deg, var(--pertamina-blue) 0%, var(--pertamina-light-blue) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-pertamina:hover {
            background: linear-gradient(135deg, var(--pertamina-dark-blue) 0%, var(--pertamina-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 160, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 15px;
        }
        
        .alert-danger {
            background-color: rgba(228, 0, 43, 0.1);
            color: var(--pertamina-red);
            border-left: 4px solid var(--pertamina-red);
        }
        
        .demo-info {
            background: linear-gradient(135deg, rgba(0, 168, 89, 0.05) 0%, rgba(0, 102, 179, 0.05) 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 168, 89, 0.2);
        }
        
        .demo-info h6 {
            color: var(--pertamina-green);
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .demo-credentials {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            border: 1px solid #e9ecef;
        }
        
        .demo-credential {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .demo-credential:last-child {
            border-bottom: none;
        }
        
        .demo-credential .role {
            font-weight: 600;
        }
        
        .demo-credential .role.admin { color: var(--pertamina-red); }
        .demo-credential .role.security { color: var(--pertamina-blue); }
        .demo-credential .role.internal { color: var(--pertamina-green); }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .login-footer a {
            color: var(--pertamina-blue);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Pertamina Stripes */
        .pertamina-stripes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, 
                var(--pertamina-blue) 0%, 
                var(--pertamina-blue) 33%, 
                var(--pertamina-red) 33%, 
                var(--pertamina-red) 66%, 
                var(--pertamina-green) 66%, 
                var(--pertamina-green) 100%);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }
            
            .info-panel {
                padding: 30px;
            }
            
            .login-panel {
                padding: 30px;
            }
            
            .features-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .feature-item {
                margin-bottom: 0;
            }
        }
        
        @media (max-width: 768px) {
            .features-container {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .stat-item {
                flex: 1;
                min-width: 120px;
            }
        }
        
        @media (max-width: 576px) {
            .info-panel, .login-panel {
                padding: 20px;
            }
            
            .system-title {
                font-size: 1.8rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--pertamina-blue);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--pertamina-dark-blue);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel: Information & Branding -->
        <div class="info-panel">
            <!-- Pertamina Stripes -->
            <div class="pertamina-stripes"></div>
            
            <!-- Logo & Title -->
            <div class="logo-container fade-in">
                <div class="logo-circle">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="system-title">Visitor Management</h1>
                <div class="system-subtitle">PERTAMINA Integrated Access Control System</div>
            </div>
            
            <!-- Features List -->
            <div class="features-container fade-in" style="animation-delay: 0.2s;">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Digital ID Badge System</h5>
                        <p>Pelacakan dan manajemen badge real-time</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Appointment Management</h5>
                        <p>Penjadwalan kunjungan terintegrasi</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Enhanced Security</h5>
                        <p>Verifikasi multi-level untuk keamanan maksimal</p>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Right Panel: Login Form -->
        <div class="login-panel">
            <!-- Pertamina Stripes -->
            <div class="pertamina-stripes"></div>
            
            <!-- Login Header -->
            <div class="login-header fade-in">
                <h2 class="login-title">Login ke Sistem</h2>
                <p class="login-subtitle">Masukkan kredensial Anda untuk mengakses sistem</p>
                
                <div class="role-badges">
                    <span class="role-badge admin">Admin</span>
                    <span class="role-badge security">Security</span>
                    <span class="role-badge internal">Internal</span>
                </div>
            </div>
                    
            <!-- Login Form -->
            <div class="login-card fade-in" style="animation-delay: 0.4s;">
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif
                
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold mb-2">
                            <i class="fas fa-envelope me-2" style="color: var(--pertamina-blue);"></i>
                            Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="user@pertamina.com"
                                required 
                                autofocus
                            >
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Password Input -->
                    <div class="mb-4 position-relative">
                        <label for="password" class="form-label fw-bold mb-2">
                            <i class="fas fa-lock me-2" style="color: var(--pertamina-red);"></i>
                            Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="••••••••"
                                required
                            >
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: var(--pertamina-blue); font-size: 0.9rem;">
                                Lupa Password?
                            </a>
                        @endif
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-pertamina">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Masuk ke Sistem
                    </button>
                    
                    <!-- Quick Help -->
                    <div class="text-center mt-3" style="font-size: 0.85rem; color: #6c757d;">
                        <i class="fas fa-life-ring me-1"></i>
                        Butuh bantuan? Hubungi IT Support: <strong>000 000</strong>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = togglePassword.querySelector('i');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
            
            // Auto-focus on email field
            const emailField = document.getElementById('email');
            if (emailField && !emailField.value) {
                setTimeout(() => {
                    emailField.focus();
                }, 300);
            }
            
            // Form submission animation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-pertamina');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                btn.disabled = true;
                
                // Re-enable button after 5 seconds (just in case)
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 5000);
            });
            
            // Add animation to feature items on hover
            document.querySelectorAll('.feature-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
            
            // Animate stats counters
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const target = stat.textContent;
                if (!isNaN(target.replace('%', ''))) {
                    let count = 0;
                    const increment = target.replace('%', '') / 50;
                    const timer = setInterval(() => {
                        count += increment;
                        if (count >= target.replace('%', '')) {
                            stat.textContent = target;
                            clearInterval(timer);
                        } else {
                            stat.textContent = Math.floor(count) + (target.includes('%') ? '%' : '');
                        }
                    }, 30);
                }
            });
            
            // Add scroll effect to info panel
            const infoPanel = document.querySelector('.info-panel');
            window.addEventListener('scroll', function() {
                if (window.innerWidth > 992) {
                    const scrolled = window.pageYOffset;
                    infoPanel.style.transform = `translateY(${scrolled * 0.1}px)`;
                }
            });
        });
    </script>
</body>
</html>