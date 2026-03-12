<?php
if (isset($_POST['archive_visible_activity_logs']) && isset($_POST['visible_log_ids'])) {
    try {
        $pdo->beginTransaction();
        
        $log_ids = trim($_POST['visible_log_ids']);
        
        if (empty($log_ids)) {
            header("Location: index.php?page=activity_logs&error=no_ids");
            exit();
        }
        
        $log_ids_array = explode(',', $log_ids);
        $log_ids_array = array_filter($log_ids_array, function($id) {
            return is_numeric(trim($id)) && $id > 0;
        });
        
        if (empty($log_ids_array)) {
            header("Location: index.php?page=activity_logs&error=invalid_ids");
            exit();
        }
        
        $log_ids_array  = array_unique($log_ids_array);
        $placeholders   = implode(',', array_fill(0, count($log_ids_array), '?'));
        
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs WHERE id IN ($placeholders) AND is_deleted = 0");
        $count_stmt->execute($log_ids_array);
        $count = $count_stmt->fetchColumn();
        
        if ($count > 0) {
            $archive_stmt = $pdo->prepare("UPDATE activity_logs SET deleted_at = NOW(), is_deleted = 1 WHERE id IN ($placeholders) AND is_deleted = 0");
            $archive_stmt->execute($log_ids_array);
            $affected_rows = $archive_stmt->rowCount();
            
            add_activity_log(
                $pdo,
                $user_id,
                null,
                "Archived {$affected_rows} activity logs (IDs: " . implode(', ', array_slice($log_ids_array, 0, 10)) . (count($log_ids_array) > 10 ? '...' : '') . ")"
            );
        }
        
        $pdo->commit();
        header("Location: index.php?page=activity_logs&bulk_archived=" . ($count ?? 0));
        exit();
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Error archiving visible activity logs: " . $e->getMessage());
        header("Location: index.php?page=activity_logs&error=archive_failed");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Grease Monkey</title>
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

        .navbar-brand-custom { display: flex; align-items: center; gap: 1rem; text-decoration: none; }

        .logo-img {
            width: 50px; height: 50px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(255, 215, 0, 0.4));
        }

        .brand-text h1 { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin: 0; line-height: 1; }
        .brand-text p  { color: rgba(255, 215, 0, 0.6); font-size: 0.7rem; font-weight: 500; margin: 0; }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600; font-size: 0.95rem;
            padding: 0.5rem 1.25rem !important;
            border-radius: 8px; transition: all 0.3s ease;
            text-decoration: none; background: transparent !important;
        }
        .nav-link-custom:hover { color: var(--primary) !important; background: rgba(255, 215, 0, 0.1) !important; }
        .nav-link-custom.active {
            color: #000 !important;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            box-shadow: 0 2px 12px rgba(255, 215, 0, 0.3);
        }

        .user-info {
            color: rgba(255, 255, 255, 0.7); font-size: 0.875rem;
            margin-right: 1rem; padding-right: 1rem;
            border-right: 1px solid rgba(255, 215, 0, 0.2);
            background: transparent !important;
        }
        .user-info strong { color: var(--primary); }

        .btn-logout {
            background: transparent; border: 1px solid var(--primary); color: var(--primary);
            font-weight: 600; padding: 0.5rem 1.5rem; border-radius: 8px;
            transition: all 0.3s ease; font-size: 0.875rem; text-decoration: none; display: inline-block;
        }
        .btn-logout:hover { background: var(--primary); color: #000; transform: translateY(-2px); }

        /* ── Mobile Top Bar ─────────────────────────────────────────────── */
        .mobile-topbar {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; z-index: 1100;
            background: rgba(26,26,26,.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,215,0,.3);
            padding: .75rem 1rem;
            align-items: center; justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,.5);
        }
        .mobile-brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; }

        .hamburger-btn {
            background: transparent; border: 1px solid rgba(255,215,0,.4); border-radius: 8px;
            color: var(--primary); width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1.2rem; transition: all .3s ease; flex-shrink: 0;
        }
        .hamburger-btn:hover { background: rgba(255,215,0,.1); border-color: var(--primary); }

        /* ── Sidebar Overlay ────────────────────────────────────────────── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0; z-index: 1200;
            background: rgba(0,0,0,.7); backdrop-filter: blur(4px);
            opacity: 0; transition: opacity .3s ease;
        }
        .sidebar-overlay.active { opacity: 1; }

        /* ── Mobile Sidebar ─────────────────────────────────────────────── */
        .mobile-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 1300;
            width: 280px; background: rgba(15,15,15,.98);
            border-right: 1px solid rgba(255,215,0,.3);
            box-shadow: 4px 0 30px rgba(0,0,0,.8);
            transform: translateX(-100%);
            transition: transform .35s cubic-bezier(.4,0,.2,1);
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .mobile-sidebar.open { transform: translateX(0); }

        .sidebar-header {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,215,0,.15);
            display: flex; align-items: center; justify-content: space-between;
        }
        .sidebar-close {
            background: rgba(255,68,68,.1); border: 1px solid rgba(255,68,68,.3);
            border-radius: 8px; color: var(--danger); width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1.1rem; transition: all .2s ease;
        }
        .sidebar-close:hover { background: rgba(255,68,68,.2); }

        .sidebar-user {
            padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,215,0,.1);
            background: rgba(255,215,0,.03);
        }
        .sidebar-user-label { font-size: .7rem; color: var(--gray-light); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .25rem; }
        .sidebar-user-name  { font-size: 1rem; font-weight: 700; color: var(--primary); }

        .sidebar-nav { padding: 1rem .75rem; flex: 1; }
        .sidebar-nav-item {
            display: flex; align-items: center; gap: .875rem;
            padding: .875rem 1rem; border-radius: 10px;
            color: rgba(255,255,255,.75); text-decoration: none;
            font-weight: 600; font-size: .95rem; transition: all .25s ease;
            margin-bottom: .25rem; position: relative;
        }
        .sidebar-nav-item:hover { background: rgba(255,215,0,.08); color: var(--primary); transform: translateX(4px); }
        .sidebar-nav-item.active {
            background: linear-gradient(135deg, rgba(255,215,0,.2) 0%, rgba(184,134,11,.1) 100%);
            color: var(--primary); border: 1px solid rgba(255,215,0,.25);
        }
        .sidebar-nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; background: var(--primary); border-radius: 0 3px 3px 0;
        }
        .sidebar-nav-icon { font-size: 1.1rem; width: 24px; text-align: center; flex-shrink: 0; }

        .sidebar-footer { padding: 1rem .75rem; border-top: 1px solid rgba(255,215,0,.1); }
        .sidebar-logout {
            display: flex; align-items: center; gap: .875rem;
            padding: .875rem 1rem; border-radius: 10px;
            background: rgba(255,68,68,.08); border: 1px solid rgba(255,68,68,.25);
            color: var(--danger); text-decoration: none;
            font-weight: 600; font-size: .95rem; transition: all .25s ease;
        }
        .sidebar-logout:hover { background: rgba(255,68,68,.15); transform: translateX(4px); color: var(--danger); }

        /* ── Main Content ───────────────────────────────────────────────── */
        .main-content {
            margin-top: 100px; padding: 2rem;
            position: relative; z-index: 10; background: transparent !important;
            max-width: 1600px; margin-left: auto; margin-right: auto;
        }

        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 2rem; animation: fadeInUp 0.6s ease-out;
            background: transparent !important; flex-wrap: wrap; gap: 1.5rem;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .page-title-section h1 { font-size: 2rem; font-weight: 800; color: var(--primary); margin: 0 0 0.5rem 0; }
        .page-title-section p  { color: var(--gray-light); margin: 0; font-size: 0.875rem; }

        .header-actions { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }

        .search-wrapper { position: relative; }

        .search-input {
            padding: 0.875rem 3rem 0.875rem 1rem;
            background: var(--card-bg); border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px; color: #fff; font-size: 0.95rem;
            transition: all 0.3s ease; font-weight: 500; min-width: 250px;
        }
        .search-input:focus { outline: none; background: var(--card-bg); border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1); }
        .search-input::placeholder { color: var(--gray-light); }

        .search-icon {
            position: absolute; right: 1rem; top: 50%;
            transform: translateY(-50%); color: var(--primary);
            font-size: 1.125rem; pointer-events: none;
        }

        .btn-toggle-filter {
            background: rgba(255, 215, 0, 0.1); border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--primary); font-weight: 600; padding: 0.875rem 1.5rem;
            border-radius: 12px; transition: all 0.3s ease; font-size: 0.875rem;
            cursor: pointer; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;
        }
        .btn-toggle-filter:hover { background: rgba(255, 215, 0, 0.2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3); }

        .btn-history-sm {
            background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger); font-weight: 600; padding: 0.875rem 1.5rem;
            border-radius: 12px; transition: all 0.3s ease; font-size: 0.875rem;
            cursor: pointer; display: flex; align-items: center; gap: 0.5rem;
            text-decoration: none; white-space: nowrap;
        }
        .btn-history-sm:hover { background: rgba(255, 68, 68, 0.2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3); }

        .btn-secondary {
            background: rgba(255, 215, 0, 0.1); border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--primary); font-weight: 600; padding: 0.875rem 1.5rem;
            border-radius: 12px; transition: all 0.3s ease; font-size: 0.875rem;
            display: flex; align-items: center; gap: 0.5rem; text-decoration: none; cursor: pointer;
        }
        .btn-secondary:hover { background: rgba(255, 215, 0, 0.2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3); color: var(--primary); }

        .alert-success {
            background: rgba(50, 205, 50, 0.1) !important;
            border: 1px solid rgba(50, 205, 50, 0.3) !important;
            color: var(--success) !important;
            border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 2rem;
            display: flex; align-items: center; gap: 0.75rem;
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
            border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
        }

        .filter-card {
            background: var(--card-bg); border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;
            display: none; transition: all 0.3s ease;
        }
        .filter-card.show { display: block; animation: slideDown 0.3s ease; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-label { color: var(--primary); font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; }

        .form-control {
            background: var(--card-bg); border: 1px solid rgba(255, 215, 0, 0.2);
            color: #fff; border-radius: 12px; padding: 0.75rem; font-size: 0.95rem; transition: all 0.3s ease;
        }
        .form-control:focus { background: var(--card-bg); border-color: var(--primary); color: #fff; box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1); outline: none; }
        .form-control::placeholder { color: var(--gray-light); }

        .btn-filter {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #000; font-weight: 700; border: none; border-radius: 12px;
            padding: 0.875rem 1.5rem; transition: all 0.3s ease; width: 100%; cursor: pointer;
        }
        .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4); }

        .btn-clear {
            background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger); font-weight: 600; padding: 0.875rem 1.5rem;
            border-radius: 12px; transition: all 0.3s ease; text-decoration: none;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.875rem; width: 100%;
        }
        .btn-clear:hover { background: rgba(255, 68, 68, 0.2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3); }

        .table-container {
            background: var(--card-bg); border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); min-height: 300px;
        }

        .table {
            color: var(--gray-light) !important; margin: 0;
            background: transparent !important; border-collapse: separate;
            border-spacing: 0; width: 100%;
        }

        .table thead th {
            background: rgba(255, 215, 0, 0.1) !important;
            color: var(--primary) !important; font-weight: 700;
            text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;
            padding: 1rem; border: none !important; white-space: nowrap;
            position: sticky; top: 0; z-index: 10;
        }

        .table thead th:nth-child(1) { width: 50px; text-align: center; }
        .table thead th:nth-child(2) { width: 80px; }
        .table thead th:nth-child(3) { width: auto; min-width: 300px; }
        .table thead th:nth-child(4) { width: 180px; }
        .table thead th:nth-child(5) { width: 120px; }

        .table tbody td {
            padding: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            vertical-align: middle; font-size: 0.875rem;
            background: transparent !important; color: var(--gray-light) !important; line-height: 1.4;
        }
        .table tbody tr:hover    { background: rgba(255, 215, 0, 0.05) !important; }
        .table tbody tr:last-child td { border-bottom: none !important; }

        .log-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); }
        .log-id       { color: var(--primary); font-weight: 800; font-size: 0.9rem; white-space: nowrap; }
        .activity-text { color: #fff; font-size: 0.9rem; font-weight: 500; line-height: 1.5; word-wrap: break-word; overflow-wrap: break-word; }
        .date-text     { color: var(--gray-light); font-size: 0.85rem; white-space: nowrap; }

        .btn-delete {
            background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--danger); font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 8px;
            transition: all 0.3s ease; font-size: 0.75rem; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.25rem; min-width: 80px; justify-content: center;
        }
        .btn-delete:hover { background: rgba(255, 68, 68, 0.2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3); }

        .btn-restore {
            background: rgba(50, 205, 50, 0.1); border: 1px solid rgba(50, 205, 50, 0.3);
            color: var(--success); font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 8px;
            transition: all 0.3s ease; font-size: 0.75rem; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.25rem; min-width: 80px; justify-content: center;
        }
        .btn-restore:hover { background: rgba(50, 205, 50, 0.2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(50, 205, 50, 0.3); }

        .btn-permanent-delete {
            background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.5);
            color: var(--danger); font-weight: 700; padding: 0.5rem 0.75rem; border-radius: 8px;
            transition: all 0.3s ease; font-size: 0.75rem; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.25rem; min-width: 80px; justify-content: center;
        }
        .btn-permanent-delete:hover { background: rgba(255, 68, 68, 0.3); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3); }

        #bulkArchiveBtn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }
        #bulkArchiveBtn:disabled:hover { background: rgba(255, 215, 0, 0.1); transform: none !important; box-shadow: none !important; }

        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--gray-light); background: transparent !important; }
        .empty-state-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; color: var(--gray-medium); }
        .empty-state-text { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-light); }

        .selected-counter {
            background: rgba(255, 215, 0, 0.1); border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--primary); border-radius: 12px; padding: 0.75rem 1.25rem;
            margin-bottom: 1rem; display: none; align-items: center; gap: 0.75rem;
            animation: slideIn 0.3s ease-out;
        }

        .modal-content { background: var(--card-bg); border: 1px solid rgba(255, 215, 0, 0.3); border-radius: 16px; color: #fff; }
        .modal-header  { border-bottom: 1px solid rgba(255, 215, 0, 0.2); padding: 1.5rem; background: var(--card-bg); position: sticky; top: 0; z-index: 1055; }
        .modal-title   { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
        .btn-close     { filter: brightness(0) saturate(100%) invert(80%) sepia(58%) saturate(458%) hue-rotate(358deg); }
        .modal-body    { padding: 1.5rem; max-height: 70vh; overflow-y: auto; }

        .modal-table-container { max-height: 50vh; overflow-y: auto; border-radius: 8px; border: 1px solid rgba(255, 215, 0, 0.2); }
        .modal-table-container .table { margin: 0; }
        .modal-table-container .table thead th {
            position: sticky; top: 0; z-index: 10;
            background: var(--card-bg) !important;
            border-bottom: 2px solid rgba(255, 215, 0, 0.4) !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.55);
        }

        /* ── Pagination ─────────────────────────────────────────────────── */
        .pagination-wrapper {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(255,215,0,.1);
            flex-wrap: wrap; gap: 1rem;
        }

        .pagination-info { color: var(--gray-light); font-size: 0.875rem; }
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

        .per-page-select {
            background: var(--card-bg); border: 1px solid rgba(255,215,0,.2);
            color: #fff; border-radius: 8px; padding: .4rem .75rem;
            font-size: 0.8rem; cursor: pointer;
        }
        .per-page-select:focus { outline: none; border-color: var(--primary); }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar-custom  { display: none !important; }
            .mobile-topbar  { display: flex; }
            .sidebar-overlay { display: block; pointer-events: none; }
            .sidebar-overlay.active { pointer-events: all; }

            .main-content   { margin-top: 68px !important; padding: 1rem; }
            .page-header    { flex-direction: column; align-items: stretch; }
            .page-title-section h1 { font-size: 1.5rem; }

            .header-actions { flex-direction: column; width: 100%; }

            .btn-toggle-filter,
            .btn-history-sm,
            .btn-secondary  { width: 100%; justify-content: center; }

            .search-input   { min-width: 100%; }
            .table-container { overflow-x: auto; }
            .activity-text  { font-size: 0.85rem; }
            .date-text      { font-size: 0.8rem; }
        }

        @media (max-width: 1200px) {
            .table { table-layout: auto; }
            .table thead th:nth-child(n) { width: auto; }
        }

        @media (max-width: 480px) {
            .page-title-section h1 { font-size: 1.75rem; }
            .header-actions { gap: 0.5rem; }
            .btn-delete, .btn-restore, .btn-permanent-delete {
                width: 100%; margin-bottom: 0.25rem; font-size: 0.7rem; padding: 0.4rem 0.5rem;
            }
            .table thead th, .table tbody td { padding: 0.75rem 0.5rem; }
        }
    </style>
