<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Grease Monkey</title>
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
        .sidebar-user-name { font-size: 1rem; font-weight: 700; color: var(--primary); }

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
            flex-wrap: wrap;
            gap: 1rem;
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

        .alert-danger-custom {
            background: rgba(255, 68, 68, 0.1) !important;
            border-color: rgba(255, 68, 68, 0.3) !important;
            color: #ff9999 !important;
        }

        .alert-success-custom {
            background: rgba(50, 205, 50, 0.1) !important;
            border-color: rgba(50, 205, 50, 0.3) !important;
            color: #90ee90 !important;
        }

        /* Header Buttons */
        .header-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-add-product {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #000;
            font-weight: 700;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-add-product:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4);
        }

        .btn-deleted-products {
            background: rgba(255, 68, 68, 0.1);
            color: var(--danger);
            font-weight: 700;
            padding: 0.875rem 2rem;
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-deleted-products:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 68, 68, 0.3);
        }

        /* Search and Filters */
        .search-section {
            margin-bottom: 2rem;
            background: transparent !important;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 3rem 0.875rem 1rem;
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: var(--card-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .search-input::placeholder { color: var(--gray-light); }

        .search-wrapper { position: relative; }

        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.125rem;
            pointer-events: none;
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
        }

        .form-select:focus {
            background: var(--card-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
            color: #fff;
        }

        .form-select option {
            background: var(--card-bg);
            color: #fff;
        }

        .btn-clear-filters {
            background: rgba(255, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-clear-filters:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        /* Active Filters Alert */
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

        .btn-edit {
            background: rgba(255, 215, 0, 0.1);
            border-color: rgba(255, 215, 0, 0.3);
            color: var(--primary);
        }

        .btn-edit:hover {
            background: rgba(255, 215, 0, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
        }

        .btn-delete {
            background: rgba(255, 68, 68, 0.1);
            border-color: rgba(255, 68, 68, 0.3);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-light);
            background: transparent !important;
            grid-column: 1 / -1;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
            color: var(--gray-medium);
        }

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

        .btn-close {
            filter: brightness(0) saturate(100%) invert(80%) sepia(58%) saturate(458%) hue-rotate(358deg);
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

        .form-control::placeholder { color: var(--gray-light); }

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

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4);
        }

        /* Table */
        .table-container { overflow-x: auto; background: transparent !important; }
        .table { color: var(--gray-light) !important; margin: 0; background: transparent !important; }

        .table thead th {
            background: rgba(255, 215, 0, 0.1) !important;
            color: var(--primary) !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            padding: 0.875rem 1rem;
            border: none !important;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            vertical-align: middle;
            font-size: 0.875rem;
            background: transparent !important;
            color: var(--gray-light) !important;
        }

        .table tbody tr:hover { background: rgba(255, 215, 0, 0.05) !important; }
        .table tbody tr:last-child td { border-bottom: none !important; }

        .table-btn {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .table-btn-restore {
            background: rgba(50, 205, 50, 0.1);
            border-color: rgba(50, 205, 50, 0.3);
            color: var(--success);
        }

        .table-btn-restore:hover {
            background: rgba(50, 205, 50, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(50, 205, 50, 0.3);
        }

        .table-btn-delete {
            background: rgba(255, 68, 68, 0.1);
            border-color: rgba(255, 68, 68, 0.3);
            color: var(--danger);
        }

        .table-btn-delete:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(255, 68, 68, 0.3);
        }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar-custom { display: none !important; }
            .mobile-topbar { display: flex; }
            .sidebar-overlay { display: block; pointer-events: none; }
            .sidebar-overlay.active { pointer-events: all; }

            .main-content { margin-top: 68px; padding: 1rem; }

            .page-title { font-size: 1.5rem; }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .header-actions {
                flex-direction: column;
            }

            .btn-add-product,
            .btn-deleted-products,
            .btn-clear-filters {
                width: 100%;
                text-align: center;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .search-section .row > div {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            .product-actions { flex-direction: column; }
            .btn-action { width: 100%; }
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
                    <li class="nav-item"><a href="index.php?page=manage_products" class="nav-link-custom active">🛠️ Products</a></li>
                    <li class="nav-item"><a href="index.php?page=manage_stocks"   class="nav-link-custom">📦 Stock</a></li>
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
            <a href="index.php?page=manage_products" class="sidebar-nav-item active"><span class="sidebar-nav-icon">🛠️</span> Products</a>
            <a href="index.php?page=manage_stocks"   class="sidebar-nav-item"><span class="sidebar-nav-icon">📦</span> Stock</a>
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
                <h1 class="page-title">🛠️ Manage Products</h1>
                <div class="header-actions">
                    <button id="printReportBtn" class="btn btn-add-product">
                        🖨️ Print Report
                    </button>
                    <button class="btn btn-add-product" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                        📂 Categories
                    </button>
                    <button class="btn btn-add-product" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        + Add New Product
                    </button>
                    <button class="btn btn-deleted-products" data-bs-toggle="modal" data-bs-target="#deletedProductsModal">
                        🗑️ Deleted Products
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert-custom alert-danger-custom">
                    <span style="font-size: 1.25rem;">⚠️</span>
                    <div><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-custom alert-success-custom">
                    <span style="font-size: 1.25rem;">✓</span>
                    <div><strong>Success:</strong> <?php echo htmlspecialchars($success); ?></div>
                </div>
            <?php endif; ?>

            <!-- Search and Filters Section -->
            <div class="search-section">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Category</label>
                        <select id="categoryFilter" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Search Products</label>
                        <div class="search-wrapper">
                            <input type="text" 
                                id="liveSearch" 
                                class="search-input" 
                                placeholder="Search by name, SKU, part number, or model...">
                            <span class="search-icon">🔍</span>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="clearFilters" class="btn-clear-filters" style="display: none;">
                            ✖ Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Alert -->
            <div id="activeFiltersAlert" class="active-filters-alert" style="display: none;">
                <span style="font-size: 1.25rem;">🔍</span>
                <div>
                    <span id="filterText"></span>
                    <span id="resultsCount" style="margin-left: 1rem; opacity: 0.7;"></span>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <!-- loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id="addProductForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name *</label>
                                <input type="text" name="product_name" class="form-control" placeholder="e.g., Engine Oil Filter" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            data-requires-oem="<?php echo $category['requires_oem']; ?>">
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Part Number (OEM Number)</label>
                                <input type="text" name="part_number" id="part_number" class="form-control" placeholder="e.g., OF-12345">
                                <small style="color: var(--gray-light); font-weight: 400; display: block; font-size: 0.7rem; margin-top: 2px;">
                                    Only for: ABS Module/Brake System, Water Pump, Transmission
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price *</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Applicable Models</label>
                                <input type="text" name="applicable_models" class="form-control" placeholder="e.g., EcoSport, Fiesta, Focus, Explorer, Everest">
                                <small style="color: var(--gray-light); font-size: 0.75rem; margin-top: 0.25rem;">Optional: List compatible vehicle models</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter product description..."></textarea>
                            </div>
                            <div class="col-12">
                                    <label class="form-label">Product Image</label>
                                    <div id="addCurrentImagePreview" style="display:none; margin-bottom:0.75rem;">
                                        <img id="addPreviewImg" src="" alt="Preview" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid rgba(255,215,0,0.3);display:block;margin-bottom:0.5rem;">
                                        <button type="button" id="addRemoveImageBtn"
                                                style="background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.3);color:var(--danger);border-radius:8px;padding:0.4rem 1rem;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .2s ease;">
                                            🗑️ Remove Picture
                                        </button>
                                    </div>
                                    <input type="file" name="product_image" id="addProductImageInput" class="form-control" accept="image/*">
                                </div>
                            <div class="col-12">
                                <button type="submit" name="add_product" class="btn-submit">Add Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id="editForm">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <input type="hidden" name="current_image" id="edit_existing_image">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name *</label>
                                <input type="text" name="product_name" id="edit_product_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <select name="category_id" id="edit_category_id" class="form-select" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            data-requires-oem="<?php echo $category['requires_oem']; ?>">
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Part Number (OEM Number)</label>
                                <input type="text" name="part_number" id="edit_part_number" class="form-control" required>
                                <small style="color: var(--gray-light); font-weight: 400; display: block; font-size: 0.7rem; margin-top: 2px;">
                                    Only for: ABS Module/Brake System, Water Pump, Transmission
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price *</label>
                                <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Applicable Models</label>
                                <input type="text" name="applicable_models" id="edit_applicable_models" class="form-control" placeholder="e.g., EcoSport, Fiesta, Focus, Explorer, Everest">
                                <small style="color: var(--gray-light); font-size: 0.75rem; margin-top: 0.25rem;">Optional: List compatible vehicle models</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Product Image</label>
                                <div id="editCurrentImagePreview" style="display:none; margin-bottom:0.75rem;">
                                    <img id="editPreviewImg" src="" alt="Current" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid rgba(255,215,0,0.3);display:block;margin-bottom:0.5rem;">
                                    <button type="button" id="editRemoveImageBtn"
                                            style="background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.3);color:var(--danger);border-radius:8px;padding:0.4rem 1rem;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .2s ease;">
                                        🗑️ Remove Picture
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image" id="editRemoveImageFlag" value="0">
                                <input type="file" name="product_image" id="editProductImageInput" class="form-control" accept="image/*">
                                <small style="color: var(--gray-light); font-size: 0.75rem; margin-top: 0.25rem;">Leave empty to keep current image</small>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="update_product" class="btn-submit">Update Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Deleted Products Modal -->
    <div class="modal fade" id="deletedProductsModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🗑️ Deleted Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $deleted_query = "SELECT products.*, categories.category_name
                                     FROM products
                                     LEFT JOIN categories ON products.category_id = categories.id
                                     WHERE products.is_deleted = 1
                                     ORDER BY products.deleted_at DESC";
                    $deleted_stmt = $pdo->prepare($deleted_query);
                    $deleted_stmt->execute();
                    $deleted_products = $deleted_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (empty($deleted_products)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">🗑️</div>
                            <h4>No deleted products</h4>
                            <p style="color: var(--gray-light);">Deleted products will appear here</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Part Number</th>
                                        <th>Models</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Archive At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deleted_products as $product): ?>
                                    <tr>
                                        <td><strong style="color: var(--primary);">#<?php echo $product['id']; ?></strong></td>
                                        <td>
                                            <?php if ($product['product_image']): ?>
                                                <img src="assets/images/<?php echo htmlspecialchars($product['product_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255, 215, 0, 0.2);">
                                            <?php else: ?>
                                                <span style="font-size: 2rem; color: var(--gray-medium);">📷</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($product['product_name']); ?></strong></td>
                                        <td><span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($product['part_number'] ?? 'N/A'); ?></span></td>
                                        <td style="max-width: 200px; font-size: 0.8rem; color: var(--gray-light);">
                                            <?php echo htmlspecialchars($product['applicable_models'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <span style="background: rgba(255, 215, 0, 0.1); color: var(--primary); padding: 0.25rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                                <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td><strong style="color: var(--primary);">₱<?php echo number_format($product['price'], 2); ?></strong></td>
                                        <td>
                                            <strong style="color: <?php echo $product['stock'] > 0 ? 'var(--success)' : 'var(--danger)'; ?>;">
                                                <?php echo $product['stock']; ?> units
                                            </strong>
                                        </td>
                                        <td style="color: var(--gray-light); font-size: 0.875rem;">
                                            <?php echo $product['deleted_at'] ? date('M d, Y - h:i A', strtotime($product['deleted_at'])) : 'N/A'; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                <button class="table-btn table-btn-restore"
                                                        onclick="confirmRestore(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')">
                                                    ↻ Restore
                                                </button>
                                                <button class="table-btn table-btn-delete"
                                                        onclick="confirmPermanentDelete(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')">
                                                    🗑️ Delete Permanently
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Report Modal -->
    <div class="modal fade" id="printReportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-color: rgba(255,215,0,0.4);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,215,0,0.2); padding: 1.25rem 1.5rem;">
                    <h5 class="modal-title" style="display:flex;align-items:center;gap:.625rem;">
                        <span style="font-size:1.4rem;">🖨️</span> Generate Inventory Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.75rem 1.5rem;">
                    <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:1.25rem;">
                        <div style="background:rgba(255,215,0,.1);border:1px solid rgba(255,215,0,.3);border-radius:12px;padding:.875rem;flex-shrink:0;">
                            <span style="font-size:1.75rem;">📋</span>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:600;margin:0 0 .375rem;">This will generate and log an inventory report.</p>
                            <p style="color:var(--gray-light);font-size:.875rem;margin:0;line-height:1.6;">
                                The report will open in a new tab and the action will be recorded in the activity log.
                                Any active search or category filters will be applied.
                            </p>
                        </div>
                    </div>
                    <div id="printFilterSummary" style="background:rgba(255,215,0,.05);border:1px solid rgba(255,215,0,.15);border-radius:10px;padding:.875rem 1rem;font-size:.8rem;color:var(--gray-light);display:none;">
                        <span style="color:var(--primary);font-weight:700;margin-right:.5rem;">⚙️ Active filters:</span>
                        <span id="printFilterDetails"></span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,215,0,.15);padding:1rem 1.5rem;gap:.75rem;">
                    <button type="button" class="btn" data-bs-dismiss="modal"
                            style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:8px;padding:.625rem 1.25rem;font-weight:600;">
                        Cancel
                    </button>
                    <button type="button" id="confirmPrintBtn"
                            style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border:none;color:#000;font-weight:700;border-radius:8px;padding:.625rem 1.5rem;transition:all .3s ease;cursor:pointer;">
                        🖨️ Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Product Modal -->
    <div class="modal fade" id="archiveProductModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-color:rgba(255,165,0,0.4);">
                <div class="modal-header" style="border-bottom:1px solid rgba(255,165,0,0.2);padding:1.25rem 1.5rem;">
                    <h5 class="modal-title" style="display:flex;align-items:center;gap:.625rem;">
                        <span style="font-size:1.4rem;">🗑️</span> Archive Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.75rem 1.5rem;">
                    <div style="display:flex;align-items:flex-start;gap:1rem;">
                        <div style="background:rgba(255,165,0,.1);border:1px solid rgba(255,165,0,.3);border-radius:12px;padding:.875rem;flex-shrink:0;">
                            <span style="font-size:1.75rem;">📦</span>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:600;margin:0 0 .375rem;">Are you sure you want to archive this product?</p>
                            <p style="color:var(--warning);font-size:.95rem;font-weight:700;margin:0 0 .5rem;" id="archiveProductName"></p>
                            <p style="color:var(--gray-light);font-size:.875rem;margin:0;line-height:1.6;">
                                This product will be moved to the archive. You can restore it anytime from <strong style="color:rgba(255,255,255,.7);">Deleted Products</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,165,0,.15);padding:1rem 1.5rem;gap:.75rem;">
                    <input type="hidden" id="archiveProductId">
                    <button type="button" class="btn" data-bs-dismiss="modal"
                            style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:8px;padding:.625rem 1.25rem;font-weight:600;">
                        Cancel
                    </button>
                    <button type="button" id="confirmArchiveBtn"
                            style="background:linear-gradient(135deg,#FFA500 0%,#cc7a00 100%);border:none;color:#000;font-weight:700;border-radius:8px;padding:.625rem 1.5rem;transition:all .3s ease;cursor:pointer;">
                        🗑️ Archive Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Permanently Delete Modal -->
    <div class="modal fade" id="permDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-color:rgba(255,68,68,0.5);">
                <div class="modal-header" style="border-bottom:1px solid rgba(255,68,68,0.2);padding:1.25rem 1.5rem;">
                    <h5 class="modal-title" style="display:flex;align-items:center;gap:.625rem;color:var(--danger);">
                        <span style="font-size:1.4rem;">⚠️</span> Permanently Delete Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.75rem 1.5rem;">
                    <div style="display:flex;align-items:flex-start;gap:1rem;">
                        <div style="background:rgba(255,68,68,.1);border:1px solid rgba(255,68,68,.3);border-radius:12px;padding:.875rem;flex-shrink:0;">
                            <span style="font-size:1.75rem;">🚫</span>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:600;margin:0 0 .375rem;">This action <span style="color:var(--danger);">cannot be undone.</span></p>
                            <p style="color:var(--danger);font-size:.95rem;font-weight:700;margin:0 0 .5rem;" id="permDeleteProductName"></p>
                            <p style="color:var(--gray-light);font-size:.875rem;margin:0;line-height:1.6;">
                                This product will be <strong style="color:var(--danger);">permanently removed</strong> from the database along with all associated data. There is no way to recover it.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,68,68,.15);padding:1rem 1.5rem;gap:.75rem;">
                    <input type="hidden" id="permDeleteProductId">
                    <button type="button" class="btn" data-bs-dismiss="modal"
                            style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:8px;padding:.625rem 1.25rem;font-weight:600;">
                        Cancel
                    </button>
                    <button type="button" id="confirmPermDeleteBtn"
                            style="background:linear-gradient(135deg,var(--danger) 0%,#cc0000 100%);border:none;color:#fff;font-weight:700;border-radius:8px;padding:.625rem 1.5rem;transition:all .3s ease;cursor:pointer;">
                        🗑️ Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Product Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-color:rgba(50,205,50,0.4);">
                <div class="modal-header" style="border-bottom:1px solid rgba(50,205,50,0.2);padding:1.25rem 1.5rem;">
                    <h5 class="modal-title" style="display:flex;align-items:center;gap:.625rem;color:var(--success);">
                        <span style="font-size:1.4rem;">↻</span> Restore Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.75rem 1.5rem;">
                    <div style="display:flex;align-items:flex-start;gap:1rem;">
                        <div style="background:rgba(50,205,50,.1);border:1px solid rgba(50,205,50,.3);border-radius:12px;padding:.875rem;flex-shrink:0;">
                            <span style="font-size:1.75rem;">♻️</span>
                        </div>
                        <div>
                            <p style="color:#fff;font-weight:600;margin:0 0 .375rem;">Restore this product to the active inventory?</p>
                            <p style="color:var(--success);font-size:.95rem;font-weight:700;margin:0 0 .5rem;" id="restoreProductName"></p>
                            <p style="color:var(--gray-light);font-size:.875rem;margin:0;line-height:1.6;">
                                This product will be moved back to the active products list and will be visible in the inventory again.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(50,205,50,.15);padding:1rem 1.5rem;gap:.75rem;">
                    <input type="hidden" id="restoreProductId">
                    <button type="button" class="btn" data-bs-dismiss="modal"
                            style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);border-radius:8px;padding:.625rem 1.25rem;font-weight:600;">
                        Cancel
                    </button>
                    <button type="button" id="confirmRestoreBtn"
                            style="background:linear-gradient(135deg,var(--success) 0%,#228B22 100%);border:none;color:#000;font-weight:700;border-radius:8px;padding:.625rem 1.5rem;transition:all .3s ease;cursor:pointer;">
                        ↻ Restore Product
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Manage Categories Modal -->
<div class="modal fade" id="manageCategoriesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📂 Manage Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Add Category Form -->
                <form method="POST" action="index.php?page=manage_products" style="margin-bottom:1.5rem;">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="category_name" class="form-control"
                            placeholder="Category name e.g. Brakes" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="sku_prefix" class="form-control"
                            placeholder="SKU prefix e.g. BRK"
                            maxlength="10" required
                            style="text-transform:uppercase;">
                        <small style="color:var(--gray-light);font-size:0.7rem;">Used for SKU generation</small>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check" style="margin:0;">
                            <input class="form-check-input" type="checkbox" name="requires_oem" id="addRequiresOem">
                            <label class="form-check-label" for="addRequiresOem"
                                style="color:rgba(255,255,255,0.8);font-size:0.85rem;">
                                Requires OEM
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_category"
                                style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);
                                    border:none;color:#000;font-weight:700;border-radius:8px;
                                    padding:0.75rem 1rem;width:100%;cursor:pointer;">
                            + Add
                        </button>
                    </div>
                </div>
            </form>

                <!-- Categories Table -->
                <?php if (empty($categories)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📂</div>
                        <p>No categories yet. Add one above.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>SKU Prefix</th>
                                    <th>OEM Required</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td style="color:var(--primary);font-weight:700;"><?php echo $cat['id']; ?></td>
                                    <td style="color:#fff;font-weight:600;"><?php echo htmlspecialchars($cat['category_name']); ?></td>
                                    <td>
                                        <span style="background:rgba(255,215,0,0.1);color:var(--primary);
                                                    padding:0.25rem 0.75rem;border-radius:6px;font-size:0.75rem;font-weight:700;">
                                            <?php echo htmlspecialchars($cat['sku_prefix']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($cat['requires_oem']): ?>
                                            <span style="background:rgba(50,205,50,0.1);color:var(--success);
                                                        padding:0.25rem 0.75rem;border-radius:6px;font-size:0.75rem;font-weight:600;">
                                                ✓ Yes
                                            </span>
                                        <?php else: ?>
                                            <span style="color:var(--gray-medium);font-size:0.75rem;">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="background:rgba(255,215,0,0.1);color:var(--primary);
                                                    padding:0.25rem 0.75rem;border-radius:6px;font-size:0.75rem;font-weight:600;">
                                            <?php echo isset($cat['product_count']) ? $cat['product_count'] : 0; ?> product(s)
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:0.5rem;">
                                            <button class="table-btn table-btn-restore"
                                                    onclick="openEditCategory(
                                                        <?php echo $cat['id']; ?>,
                                                        '<?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($cat['sku_prefix'], ENT_QUOTES); ?>',
                                                        <?php echo $cat['requires_oem']; ?>
                                                    )">
                                                ✏️ Edit
                                            </button>
                                            <?php if (($cat['product_count'] ?? 0) == 0): ?>
                                                <button class="table-btn table-btn-delete"
                                                        onclick="openDeleteCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES); ?>')">
                                                    🗑️ Delete
                                                </button>
                                            <?php else: ?>
                                                <span style="color:var(--gray-medium);font-size:0.75rem;padding:0.5rem;align-self:center;">In use</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" style="z-index:1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ Edit Category</h5>
                <button type="button" class="btn-close" id="editCatCloseBtn"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="index.php?page=manage_products">
                    <input type="hidden" name="category_id" id="editCatId">
                    <div style="margin-bottom:1rem;">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="category_name" id="editCatName" class="form-control" required>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label class="form-label">SKU Prefix *</label>
                        <input type="text" name="sku_prefix" id="editCatPrefix" class="form-control"
                               maxlength="10" required style="text-transform:uppercase;">
                        <small style="color:var(--gray-light);font-size:0.7rem;">Changing this won't affect existing product SKUs</small>
                    </div>
                    <div style="margin-bottom:1.5rem;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_oem" id="editRequiresOem">
                            <label class="form-check-label" for="editRequiresOem"
                                   style="color:rgba(255,255,255,0.8);">
                                Requires OEM Part Number
                            </label>
                        </div>
                    </div>
                    <button type="submit" name="edit_category" class="btn-submit">💾 Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Category Confirm Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" style="z-index:1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-color:rgba(255,68,68,0.5);">
            <div class="modal-header" style="border-bottom:1px solid rgba(255,68,68,0.2);">
                <h5 class="modal-title" style="color:var(--danger);">⚠️ Delete Category</h5>
                <button type="button" class="btn-close" id="deleteCatCloseBtn"></button>
            </div>
            <div class="modal-body">
                <p style="color:#fff;margin-bottom:0.5rem;">
                    Are you sure you want to delete
                    <strong id="deleteCatNameDisplay" style="color:var(--danger);"></strong>?
                </p>
                <p style="color:var(--gray-light);font-size:0.875rem;margin:0;">This cannot be undone.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid rgba(255,68,68,0.15);gap:0.75rem;">
                <form method="POST" style="display:flex;gap:0.75rem;width:100%;justify-content:flex-end;">
                    <input type="hidden" name="category_id" id="deleteCatId">
                    <button type="button" id="deleteCatCancelBtn"
                            style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);
                                   color:rgba(255,255,255,.7);border-radius:8px;padding:.625rem 1.25rem;font-weight:600;cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit" name="delete_category"
                            style="background:linear-gradient(135deg,var(--danger) 0%,#cc0000 100%);
                                   border:none;color:#fff;font-weight:700;border-radius:8px;
                                   padding:.625rem 1.5rem;cursor:pointer;">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- Hidden Forms -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="product_id" id="delete_product_id">
        <input type="hidden" name="delete_product" value="1">
    </form>

    <form method="POST" id="permanentDeleteForm" style="display: none;">
        <input type="hidden" name="product_id" id="permanent_delete_product_id">
        <input type="hidden" name="permanent_delete_product" value="1">
    </form>

    <form method="POST" id="restoreForm" style="display: none;">
        <input type="hidden" name="product_id" id="restore_product_id">
        <input type="hidden" name="restore_product" value="1">
    </form>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar Logic ────────────────────────────────────────────────────────
(function() {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const close   = document.getElementById('sidebarClose');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    toggle?.addEventListener('click', openSidebar);
    close?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
    document.querySelectorAll('.sidebar-nav-item, .sidebar-logout').forEach(link => {
        link.addEventListener('click', () => setTimeout(closeSidebar, 150));
    });
})();

// ── Products Logic ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    const ALLOWED_OEM_CATEGORIES = ['ABS Module', 'Water Pump', 'Transmission'];

    class OEMRestrictionManager {
        constructor(categorySelectId, partNumberInputId, isEditForm = false) {
            this.categorySelect  = document.getElementById(categorySelectId);
            this.partNumberInput = document.getElementById(partNumberInputId);
            this.isEditForm      = isEditForm;
            if (!this.categorySelect || !this.partNumberInput) return;
            this.init();
        }
        init() {
            this.categorySelect.addEventListener('change', () => this.updateFieldState(false));
            this.updateFieldState(true);
        }
        updateFieldState(isInitialLoad = false) {
            const categoryId = this.categorySelect.value;
            const option     = this.categorySelect.querySelector(`option[value="${categoryId}"]`);
            const isAllowed  = option ? option.dataset.requiresOem === '1' : false;

            if (categoryId && !isAllowed) {
                this.partNumberInput.disabled    = true;
                this.partNumberInput.placeholder = 'OEM Number not applicable for this category';
                this.partNumberInput.style.background  = 'rgba(255, 68, 68, 0.1)';
                this.partNumberInput.style.borderColor = 'rgba(255, 68, 68, 0.3)';
                this.partNumberInput.removeAttribute('required');
                if (!this.isEditForm && !isInitialLoad) this.partNumberInput.value = '';
            } else {
                this.partNumberInput.disabled    = false;
                this.partNumberInput.placeholder = 'e.g., OF-12345';
                this.partNumberInput.style.background  = 'var(--card-bg)';
                this.partNumberInput.style.borderColor = 'rgba(255, 215, 0, 0.2)';
                if (isAllowed && categoryId) this.partNumberInput.setAttribute('required', 'required');
                else this.partNumberInput.removeAttribute('required');
            }
        }
        getCategoryName(categoryId) {
            const option = this.categorySelect.querySelector(`option[value="${categoryId}"]`);
            return option ? option.textContent.trim() : '';
        }
    }

    new OEMRestrictionManager('category_id', 'part_number', false);
    document.getElementById('editProductModal')?.addEventListener('shown.bs.modal', () => {
        new OEMRestrictionManager('edit_category_id', 'edit_part_number', true);
    });

    // ── DOM refs ───────────────────────────────────────────────────────────
    const liveSearch         = document.getElementById('liveSearch');
    const categoryFilter     = document.getElementById('categoryFilter');
    const clearFiltersBtn    = document.getElementById('clearFilters');
    const activeFiltersAlert = document.getElementById('activeFiltersAlert');
    const filterText         = document.getElementById('filterText');
    const resultsCount       = document.getElementById('resultsCount');
    const productsGrid       = document.querySelector('.products-grid');
    let   debounceTimer      = null;
    let   lastProductHash    = '';

    // ── fetchProducts ──────────────────────────────────────────────────────
    window.fetchProducts = function() {
        const search     = liveSearch.value.trim();
        const categoryId = categoryFilter.value;
        const params     = new URLSearchParams({ search, category_id: categoryId });

        fetch('app/ajax/ajax_products.php?' + params)
            .then(res => { if (!res.ok) throw new Error(); return res.json(); })
            .then(data => {
                const newHash = JSON.stringify(data.products);
                if (newHash !== lastProductHash) {
                    lastProductHash = newHash;
                    renderProducts(data.products, data.count, search, categoryId);
                }
            })
            .catch(() => {});

        const hasFilters = search || categoryId;
        if (hasFilters) {
            const msgs = [];
            if (categoryId) msgs.push('Category: <strong>' + categoryFilter.options[categoryFilter.selectedIndex].text + '</strong>');
            if (search)     msgs.push('Search: <strong>"' + search + '"</strong>');
            filterText.innerHTML             = 'Active filters: ' + msgs.join(' | ');
            activeFiltersAlert.style.display = 'flex';
            clearFiltersBtn.style.display    = 'block';
        } else {
            activeFiltersAlert.style.display = 'none';
            clearFiltersBtn.style.display    = 'none';
        }
    };

    // ── renderProducts ─────────────────────────────────────────────────────
    function renderProducts(products, count, search, categoryId) {
        if (search || categoryId) {
            resultsCount.textContent = '(' + count + ' product' + (count !== 1 ? 's' : '') + ' found)';
        }

        if (products.length === 0) {
            productsGrid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1;">
                    <div class="empty-state-icon">${(search || categoryId) ? '🔍' : '📦'}</div>
                    <h3>No products found</h3>
                    <p>${(search || categoryId) ? 'No products match your filters.' : 'Click "Add New Product" to get started!'}</p>
                </div>`;
            return;
        }

        productsGrid.innerHTML = products.map(p => `
            <div class="product-card">
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
                            <span class="meta-badge meta-badge-category" data-category-id="${esc(String(p.category_id ?? ''))}">
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
                        <button class="btn-action btn-edit"
                                data-product='${JSON.stringify(p).replace(/'/g, "&#39;")}'>
                            ✏️ Edit
                        </button>
                        <button class="btn-action btn-delete"
                                data-id="${p.id}"
                                data-name="${esc(p.product_name)}">
                            🗑️ Archive
                        </button>
                    </div>
                    <div class="product-footer">
                        <div class="product-price">
                            ₱${parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}
                        </div>
                        <div class="product-stock">
                            <div class="product-stock-label">QUANTITY</div>
                            <div class="product-stock-value">${p.stock} stock</div>
                        </div>
                    </div>
                </div>
            </div>`
        ).join('');

        productsGrid.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => openEditModal(JSON.parse(btn.dataset.product)));
        });
        productsGrid.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => confirmDelete(btn.dataset.id, btn.dataset.name));
        });
    }

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Filter listeners ───────────────────────────────────────────────────
    liveSearch.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(window.fetchProducts, 300);
    });
    categoryFilter.addEventListener('change', window.fetchProducts);
    clearFiltersBtn.addEventListener('click', () => {
        liveSearch.value     = '';
        categoryFilter.value = '';
        window.fetchProducts();
    });

    setInterval(() => {
        if (document.activeElement !== liveSearch) {
            window.fetchProducts();
        }
    }, 3000);

    // ── Modal helpers ──────────────────────────────────────────────────────
    window.openEditModal = function(product) {
        document.getElementById('edit_product_id').value        = product.id;
        document.getElementById('edit_product_name').value      = product.product_name;
        document.getElementById('edit_category_id').value       = product.category_id;
        document.getElementById('edit_part_number').value       = product.part_number || '';
        document.getElementById('edit_price').value             = product.price;
        document.getElementById('edit_applicable_models').value = product.applicable_models || '';
        document.getElementById('edit_description').value       = product.description || '';
        document.getElementById('edit_existing_image').value    = product.product_image || '';
        new bootstrap.Modal(document.getElementById('editProductModal')).show();
    };

    window.confirmDelete = function(productId, productName) {
        document.getElementById('archiveProductName').textContent = productName;
        document.getElementById('archiveProductId').value = productId;
        new bootstrap.Modal(document.getElementById('archiveProductModal')).show();
    };

    document.getElementById('confirmArchiveBtn').addEventListener('click', function() {
        document.getElementById('delete_product_id').value = document.getElementById('archiveProductId').value;
        bootstrap.Modal.getInstance(document.getElementById('archiveProductModal')).hide();
        document.getElementById('deleteForm').submit();
    });

    document.getElementById('confirmPermDeleteBtn').addEventListener('click', function() {
        document.getElementById('permanent_delete_product_id').value = document.getElementById('permDeleteProductId').value;
        bootstrap.Modal.getInstance(document.getElementById('permDeleteModal')).hide();
        document.getElementById('permanentDeleteForm').submit();
    });

    document.getElementById('confirmRestoreBtn').addEventListener('click', function() {
        document.getElementById('restore_product_id').value = document.getElementById('restoreProductId').value;
        bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
        document.getElementById('restoreForm').submit();
    });

    window.confirmPermanentDelete = function(productId, productName) {
        document.getElementById('permDeleteProductName').textContent = productName;
        document.getElementById('permDeleteProductId').value = productId;
        new bootstrap.Modal(document.getElementById('permDeleteModal')).show();
    };

    window.confirmRestore = function(productId, productName) {
        document.getElementById('restoreProductName').textContent = productName;
        document.getElementById('restoreProductId').value = productId;
        new bootstrap.Modal(document.getElementById('restoreModal')).show();
    };

    // ── Print ──────────────────────────────────────────────────────────────
    document.getElementById('printReportBtn').addEventListener('click', function() {
        // Show active filter summary inside the modal
        const cat    = categoryFilter.value;
        const search = liveSearch.value.trim();
        const summary = document.getElementById('printFilterSummary');
        const details = document.getElementById('printFilterDetails');
        const parts = [];
        if (cat)    parts.push('Category: <strong style="color:var(--primary);">' + categoryFilter.options[categoryFilter.selectedIndex].text + '</strong>');
        if (search) parts.push('Search: <strong style="color:var(--primary);">"' + search + '"</strong>');
        if (parts.length) {
            details.innerHTML      = parts.join(' &nbsp;|&nbsp; ');
            summary.style.display  = 'block';
        } else {
            summary.style.display  = 'none';
        }
        new bootstrap.Modal(document.getElementById('printReportModal')).show();
    });

    document.getElementById('confirmPrintBtn').addEventListener('click', function() {
        const cat    = categoryFilter.value;
        const search = liveSearch.value.trim();
        let url = 'index.php?page=print_inventory&log=1';
        if (cat)    url += '&category_id=' + encodeURIComponent(cat) + '&category_name=' + encodeURIComponent(categoryFilter.options[categoryFilter.selectedIndex].text);
        if (search) url += '&search=' + encodeURIComponent(search);
        bootstrap.Modal.getInstance(document.getElementById('printReportModal')).hide();
        window.open(url, '_blank');
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            document.getElementById('printReportBtn').click();
        }
    });

    // ── Auto-dismiss alerts ────────────────────────────────────────────────
    document.querySelectorAll('.alert-custom').forEach(a => {
        setTimeout(() => {
            a.style.transition = 'opacity 0.3s ease';
            a.style.opacity    = '0';
            setTimeout(() => a.remove(), 300);
        }, 5000);
    });

    // ── Initial load ───────────────────────────────────────────────────────
    window.fetchProducts();
    // ── Remove Picture (Edit Modal) ────────────────────────────────────────
const editImageInput   = document.getElementById('editProductImageInput');
const editPreview      = document.getElementById('editCurrentImagePreview');
const editPreviewImg   = document.getElementById('editPreviewImg');
const editRemoveFlag   = document.getElementById('editRemoveImageFlag');
const editRemoveBtn    = document.getElementById('editRemoveImageBtn');

// Override openEditModal to show preview when product has an image
const _origOpenEdit = window.openEditModal;
window.openEditModal = function(product) {
    _origOpenEdit(product);
    editRemoveFlag.value = '0';
    if (product.product_image) {
        editPreviewImg.src    = 'assets/images/' + product.product_image;
        editPreview.style.display = 'block';
        editImageInput.value  = '';
    } else {
        editPreview.style.display = 'none';
    }
};
// ── Category Modal Helpers ─────────────────────────────────────────────
const manageCatModal  = document.getElementById('manageCategoriesModal');
const editCatModal    = document.getElementById('editCategoryModal');
const deleteCatModal  = document.getElementById('deleteCategoryModal');

function backToManageCategories() {
    bootstrap.Modal.getOrCreateInstance(manageCatModal).show();
}

window.openEditCategory = function(id, name, prefix, requiresOem) {
    document.getElementById('editCatId').value     = id;
    document.getElementById('editCatName').value   = name;
    document.getElementById('editCatPrefix').value = prefix;
    document.getElementById('editRequiresOem').checked = requiresOem == 1;
    bootstrap.Modal.getInstance(manageCatModal)?.hide();
    setTimeout(() => new bootstrap.Modal(editCatModal).show(), 200);
};

window.openDeleteCategory = function(id, name) {
    document.getElementById('deleteCatId').value              = id;
    document.getElementById('deleteCatNameDisplay').textContent = name;
    bootstrap.Modal.getInstance(manageCatModal)?.hide();
    setTimeout(() => new bootstrap.Modal(deleteCatModal).show(), 200);
};

// Close buttons go back to categories list
document.getElementById('editCatCloseBtn')?.addEventListener('click', () => {
    bootstrap.Modal.getInstance(editCatModal)?.hide();
    setTimeout(backToManageCategories, 200);
});
document.getElementById('deleteCatCloseBtn')?.addEventListener('click', () => {
    bootstrap.Modal.getInstance(deleteCatModal)?.hide();
    setTimeout(backToManageCategories, 200);
});
document.getElementById('deleteCatCancelBtn')?.addEventListener('click', () => {
    bootstrap.Modal.getInstance(deleteCatModal)?.hide();
    setTimeout(backToManageCategories, 200);
});
editRemoveBtn?.addEventListener('click', () => {
    editRemoveFlag.value      = '1';
    editPreview.style.display = 'none';
    editImageInput.value      = '';
});

editImageInput?.addEventListener('change', () => {
    editRemoveFlag.value = '0'; // new file overrides remove flag
});

// ── Remove Picture (Add Modal) ─────────────────────────────────────────
const addImageInput  = document.getElementById('addProductImageInput');
const addPreview     = document.getElementById('addCurrentImagePreview');
const addPreviewImg  = document.getElementById('addPreviewImg');
const addRemoveBtn   = document.getElementById('addRemoveImageBtn');

addImageInput?.addEventListener('change', () => {
    const file = addImageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            addPreviewImg.src         = e.target.result;
            addPreview.style.display  = 'block';
        };
        reader.readAsDataURL(file);
    }
});

addRemoveBtn?.addEventListener('click', () => {
    addImageInput.value          = '';
    addPreview.style.display     = 'none';
    addPreviewImg.src            = '';
});
});
</script>
<script src="/inventory/assets/js/ws.js"></script>
<?php if (isset($_SESSION['user_id'])): ?>
    <script src="assets/js/session-timeout.js"></script>
<?php endif; ?>
</body>
</html>