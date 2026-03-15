<?php
date_default_timezone_set('Asia/Manila'); // UTC+8
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/logs_model.php';

// PHPMailer includes
require __DIR__ . '/../../src/PHPMailer.php';
require __DIR__ . '/../../src/SMTP.php';
require __DIR__ . '/../../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ----------------------------
// HANDLE FORGOT PASSWORD
// ----------------------------
if (isset($_POST['email'])) {
	
    $email = trim($_POST['email']);

    // RATE LIMITER: max 3 requests per 15 mins
    $limit = 3;
    $window_mins = 15;

    $pdo->exec("SET time_zone = '+00:00'");
    $pdo->prepare("DELETE FROM password_resets WHERE expires_at < UTC_TIMESTAMP()")->execute();

    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS request_count
        FROM password_resets pr
        JOIN users u ON u.id = pr.user_id
        WHERE u.email = ?
          AND pr.created_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$window_mins} MINUTE)
    ");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ((int)$row['request_count'] >= $limit) {
        $_SESSION['reset_message'] = "Too many reset attempts. Please wait {$window_mins} minutes.";
        header("Location: index.php?page=reset_notice");
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
	$ADMIN_EMAIL = 'dinshammykenan012@gmail.com';
    if ($user && $email === $ADMIN_EMAIL) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, token, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user['id'], $token, $expires]);

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? ($_ENV['APP_HOST'] ?? 'grease-monkey.ct.ws');
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $projectFolder = $scriptName ? dirname($scriptName) : '';
    if ($projectFolder === '/' || $projectFolder === '\\') $projectFolder = '';

    $reset_link = "{$protocol}://{$host}{$projectFolder}/reset_password.php?token={$token}";
    sendResetEmail($email, $reset_link);

    $_SESSION['reset_message'] = "A reset link has been sent to the administrator email.";
} else {
    $_SESSION['reset_message'] = "Only the official admin Gmail account is allowed to request a reset link.";
}

header("Location: index.php?page=reset_notice");
exit;
}

// ----------------------------
// SEND EMAIL FUNCTION
// ----------------------------
function sendResetEmail($to, $link) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPDebug = 0; // 0 = off, 2 = show errors
        $mail->Debugoutput = 'error_log';

        $mail->Host = 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'a0c11f001@smtp-brevo.com';
        $mail->Password = 'pI3PBAKdFrhjgZNT';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('dinshammykenan012@gmail.com', 'Grease Monkey');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body = "
            <p>You requested a password reset.</p>
            <p><a href='{$link}'>Click here to reset your password</a></p>
            <p><small>This link expires in 10 minutes.</small></p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log('Mail Error: ' . $mail->ErrorInfo);
    }
}
//'svft xfom nwea tkhk';