<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Logs - Grease Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
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

        .page-title-section h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        .page-title-section p {
            color: var(--gray-light);
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-toggle-filter {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--primary);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-toggle-filter:hover {
            background: rgba(255, 215, 0, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
        }

        .btn-deleted-history {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-deleted-history:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--primary);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: rgba(255, 215, 0, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
            color: var(--primary);
        }

        /* Alert Messages */
        .alert-success {
            background: rgba(50, 205, 50, 0.1) !important;
            border: 1px solid rgba(50, 205, 50, 0.3) !important;
            color: var(--success) !important;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
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

        /* Filter Card */
        .filter-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: none;
            transition: all 0.3s ease;
        }

        .filter-card.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Form Elements */
        .form-label {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            color: #fff;
            border-radius: 12px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: var(--card-bg);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
            outline: none;
        }

        .form-control::placeholder { color: var(--gray-light); }

        select.form-control {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23FFD700' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .btn-filter {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #000;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 0.875rem 1.5rem;
            transition: all 0.3s ease;
            width: 100%;
            cursor: pointer;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4);
        }

        .btn-clear {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            width: 100%;
        }

        .btn-clear:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

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

        /* Table Container */
        .table-container {
            background: var(--card-bg);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            min-height: 300px;
        }

        .table {
            color: var(--gray-light) !important;
            margin: 0;
            background: transparent !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: rgba(255, 215, 0, 0.1) !important;
            color: var(--primary) !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none !important;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .log-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
            margin: 0;
        }

        .selected-counter {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--primary);
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            display: none;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease-out;
        }

        #bulkArchiveBtn {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            white-space: nowrap;
        }

        #bulkArchiveBtn:hover:not(:disabled) {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        #bulkArchiveBtn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .table thead th:nth-child(1)  { width: 50px; text-align: center; }
        .table thead th:nth-child(2)  { width: 80px; }
        .table thead th:nth-child(3)  { width: 200px; }
        .table thead th:nth-child(4)  { width: 120px; }
        .table thead th:nth-child(5)  { width: 150px; }
        .table thead th:nth-child(6)  { width: 100px; }
        .table thead th:nth-child(7)  { width: 100px; }
        .table thead th:nth-child(8)  { width: 100px; }
        .table thead th:nth-child(9)  { width: 100px; }
        .table thead th:nth-child(10) { width: 200px; }
        .table thead th:nth-child(11) { width: 160px; }
        .table thead th:nth-child(12) { width: 120px; }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            vertical-align: middle;
            font-size: 0.875rem;
            background: transparent !important;
            color: var(--gray-light) !important;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
        }

        .table tbody tr:hover { background: rgba(255, 215, 0, 0.05) !important; }
        .table tbody tr:last-child td { border-bottom: none !important; }

        .product-cell { display: flex; flex-direction: column; gap: 0.25rem; }
        .product-name-cell { font-weight: 700; color: #fff; font-size: 0.9rem; line-height: 1.3; word-break: break-word; }
        .product-id-cell { font-size: 0.75rem; color: var(--primary); font-weight: 600; margin-top: 0.125rem; }

        .part-number-cell {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.85rem;
            color: #fff;
            font-weight: 500;
            word-break: break-all;
        }

        .models-cell {
            font-size: 0.85rem;
            color: var(--gray-light);
            line-height: 1.4;
            word-break: break-word;
            max-height: 60px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .models-cell::-webkit-scrollbar { width: 4px; }
        .models-cell::-webkit-scrollbar-track { background: rgba(255, 215, 0, 0.1); border-radius: 2px; }
        .models-cell::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 2px; }

        .badge-action {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            white-space: nowrap;
            min-width: 80px;
            text-align: center;
        }

        .badge-add      { background: rgba(50, 205, 50, 0.15) !important; color: var(--success) !important; border: 1px solid rgba(50, 205, 50, 0.3); }
        .badge-decrease { background: rgba(255, 68, 68, 0.15) !important; color: var(--danger)  !important; border: 1px solid rgba(255, 68, 68, 0.3); }

        .quantity-value {
            font-weight: 800;
            font-size: 1rem;
            white-space: nowrap;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .quantity-add      { color: var(--success); }
        .quantity-decrease { color: var(--danger); }

        .remarks-text {
            color: var(--gray-light);
            font-style: italic;
            font-size: 0.85rem;
            line-height: 1.4;
            word-wrap: break-word;
            max-height: 60px;
            overflow-y: auto;
            padding-right: 4px;
            display: block;
        }

        .remarks-text::-webkit-scrollbar { width: 4px; }
        .remarks-text::-webkit-scrollbar-track { background: rgba(255, 215, 0, 0.1); border-radius: 2px; }
        .remarks-text::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 2px; }

        .date-text { color: var(--gray-light); font-size: 0.85rem; white-space: nowrap; }
        .log-id    { color: var(--primary); font-weight: 800; font-size: 0.9rem; white-space: nowrap; }

        .stock-value {
            font-weight: 700;
            font-size: 0.9rem;
            white-space: nowrap;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .btn-delete {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger);
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
            min-width: 80px;
            text-align: center;
        }

        .btn-delete:hover {
            background: rgba(255, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        .btn-restore {
            background: rgba(50, 205, 50, 0.1);
            border: 1px solid rgba(50, 205, 50, 0.3);
            color: var(--success);
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
            min-width: 80px;
            text-align: center;
        }

        .btn-restore:hover {
            background: rgba(50, 205, 50, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 205, 50, 0.3);
        }

        .btn-permanent-delete {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.5);
            color: var(--danger);
            font-weight: 700;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
            min-width: 80px;
            text-align: center;
        }

        .btn-permanent-delete:hover {
            background: rgba(255, 68, 68, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-light);
            background: transparent !important;
        }

        .empty-state-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; color: var(--gray-medium); }
        .empty-state-text { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-light); }

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
            background: var(--card-bg);
            position: sticky;
            top: 0;
            z-index: 1055;
        }

        .modal-title { font-size: 1.5rem; font-weight: 800; color: var(--primary); }

        .btn-close {
            filter: brightness(0) saturate(100%) invert(80%) sepia(58%) saturate(458%) hue-rotate(358deg);
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-table-container {
            max-height: 50vh;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid rgba(255, 215, 0, 0.2);
        }

        .modal-table-container .table { margin: 0; }

        .modal-table-container .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--card-bg) !important;
            border-bottom: 2px solid rgba(255, 215, 0, 0.4) !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
        }

        /* ── Pagination ─────────────────────────────────────────────────── */
        .pagination-wrapper {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(255,215,0,.1);
            flex-wrap: wrap; gap: 1rem;
        }

        .pagination-info {
            color: var(--gray-light); font-size: 0.875rem;
        }
        .pagination-info strong { color: var(--primary); }

        .pagination-controls { display: flex; align-items: center; gap: .375rem; flex-wrap: wrap; }

        .page-btn {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            color: rgba(255,255,255,.7);
            font-weight: 600; font-size: 0.8rem;
            padding: .5rem .875rem; border-radius: 8px;
            cursor: pointer; transition: all .25s ease;
            text-decoration: none; display: inline-flex; align-items: center; gap: .375rem;
            min-width: 36px; justify-content: center;
        }
        .page-btn:hover:not(:disabled):not(.active) {
            background: rgba(255,215,0,.1); border-color: rgba(255,215,0,.3);
            color: var(--primary); transform: translateY(-1px);
        }
        .page-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-color: var(--primary); color: #000;
        }
        .page-btn:disabled { opacity: .35; cursor: not-allowed; }

        .page-btn-arrow { font-size: 1rem; }

        .per-page-select {
            background: var(--card-bg); border: 1px solid rgba(255,215,0,.2);
            color: #fff; border-radius: 8px; padding: .4rem .75rem;
            font-size: 0.8rem; cursor: pointer;
        }
        .per-page-select:focus { outline: none; border-color: var(--primary); }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar-custom { display: none !important; }
            .mobile-topbar { display: flex; }
            .sidebar-overlay { display: block; pointer-events: none; }
            .sidebar-overlay.active { pointer-events: all; }

            .main-content { margin-top: 68px; padding: 1rem; }

            .page-header { flex-direction: column; align-items: stretch; }
            .page-title-section h1 { font-size: 1.5rem; }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-toggle-filter,
            .btn-deleted-history,
            .btn-secondary,
            #bulkArchiveBtn {
                width: 100%;
                justify-content: center;
            }

            .table-container { overflow-x: auto; }

            .models-cell,
            .remarks-text { max-height: 40px; font-size: 0.8rem; }

            .btn-delete,
            .btn-restore,
            .btn-permanent-delete { min-width: 70px; padding: 0.4rem 0.5rem; font-size: 0.7rem; }
        }

        @media (max-width: 1200px) {
            .table { table-layout: auto; }
            .table thead th:nth-child(n) { width: auto; }
        }

        @media (max-width: 480px) {
            .header-actions { flex-direction: column; }

            .btn-delete,
            .btn-restore,
            .btn-permanent-delete { width: 100%; margin-bottom: 0.25rem; }

            .table { font-size: 0.8rem; }
            .table thead th,
            .table tbody td { padding: 0.75rem 0.5rem; }
            .product-name-cell { font-size: 0.8rem; }
            .product-id-cell   { font-size: 0.7rem; }
            .part-number-cell  { font-size: 0.75rem; }
            .models-cell       { font-size: 0.75rem; }
            .remarks-text      { font-size: 0.75rem; }
            .date-text         { font-size: 0.75rem; }
        }
    </style>
