<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grease Monkey</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #fbbf24;
            --secondary: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #000000;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 1;
        }

        .orb-right {
            top: -20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.08) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        .orb-left {
            bottom: -20%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.08) 0%, transparent 70%);
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
        }

        /* Logo Header */
        .logo-header {
            position: fixed;
            top: 2rem;
            left: 2rem;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container {
            animation: logoFloat 3s ease-in-out infinite;
            filter: drop-shadow(0 0 30px rgba(251, 191, 36, 0.6));
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .brand-text h1 {
            font-size: 1.75rem;
            font-weight: 900;
            color: #fbbf24;
            text-shadow: 0 0 30px rgba(251, 191, 36, 0.6), 0 0 15px rgba(251, 191, 36, 0.4);
            animation: glow 2s ease-in-out infinite;
            margin: 0;
            line-height: 1;
        }

        @keyframes glow {
            0%, 100% { text-shadow: 0 0 30px rgba(251, 191, 36, 0.6), 0 0 15px rgba(251, 191, 36, 0.4); }
            50% { text-shadow: 0 0 50px rgba(251, 191, 36, 0.8), 0 0 25px rgba(251, 191, 36, 0.6); }
        }

        .brand-text p {
            color: #fbbf24;
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0.8;
            margin: 0;
            line-height: 1;
        }

        /* Modal Container */
        .modal-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        .auth-modal {
            max-width: 500px;
            width: 100%;
            background: rgba(15, 15, 15, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 2px solid #fbbf24;
            overflow: hidden;
            box-shadow: 0 0 60px rgba(251, 191, 36, 0.4), 0 25px 50px rgba(0, 0, 0, 0.8);
            animation: modalSlideIn 0.5s ease-out;
        }

        @keyframes modalSlideIn {
            from { 
                opacity: 0; 
                transform: translateY(-20px) scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        .modal-header-custom {
            padding: 2rem 2.5rem 1rem 2.5rem;
            border-bottom: 1px solid rgba(251, 191, 36, 0.2);
            text-align: center;
        }

        .modal-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fbbf24;
            margin-bottom: 0.5rem;
        }

        .modal-subtitle {
            font-size: 0.875rem;
            color: rgba(251, 191, 36, 0.7);
            font-weight: 500;
        }

        .modal-body-custom {
            padding: 2rem 2.5rem;
        }

        .modal-content-wrapper {
            display: none;
        }

        .modal-content-wrapper.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-custom {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-custom.show {
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success-custom {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #86efac;
        }

        .form-label-custom {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .form-control-custom {
            width: 100%;
            background: rgba(25, 25, 25, 0.8);
            border: 2px solid rgba(71, 85, 105, 0.5);
            border-radius: 12px;
            padding: 0.875rem 3rem 0.875rem 3rem;
            font-size: 0.9375rem;
            color: #fbbf24;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .form-control-custom::placeholder {
            color: #64748b;
            font-weight: 400;
        }

        .form-control-custom:focus {
            outline: none;
            background: rgba(30, 30, 30, 0.9);
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2), 0 0 20px rgba(251, 191, 36, 0.3);
            color: #fff;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            opacity: 0.8;
            pointer-events: none;
            width: 1.5rem;
            height: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.125rem;
            opacity: 0.8;
            transition: opacity 0.2s ease;
            user-select: none;
            width: 1.5rem;
            height: 100%;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        .btn-custom {
            width: 100%;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border: none;
            color: #000;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(251, 191, 36, 0.5), 0 4px 20px rgba(251, 191, 36, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-custom:hover::before {
            left: 100%;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 50px rgba(251, 191, 36, 0.8), 0 8px 30px rgba(251, 191, 36, 0.5);
        }

        .btn-custom:active {
            transform: translateY(0);
        }

        .modal-footer-custom {
            padding: 1.5rem 2.5rem 2rem 2.5rem;
            text-align: center;
            border-top: 1px solid rgba(251, 191, 36, 0.2);
        }

        .switch-text {
            color: rgba(251, 191, 36, 0.7);
            font-size: 0.9375rem;
            font-weight: 500;
            margin: 0;
        }

        .switch-link {
            color: #fbbf24;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
            position: relative;
        }

        .switch-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            transition: width 0.3s ease;
        }

        .switch-link:hover {
            text-shadow: 0 0 20px rgba(251, 191, 36, 0.6);
        }

        .switch-link:hover::after {
            width: 100%;
        }

        .info-text {
            font-size: 0.8125rem;
            color: rgba(251, 191, 36, 0.6);
            margin-top: 0.75rem;
            text-align: center;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .logo-header {
                top: 1rem;
                left: 1rem;
            }

            .logo-container {
                width: 50px;
                height: 50px;
            }

            .brand-text h1 {
                font-size: 1.5rem;
            }

            .brand-text p {
                font-size: 0.7rem;
            }

            .modal-header-custom,
            .modal-body-custom,
            .modal-footer-custom {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .modal-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="orb orb-right"></div>
    <div class="orb orb-left"></div>

    <div class="logo-header">
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="Grease Monkey Logo">
        </div>
        <div class="brand-text">
            <h1>Grease Monkey</h1>
            <p>Inventory System</p>
        </div>
    </div>

    <div class="modal-container">
        <div class="auth-modal">

            <div id="login-modal" class="modal-content-wrapper active">
                <div class="modal-header-custom">
                    <h2 class="modal-title">🔐 Welcome Back</h2>
                    <p class="modal-subtitle">Sign in to your account</p>
                </div>
                <?php if (!empty($error)): ?>
                <div class="alert-custom alert-danger-custom">
                    <span style="font-size: 1.25rem;">⚠️</span>
                    <div><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>
                <div class="modal-body-custom">
                    <form id="login-form" method="POST" action="index.php?page=login">
                        <div class="input-wrapper">
                            <label for="login-username" class="form-label-custom">Username</label>
                            <span class="input-icon">👤</span>
                            <input type="text" name="username" id="login-username" class="form-control-custom" placeholder="Enter your username" required>
                        </div>

                        <div class="input-wrapper">
                            <label for="login-password" class="form-label-custom">Password</label>
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" id="login-password" class="form-control-custom" placeholder="Enter your password" required>
                            <span class="toggle-password" onclick="togglePassword('login-password', this)">👁️</span>
                        </div>

                        <button type="submit" name="login" class="btn-custom">
                            Login to Account
                        </button>
                    </form>
                </div>
                <div class="modal-footer-custom">
                    <p class="switch-text">
                        Forgot password? 
                        <a class="switch-link" onclick="switchToForgotPassword()">Reset password</a>
                    </p>
                </div>
            </div>


            <div id="forgot-password-modal" class="modal-content-wrapper">
                <div class="modal-header-custom">
                    <h2 class="modal-title">🔑 Reset Password</h2>
                    <p class="modal-subtitle">Enter your email to receive a reset link</p>
                </div>

                <div class="modal-body-custom">
                    <form id="forgot-password-form" method="POST" action="index.php?page=forgot_password">
                        <div class="input-wrapper">
                            <label for="reset-email" class="form-label-custom">Email Address</label>
                            <span class="input-icon">📧</span>
                            <input type="email" name="email" id="reset-email" class="form-control-custom" placeholder="Enter your email" required>
                        </div>

                        <button type="submit" class="btn-custom">
                            Send Reset Link
                        </button>

                        <p class="info-text">
                            We'll send you an email with instructions to reset your password
                        </p>
                    </form>
                </div>

                <div class="modal-footer-custom">
                    <p class="switch-text">
                        
                        <a class="switch-link" onclick="switchToLogin()">Go back to login?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconElement) {
            const passwordInput = document.getElementById(inputId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconElement.textContent = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                iconElement.textContent = '👁️';
            }
        }

        function switchToForgotPassword() {
            document.getElementById('login-modal').classList.remove('active');
            document.getElementById('forgot-password-modal').classList.add('active');
        }

        function switchToLogin() {
            document.getElementById('forgot-password-modal').classList.remove('active');
            document.getElementById('login-modal').classList.add('active');
        }

        // Input focus animations
        const inputs = document.querySelectorAll('.form-control-custom');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Auto-hide success alerts after 3 seconds
        setTimeout(() => {
            const successAlerts = document.querySelectorAll('.alert-success-custom');
            successAlerts.forEach(alert => {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 3000);
    </script>
    <script src="/assets/js/ws.js"></script>
</body>
</html>