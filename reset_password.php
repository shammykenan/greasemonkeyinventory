<?php
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/app/model/logs_model.php';

$error = '';
$success = '';

// Get token from POST (after form submit) or GET (from email link)
$token = $_POST['token'] ?? $_GET['token'] ?? '';
if (!$token) {
    die("Invalid reset link.");
}

// Verify token
$stmt = $pdo->prepare("SELECT user_id, expires_at, used FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT user_id
    FROM password_resets
    WHERE token = ?
      AND used = 0
      AND expires_at > UTC_TIMESTAMP()
");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    die("This reset link is invalid or has expired.");
}

// Handle form submission
if (isset($_POST['password'])) {
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            // Update user password
            $pdo->prepare("UPDATE users SET password = ?, password_updated_at = NOW()  WHERE id = ?")
                ->execute([$hash, $reset['user_id']]);

            // Mark token as used
            $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")
                ->execute([$token]);

            // Log activity
            add_activity_log($pdo, $reset['user_id'], null, "Reset password");

            $pdo->commit();
            $success = "Your password has been reset successfully.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Failed to reset password. Please try again.";
            error_log("Reset password error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Grease Monkey</title>
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
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .auth-modal {
            max-width: 440px;
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
            padding: 2.5rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(251, 191, 36, 0.2);
            text-align: center;
        }

        .modal-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fbbf24;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .modal-subtitle {
            font-size: 0.875rem;
            color: rgba(251, 191, 36, 0.7);
            font-weight: 500;
            line-height: 1.5;
        }

        .modal-body-custom {
            padding: 1.5rem 2rem;
        }

        .alert-custom {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { 
                opacity: 0; 
                transform: translateY(-10px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
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

        .alert-custom span:first-child {
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .alert-custom span:last-child {
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
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
            margin-bottom: 1rem;
        }

        .form-control-custom {
            width: 100%;
            background: rgba(25, 25, 25, 0.9);
            border: 2px solid rgba(71, 85, 105, 0.4);
            border-radius: 12px;
            padding: 0.875rem 3rem 0.875rem 3rem;
            font-size: 0.9375rem;
            color: #ffffff;
            transition: all 0.2s ease;
            font-weight: 500;
            height: 52px;
        }

        .form-control-custom::placeholder {
            color: rgba(100, 116, 139, 0.7);
            font-weight: 400;
        }

        .form-control-custom:focus {
            outline: none;
            background: rgba(30, 30, 30, 0.95);
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.15);
            color: #ffffff;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            opacity: 0.8;
            pointer-events: none;
            width: 1.5rem;
            height: 1.5rem;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.125rem;
            opacity: 0.8;
            transition: opacity 0.2s ease;
            user-select: none;
            width: 1.5rem;
            height: 1.5rem;
            background: none;
            border: none;
            padding: 0;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        .info-text {
            font-size: 0.8125rem;
            color: rgba(251, 191, 36, 0.7);
            margin: 0.5rem 0 1.5rem;
            text-align: center;
            line-height: 1.5;
            padding: 0 0.5rem;
        }

        .btn-custom {
            width: 100%;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border: none;
            color: #000;
            padding: 0.875rem;
            font-size: 0.9375rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-custom:hover::before {
            left: 100%;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);
        }

        .btn-custom:active {
            transform: translateY(0);
        }

        .modal-footer-custom {
            padding: 1.5rem 2rem 2rem;
            text-align: center;
            border-top: 1px solid rgba(251, 191, 36, 0.15);
        }

        .back-link {
            color: #fbbf24;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            padding: 0.5rem;
        }

        .back-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            transition: width 0.3s ease;
        }

        .back-link:hover {
            text-shadow: 0 0 15px rgba(251, 191, 36, 0.6);
            color: #fbbf24;
        }

        .back-link:hover::after {
            width: 100%;
        }

        .success-content {
            text-align: center;
            padding: 1rem 0;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: successPop 0.5s ease-out;
            display: inline-block;
        }

        @keyframes successPop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-message {
            color: #86efac;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .password-requirements {
            display: none;
        }

        @media (max-width: 768px) {
            .logo-header {
                top: 1.5rem;
                left: 1.5rem;
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

            .modal-container {
                padding: 1.5rem;
            }

            .auth-modal {
                max-width: 100%;
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

        @media (max-width: 480px) {
            .logo-header {
                top: 1rem;
                left: 1rem;
                gap: 0.75rem;
            }

            .logo-container {
                width: 45px;
                height: 45px;
            }

            .brand-text h1 {
                font-size: 1.25rem;
            }

            .modal-container {
                padding: 1rem;
            }

            .modal-header-custom {
                padding: 2rem 1.5rem 1.25rem;
            }

            .modal-body-custom {
                padding: 1.25rem 1.5rem;
            }

            .modal-footer-custom {
                padding: 1.25rem 1.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="orb orb-right"></div>
    <div class="orb orb-left"></div>

    <!-- Logo Header (Top Left) -->
    <div class="logo-header">
        <div class="logo-container">
            <img src="/inventory/assets/images/logo.png" alt="Grease Monkey Logo">
        </div>
        <div class="brand-text">
            <h1>Grease Monkey</h1>
            <p>Inventory System</p>
        </div>
    </div>

    <!-- Modal Container (Centered) -->
    <div class="modal-container">
        <div class="auth-modal">
            <div class="modal-header-custom">
                <h2 class="modal-title">
                    <span>🔑</span>
                    <span>Reset Your Password</span>
                </h2>
                <p class="modal-subtitle">Enter your new password below</p>
            </div>

            <div class="modal-body-custom">
                <?php if($error): ?>
                    <div class="alert-custom alert-danger-custom">
                        <span>⚠️</span>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="success-content">
                        <div class="success-icon">✅</div>
                        <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
                        <a href="index.php?page=landing_page" class="btn-custom">
                            Go to Login
                        </a>
                    </div>
                <?php else: ?>
                    <form method="post" id="resetForm">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="input-wrapper">
                            <label for="password" class="form-label-custom">New Password</label>
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" id="password" class="form-control-custom" placeholder="Enter your new password" required minlength="6">
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                👁️
                            </button>
                        </div>

                        <p class="info-text">
                            Password must be at least 6 characters long
                        </p>

                        <button type="submit" class="btn-custom">
                            Reset Password
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, buttonElement) {
            const passwordInput = document.getElementById(inputId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                buttonElement.innerHTML = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                buttonElement.innerHTML = '👁️';
            }
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

            // Real-time validation
            input.addEventListener('input', function() {
                if (this.id === 'password') {
                    const isValid = this.value.length >= 6;
                    this.style.borderColor = isValid ? 'rgba(16, 185, 129, 0.4)' : 'rgba(71, 85, 105, 0.4)';
                    
                    if (this.value.length > 0) {
                        this.style.boxShadow = isValid ? 
                            '0 0 0 3px rgba(16, 185, 129, 0.1)' : 
                            '0 0 0 3px rgba(239, 68, 68, 0.1)';
                    } else {
                        this.style.boxShadow = 'none';
                    }
                }
            });
        });

        // Form submission
        const form = document.getElementById('resetForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password');
                if (password.value.length < 6) {
                    e.preventDefault();
                    password.focus();
                    password.style.borderColor = 'rgba(239, 68, 68, 0.6)';
                    password.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                    
                    // Show error message
                    let errorAlert = document.querySelector('.alert-custom');
                    if (!errorAlert) {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert-custom alert-danger-custom';
                        alertDiv.innerHTML = `
                            <span>⚠️</span>
                            <span>Password must be at least 6 characters long</span>
                        `;
                        form.insertBefore(alertDiv, form.firstChild);
                    }
                }
            });
        }
    </script>
</body>
</html>