</head>
<body>

    <!-- ── Desktop Navbar ──────────────────────────────────────────────── -->
    <nav class="navbar-custom navbar navbar-expand-lg">
        <div class="container-fluid">
            <a href="index.php?page=home" class="navbar-brand-custom">
                <img src="assets/images/logo.png" alt="Logo" class="logo-img">
                <div class="brand-text"><h1>Grease Monkey</h1><p>Inventory System</p></div>
            </a>
            <div class="collapse navbar-collapse show" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a href="index.php?page=dashboard"       class="nav-link-custom">📊 Dashboard</a></li>
                    <li class="nav-item"><a href="index.php?page=manage_products" class="nav-link-custom">🛠️ Products</a></li>
                    <li class="nav-item"><a href="index.php?page=manage_stocks"   class="nav-link-custom">📦 Stock</a></li>
                    <li class="nav-item"><a href="index.php?page=stock_logs"      class="nav-link-custom">📋 Logs</a></li>
                    <li class="nav-item"><a href="index.php?page=activity_logs"   class="nav-link-custom active">📝 Activity</a></li>
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
        <a href="index.php?page=home" class="mobile-brand">
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
            <a href="index.php?page=home" class="navbar-brand-custom" style="text-decoration:none;">
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
            <a href="index.php?page=stock_logs"      class="sidebar-nav-item"><span class="sidebar-nav-icon">📋</span> Logs</a>
            <a href="index.php?page=activity_logs"   class="sidebar-nav-item active"><span class="sidebar-nav-icon">📝</span> Activity</a>
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
                    <h1>📝 Activity Logs</h1>
                    <p>Monitor all user activities</p>
                </div>
                <div class="header-actions">
                    <?php
                    $printUrl = "index.php?page=print_activity_logs"
                        . "&search="    . urlencode(trim($_GET['search']    ?? ''))
                        . "&date_from=" . urlencode(trim($_GET['date_from'] ?? ''))
                        . "&date_to="   . urlencode(trim($_GET['date_to']   ?? ''))
                        . "&log=1";
                    ?>
                    <a href="<?= $printUrl ?>" target="_blank" class="btn-secondary"
                       onclick="return confirm('This will generate an activity logs report. Continue?');">
                        🖨️ Print Report
                    </a>
                    <button class="btn-toggle-filter" onclick="toggleFilter()">
                        🔍 Filter
                    </button>
                    <button type="button" class="btn-toggle-filter" data-bs-toggle="modal" data-bs-target="#deletedHistoryModal">
                        🗑️ Show Archived Logs
                    </button>
                    <button type="button" class="btn-history-sm" onclick="archiveSelectedLogs()" id="bulkArchiveBtn" disabled>
                        🗃️ Archive Selected
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Successfully archived <?php echo htmlspecialchars($_GET['deleted']); ?> Activity log.</div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['restored'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Successfully restored <?php echo htmlspecialchars($_GET['restored']); ?> Activity log.</div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['permanently_deleted'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Activity log #<?php echo htmlspecialchars($_GET['permanently_deleted']); ?> has been permanently deleted.</div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['bulk_archived'])): ?>
                <div class="alert-success">
                    <span style="font-size: 1.25rem;">✅</span>
                    <div>Successfully archived <?php echo htmlspecialchars($_GET['bulk_archived']); ?> activity log(s).</div>
                </div>
            <?php endif; ?>

            <!-- Selected Counter -->
            <div id="selectedCounter" class="selected-counter" style="display: none;">
                <span style="font-size: 1.25rem;">📋</span>
                <div>
                    <span id="selectedCount">0</span> log(s) selected for bulk action
                    <button type="button" class="btn-history-sm" onclick="clearSelection()" style="margin-left: 1rem; padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                        Clear Selection
                    </button>
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

            <!-- Filter Card -->
            <div class="filter-card" id="filterCard">
                <form method="GET" action="index.php" id="filterForm">
                    <input type="hidden" name="page" value="activity_logs">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Search Activity</label>
                            <div class="search-wrapper">
                                <input type="text" name="search" class="form-control search-input"
                                       placeholder="Search by activity content..." autocomplete="off"
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                       id="searchInput">
                                <span class="search-icon">🔍</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" class="form-control"
                                   value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" class="form-control"
                                   value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn-filter">Apply</button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="index.php?page=activity_logs" class="btn-clear">Clear All Filters</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <?php if (empty($activity_logs)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📝</div>
                        <div class="empty-state-text">No activity logs found</div>
                        <p style="color: var(--gray-light); font-size: 0.875rem;">User activities will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table" id="logsTable">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                    </th>
                                    <th>ID</th>
                                    <th>Activity</th>
                                    <th>Date &amp; Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activity_logs as $log):
                                    $searchText = strtolower($log['id'] . ' ' . $log['activity']);
                                ?>
                                    <tr data-search="<?php echo htmlspecialchars($searchText); ?>">
                                        <td class="align-middle text-center">
                                            <input type="checkbox" class="log-checkbox" name="log_ids[]" value="<?php echo $log['id']; ?>"
                                                   onchange="updateSelectedCount()">
                                        </td>
                                        <td class="align-middle">
                                            <strong class="log-id">#<?php echo $log['id']; ?></strong>
                                        </td>
                                        <td class="align-middle">
                                            <span class="activity-text"><?php echo htmlspecialchars($log['activity']); ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="date-text"><?php echo date('M d, Y - h:i A', strtotime($log['created_at'])); ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to archive this activity log?');">
                                                <input type="hidden" name="id" value="<?php echo $log['id']; ?>">
                                                <button type="submit" name="delete_activity_log" class="btn-delete">Archive</button>
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
                    $total_logs   = $totalActivity_logs;                           // ← FIXED
                    $total_pages  = $totalActivity_pages;                         // ← FIXED
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
                                $start_page = max(1, $current_page - 2);
                                $end_page   = min($total_pages, $current_page + 2);
                            ?>
                            <?php if ($current_page > 1): ?>
                                <a href="?<?= $base_query ?>&page_num=1&per_page=<?= $per_page ?>" class="page-btn" title="First">«</a>
                                <a href="?<?= $base_query ?>&page_num=<?= $current_page - 1 ?>&per_page=<?= $per_page ?>" class="page-btn">‹ Prev</a>
                            <?php else: ?>
                                <button class="page-btn" disabled>«</button>
                                <button class="page-btn" disabled>‹ Prev</button>
                            <?php endif; ?>

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
                    <h5 class="modal-title" id="deletedHistoryModalLabel">🗑️ Archived Activity Logs History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($deleted_activity_logs)): ?>
                        <div class="mb-3 d-flex justify-content-end gap-2">
                            <form method="POST" onsubmit="return confirm('❗CAUTION: This will RESTORE all archived activity logs.');">
                                <button type="submit" name="restore_all_activity_logs" class="btn-restore">
                                    Restore All Logs
                                </button>
                            </form>
                            <form method="POST" onsubmit="return confirm('⚠️ CRITICAL WARNING: This will PERMANENTLY delete ALL deleted logs. This action CANNOT be undone. Are you absolutely sure you want to proceed?');">
                                <button type="submit" name="permanent_delete_all_activity_logs" class="btn-permanent-delete">
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
                                    <th>Activity</th>
                                    <th>Original Date</th>
                                    <th>Archived At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($deleted_activity_logs)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5" style="color: var(--gray-light);">
                                            <div class="empty-state-icon">🗑️</div>
                                            <div class="empty-state-text">No archived activity logs found</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($deleted_activity_logs as $log): ?>
                                        <tr>
                                            <td class="align-middle"><strong class="log-id">#<?php echo $log['id']; ?></strong></td>
                                            <td class="align-middle">
                                                <span class="activity-text"><?php echo htmlspecialchars($log['activity']); ?></span>
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
                                                        <button type="submit" name="restore_activity_log" class="btn-restore w-100">Restore</button>
                                                    </form>
                                                    <form method="POST" onsubmit="return confirm('⚠️ WARNING: This will PERMANENTLY delete this log. This action cannot be undone. Are you absolutely sure?');">
                                                        <input type="hidden" name="id" value="<?php echo $log['id']; ?>">
                                                        <button type="submit" name="permanent_delete_activity_log" class="btn-permanent-delete w-100">Delete</button>
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

    // ── All existing JS logic (unchanged) ───────────────────────────────────
    function toggleFilter() {
        document.getElementById('filterCard').classList.toggle('show');
    }

    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const hasActiveFilters = urlParams.has('search') || urlParams.has('date_from') || urlParams.has('date_to');
        if (hasActiveFilters) {
            document.getElementById('filterCard').classList.add('show');
            updateActiveFiltersDisplay();
        }
        initializeLiveSearch();
        initializeBulkArchive();
    });

    function updateActiveFiltersDisplay() {
        const urlParams          = new URLSearchParams(window.location.search);
        const activeFiltersAlert = document.getElementById('activeFiltersAlert');
        const filterText         = document.getElementById('filterText');
        const resultsCount       = document.getElementById('resultsCount');
        let filterMessages = [];
        const search   = urlParams.get('search')    || '';
        const dateFrom = urlParams.get('date_from') || '';
        const dateTo   = urlParams.get('date_to')   || '';
        if (search) filterMessages.push(`Search: <strong>"${search}"</strong>`);
        if (dateFrom || dateTo) {
            const dr = [];
            if (dateFrom) dr.push(`From: <strong>${dateFrom}</strong>`);
            if (dateTo)   dr.push(`To: <strong>${dateTo}</strong>`);
            filterMessages.push(`Date: ${dr.join(' ')}`);
        }
        const tableRows = document.querySelectorAll('#logsTable tbody tr');
        let visibleCount = 0, hasDataRows = false;
        tableRows.forEach(row => {
            if (row.querySelector('.log-id')) {
                hasDataRows = true;
                if (row.style.display !== 'none') visibleCount++;
            }
        });
        if (filterMessages.length > 0 && hasDataRows) {
            filterText.innerHTML = 'Active filters: ' + filterMessages.join(' | ');
            resultsCount.textContent = `(${visibleCount} log${visibleCount !== 1 ? 's' : ''} found)`;
            if (activeFiltersAlert.style.display === 'none') {
                activeFiltersAlert.style.display = 'flex'; activeFiltersAlert.style.opacity = '0';
                setTimeout(() => { activeFiltersAlert.style.transition = 'opacity 0.3s ease'; activeFiltersAlert.style.opacity = '1'; }, 10);
            }
        } else {
            activeFiltersAlert.style.transition = 'opacity 0.3s ease'; activeFiltersAlert.style.opacity = '0';
            setTimeout(() => { activeFiltersAlert.style.display = 'none'; }, 300);
        }
    }

    function initializeLiveSearch() {
        const searchInput = document.getElementById('searchInput');
        const tableRows   = document.querySelectorAll('#logsTable tbody tr');
        if (!searchInput) return;
        const rowData = [];
        tableRows.forEach(row => {
            if (!row.querySelector('.log-id')) return;
            rowData.push({
                element:    row,
                searchText: (row.getAttribute('data-search') || '').toLowerCase()
            });
        });
        function debounce(func, wait) {
            let timeout;
            return function(...args) { clearTimeout(timeout); timeout = setTimeout(() => func(...args), wait); };
        }
        function filterRows() {
            const term = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            rowData.forEach(row => {
                const match = !term || row.searchText.includes(term);
                row.element.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            updateLiveSearchUI(term, visibleCount);
            updateEmptyState(visibleCount);
            updateSelectedCount();
        }
        function updateLiveSearchUI(term, count) {
            const alert = document.getElementById('activeFiltersAlert');
            const ft    = document.getElementById('filterText');
            const rc    = document.getElementById('resultsCount');
            if (term) {
                ft.innerHTML = `Live search: <strong>"${term}"</strong>`;
                rc.textContent = `(${count} log${count !== 1 ? 's' : ''} found)`;
                if (alert.style.display === 'none') {
                    alert.style.display = 'flex'; alert.style.opacity = '0';
                    setTimeout(() => { alert.style.transition = 'opacity 0.3s ease'; alert.style.opacity = '1'; }, 10);
                }
            } else {
                alert.style.transition = 'opacity 0.3s ease'; alert.style.opacity = '0';
                setTimeout(() => { alert.style.display = 'none'; }, 300);
            }
        }
        function updateEmptyState(visibleCount) {
            const tc = document.querySelector('.table-container');
            let es   = tc.querySelector('.empty-state-live-search');
            if (visibleCount === 0 && rowData.length > 0) {
                if (!es) {
                    es = document.createElement('div');
                    es.className = 'empty-state empty-state-live-search';
                    es.style.display = 'block';
                    es.innerHTML = `<div class="empty-state-icon">🔍</div><div class="empty-state-text">No matching logs found</div><p style="color: var(--gray-light); font-size: 0.875rem;">Try adjusting your search terms</p>`;
                    tc.insertBefore(es, tc.querySelector('.table-responsive'));
                }
                es.style.display = 'block';
            } else if (es) {
                es.style.display = 'none';
            }
        }
        const debouncedFilter = debounce(filterRows, 300);
        searchInput.addEventListener('input', debouncedFilter);
        const clearBtn = document.querySelector('.btn-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                if (e.target.closest('form')) return;
                searchInput.value = '';
                filterRows();
            });
        }
        if (searchInput.value) filterRows();
    }

    function initializeBulkArchive() {
        document.querySelectorAll('.log-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));
        updateSelectedCount();
    }

    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.log-checkbox').forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') cb.checked = checkbox.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked    = document.querySelectorAll('.log-checkbox:checked');
        const btn        = document.getElementById('bulkArchiveBtn');
        const counter    = document.getElementById('selectedCounter');
        const countEl    = document.getElementById('selectedCount');
        const count      = checked.length;
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
        const allVisible     = Array.from(document.querySelectorAll('.log-checkbox')).filter(cb => cb.closest('tr').style.display !== 'none');
        const selectAll      = document.getElementById('selectAllCheckbox');
        if (allVisible.length > 0) {
            const allChecked     = allVisible.every(cb => cb.checked);
            selectAll.checked    = allChecked;
            selectAll.indeterminate = !allChecked && allVisible.some(cb => cb.checked);
        }
    }

    function clearSelection() {
        document.querySelectorAll('.log-checkbox:checked').forEach(cb => { cb.checked = false; });
        updateSelectedCount();
    }

    function archiveSelectedLogs() {
        const logIds = Array.from(document.querySelectorAll('.log-checkbox:checked')).map(cb => cb.value);
        if (logIds.length === 0) return;
        if (confirm(`Are you sure you want to archive ${logIds.length} selected activity log(s)?`)) {
            const form = document.createElement('form');
            form.method = 'POST'; form.style.display = 'none';
            const ai = document.createElement('input'); ai.type='hidden'; ai.name='archive_visible_activity_logs'; ai.value='1'; form.appendChild(ai);
            const ii = document.createElement('input'); ii.type='hidden'; ii.name='visible_log_ids'; ii.value=logIds.join(','); form.appendChild(ii);
            document.body.appendChild(form); form.submit();
        }
    }

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
    <script src="/inventory/assets/js/ws.js"></script>
    <?php if (isset($_SESSION['user_id'])): ?>
    <script src="assets/js/session-timeout.js"></script>
<?php endif; ?>
</body>
</html>