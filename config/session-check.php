<?php
// Only for protected pages
$page = $_GET['page'] ?? 'home';
$public_pages = ['landing_page', 'forgot_password', 'reset_password', 'reset_notice'];

if (!in_array($page, $public_pages)) {

    // Check if logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=landing_page");
        exit();
    }

    // Server-side timeout (15 min)
    $timeout_duration = 900; // seconds

    if (isset($_SESSION['LAST_ACTIVITY'])) {
        if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: index.php?page=logout");
            exit();
        }
    }

    $_SESSION['LAST_ACTIVITY'] = time();
}