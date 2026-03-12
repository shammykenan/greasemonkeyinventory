<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/manage_stocks_model.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=landing_page");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'staff';

$products = get_all_products($pdo);

if (isset($_POST['add_stock'])) {
    $id  = (int) $_POST['id'];
    $qty = (int) $_POST['qty'];

    if ($qty <= 0) {
        echo "Invalid quantity.";
        exit;
    }
    // Get current stock
$stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$max_stock = 1000000000; // INT max

if ($product['stock'] + $qty > $max_stock) {
    die("Cannot add stock. " . $max_stock . " is the maximum limit.");
}

try {
    add_stock($pdo, $id, $qty, $user_id);
    header("Location: index.php?page=manage_stocks&added_stock=1");
    exit;
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
}

// Server-side validation added
if (isset($_POST['decrease_stock'])) {
    $id  = (int) $_POST['id'];
    $qty = (int) $_POST['qty'];

    if ($qty <= 0) {
        die('Invalid quantity');
    }

    // Get current product quantity
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if decreasing quantity is greater than current stock
    if ($qty > $product['stock']) {
        header("Location: index.php?page=manage_stocks&error=insufficient_stock");
        exit;
    }

    try {
        $reason = $_POST['reason'];
        decrease_stock($pdo, $id, $qty, $reason, $user_id);
        header("Location: index.php?page=manage_stocks&decreased_stock=1");
        exit;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}