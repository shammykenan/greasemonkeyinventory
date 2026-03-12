<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/login_model.php';
require_once __DIR__ . '/../model/logs_model.php';

$error = '';
$success = '';

if (isset($_POST['login'])) {
    $username     = trim($_POST['username']);
    $password     = $_POST['password'];
    $ip           = $_SERVER['REMOTE_ADDR'];
    $max_attempts = 5;
    $lockout_mins = 3;

    // Force UTC so PHP and MySQL timestamps match
    $pdo->exec("SET time_zone = '+00:00'");

    // ── RATE LIMITER ─────────────────────────────────────────────────

    // Clean up attempts outside the lockout window
    $pdo->prepare("
        DELETE FROM login_attempts
        WHERE attempted_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$lockout_mins} MINUTE)
    ")->execute();

    // Count recent attempts for this username OR IP
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS attempt_count
        FROM login_attempts
        WHERE (username = ? OR ip = ?)
          AND attempted_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$lockout_mins} MINUTE)
    ");
    $stmt->execute([$username, $ip]);
    $attempt_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['attempt_count'];

    if ($attempt_count >= $max_attempts) {

        // Calculate exact seconds remaining in lockout
        $stmt = $pdo->prepare("
            SELECT TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(),
                DATE_ADD(MIN(attempted_at), INTERVAL {$lockout_mins} MINUTE)
            ) AS seconds_left
            FROM login_attempts
            WHERE (username = ? OR ip = ?)
        ");
        $stmt->execute([$username, $ip]);
        $secs_left = max(0, (int)$stmt->fetch(PDO::FETCH_ASSOC)['seconds_left']);
        $mins_left = ceil($secs_left / 60);

        $error = "Too many failed login attempts. Please wait {$mins_left} minute(s) and try again.";

    // ── END RATE LIMITER ─────────────────────────────────────────────

    } else {

        $user = login_user($pdo, $username, $password);

        if ($user) {
            // ✅ SUCCESS — clear attempts, start session
            $pdo->prepare("
                DELETE FROM login_attempts WHERE username = ? OR ip = ?
            ")->execute([$username, $ip]);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            add_activity_log($pdo, $_SESSION['user_id'], null, "Login Account");

            header("Location: index.php?page=dashboard");
            exit();

        } else {
            // ❌ FAILED — log the attempt
            $pdo->prepare("
                INSERT INTO login_attempts (username, ip, attempted_at)
                VALUES (?, ?, UTC_TIMESTAMP())
            ")->execute([$username, $ip]);

            // Warn user how many attempts are left
            $remaining = max(0, $max_attempts - ($attempt_count + 1));

            if ($remaining > 0) {
                $error = "Invalid username or password. {$remaining} attempt(s) remaining before lockout.";
            } else {
                $error = "Too many failed login attempts. Please wait {$lockout_mins} minutes and try again.";
            }
        }
    }
}

/*if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = (int) $_POST['role'];

    try {
        register_user($pdo, $username, $password, $role);
        $success = "Account created successfully! Please login.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}*/

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Account created successfully! Please login.";
}
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = "Invalid username or password";
}

require_once __DIR__ . '/../view/landing_page.php';