<?php
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../app/model/logs_model.php';

// Only log if user_id exists
if (isset($_SESSION['user_id'])) {
    add_activity_log(
        $pdo,
        $_SESSION['user_id'],
        null,
        "Logout Account"
    );
}

// Clear session
session_unset();
session_destroy();

// Redirect to landing page
header("Location: index.php?page=landing_page");
exit();