</head>
<body>

    <!-- ── Desktop Navbar ──────────────────────────────────────────────── -->
    <nav class="navbar-custom navbar navbar-expand-lg">
        <div class="container-fluid">
            <a href="index.php?page=dashboard" class="navbar-brand-custom">
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
                    <li class="nav-item"><a href="index.php?page=manage_stocks"   class="nav-link-custom">📦 Stock</a></li>
                    <li class="nav-item"><a href="index.php?page=stock_logs"      class="nav-link-custom active">📋 Logs</a></li>
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
        <a href="index.php?page=dashboard" class="mobile-brand">
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
            <a href="index.php?page=dashboard" class="navbar-brand-custom" style="text-decoration:none;">
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
            <a href="index.php?page=manage_stocks"   class="sidebar-nav-item"><span class="sidebar-nav-icon">📦</span> Stock</a>
            <a href="index.php?page=stock_logs"      class="sidebar-nav-item active"><span class="sidebar-nav-icon">📋</span> Logs</a>
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
                <div class="page-title-section">
                    <h1>📋 Stock Logs</h1>
                    <p>Track all stock movements</p>
                </div>
                <div class="header-actions">
                    <a href="#" class="btn-secondary" onclick="printFilteredData(); return false;">
                        🖨️ Print Report
                    </a>
                    <button class="btn-toggle-filter" onclick="toggleFilter()">
                        🔍 Filter
                    </button>
                    <button class="btn-toggle-filter" data-bs-toggle="modal" data-bs-target="#deletedHistoryModal">
                        🗑️ Show Archived Logs
                    </button>
                    <button type="button" class="btn-deleted-history" onclick="archiveSelectedLogs()" id="bulkArchiveBtn" disabled>
                        🗃️ Archive Selected
                    </button>
                </div>
            </div>

            <div id="selectedCounter" class="selected-counter" style="display: none;">
                <span style="font-size: 1.25rem;">📋</span>
                <div>
                    <span id="selectedCount">0</span> log(s) selected for bulk action
                    <button type="button" class="btn-deleted-history" onclick="clearSelection()" style="margin-left: 1rem; padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                        Clear Selection
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Stock log #<?php echo htmlspecialchars($_GET['deleted']); ?> has been archived successfully.</div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['restored'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Stock log #<?php echo htmlspecialchars($_GET['restored']); ?> has been restored successfully.</div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['permanently_deleted'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Stock log #<?php echo htmlspecialchars($_GET['permanently_deleted']); ?> has been permanently deleted.</div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['bulk_archived'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>
                        Successfully archived <?php echo htmlspecialchars($_GET['bulk_archived']); ?> log(s).
                        <?php if (isset($_GET['errors']) && $_GET['errors'] > 0): ?>
                            <span style="color: var(--warning); font-size: 0.9rem; margin-left: 0.5rem;">
                                (<?php echo htmlspecialchars($_GET['errors']); ?> failed)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filter Card -->
            <div class="filter-card" id="filterCard">
                <form method="GET" action="index.php" id="filterForm">
                    <input type="hidden" name="page" value="stock_logs">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search Product</label>
                            <div class="search-wrapper">
                                <input type="text" name="search" class="form-control search-input" 
                                       placeholder="Search by product name..." autocomplete="off"
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                       id="productSearch">
                                <span class="search-icon">🔍</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-control" 
                                   value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-control" 
                                   value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Action Type</label>
                            <select name="action_type" class="form-control">
                                <option value="">All</option>
                                <option value="IN"  <?= (isset($_GET['action_type']) && $_GET['action_type'] === 'IN')  ? 'selected' : ''; ?>>Stock In</option>
                                <option value="OUT" <?= (isset($_GET['action_type']) && $_GET['action_type'] === 'OUT') ? 'selected' : ''; ?>>Stock Out</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Search Remarks</label>
                            <div class="search-wrapper">
                                <input type="text" name="remarks_search" class="form-control search-input" 
                                       placeholder="Search in remarks..." autocomplete="off"
                                       value="<?php echo htmlspecialchars($_GET['remarks_search'] ?? ''); ?>"
                                       id="remarksSearch">
                                <span class="search-icon">💬</span>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="index.php?page=stock_logs" class="btn-clear">Clear All Filters</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Active Filters Alert -->
            <div id="activeFiltersAlert" class="active-filters-alert" style="display: none;">
                <span style="font-size: 1.25rem;">🔍</span>
                <div>
                    <span id="filterText"></span>
                    <span id="resultsCount" style="margin-left: 1rem; opacity: 0.7;"></span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <?php if (empty($stock_logs)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <div class="empty-state-text">No stock logs found</div>
                        <p style="color: var(--gray-light); font-size: 0.875rem;">Stock changes will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                    </th>
                                    <th>Log ID</th>
                                    <th>Product</th>
                                    <th>Applicable Models</th>
                                    <th>Action</th>
                                    <th>Quantity</th>
                                    <th>Total Before</th>
                                    <th>Total After</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock_logs as $log): 
                                    $productName = isset($log['product_name']) ? $log['product_name'] : 'Unknown Product';
                                    $quantity    = (int)$log['quantity'];
                                    $action      = strtoupper(trim($log['action']));
                                    $isAdd       = ($action === 'IN');
                                    $searchText  = strtolower($log['id'] . ' ' . $productName . ' ' . ($log['part_number'] ?? '') . ' ' . ($log['applicable_models'] ?? '') . ' ' . ($log['remarks'] ?? ''));
                                ?>
                                    <tr data-log-type="active" data-log-id="<?php echo $log['id']; ?>" data-search="<?php echo htmlspecialchars($searchText); ?>">
                                        <td class="align-middle text-center">
                                            <input type="checkbox" class="log-checkbox" name="log_ids[]" value="<?php echo $log['id']; ?>"
                                                onchange="updateSelectedCount()">
                                        </td>
                                        <td class="align-middle">
                                            <strong class="log-id">#<?php echo $log['id']; ?></strong>
                                        </td>
                                        <td class="align-middle">
                                            <div class="product-cell">
                                                <div class="product-name-cell"><?php echo htmlspecialchars($productName); ?></div>
                                                <div class="product-id-cell">ID: <?php echo $log['product_id']; ?></div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="models-cell">
                                                <?php echo !empty($log['applicable_models']) ? htmlspecialchars($log['applicable_models']) : '<span style="opacity:0.5;">—</span>'; ?>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge-action <?php echo $isAdd ? 'badge-add' : 'badge-decrease'; ?>">
                                                <?php echo $isAdd ? 'Stock In' : 'Stock Out'; ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong class="quantity-value <?php echo $isAdd ? 'quantity-add' : 'quantity-decrease'; ?>">
                                                <?php echo $isAdd ? '+' . abs($quantity) : '-' . abs($quantity); ?>
                                            </strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php if ($log['balance_before'] !== null): ?>
                                                <strong class="stock-value"><?php echo $log['balance_before']; ?></strong>
                                            <?php else: ?>
                                                <span style="opacity:0.4;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php if ($log['balance_after'] !== null): ?>
                                                <strong class="stock-value"><?php echo $log['balance_after']; ?></strong>
                                            <?php else: ?>
                                                <span style="opacity:0.4;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <span class="remarks-text"><?php echo htmlspecialchars($log['remarks'] ?? '-'); ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="date-text"><?php echo date('M d, Y - h:i A', strtotime($log['created_at'])); ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to archive this log?');">
                                                <input type="hidden" name="id" value="<?php echo $log['id']; ?>">
                                                <button type="submit" name="delete_stock_log" class="btn-delete">Archive</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php
                    $current_page = max(1, (int)($_GET['page_num'] ?? 1));
                    $per_page     = max(1, (int)($_GET['per_page'] ?? 50));
                    $total_logs   = $totalStock_logs;                           // ← FIXED
                    $total_pages  = $totalStock_pages;                         // ← FIXED
                    $offset_start = (($current_page - 1) * $per_page) + 1;
                    $offset_end   = min($current_page * $per_page, $total_logs);

                    $base_params  = $_GET;
                    unset($base_params['page_num']);
                    $base_query   = http_build_query($base_params);
                    ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <strong><?= $offset_start ?>–<?= $offset_end ?></strong>
                            of <strong><?= number_format($total_logs) ?></strong> logs
                        </div>

                        <div class="pagination-controls">
                            <!-- Per-page selector -->
                            <form method="GET" style="display:inline-flex; align-items:center; gap:.5rem; margin-right:.5rem;">
                                <?php foreach ($base_params as $k => $v): ?>
                                    <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                                <?php endforeach; ?>
                                <label style="color:var(--gray-light);font-size:.8rem;">Rows:</label>
                                <select name="per_page" class="per-page-select" onchange="this.form.submit()">
                                    <?php foreach ([25, 50, 100, 200] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $per_page == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>

                            <?php if ($total_pages > 1):
                                // Show at most 5 page buttons around current
                                $start_page = max(1, $current_page - 2);
                                $end_page   = min($total_pages, $current_page + 2);
                            ?>
                            <!-- First & Prev -->
                            <?php if ($current_page > 1): ?>
                                <a href="?<?= $base_query ?>&page_num=1&per_page=<?= $per_page ?>" class="page-btn" title="First">«</a>
                                <a href="?<?= $base_query ?>&page_num=<?= $current_page - 1 ?>&per_page=<?= $per_page ?>" class="page-btn">‹ Prev</a>
                            <?php else: ?>
                                <button class="page-btn" disabled>«</button>
                                <button class="page-btn" disabled>‹ Prev</button>
                            <?php endif; ?>

                            <!-- Page numbers -->
                            <?php if ($start_page > 1): ?>
                                <span style="color:var(--gray-light);padding:0 .25rem;">…</span>
                            <?php endif; ?>

                            <?php for ($p = $start_page; $p <= $end_page; $p++): ?>
                                <a href="?<?= $base_query ?>&page_num=<?= $p ?>&per_page=<?= $per_page ?>"
                                   class="page-btn <?= $p === $current_page ? 'active' : '' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($end_page < $total_pages): ?>
                                <span style="color:var(--gray-light);padding:0 .25rem;">…</span>
                            <?php endif; ?>

                            <!-- Next & Last -->
                            <?php if ($current_page < $total_pages): ?>
                                <a href="?<?= $base_query ?>&page_num=<?= $current_page + 1 ?>&per_page=<?= $per_page ?>" class="page-btn">Next ›</a>
                                <a href="?<?= $base_query ?>&page_num=<?= $total_pages ?>&per_page=<?= $per_page ?>" class="page-btn" title="Last">»</a>
                            <?php else: ?>
                                <button class="page-btn" disabled>Next ›</button>
                                <button class="page-btn" disabled>»</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Deleted History Modal -->
    <div class="modal fade" id="deletedHistoryModal" tabindex="-1" aria-labelledby="deletedHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletedHistoryModalLabel">🗑️ Archived Logs History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($deleted_stock_logs)): ?>
                        <div class="mb-3 d-flex justify-content-end gap-2">
                            <form method="POST" onsubmit="return confirm('❗CAUTION: This will RESTORE all the archived stock log.');">
                                <button type="submit" name="restore_all_stock_logs" class="btn-restore">
                                    Restore All Logs
                                </button>
                            </form>
                            <form method="POST" onsubmit="return confirm('⚠️ CRITICAL WARNING: This will PERMANENTLY delete ALL deleted logs. This action CANNOT be undone. Are you absolutely sure you want to proceed?');">
                                <button type="submit" name="permanent_delete_all_stock_logs" class="btn-permanent-delete">
                                    Delete All Logs Permanently
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="modal-table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Log ID</th>
                                    <th>Product</th>
                                    <th>Part Number</th>
                                    <th>Applicable Models</th>
                                    <th>Action</th>
                                    <th>Quantity</th>
                                    <th>Total Before</th>
                                    <th>Total After</th>
                                    <th>Remarks</th>
                                    <th>Original Date</th>
                                    <th>Archived At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($deleted_stock_logs)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center py-5" style="color: var(--gray-light);">
                                            <div class="empty-state-icon">🗑️</div>
                                            <div class="empty-state-text">No archived logs found</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($deleted_stock_logs as $log): 
                                        $productName = isset($log['product_name']) ? $log['product_name'] : 'Unknown Product';
                                        $quantity    = (int)$log['quantity'];
                                        $action      = strtoupper(trim($log['action']));
                                        $isAdd       = ($action === 'IN');
                                    ?>
                                        <tr data-log-type="deleted" data-log-id="<?php echo $log['id']; ?>">
                                            <td class="align-middle"><strong class="log-id">#<?php echo $log['id']; ?></strong></td>
                                            <td class="align-middle">
                                                <div class="product-cell">
                                                    <div class="product-name-cell"><?php echo htmlspecialchars($productName); ?></div>
                                                    <div class="product-id-cell">ID: <?php echo $log['product_id']; ?></div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="part-number-cell">
                                                    <?php echo !empty($log['part_number']) ? htmlspecialchars($log['part_number']) : '<span style="opacity:0.5;">—</span>'; ?>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="models-cell">
                                                    <?php echo !empty($log['applicable_models']) ? htmlspecialchars($log['applicable_models']) : '<span style="opacity:0.5;">—</span>'; ?>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge-action <?php echo $isAdd ? 'badge-add' : 'badge-decrease'; ?>">
                                                    <?php echo $isAdd ? 'Stock In' : 'Stock Out'; ?>
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <strong class="quantity-value <?php echo $isAdd ? 'quantity-add' : 'quantity-decrease'; ?>">
                                                    <?php echo $isAdd ? '+' . abs($quantity) : '-' . abs($quantity); ?>
                                                </strong>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?php if ($log['balance_before'] !== null): ?>
                                                    <strong class="stock-value"><?php echo $log['balance_before']; ?></strong>
                                                <?php else: ?>
                                                    <span style="opacity:0.4;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?php if ($log['balance_after'] !== null): ?>
                                                    <strong class="stock-value"><?php echo $log['balance_after']; ?></strong>
                                                <?php else: ?>
                                                    <span style="opacity:0.4;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <span class="remarks-text"><?php echo htmlspecialchars($log['remarks'] ?? '-'); ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="date-text"><?php echo date('M d, Y - h:i A', strtotime($log['created_at'])); ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="date-text"><?php echo date('M d, Y - h:i A', strtotime($log['deleted_at'])); ?></span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex flex-column gap-1" style="min-width: 120px;">
                                                    <form method="POST" onsubmit="return confirm('Are you sure you want to restore this log?');">
                                                        <input type="hidden" name="id" value="<?php echo $log['id']; ?>">
                                                        <button type="submit" name="restore_stock_log" class="btn-restore w-100">Restore</button>
                                                    </form>
                                                    <form method="POST" onsubmit="return confirm('⚠️ WARNING: This will PERMANENTLY delete this log. This action cannot be undone. Are you absolutely sure?');">
                                                        <input type="hidden" name="id" value="<?php echo $log['id']; ?>">
                                                        <button type="submit" name="permanent_delete_stock_log" class="btn-permanent-delete w-100">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    // ── All existing JS logic (unchanged) ───────────────────────────────────
    function archiveVisibleLogs() {
        const visibleLogIds = [];
        const allRows = document.querySelectorAll('.table tbody tr[data-log-type="active"]');
        allRows.forEach((row) => {
            if (row.style.display === 'none') return;
            const logIdElement = row.querySelector('.log-id');
            if (logIdElement) {
                const id = logIdElement.textContent.replace('#', '').trim();
                if (id && !isNaN(id)) visibleLogIds.push(id);
            }
        });
        if (visibleLogIds.length === 0) { alert('No visible logs to archive!'); return; }
        const allActiveRows = document.querySelectorAll('.table tbody tr[data-log-type="active"]').length;
        let msg = `Archive ${visibleLogIds.length} visible log(s)?\n\n`;
        if (visibleLogIds.length < allActiveRows) msg += `Currently showing ${visibleLogIds.length} of ${allActiveRows} total logs due to active filters.\n`;
        else msg += `This will archive ALL ${allActiveRows} logs.\n`;
        msg += `\nIDs: ${visibleLogIds.slice(0, 5).join(', ')}${visibleLogIds.length > 5 ? '...' : ''}`;
        msg += `\n\n⚠️ Archived logs can be restored from the 'Show Archived Logs' section.`;
        if (confirm(msg)) {
            const btn = document.getElementById('archiveAllBtn');
            if (btn) { btn.textContent = '⏳ Archiving...'; btn.disabled = true; }
            const form = document.createElement('form');
            form.method = 'POST'; form.action = 'index.php?page=stock_logs';
            <?php if(isset($_SESSION['csrf_token'])): ?>
            const ci = document.createElement('input'); ci.type='hidden'; ci.name='csrf_token'; ci.value='<?php echo $_SESSION["csrf_token"]; ?>'; form.appendChild(ci);
            <?php endif; ?>
            const ii = document.createElement('input'); ii.type='hidden'; ii.name='visible_log_ids'; ii.value=visibleLogIds.join(','); form.appendChild(ii);
            const ai = document.createElement('input'); ai.type='hidden'; ai.name='archive_visible_stock_logs'; ai.value='1'; form.appendChild(ai);
            document.body.appendChild(form); form.submit();
        }
    }

    function printFilteredData() {
        const productSearch = document.getElementById('productSearch')?.value || '';
        const remarksSearch = document.getElementById('remarksSearch')?.value || '';
        const dateFrom      = document.querySelector('input[name="date_from"]')?.value || '';
        const dateTo        = document.querySelector('input[name="date_to"]')?.value || '';
        const actionType    = document.querySelector('select[name="action_type"]')?.value || '';
        const urlParams     = new URLSearchParams(window.location.search);
        const finalSearch      = productSearch || urlParams.get('search') || '';
        const finalRemarks     = remarksSearch || urlParams.get('remarks_search') || '';
        const finalDateFrom    = dateFrom      || urlParams.get('date_from') || '';
        const finalDateTo      = dateTo        || urlParams.get('date_to') || '';
        const finalActionType  = actionType    || urlParams.get('action_type') || '';
        const printUrl = "index.php?page=print_stock_logs" +
            "&search=" + encodeURIComponent(finalSearch.trim()) +
            "&date_from=" + encodeURIComponent(finalDateFrom.trim()) +
            "&date_to=" + encodeURIComponent(finalDateTo.trim()) +
            "&action_type=" + encodeURIComponent(finalActionType.trim()) +
            "&remarks_search=" + encodeURIComponent(finalRemarks.trim()) +
            "&log=1";
        const visibleRows = Array.from(document.querySelectorAll('.table tbody tr[data-log-type="active"]')).filter(r => r.style.display !== 'none').length;
        const totalRows   = document.querySelectorAll('.table tbody tr[data-log-type="active"]').length;
        let msg = "This will generate a stock logs report.";
        if (visibleRows !== totalRows) msg += `\n\nCurrently showing ${visibleRows} of ${totalRows} logs due to active filters.`;
        if (confirm(msg)) window.open(printUrl, '_blank');
    }

    function toggleFilter() {
        document.getElementById('filterCard').classList.toggle('show');
    }

    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const hasActiveFilters = urlParams.has('search') || urlParams.has('date_from') || 
                                 urlParams.has('date_to') || urlParams.has('action_type') || 
                                 urlParams.has('remarks_search');
        if (hasActiveFilters) {
            document.getElementById('filterCard').classList.add('show');
            updateActiveFiltersDisplay();
        }
        initializeLiveSearch();
    });

    function updateActiveFiltersDisplay() {
        const urlParams          = new URLSearchParams(window.location.search);
        const activeFiltersAlert = document.getElementById('activeFiltersAlert');
        const filterText         = document.getElementById('filterText');
        const resultsCount       = document.getElementById('resultsCount');
        let filterMessages = [];
        const search       = urlParams.get('search') || '';
        const remarksSearch= urlParams.get('remarks_search') || '';
        const dateFrom     = urlParams.get('date_from') || '';
        const dateTo       = urlParams.get('date_to') || '';
        const actionType   = urlParams.get('action_type') || '';
        if (search)      filterMessages.push(`Product: <strong>"${search}"</strong>`);
        if (remarksSearch) filterMessages.push(`Remarks: <strong>"${remarksSearch}"</strong>`);
        if (dateFrom || dateTo) {
            const dr = [];
            if (dateFrom) dr.push(`From: <strong>${dateFrom}</strong>`);
            if (dateTo)   dr.push(`To: <strong>${dateTo}</strong>`);
            filterMessages.push(`Date: ${dr.join(' ')}`);
        }
        if (actionType) filterMessages.push(`Action: <strong>${actionType === 'IN' ? 'Stock In' : 'Stock Out'}</strong>`);
        const tableRows = document.querySelectorAll('.table tbody tr[data-log-type="active"]');
        let visibleCount = 0, hasDataRows = false;
        tableRows.forEach(row => { hasDataRows = true; if (row.style.display !== 'none') visibleCount++; });
        if (filterMessages.length > 0 && hasDataRows) {
            filterText.innerHTML = 'Active filters: ' + filterMessages.join(' | ');
            resultsCount.textContent = `(${visibleCount} log${visibleCount !== 1 ? 's' : ''} found)`;
            if (activeFiltersAlert.style.display === 'none') {
                activeFiltersAlert.style.display = 'flex';
                activeFiltersAlert.style.opacity = '0';
                setTimeout(() => { activeFiltersAlert.style.transition = 'opacity 0.3s ease'; activeFiltersAlert.style.opacity = '1'; }, 10);
            }
        } else {
            activeFiltersAlert.style.transition = 'opacity 0.3s ease';
            activeFiltersAlert.style.opacity = '0';
            setTimeout(() => { activeFiltersAlert.style.display = 'none'; }, 300);
        }
    }

    function initializeLiveSearch() {
        const productSearch = document.getElementById('productSearch');
        const remarksSearch = document.getElementById('remarksSearch');
        const dateFrom      = document.querySelector('input[name="date_from"]');
        const dateTo        = document.querySelector('input[name="date_to"]');
        const actionType    = document.querySelector('select[name="action_type"]');
        const tableRows     = document.querySelectorAll('.table tbody tr[data-log-type="active"]');
        if (!productSearch || !remarksSearch) return;
        const rowData = [];
        tableRows.forEach(row => {
            if (!row.querySelector('.log-id') && !row.querySelector('.product-name-cell')) return;
            if (row.dataset.logType !== 'active') { row.style.display = 'none'; return; }
            rowData.push({
                element:     row,
                productName: row.querySelector('.product-name-cell')?.textContent?.toLowerCase() || '',
                partNumber:  row.querySelector('.part-number-cell')?.textContent?.toLowerCase() || '',
                models:      row.querySelector('.models-cell')?.textContent?.toLowerCase() || '',
                remarks:     row.querySelector('.remarks-text')?.textContent?.toLowerCase() || '',
                action:      row.querySelector('.badge-action')?.textContent?.toLowerCase() || '',
                dateText:    row.querySelector('.date-text')?.textContent || '',
                get searchText() { return `${this.productName} ${this.partNumber} ${this.models}`; },
                get remarksText() { return this.remarks; }
            });
        });
        function debounce(func, wait) {
            let timeout;
            return function(...args) { clearTimeout(timeout); timeout = setTimeout(() => func(...args), wait); };
        }
        function filterRows() {
            const ps   = productSearch.value.toLowerCase().trim();
            const rs   = remarksSearch.value.toLowerCase().trim();
            const dfv  = dateFrom?.value;
            const dtv  = dateTo?.value;
            const atv  = actionType?.value;
            let visibleCount = 0;
            rowData.forEach(row => {
                let mp = true, mr = true, md = true, ma = true;
                if (ps) mp = row.searchText.includes(ps);
                if (rs) mr = row.remarksText.includes(rs);
                let logDate = null;
                try { const dp = row.dateText.split(' - '); if (dp.length >= 1) logDate = new Date(dp[0]); } catch(e){}
                if (dfv && logDate && !isNaN(logDate)) { const fd = new Date(dfv); fd.setHours(0,0,0,0); md = md && logDate >= fd; }
                if (dtv && logDate && !isNaN(logDate)) { const td = new Date(dtv); td.setHours(23,59,59,999); md = md && logDate <= td; }
                if (atv) { const am = {'IN':'stock in','OUT':'stock out'}; ma = row.action.includes(am[atv] || ''); }
                if (mp && mr && md && ma) { row.element.style.display = ''; visibleCount++; }
                else row.element.style.display = 'none';
            });
            updateLiveSearchUI(ps, rs, dfv, dtv, atv, visibleCount);
            updateEmptyState(visibleCount);
        }
        function updateLiveSearchUI(ps, rs, dfv, dtv, atv, count) {
            const alert = document.getElementById('activeFiltersAlert');
            const ft    = document.getElementById('filterText');
            const rc    = document.getElementById('resultsCount');
            let msgs = [];
            if (ps)  msgs.push(`Product: <strong>"${ps}"</strong>`);
            if (rs)  msgs.push(`Remarks: <strong>"${rs}"</strong>`);
            if (dfv || dtv) { const dr=[]; if(dfv) dr.push(`From: <strong>${dfv}</strong>`); if(dtv) dr.push(`To: <strong>${dtv}</strong>`); msgs.push(`Date: ${dr.join(' ')}`); }
            if (atv) msgs.push(`Action: <strong>${atv === 'IN' ? 'Stock In' : 'Stock Out'}</strong>`);
            if (msgs.length > 0) {
                ft.innerHTML = 'Live search: ' + msgs.join(' | ');
                rc.textContent = `(${count} log${count !== 1 ? 's' : ''} found)`;
                if (alert.style.display === 'none') { alert.style.display='flex'; alert.style.opacity='0'; setTimeout(()=>{ alert.style.transition='opacity 0.3s ease'; alert.style.opacity='1'; },10); }
            } else {
                alert.style.transition='opacity 0.3s ease'; alert.style.opacity='0';
                setTimeout(()=>{ alert.style.display='none'; },300);
            }
        }
        function updateEmptyState(visibleCount) {
            const tc = document.querySelector('.table-container');
            let es = tc.querySelector('.empty-state-live-search');
            if (visibleCount === 0 && rowData.length > 0) {
                if (!es) { es=document.createElement('div'); es.className='empty-state empty-state-live-search'; es.style.display='block'; es.innerHTML=`<div class="empty-state-icon">🔍</div><div class="empty-state-text">No matching logs found</div><p style="color: var(--gray-light); font-size: 0.875rem;">Try adjusting your search terms</p>`; tc.insertBefore(es, tc.querySelector('.table-responsive')); }
                es.style.display='block';
                const oes = tc.querySelector('.empty-state:not(.empty-state-live-search)');
                if (oes) oes.style.display='none';
            } else if (es) {
                es.style.display='none';
                if (visibleCount===0 && rowData.length===0) { const oes=tc.querySelector('.empty-state:not(.empty-state-live-search)'); if(oes) oes.style.display='block'; }
            }
        }
        const debouncedFilter = debounce(filterRows, 300);
        productSearch.addEventListener('input', debouncedFilter);
        remarksSearch.addEventListener('input', debouncedFilter);
        if (dateFrom)  dateFrom.addEventListener('change', debouncedFilter);
        if (dateTo)    dateTo.addEventListener('change', debouncedFilter);
        if (actionType) actionType.addEventListener('change', debouncedFilter);
        if (productSearch.value || remarksSearch.value || dateFrom?.value || dateTo?.value || actionType?.value) filterRows();
    }

    function initializeBulkSelection() {
        document.querySelectorAll('.log-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));
        updateSelectedCount();
    }

    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.log-checkbox').forEach(cb => {
            const row = cb.closest('tr');
            if (row.style.display !== 'none' && row.dataset.logType === 'active') cb.checked = checkbox.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked = Array.from(document.querySelectorAll('.log-checkbox:checked')).filter(cb => {
            const row = cb.closest('tr');
            return row.style.display !== 'none' && row.dataset.logType === 'active';
        });
        const btn     = document.getElementById('bulkArchiveBtn');
        const counter = document.getElementById('selectedCounter');
        const countEl = document.getElementById('selectedCount');
        const count   = checked.length;
        if (count > 0) {
            btn.disabled = false;
            btn.innerHTML = `🗃️ Archive Selected (${count})`;
            countEl.textContent = count;
            counter.style.display = 'flex'; counter.style.opacity = '1';
        } else {
            btn.disabled = true;
            btn.innerHTML = '🗃️ Archive Selected';
            counter.style.opacity = '0';
            setTimeout(() => { counter.style.display = 'none'; }, 300);
        }
        const allVisible = Array.from(document.querySelectorAll('.log-checkbox')).filter(cb => {
            const row = cb.closest('tr');
            return row.style.display !== 'none' && row.dataset.logType === 'active';
        });
        const selectAll = document.getElementById('selectAllCheckbox');
        if (allVisible.length > 0) {
            const allChecked = allVisible.every(cb => cb.checked);
            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && allVisible.some(cb => cb.checked);
        } else {
            selectAll.checked = false; selectAll.indeterminate = false;
        }
    }

    function clearSelection() {
        document.querySelectorAll('.log-checkbox:checked').forEach(cb => { cb.checked = false; });
        updateSelectedCount();
    }

    function archiveSelectedLogs() {
        const logIds = Array.from(document.querySelectorAll('.log-checkbox:checked')).filter(cb => {
            const row = cb.closest('tr');
            return row.style.display !== 'none' && row.dataset.logType === 'active';
        }).map(cb => cb.value);
        if (logIds.length === 0) { alert('No visible active logs selected!'); return; }
        if (confirm(`Are you sure you want to archive ${logIds.length} selected stock log(s)?\n\nIDs: ${logIds.slice(0, 10).join(', ')}${logIds.length > 10 ? '...' : ''}`)) {
            const btn = document.getElementById('bulkArchiveBtn');
            btn.innerHTML = '⏳ Archiving...'; btn.disabled = true;
            const form = document.createElement('form');
            form.method = 'POST'; form.action = 'index.php?page=stock_logs';
            <?php if(isset($_SESSION['csrf_token'])): ?>
            const ci = document.createElement('input'); ci.type='hidden'; ci.name='csrf_token'; ci.value='<?php echo $_SESSION["csrf_token"]; ?>'; form.appendChild(ci);
            <?php endif; ?>
            const ii = document.createElement('input'); ii.type='hidden'; ii.name='selected_log_ids'; ii.value=logIds.join(','); form.appendChild(ii);
            const ai = document.createElement('input'); ai.type='hidden'; ai.name='archive_selected_stock_logs'; ai.value='1'; form.appendChild(ai);
            document.body.appendChild(form); form.submit();
        }
    }

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            const sa = document.getElementById('selectAllCheckbox');
            if (sa) { sa.checked = !sa.checked; toggleSelectAll(sa); }
        }
        if (e.key === 'Escape') clearSelection();
    });

    window.addEventListener('DOMContentLoaded', function() {
        initializeBulkSelection();
        const urlParams = new URLSearchParams(window.location.search);
        const hasActiveFilters = urlParams.has('search') || urlParams.has('date_from') || urlParams.has('date_to') || urlParams.has('action_type') || urlParams.has('remarks_search');
        if (hasActiveFilters) { document.getElementById('filterCard').classList.add('show'); updateActiveFiltersDisplay(); }
        initializeLiveSearch();
    });

    window.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.alert-success').forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.3s ease'; alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        });
    });

    document.addEventListener('click', function(e) {
        const alert = e.target.closest('.alert-success');
        if (alert) { alert.style.transition = 'opacity 0.3s ease'; alert.style.opacity = '0'; setTimeout(() => alert.remove(), 300); }
    });
    </script>
    <script src="/assets/js/ws.js"></script>
    <?php if (isset($_SESSION['user_id'])): ?>
    <script src="assets/js/session-timeout.js"></script>
    <?php endif; ?>
</body>
</html>