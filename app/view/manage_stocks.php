<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/manage_stocks_model.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=landing_page");
    exit();
}
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role     = $_SESSION['role'] ?? 'staff';
$products = get_all_products($pdo);

if (isset($_POST['add_stock'])) {
    $id  = (int) $_POST['id'];
    $qty = (int) $_POST['qty'];
    if ($qty <= 0) {
        echo "Invalid quantity.";
        exit;
    }
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product   = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_stock = 1000000000;
    if ($product['stock'] + $qty > $max_stock) {
        die("Cannot add stock. " . number_format($max_stock) . " is the maximum stock limit.");
    }
    try {
        add_stock($pdo, $id, $qty, $user_id);
        header("Location: index.php?page=manage_stocks&added_stock=1");
        exit;
    } catch (PDOException $e) {
        echo "Query failed: " . $e->getMessage();
    }
}

if (isset($_POST['decrease_stock'])) {
    $id  = (int) $_POST['id'];
    $qty = (int) $_POST['qty'];
    if ($qty <= 0) {
        die('Invalid quantity');
    }
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stocks - Grease Monkey</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/logo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #FFD700;
            --primary-dark: #B8860B;
            --accent: #FFE55C;
            --success: #32CD32;
            --danger: #FF4444;
            --warning: #FFA500;
            --dark-bg: #0a0a0a;
            --card-bg: #1a1a1a;
            --gray-dark: #333333;
            --gray-medium: #666666;
            --gray-light: #999999;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #000000 !important;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: #ffffff !important;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(255, 215, 0, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 215, 0, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        /* ── Desktop Navbar ─────────────────────────────────────────────── */
        .navbar-custom {
            background: rgba(26, 26, 26, 0.98) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(255, 215, 0, 0.4));
        }

        .brand-text h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
            line-height: 1;
        }

        .brand-text p {
            color: rgba(255, 215, 0, 0.6);
            font-size: 0.7rem;
            font-weight: 500;
            margin: 0;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.5rem 1.25rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            background: transparent !important;
        }

        .nav-link-custom:hover {
            color: var(--primary) !important;
            background: rgba(255, 215, 0, 0.1) !important;
        }

        .nav-link-custom.active {
            color: #000 !important;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            box-shadow: 0 2px 12px rgba(255, 215, 0, 0.3);
        }

        .user-info {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            margin-right: 1rem;
            padding-right: 1rem;
            border-right: 1px solid rgba(255, 215, 0, 0.2);
            background: transparent !important;
        }

        .user-info strong { color: var(--primary); }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-logout:hover {
            background: var(--primary);
            color: #000;
            transform: translateY(-2px);
        }

        /* ── Mobile Top Bar ─────────────────────────────────────────────── */
        .mobile-topbar {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; z-index: 1100;
            background: rgba(26,26,26,.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,215,0,.3);
            padding: .75rem 1rem;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,.5);
        }

        .mobile-brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; }

        .hamburger-btn {
            background: transparent;
            border: 1px solid rgba(255,215,0,.4);
            border-radius: 8px;
            color: var(--primary);
            width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1.2rem;
            transition: all .3s ease;
            flex-shrink: 0;
        }
        .hamburger-btn:hover { background: rgba(255,215,0,.1); border-color: var(--primary); }

        /* ── Sidebar Overlay ────────────────────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 1200;
            background: rgba(0,0,0,.7);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity .3s ease;
        }
        .sidebar-overlay.active { opacity: 1; }

        /* ── Mobile Sidebar ─────────────────────────────────────────────── */
        .mobile-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 1300;
            width: 280px;
            background: rgba(15,15,15,.98);
            border-right: 1px solid rgba(255,215,0,.3);
            box-shadow: 4px 0 30px rgba(0,0,0,.8);
            transform: translateX(-100%);
            transition: transform .35s cubic-bezier(.4,0,.2,1);
            display: flex; flex-direction: column;
            overflow-y: auto;
        }
        .mobile-sidebar.open { transform: translateX(0); }

        .sidebar-header {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,215,0,.15);
            display: flex; align-items: center; justify-content: space-between;
        }

        .sidebar-close {
            background: rgba(255,68,68,.1);
            border: 1px solid rgba(255,68,68,.3);
            border-radius: 8px;
            color: var(--danger);
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1.1rem;
            transition: all .2s ease;
        }
        .sidebar-close:hover { background: rgba(255,68,68,.2); }

        .sidebar-user {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,215,0,.1);
            background: rgba(255,215,0,.03);
        }
        .sidebar-user-label { font-size: .7rem; color: var(--gray-light); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .25rem; }
        .sidebar-user-name  { font-size: 1rem; font-weight: 700; color: var(--primary); }

        .sidebar-nav { padding: 1rem .75rem; flex: 1; }
        .sidebar-nav-item {
            display: flex; align-items: center; gap: .875rem;
            padding: .875rem 1rem;
            border-radius: 10px;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-weight: 600; font-size: .95rem;
            transition: all .25s ease;
            margin-bottom: .25rem;
            position: relative;
        }
        .sidebar-nav-item:hover { background: rgba(255,215,0,.08); color: var(--primary); transform: translateX(4px); }
        .sidebar-nav-item.active {
            background: linear-gradient(135deg, rgba(255,215,0,.2) 0%, rgba(184,134,11,.1) 100%);
            color: var(--primary);
            border: 1px solid rgba(255,215,0,.25);
        }
        .sidebar-nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }
        .sidebar-nav-icon { font-size: 1.1rem; width: 24px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            padding: 1rem .75rem;
            border-top: 1px solid rgba(255,215,0,.1);
        }
        .sidebar-logout {
            display: flex; align-items: center; gap: .875rem;
            padding: .875rem 1rem;
            border-radius: 10px;
            background: rgba(255,68,68,.08);
            border: 1px solid rgba(255,68,68,.25);
            color: var(--danger);
            text-decoration: none;
            font-weight: 600; font-size: .95rem;
            transition: all .25s ease;
        }
        .sidebar-logout:hover { background: rgba(255,68,68,.15); transform: translateX(4px); color: var(--danger); }

        /* ── Main Content ───────────────────────────────────────────────── */
        .main-content {
            margin-top: 100px;
            padding: 2rem;
            position: relative;
            z-index: 10;
            background: transparent !important;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out;
            background: transparent !important;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        /* Alert Messages */
        .alert-custom {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-error {
            background: rgba(255, 68, 68, 0.1) !important;
            border-color: rgba(255, 68, 68, 0.3) !important;
            color: #ff9999 !important;
        }

        .alert-success {
            background: rgba(50, 205, 50, 0.1) !important;
            border-color: rgba(50, 205, 50, 0.3) !important;
            color: #90ee90 !important;
        }

        .btn-close {
            filter: brightness(0) saturate(100%) invert(80%) sepia(58%) saturate(458%) hue-rotate(358deg);
        }

        /* Filters */
        .filter-section {
            margin-bottom: 2rem;
            background: transparent !important;
        }

        .form-label {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-select {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            color: #fff;
            border-radius: 12px;
            padding: 0.875rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .form-select:focus {
            background: var(--card-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
            color: #fff;
        }

        .form-select option { background: var(--card-bg); color: #fff; }

        .search-wrapper { position: relative; }

        .search-input {
            width: 100%;
            padding: 0.875rem 3rem 0.875rem 1rem;
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .search-input:focus {
            outline: none;
            background: var(--card-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .search-input::placeholder { color: var(--gray-light); }

        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.125rem;
            pointer-events: none;
        }

        .btn-clear-filters {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
            cursor: pointer;
        }

        .btn-clear-filters:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        .active-filters-alert {
            background: rgba(50, 205, 50, 0.1) !important;
            border: 1px solid rgba(50, 205, 50, 0.3) !important;
            color: var(--success) !important;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 12px 32px rgba(255, 215, 0, 0.2);
        }

        .product-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        }

        .product-image { width: 100%; height: 100%; object-fit: cover; }
        .no-image { font-size: 3rem; color: rgba(255, 215, 0, 0.2); }

        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary);
            color: #000;
            padding: 0.375rem 0.875rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .product-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-header { margin-bottom: 1rem; }

        .product-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .product-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            background: transparent !important;
        }

        .meta-badge-category {
            background: rgba(255, 215, 0, 0.15) !important;
            color: var(--primary);
            border: 1px solid rgba(255, 215, 0, 0.3);
        }

        .meta-badge-part {
            background: rgba(255, 215, 0, 0.1) !important;
            color: #ffffff;
            border: 1px solid rgba(255, 215, 0, 0.2);
        }

        .product-models {
            background: rgba(50, 205, 50, 0.1);
            border-left: 3px solid var(--success);
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 6px;
        }

        .product-models-label {
            color: var(--success);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .product-models-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .product-description {
            color: var(--gray-light);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            margin: 0 -1.5rem -1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 215, 0, 0.15);
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }

        .product-stock { display: flex; flex-direction: column; align-items: flex-end; }
        .product-stock-label { font-size: 0.7rem; color: var(--gray-light); text-transform: uppercase; letter-spacing: 0.5px; }
        .product-stock-value { font-size: 1rem; font-weight: 700; color: #ffffff; }

        .product-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-action {
            flex: 1;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-add-stock {
            background: rgba(50, 205, 50, 0.1);
            border-color: rgba(50, 205, 50, 0.3);
            color: var(--success);
        }

        .btn-add-stock:hover {
            background: rgba(50, 205, 50, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.3);
        }

        .btn-decrease-stock {
            background: rgba(255, 68, 68, 0.1);
            border-color: rgba(255, 68, 68, 0.3);
            color: var(--danger);
        }

        .btn-decrease-stock:hover:not(:disabled) {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        .btn-decrease-stock:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            border-color: rgba(255, 68, 68, 0.2);
            color: rgba(255, 68, 68, 0.5);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-light);
            background: transparent !important;
            grid-column: 1 / -1;
        }

        .empty-state-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; color: var(--gray-medium); }
        .empty-state-text { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #fff; }

        /* Modal Styles */
        .modal-content {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 16px;
            color: #fff;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            padding: 1.5rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .modal-body { padding: 1.5rem; }

        .form-control {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            color: #fff;
            border-radius: 8px;
            padding: 0.75rem;
        }

        .form-control:focus {
            background: var(--card-bg);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .form-control:read-only {
            background: rgba(255, 215, 0, 0.05);
            border-color: rgba(255, 215, 0, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #000;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 0.875rem 1.5rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4);
        }

        .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

        .error-message {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
            padding: 0.5rem;
            background: rgba(255, 68, 68, 0.1);
            border-radius: 6px;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .error-message.show { display: block; }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar-custom { display: none !important; }
            .mobile-topbar { display: flex; }
            .sidebar-overlay { display: block; pointer-events: none; }
            .sidebar-overlay.active { pointer-events: all; }

            .main-content { margin-top: 68px; padding: 1rem; }
            .page-title { font-size: 1.5rem; }
            .products-grid { grid-template-columns: 1fr; }
            .filter-section .row > div { margin-bottom: 1rem; }
            .btn-clear-filters { width: 100%; }
        }

        @media (max-width: 480px) {
            .product-actions { flex-direction: column; }
            .btn-action { width: 100%; }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .products-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

    <!-- ── Desktop Navbar ──────────────────────────────────────────────── -->
    <nav class="navbar-custom navbar navbar-expand-lg">
        <div class="container-fluid">
            <a href="home.php" class="navbar-brand-custom">
                <img src="assets/images/logo.png" alt="Logo" class="logo-img">
                <div class="brand-text">
                    <h1>Grease Monkey</h1>
                    <p>Inventory System</p>
                </div>
            </a>
            <div class="collapse navbar-collapse show" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a href="index.php?page=dashboard"       class="nav-link-custom">📊 Dashboard</a></li>
                    <li class="nav-item"><a href="index.php?page=manage_products" class="nav-link-custom">🛠️ Products</a></li>
                    <li class="nav-item"><a href="index.php?page=manage_stocks"   class="nav-link-custom active">📦 Stock</a></li>
                    <li class="nav-item"><a href="index.php?page=stock_logs"      class="nav-link-custom">📋 Logs</a></li>
                    <li class="nav-item"><a href="index.php?page=activity_logs"   class="nav-link-custom">📝 Activity</a></li>
                    <li class="nav-item">
                        <span class="user-info d-none d-lg-block">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    </li>
                    <li class="nav-item"><a href="index.php?page=logout" class="btn-logout">🚪 Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ── Mobile Top Bar ─────────────────────────────────────────────── -->
    <div class="mobile-topbar">
        <a href="home.php" class="mobile-brand">
            <img src="assets/images/logo.png" alt="Logo" class="logo-img" style="width:38px;height:38px;">
            <div class="brand-text"><h1>Grease Monkey</h1><p>Inventory System</p></div>
        </a>
        <button class="hamburger-btn" id="sidebarToggle" aria-label="Open menu">☰</button>
    </div>

    <!-- ── Sidebar Overlay ────────────────────────────────────────────── -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ── Mobile Sidebar ─────────────────────────────────────────────── -->
    <aside class="mobile-sidebar" id="mobileSidebar" aria-label="Navigation">
        <div class="sidebar-header">
            <a href="home.php" class="navbar-brand-custom" style="text-decoration:none;">
                <img src="assets/images/logo.png" alt="Logo" class="logo-img" style="width:36px;height:36px;">
                <div class="brand-text"><h1 style="font-size:1.1rem;">Grease Monkey</h1><p>Inventory System</p></div>
            </a>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">✕</button>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-user-label">Logged in as</div>
            <div class="sidebar-user-name">👤 <?php echo htmlspecialchars($username); ?></div>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php?page=dashboard"       class="sidebar-nav-item"><span class="sidebar-nav-icon">📊</span> Dashboard</a>
            <a href="index.php?page=manage_products" class="sidebar-nav-item"><span class="sidebar-nav-icon">🛠️</span> Products</a>
            <a href="index.php?page=manage_stocks"   class="sidebar-nav-item active"><span class="sidebar-nav-icon">📦</span> Stock</a>
            <a href="index.php?page=stock_logs"      class="sidebar-nav-item"><span class="sidebar-nav-icon">📋</span> Logs</a>
            <a href="index.php?page=activity_logs"   class="sidebar-nav-item"><span class="sidebar-nav-icon">📝</span> Activity</a>
        </nav>

        <div class="sidebar-footer">
            <a href="index.php?page=logout" class="sidebar-logout">
                <span class="sidebar-nav-icon">🚪</span> Logout
            </a>
        </div>
    </aside>

    <!-- ── Main Content ───────────────────────────────────────────────── -->
    <div class="main-content">
        <div class="container">

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">📦 Manage Stocks</h1>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'insufficient_stock'): ?>
                <div class="alert-custom alert-error alert-dismissible fade show" role="alert">
                    <span style="font-size:1.25rem;">⚠️</span>
                    <div>Cannot decrease stock: The quantity you're trying to remove exceeds the current stock level.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['added_stock'])): ?>
                <div class="alert-custom alert-success alert-dismissible fade show" role="alert">
                    <span style="font-size:1.25rem;">✅</span>
                    <div>Stock added successfully!</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['decreased_stock'])): ?>
                <div class="alert-custom alert-success alert-dismissible fade show" role="alert">
                    <span style="font-size:1.25rem;">✅</span>
                    <div>Stock decreased successfully!</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filters and Search Section -->
            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Stock Filter</label>
                        <select id="stockFilter" class="form-select">
                            <option value="">All Stock</option>
                            <option value="low">⚠️ Low Stock</option>
                            <option value="out">🚫 Out of Stock</option>
                            <option value="available">✅ In Stock</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Search Products</label>
                        <div class="search-wrapper">
                            <input type="text"
                                id="liveSearch"
                                class="search-input"
                                placeholder="Search by name, part number, SKU, or model...">
                            <span class="search-icon">🔍</span>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="clearFilters" class="btn-clear-filters" style="display:none;">
                            ✖ Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Alert -->
            <div id="activeFiltersAlert" class="active-filters-alert" style="display:none;">
                <span style="font-size:1.25rem;">🔍</span>
                <div>
                    <span id="filterText"></span>
                    <span id="resultsCount" style="margin-left:1rem;opacity:.7;"></span>
                </div>
            </div>

            <div class="products-grid">
                <!-- loaded via AJAX -->
            </div>

        </div>
    </div>

    <!-- ── Add Stock Modal ─────────────────────────────────────────────── -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">➕ Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addStockForm">
                        <input type="hidden" name="id"  id="add_product_id">
                        <!-- stores current stock for max-limit calculation -->
                        <input type="hidden" id="add_current_stock_value" value="0">

                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" id="add_product_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" id="add_current_stock" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity to Add</label>
                            <input type="number"
                                   class="form-control"
                                   name="qty"
                                   id="add_qty_input"
                                   min="1"
                                   value="1"
                                   required>
                            <div class="error-message" id="add_error_message"></div>
                        </div>
                        <button type="submit" name="add_stock" id="add_submit_btn" class="btn-submit">Add Stock</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Decrease Stock Modal ────────────────────────────────────────── -->
    <div class="modal fade" id="decreaseStockModal" tabindex="-1" aria-labelledby="decreaseStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="decreaseStockModalLabel">➖ Decrease Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="decreaseStockForm">
                        <input type="hidden" name="id" id="decrease_product_id">
                        <input type="hidden" id="decrease_max_stock" value="0">

                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" id="decrease_product_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" id="decrease_current_stock" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity to Decrease</label>
                            <input type="number"
                                   class="form-control"
                                   name="qty"
                                   id="decrease_qty_input"
                                   min="1"
                                   value="1"
                                   required>
                            <div class="error-message" id="decrease_error_message">
                                ⚠️ Quantity cannot exceed current stock
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <select name="reason" class="form-select" required>
                                <option value="">Select Reason</option>
                                <option value="SALE">Sale</option>
                                <option value="REPAIR">Used in repair</option>
                                <option value="DAMAGED">Damaged</option>
                                <option value="LOST">Lost</option>
                                <option value="ADJUSTMENT">Adjustment</option>
                            </select>
                        </div>
                        <button type="submit"
                                name="decrease_stock"
                                id="decrease_submit_btn"
                                class="btn-submit">
                            ➖ Stock-out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar Logic ────────────────────────────────────────────────────────
(function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const close   = document.getElementById('sidebarClose');

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }

    toggle?.addEventListener('click', openSidebar);
    close?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
    document.querySelectorAll('.sidebar-nav-item, .sidebar-logout').forEach(link => {
        link.addEventListener('click', () => setTimeout(closeSidebar, 150));
    });
})();

// ── Stocks Logic ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    const MAX_STOCK = 1000000000; // 1 billion hard limit

    // ── Open modal helper ──────────────────────────────────────────────
    window.openStockModal = function (type, product) {
        if (type === 'add') {
            document.getElementById('add_product_id').value          = product.id;
            document.getElementById('add_product_name').value        = product.product_name;
            document.getElementById('add_current_stock').value       = product.stock;
            document.getElementById('add_current_stock_value').value = product.stock;
            document.getElementById('add_qty_input').value           = 1;
            document.getElementById('add_error_message').classList.remove('show');
            document.getElementById('add_error_message').textContent = '';
            document.getElementById('add_submit_btn').disabled       = false;
            new bootstrap.Modal(document.getElementById('addStockModal')).show();

        } else if (type === 'decrease') {
            document.getElementById('decrease_product_id').value    = product.id;
            document.getElementById('decrease_product_name').value  = product.product_name;
            document.getElementById('decrease_current_stock').value = product.stock;
            document.getElementById('decrease_max_stock').value     = product.stock;
            document.getElementById('decrease_qty_input').value     = 1;
            document.getElementById('decrease_error_message').classList.remove('show');
            document.getElementById('decrease_submit_btn').disabled = false;
            new bootstrap.Modal(document.getElementById('decreaseStockModal')).show();
        }
    };

    // ── DOM refs ───────────────────────────────────────────────────────
    const liveSearch         = document.getElementById('liveSearch');
    const stockFilter        = document.getElementById('stockFilter');
    const clearFiltersBtn    = document.getElementById('clearFilters');
    const activeFiltersAlert = document.getElementById('activeFiltersAlert');
    const filterText         = document.getElementById('filterText');
    const resultsCount       = document.getElementById('resultsCount');
    const productsGrid       = document.querySelector('.products-grid');
    let   lastStockHash      = '';
    let   debounceTimer      = null;

    // ── AJAX fetch stocks ──────────────────────────────────────────────
    window.fetchStocks = function () {
        const search     = liveSearch.value.trim();
        const stockLevel = stockFilter.value;
        const params     = new URLSearchParams({ search, stock_level: stockLevel });

        fetch('app/ajax/ajax_stocks.php?' + params)
            .then(res => { if (!res.ok) throw new Error(); return res.json(); })
            .then(data => {
                const newHash = JSON.stringify(data.products);
                if (newHash !== lastStockHash) {
                    lastStockHash = newHash;
                    renderStocks(data.products, search, stockLevel);
                }
            })
            .catch(() => {});

        const hasFilters = search || stockLevel;
        if (hasFilters) {
            const msgs = [];
            if (stockLevel) msgs.push('Stock: <strong>' + stockFilter.options[stockFilter.selectedIndex].text + '</strong>');
            if (search)     msgs.push('Search: <strong>"' + search + '"</strong>');
            filterText.innerHTML             = 'Active filters: ' + msgs.join(' | ');
            activeFiltersAlert.style.display = 'flex';
            clearFiltersBtn.style.display    = 'block';
        } else {
            activeFiltersAlert.style.display = 'none';
            clearFiltersBtn.style.display    = 'none';
        }
    };

    // ── Render stocks ──────────────────────────────────────────────────
    function renderStocks(products, search, stockLevel) {
        if (search || stockLevel) {
            resultsCount.textContent = '(' + products.length + ' product' + (products.length !== 1 ? 's' : '') + ' found)';
        }

        if (products.length === 0) {
            productsGrid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1;">
                    <div class="empty-state-icon">${(search || stockLevel) ? '🔍' : '📦'}</div>
                    <div class="empty-state-text">No products found</div>
                    <p>${(search || stockLevel) ? 'No products match your filters.' : 'Add products to get started!'}</p>
                </div>`;
            return;
        }

        productsGrid.innerHTML = products.map(p => `
            <div class="product-card" data-stock="${p.stock}">
                <div class="product-image-container">
                    ${p.product_image
                        ? `<img src="assets/images/${esc(p.product_image)}" class="product-image" alt="${esc(p.product_name)}">`
                        : `<div class="no-image">📷</div>`}
                    <div class="product-badge">#${p.id}</div>
                </div>
                <div class="product-content">
                    <div class="product-header">
                        <div class="product-name">${esc(p.product_name)}</div>
                        <div class="product-meta-row">
                            <span class="meta-badge meta-badge-part">${esc(p.sku ?? '')}</span>
                            <span class="meta-badge meta-badge-category">
                                📂 ${esc(p.category_name ?? 'Uncategorized')}
                            </span>
                            ${p.part_number ? `<span class="meta-badge meta-badge-part">${esc(p.part_number)}</span>` : ''}
                        </div>
                    </div>
                    ${p.applicable_models ? `
                        <div class="product-models">
                            <div class="product-models-label">Compatible Models</div>
                            <div class="product-models-text">${esc(p.applicable_models)}</div>
                        </div>` : ''}
                    <div class="product-description">
                        ${p.description ? esc(p.description) : 'No description available'}
                    </div>
                    <div class="product-actions">
                        <button type="button" class="btn-action btn-add-stock"
                                data-product='${JSON.stringify(p).replace(/'/g, "&#39;")}'>
                            + Stock-in
                        </button>
                        <button type="button" class="btn-action btn-decrease-stock"
                                ${p.stock <= 0 ? 'disabled' : ''}
                                data-product='${JSON.stringify(p).replace(/'/g, "&#39;")}'>
                            - Stock-out
                        </button>
                    </div>
                    <div class="product-footer">
                        <div class="product-price">
                            P${parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}
                        </div>
                        <div class="product-stock">
                            <div class="product-stock-label">QUANTITY</div>
                            <div class="product-stock-value"
                                 style="color:${p.stock === 0 ? 'var(--danger)' : p.stock <= 10 ? 'var(--warning)' : '#ffffff'}">
                                ${p.stock} stock
                            </div>
                        </div>
                    </div>
                </div>
            </div>`
        ).join('');

        productsGrid.querySelectorAll('.btn-add-stock').forEach(btn => {
            btn.addEventListener('click', () => openStockModal('add', JSON.parse(btn.dataset.product)));
        });
        productsGrid.querySelectorAll('.btn-decrease-stock').forEach(btn => {
            btn.addEventListener('click', () => openStockModal('decrease', JSON.parse(btn.dataset.product)));
        });
    }

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Filter listeners ───────────────────────────────────────────────
    liveSearch.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(window.fetchStocks, 300);
    });
    stockFilter.addEventListener('change', window.fetchStocks);
    clearFiltersBtn.addEventListener('click', () => {
        liveSearch.value  = '';
        stockFilter.value = '';
        window.fetchStocks();
    });

    setInterval(() => {
        if (document.activeElement !== liveSearch) window.fetchStocks();
    }, 5000);

    // ── ADD STOCK validation (max 1,000,000,000) ───────────────────────
    const addQtyInput  = document.getElementById('add_qty_input');
    const addSubmitBtn = document.getElementById('add_submit_btn');
    const addErrorMsg  = document.getElementById('add_error_message');

    function validateAddQty() {
        const qty      = parseInt(addQtyInput.value) || 0;
        const curStock = parseInt(document.getElementById('add_current_stock_value').value) || 0;

        if (qty < 1) {
            addSubmitBtn.disabled       = true;
            addErrorMsg.textContent     = '⚠️ Quantity must be at least 1';
            addErrorMsg.classList.add('show');
            return false;
        }
        if (curStock + qty > MAX_STOCK) {
            addSubmitBtn.disabled       = true;
            addErrorMsg.textContent     = `⚠️ Cannot add ${qty.toLocaleString()} — it would exceed the maximum stock limit of ${MAX_STOCK.toLocaleString()} (current: ${curStock.toLocaleString()})`;
            addErrorMsg.classList.add('show');
            return false;
        }
        addSubmitBtn.disabled = false;
        addErrorMsg.classList.remove('show');
        addErrorMsg.textContent = '';
        return true;
    }

    addQtyInput?.addEventListener('input', validateAddQty);

    document.getElementById('addStockForm')?.addEventListener('submit', function (e) {
        if (!validateAddQty()) e.preventDefault();
    });

    // ── DECREASE STOCK validation ──────────────────────────────────────
    const decQtyInput  = document.getElementById('decrease_qty_input');
    const decSubmitBtn = document.getElementById('decrease_submit_btn');
    const decErrorMsg  = document.getElementById('decrease_error_message');

    function validateDecreaseQty() {
        const qty      = parseInt(decQtyInput.value) || 0;
        const maxStock = parseInt(document.getElementById('decrease_max_stock').value) || 0;

        if (qty < 1) {
            decSubmitBtn.disabled   = true;
            decErrorMsg.textContent = '⚠️ Quantity must be at least 1';
            decErrorMsg.classList.add('show');
            return false;
        }
        if (qty > maxStock) {
            decSubmitBtn.disabled   = true;
            decErrorMsg.textContent = '⚠️ Quantity cannot exceed current stock';
            decErrorMsg.classList.add('show');
            return false;
        }
        decSubmitBtn.disabled = false;
        decErrorMsg.classList.remove('show');
        return true;
    }

    decQtyInput?.addEventListener('input', validateDecreaseQty);

    document.getElementById('decreaseStockForm')?.addEventListener('submit', function (e) {
        if (!validateDecreaseQty()) e.preventDefault();
    });

    // ── Auto-dismiss alerts after 5 s ──────────────────────────────────
    document.querySelectorAll('.alert-error, .alert-success').forEach(a => {
        setTimeout(() => { try { new bootstrap.Alert(a).close(); } catch (e) {} }, 5000);
    });

    // ── Initial load ───────────────────────────────────────────────────
    window.fetchStocks();
});
</script>
<script src="/inventory/assets/js/ws.js"></script>
<?php if (isset($_SESSION['user_id'])): ?>
    <script src="assets/js/session-timeout.js"></script>
<?php endif; ?>
</body>
</html>