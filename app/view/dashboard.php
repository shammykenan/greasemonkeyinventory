<?php
if(!isset($_SESSION['user_id'])){
    header("Location: index.php?page=landing_page");
    exit();
}
$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Grease Monkey</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/logo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #FFD700; --primary-dark: #B8860B; --accent: #FFE55C;
            --success: #32CD32; --danger: #FF4444; --warning: #FFA500;
            --dark-bg: #0a0a0a; --card-bg: #1a1a1a;
            --gray-dark: #333333; --gray-medium: #666666; --gray-light: #999999;
        }
        body { font-family: 'Inter', sans-serif; background: #000 !important; min-height: 100vh; overflow-x: hidden; color: #fff !important; }
        body::before { content: ''; position: fixed; inset: 0; background: radial-gradient(circle at 20% 50%, rgba(255,215,0,.08) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(255,215,0,.08) 0%, transparent 50%); pointer-events: none; z-index: 1; }

        /* ── Desktop Navbar ─────────────────────────────────────────────── */
        .navbar-custom { background: rgba(26,26,26,.98) !important; backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,215,0,.3); padding: 1rem 0; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,.5); }
        .navbar-brand-custom { display: flex; align-items: center; gap: 1rem; text-decoration: none; }
        .logo-img { width: 50px; height: 50px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(255,215,0,.4)); }
        .brand-text h1 { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin: 0; line-height: 1; }
        .brand-text p { color: rgba(255,215,0,.6); font-size: .7rem; font-weight: 500; margin: 0; }
        .nav-link-custom { color: rgba(255,255,255,.8) !important; font-weight: 600; font-size: .95rem; padding: .5rem 1.25rem !important; border-radius: 8px; transition: all .3s ease; text-decoration: none; background: transparent !important; }
        .nav-link-custom:hover { color: var(--primary) !important; background: rgba(255,215,0,.1) !important; }
        .nav-link-custom.active { color: #000 !important; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important; box-shadow: 0 2px 12px rgba(255,215,0,.3); }
        .user-info { color: rgba(255,255,255,.7); font-size: .875rem; margin-right: 1rem; padding-right: 1rem; border-right: 1px solid rgba(255,215,0,.2); }
        .user-info strong { color: var(--primary); }
        .btn-logout { background: transparent; border: 1px solid var(--primary); color: var(--primary); font-weight: 600; padding: .5rem 1.5rem; border-radius: 8px; transition: all .3s ease; font-size: .875rem; text-decoration: none; display: inline-block; }
        .btn-logout:hover { background: var(--primary); color: #000; transform: translateY(-2px); }

        /* ── Backup Button — teal/cyan palette ──────────────────────────── */
        .backup-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, #FFD700, #B8860B);
            border: 1px solid rgba(173, 184, 20, 0.45);
            color: #000000;
            padding: 0.55rem 1.1rem;
            font-size: 0.82rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.25s ease;
            white-space: nowrap;
            letter-spacing: 0.3px;
        }
        .backup-btn .backup-icon { font-size: 1rem; line-height: 1; }
        .backup-btn:hover {
            background: linear-gradient(135deg, #FFD700, #B8860B);
            border-color: #rgba;
            color: #fff;
            box-shadow: 0 0 22px rgba(176, 184, 20, 0.45);
            transform: translateY(-2px);
        }
        .backup-btn:active { transform: translateY(0); }
        .backup-btn.loading {
            opacity: 0.65;
            pointer-events: none;
            cursor: not-allowed;
        }
        .backup-error-toast {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.3);
            color: #fca5a5;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-top: 0.5rem;
        }

        /* ── Backup Confirm Modal ────────────────────────────────────────── */
        .bkp-overlay {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .bkp-overlay.active { opacity: 1; pointer-events: all; }

        .bkp-modal {
            background: #000000;
            border: 1px solid rgba(160, 184, 20, 0.35);
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 0 60px rgba(165, 184, 20, 0.2), 0 25px 50px rgba(0,0,0,0.8);
            transform: translateY(20px) scale(0.96);
            transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease;
            opacity: 0;
            overflow: hidden;
        }
        .bkp-overlay.active .bkp-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* top accent bar */
        .bkp-modal::before {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, #FFD700, #B8860B, #000000);
            background-size: 200% 100%;
            animation: bkpShine 2.5s linear infinite;
        }
        @keyframes bkpShine {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .bkp-modal-inner { padding: 1.75rem 1.75rem 1.5rem; }

        .bkp-icon-wrap {
            width: 64px; height: 64px;
            border-radius: 16px;
            background: rgba(20,184,166,0.1);
            border: 1px solid rgba(20,184,166,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.25rem;
            animation: bkpPulse 2s ease-in-out infinite;
        }
        @keyframes bkpPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(20,184,166,0.3); }
            50%      { box-shadow: 0 0 0 10px rgba(20,184,166,0); }
        }

        .bkp-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            text-align: center;
            margin: 0 0 0.5rem;
        }
        .bkp-subtitle {
            font-size: 0.82rem;
            color: #64748b;
            text-align: center;
            margin: 0 0 1.5rem;
            line-height: 1.6;
        }

        .bkp-info-box {
            background: rgba(20,184,166,0.06);
            border: 1px solid rgba(20,184,166,0.15);
            border-radius: 10px;
            padding: 0.9rem 1rem;
            margin-bottom: 1.5rem;
        }
        .bkp-info-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .bkp-info-row:last-child { margin-bottom: 0; }
        .bkp-info-icon { font-size: 0.9rem; flex-shrink: 0; }
        .bkp-info-row strong { color: #ffffff; }

        .bkp-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .bkp-cancel {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            padding: 0.75rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .bkp-cancel:hover { background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.2); }

        .bkp-confirm {
            background: linear-gradient(135deg, #FFD700, #B8860B);
            border: none;
            color: #000000;
            padding: 0.75rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            box-shadow: 0 0 20px rgba(20,184,166,0.3);
        }
        .bkp-confirm:hover { color: #ffffff;background: linear-gradient(135deg, #FFD700, #FFD700); box-shadow: 0 0 30px rgba(146, 184, 20, 0.5); transform: translateY(-1px); }
        .bkp-confirm:active { transform: translateY(0); }
        .bkp-confirm.running {
            opacity: 0.7; pointer-events: none;
        }
        .bkp-spinner {
            width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        .bkp-confirm.running .bkp-spinner { display: block; }
        .bkp-confirm.running .bkp-confirm-text { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 480px) {
            .bkp-modal-inner { padding: 1.25rem; }
            .bkp-title { font-size: 1.05rem; }
            .bkp-actions { grid-template-columns: 1fr; }
            .backup-btn .backup-label { display: none; }
            .backup-btn { padding: 0.55rem 0.75rem; }
        }

        /* ── Mobile Top Bar ─────────────────────────────────────────────── */
        .mobile-topbar { display: none; position: fixed; top: 0; left: 0; right: 0; z-index: 1100; background: rgba(26,26,26,.98); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,215,0,.3); padding: .75rem 1rem; align-items: center; justify-content: space-between; box-shadow: 0 4px 20px rgba(0,0,0,.5); }
        .mobile-brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; }
        .mobile-brand .logo-img { width: 38px; height: 38px; }
        .mobile-brand .brand-text h1 { font-size: 1.2rem; }
        .hamburger-btn { background: transparent; border: 1px solid rgba(255,215,0,.4); border-radius: 8px; color: var(--primary); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.2rem; transition: all .3s ease; flex-shrink: 0; }
        .hamburger-btn:hover { background: rgba(255,215,0,.1); border-color: var(--primary); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; z-index: 1200; background: rgba(0,0,0,.7); backdrop-filter: blur(4px); opacity: 0; transition: opacity .3s ease; }
        .sidebar-overlay.active { opacity: 1; }
        .mobile-sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1300; width: 280px; background: rgba(15,15,15,.98); border-right: 1px solid rgba(255,215,0,.3); box-shadow: 4px 0 30px rgba(0,0,0,.8); transform: translateX(-100%); transition: transform .35s cubic-bezier(.4,0,.2,1); display: flex; flex-direction: column; overflow-y: auto; }
        .mobile-sidebar.open { transform: translateX(0); }
        .sidebar-header { padding: 1.25rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,215,0,.15); display: flex; align-items: center; justify-content: space-between; }
        .sidebar-close { background: rgba(255,68,68,.1); border: 1px solid rgba(255,68,68,.3); border-radius: 8px; color: var(--danger); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; transition: all .2s ease; }
        .sidebar-close:hover { background: rgba(255,68,68,.2); }
        .sidebar-user { padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,215,0,.1); background: rgba(255,215,0,.03); }
        .sidebar-user-label { font-size: .7rem; color: var(--gray-light); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .25rem; }
        .sidebar-user-name { font-size: 1rem; font-weight: 700; color: var(--primary); }
        .sidebar-nav { padding: 1rem .75rem; flex: 1; }
        .sidebar-nav-item { display: flex; align-items: center; gap: .875rem; padding: .875rem 1rem; border-radius: 10px; color: rgba(255,255,255,.75); text-decoration: none; font-weight: 600; font-size: .95rem; transition: all .25s ease; margin-bottom: .25rem; position: relative; }
        .sidebar-nav-item:hover { background: rgba(255,215,0,.08); color: var(--primary); transform: translateX(4px); }
        .sidebar-nav-item.active { background: linear-gradient(135deg, rgba(255,215,0,.2) 0%, rgba(184,134,11,.1) 100%); color: var(--primary); border: 1px solid rgba(255,215,0,.25); }
        .sidebar-nav-item.active::before { content: ''; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: var(--primary); border-radius: 0 3px 3px 0; }
        .sidebar-nav-icon { font-size: 1.1rem; width: 24px; text-align: center; flex-shrink: 0; }
        .sidebar-footer { padding: 1rem .75rem; border-top: 1px solid rgba(255,215,0,.1); }
        .sidebar-logout { display: flex; align-items: center; gap: .875rem; padding: .875rem 1rem; border-radius: 10px; background: rgba(255,68,68,.08); border: 1px solid rgba(255,68,68,.25); color: var(--danger); text-decoration: none; font-weight: 600; font-size: .95rem; transition: all .25s ease; }
        .sidebar-logout:hover { background: rgba(255,68,68,.15); transform: translateX(4px); color: var(--danger); }

        /* ── Main Content ───────────────────────────────────────────────── */
        .main-content { margin-top: 100px; padding: 2rem; position: relative; z-index: 10; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; animation: fadeInUp .6s ease-out; flex-wrap: wrap; gap: 1rem; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .page-header h2 { font-size: 2rem; font-weight: 800; color: var(--primary); margin: 0; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--card-bg); border: 1px solid rgba(255,215,0,.2); border-radius: 12px; padding: 1.25rem; transition: all .3s ease; }
        .stat-card:hover { transform: translateY(-3px); border-color: var(--primary); box-shadow: 0 6px 20px rgba(255,215,0,.15); }
        .stat-card.alert-card { border-color: var(--danger); background: rgba(255,68,68,.05); }
        .stat-card.value-card { border-color: rgba(50,205,50,.3); background: rgba(50,205,50,.05); }
        .stat-card.value-card:hover { border-color: var(--success); box-shadow: 0 6px 20px rgba(50,205,50,.15); }
        .stat-card.value-card .stat-value { color: var(--success); }
        .stat-content { display: flex; justify-content: space-between; align-items: center; }
        .stat-info { flex: 1; }
        .stat-value { font-size: 1.75rem; font-weight: 800; color: var(--primary); margin-bottom: .25rem; line-height: 1; }
        .stat-label { font-size: .75rem; color: var(--gray-light); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .stat-sub { font-size: .75rem; color: var(--gray-light); margin-top: .25rem; }
        .stat-icon { font-size: 2rem; color: var(--gray-medium); opacity: .5; }
        .health-indicator { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem .75rem; border-radius: 8px; font-size: .875rem; font-weight: 600; }
        .health-good { background: rgba(50,205,50,.15); color: var(--success); border: 1px solid rgba(50,205,50,.3); }
        .health-fair { background: rgba(255,165,0,.15); color: var(--warning); border: 1px solid rgba(255,165,0,.3); }
        .health-poor { background: rgba(255,68,68,.15); color: var(--danger); border: 1px solid rgba(255,68,68,.3); animation: pulse-health 2s infinite; }
        @keyframes pulse-health { 0%,100%{opacity:1} 50%{opacity:.7} }
        .section-title { font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
        .dashboard-card { background: var(--card-bg) !important; border: 1px solid rgba(255,215,0,.2); border-radius: 12px; overflow: hidden; margin-bottom: 2rem; }
        .card-critical { border-color: var(--danger); }
        .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px,1fr)); gap: 1rem; padding: 1.25rem; }
        .category-card { background: var(--card-bg); border: 1px solid rgba(255,215,0,.2); border-radius: 12px; padding: 1.5rem; transition: all .3s ease; position: relative; overflow: hidden; }
        .category-card:hover { transform: translateY(-3px); border-color: var(--primary); box-shadow: 0 8px 25px rgba(255,215,0,.1); }
        .category-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary), var(--primary-dark)); opacity: 0; transition: opacity .3s ease; }
        .category-card:hover::before { opacity: 1; }
        .category-name { font-size: 1.1rem; font-weight: 700; color: var(--primary); margin-bottom: .75rem; }
        .category-stats { display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,215,0,.1); }
        .category-stat { text-align: center; }
        .category-stat-value { font-size: 1.5rem; font-weight: 800; color: var(--primary); line-height: 1; }
        .category-stat-label { font-size: .7rem; color: var(--gray-light); text-transform: uppercase; letter-spacing: .5px; margin-top: .25rem; }
        .table-container { overflow-x: auto; }
        .table { color: var(--gray-light) !important; margin: 0; background: transparent !important; }
        .table thead th { background: rgba(255,215,0,.1) !important; color: var(--primary) !important; font-weight: 700; text-transform: uppercase; font-size: .7rem; letter-spacing: .5px; padding: .875rem 1rem; border: none !important; white-space: nowrap; }
        .table tbody td { padding: .875rem 1rem; border-bottom: 1px solid rgba(255,255,255,.05) !important; vertical-align: middle; font-size: .875rem; background: transparent !important; color: var(--gray-light) !important; }
        .table tbody tr:hover { background: rgba(255,215,0,.05) !important; }
        .table tbody tr:last-child td { border-bottom: none !important; }
        .badge-custom { padding: .375rem .75rem; border-radius: 6px; font-size: .7rem; font-weight: 700; text-transform: uppercase; display: inline-block; white-space: nowrap; }
        .badge-critical { background: var(--danger) !important; color: #000 !important; }
        .badge-warning  { background: var(--warning) !important; color: #000 !important; }
        .badge-in  { background: rgba(50,205,50,.15) !important; color: var(--success) !important; border: 1px solid rgba(50,205,50,.3); }
        .badge-out { background: rgba(255,68,68,.15) !important; color: var(--danger) !important; border: 1px solid rgba(255,68,68,.3); }
        .stock-level { font-size: 1.1rem; font-weight: 700; }
        .stock-critical { color: var(--danger); }
        .stock-warning  { color: var(--warning); }
        .btn-primary-custom { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border: none; color: #000; font-weight: 600; padding: .5rem 1.5rem; border-radius: 8px; transition: all .3s ease; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255,215,0,.3); color: #000; }
        .empty-state { text-align: center; padding: 3rem 2rem; color: var(--gray-light); }
        .empty-icon { font-size: 3rem; opacity: .3; margin-bottom: 1rem; }
        .success-state { text-align: center; padding: 2rem; color: var(--success); }
        .success-icon { font-size: 3rem; margin-bottom: .75rem; }
        .success-text { font-size: 1rem; font-weight: 600; }
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap: 1rem; margin-top: 2rem; }
        .action-btn { background: var(--card-bg); border: 1px solid rgba(255,215,0,.2); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; transition: all .3s ease; display: block; }
        .action-btn:hover { transform: translateY(-3px); border-color: var(--primary); box-shadow: 0 6px 20px rgba(255,215,0,.2); text-decoration: none; }
        .action-icon { font-size: 2rem; margin-bottom: .5rem; display: block; color: var(--primary); }
        .action-title { font-size: .875rem; font-weight: 700; color: var(--primary); }
        .skeleton { background: linear-gradient(90deg, rgba(255,255,255,.05) 25%, rgba(255,255,255,.1) 50%, rgba(255,255,255,.05) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar-custom { display: none !important; }
            .mobile-topbar { display: flex; }
            .sidebar-overlay { display: block; pointer-events: none; }
            .sidebar-overlay.active { pointer-events: all; }
            .main-content { margin-top: 68px; padding: 1rem; }
            .stats-grid { grid-template-columns: 1fr !important; }
            .stats-grid .stat-card[style*="grid-column"] { grid-column: span 1 !important; }
            .quick-actions { grid-template-columns: repeat(2,1fr); }
            .page-header h2 { font-size: 1.5rem; }
            .category-grid { grid-template-columns: 1fr; padding: .875rem; }
        }
        @media (max-width: 480px) {
            .quick-actions { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .backup-btn .backup-label { display: none; }
            .backup-btn { padding: 0.55rem 0.75rem; }
        }
    </style>
</head>
<body>

<!-- ── Backup Confirm Modal ────────────────────────────────────────────── -->
<div class="bkp-overlay" id="bkpOverlay">
    <div class="bkp-modal" id="bkpModal">
        <div class="bkp-modal-inner">
            <div class="bkp-icon-wrap">🗄️</div>
            <h3 class="bkp-title">Generate Database Backup?</h3>
            <p class="bkp-subtitle">This will export your entire database as a <strong style="color:#2dd4bf;">.sql file</strong> and download it to your device.</p>

            <div class="bkp-info-box">
                <div class="bkp-info-row">
                    <span class="bkp-info-icon">📦</span>
                    <span>Includes <strong>all tables</strong> — products, stocks, logs, users</span>
                </div>
                <div class="bkp-info-row">
                    <span class="bkp-info-icon">🕐</span>
                    <span>Timestamped filename — safe to run <strong>anytime</strong></span>
                </div>
                <div class="bkp-info-row">
                    <span class="bkp-info-icon">🔒</span>
                    <span>Keep the file <strong>secure</strong> — it contains sensitive data</span>
                </div>
            </div>

            <div class="bkp-actions">
                <button class="bkp-cancel" id="bkpCancel">Cancel</button>
                <button class="bkp-confirm" id="bkpConfirm">
                    <div class="bkp-spinner"></div>
                    <span class="bkp-confirm-text">⬇️ Download Backup</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Desktop Navbar ──────────────────────────────────────────────────── -->
<nav class="navbar-custom navbar navbar-expand-lg">
    <div class="container-fluid">
        <a href="home.php" class="navbar-brand-custom">
            <img src="assets/images/logo.png" alt="Logo" class="logo-img">
            <div class="brand-text"><h1>Grease Monkey</h1><p>Inventory System</p></div>
        </a>
        <div class="collapse navbar-collapse show" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a href="index.php?page=dashboard"       class="nav-link-custom active">📊 Dashboard</a></li>
                <li class="nav-item"><a href="index.php?page=manage_products" class="nav-link-custom">🛠️ Products</a></li>
                <li class="nav-item"><a href="index.php?page=manage_stocks"   class="nav-link-custom">📦 Stock</a></li>
                <li class="nav-item"><a href="index.php?page=stock_logs"      class="nav-link-custom">📋 Logs</a></li>
                <li class="nav-item"><a href="index.php?page=activity_logs"   class="nav-link-custom">📝 Activity</a></li>
                <li class="nav-item">
                    <span class="user-info">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                </li>
                <li class="nav-item"><a href="index.php?page=logout" class="btn-logout">🚪 Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Mobile Top Bar ─────────────────────────────────────────────────── -->
<div class="mobile-topbar">
    <a href="home.php" class="mobile-brand">
        <img src="assets/images/logo.png" alt="Logo" class="logo-img">
        <div class="brand-text"><h1>Grease Monkey</h1><p>Inventory System</p></div>
    </a>
    <button class="hamburger-btn" id="sidebarToggle" aria-label="Open menu">☰</button>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

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
        <a href="index.php?page=dashboard"       class="sidebar-nav-item active"><span class="sidebar-nav-icon">📊</span> Dashboard</a>
        <a href="index.php?page=manage_products" class="sidebar-nav-item"><span class="sidebar-nav-icon">🛠️</span> Products</a>
        <a href="index.php?page=manage_stocks"   class="sidebar-nav-item"><span class="sidebar-nav-icon">📦</span> Stock</a>
        <a href="index.php?page=stock_logs"      class="sidebar-nav-item"><span class="sidebar-nav-icon">📋</span> Logs</a>
        <a href="index.php?page=activity_logs"   class="sidebar-nav-item"><span class="sidebar-nav-icon">📝</span> Activity</a>
    </nav>
    <div class="sidebar-footer">
        <a href="index.php?page=logout" class="sidebar-logout"><span class="sidebar-nav-icon">🚪</span> Logout</a>
    </div>
</aside>

<?php if (isset($_SESSION['backup_error'])): ?>
<div class="backup-error-toast" style="position:fixed;bottom:1rem;right:1rem;z-index:9999;max-width:360px;">
    ⚠️ <?= htmlspecialchars($_SESSION['backup_error']) ?>
    <?php unset($_SESSION['backup_error']); ?>
</div>
<?php endif; ?>

<!-- ── Main Content ───────────────────────────────────────────────────── -->
<div class="main-content">
    <div class="container-fluid">

        <div class="page-header">
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <h2>Dashboard Overview</h2>
            </div>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
    <button type="button" class="backup-btn" id="impOpenBtn"
        style="background:linear-gradient(135deg,#1a1a2e,#16213e);border-color:rgba(255,255,255,0.15);color:#94a3b8;">
        <span class="backup-icon">📂</span>
        <span class="backup-label">Import Backup</span>
    </button>
    <button type="button" class="backup-btn" id="backupOpenBtn">
        <span class="backup-icon">🗄️</span>
        <span class="backup-label">Backup Database</span>
    </button>
</div>
        </div>

        <div class="stats-grid" id="statsGrid">
            <div class="stat-card" style="grid-column:span 2;">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-value skeleton" style="width:80px;height:2rem;" id="totalProducts">&nbsp;</div>
                        <div class="stat-label">Total Products</div>
                        <div class="stat-sub" id="totalUnits">&nbsp;</div>
                    </div>
                    <div class="stat-icon">📦</div>
                </div>
            </div>
            <div class="stat-card" id="attentionCard">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-value" id="attentionCount">&nbsp;</div>
                        <div class="stat-label">Need Attention</div>
                        <div class="stat-sub" id="attentionSub">&nbsp;</div>
                    </div>
                    <div class="stat-icon">🚨</div>
                </div>
            </div>
            <div class="stat-card value-card">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-value" id="inventoryValue">&nbsp;</div>
                        <div class="stat-label">Total Inventory Value</div>
                        <div class="stat-sub">Based on current stock levels</div>
                    </div>
                    <div class="stat-icon">💰</div>
                </div>
            </div>
        </div>

        <h3 class="section-title"><span>📈</span> Products by Category</h3>
        <div class="dashboard-card">
            <div class="category-grid" id="categoryGrid">
                <div class="empty-state"><div class="empty-icon">📈</div><p>Loading...</p></div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;background:rgba(255,215,0,.05);padding:.75rem 1rem;border-radius:8px;border:1px solid rgba(255,215,0,.2);">
            <span style="color:var(--warning);">⚠️</span>
            <span style="font-size:.875rem;color:var(--gray-light);">
                <strong>Low-stock threshold:</strong> Items below <span style="color:var(--warning);font-weight:700;">10 units</span> are flagged
            </span>
        </div>

        <div id="attentionSection"></div>

        <h3 class="section-title"><span>📋</span> Recent Stock Movements (Last 7)</h3>
        <div class="dashboard-card">
            <div class="table-container">
                <div id="stockLogsContent">
                    <div class="empty-state"><div class="empty-icon">📋</div><p>Loading...</p></div>
                </div>
            </div>
        </div>

        <h3 class="section-title"><span>⚡</span> Quick Actions</h3>
        <div class="quick-actions">
            <a href="index.php?page=manage_products&action=add" class="action-btn"><span class="action-icon">➕</span><div class="action-title">Add Product</div></a>
            <a href="index.php?page=manage_stocks"              class="action-btn"><span class="action-icon">📦</span><div class="action-title">Update Stock</div></a>
            <a href="index.php?page=manage_products"            class="action-btn"><span class="action-icon">🔍</span><div class="action-title">Browse Products</div></a>
            <a href="index.php?page=stock_logs"                 class="action-btn"><span class="action-icon">📋</span><div class="action-title">View Logs</div></a>
        </div>
    </div>
</div>
<!-- ── Import Modal ──────────────────────────────────────────────────── -->
<div class="bkp-overlay" id="impOverlay">
    <div class="bkp-modal" id="impModal">
        <div class="bkp-modal-inner">
            <div class="bkp-icon-wrap" id="impIcon">📂</div>
            <h3 class="bkp-title" id="impTitle">Import Database Backup</h3>
            <p class="bkp-subtitle" id="impSubtitle">
                Upload a <strong style="color:#2dd4bf;">.sql backup file</strong> to restore your database.
                <strong style="color:#ff4444;">This will overwrite all existing data.</strong>
            </p>

            <div class="bkp-info-box" id="impInfoBox">
                <div class="bkp-info-row">
                    <span class="bkp-info-icon">⚠️</span>
                    <span><strong style="color:#ff4444;">Destructive action</strong> — all current data will be replaced</span>
                </div>
                <div class="bkp-info-row">
                    <span class="bkp-info-icon">📄</span>
                    <span>Only <strong>.sql files</strong> generated by this system are supported</span>
                </div>
                <div class="bkp-info-row">
                    <span class="bkp-info-icon">📦</span>
                    <span>Maximum file size: <strong>50MB</strong></span>
                </div>
            </div>

            <!-- File picker -->
            <div id="impFileWrap" style="margin-bottom:1.25rem;">
                <label for="impFileInput" id="impFileLabel"
                    style="display:flex;align-items:center;justify-content:center;gap:0.6rem;
                           border:2px dashed rgba(255,215,0,0.3);border-radius:10px;padding:1rem;
                           cursor:pointer;transition:all 0.2s;color:#94a3b8;font-size:0.85rem;
                           background:rgba(255,215,0,0.03);">
                    <span style="font-size:1.5rem;">📁</span>
                    <span id="impFileLabelText">Click to select a .sql file</span>
                </label>
                <input type="file" id="impFileInput" accept=".sql" style="display:none;">
            </div>

            <div class="bkp-actions" id="impActions">
                <button class="bkp-cancel" id="impCancel">Cancel</button>
                <button class="bkp-confirm" id="impConfirm" disabled
                    style="opacity:0.4;pointer-events:none;">
                    <div class="bkp-spinner"></div>
                    <span class="bkp-confirm-text">⬆️ Import Now</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
// ── Backup Modal ──────────────────────────────────────────────────────────
(function () {
    const overlay    = document.getElementById('bkpOverlay');
    const openBtn    = document.getElementById('backupOpenBtn');
    const cancelBtn  = document.getElementById('bkpCancel');
    const confirmBtn = document.getElementById('bkpConfirm');

    let autoCloseTimer = null;

    function openModal() {
        // Reset modal to default state every time it opens
        document.querySelector('.bkp-icon-wrap').textContent = '🗄️';
        document.querySelector('.bkp-title').textContent = 'Generate Database Backup?';
        document.querySelector('.bkp-subtitle').innerHTML =
            'This will export your entire database as a <strong style="color:#2dd4bf;">.sql file</strong> and download it to your device.';
        document.querySelector('.bkp-info-box').style.display = '';
        confirmBtn.style.display = '';
        confirmBtn.classList.remove('running');
        cancelBtn.style.flex = '';           // ← add this to reset
    	cancelBtn.textContent = 'Cancel';
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (autoCloseTimer) { clearInterval(autoCloseTimer); autoCloseTimer = null; }
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function startAutoClose() {
        let remaining = 10;
        cancelBtn.textContent = `Close (${remaining}s)`;
        autoCloseTimer = setInterval(() => {
            remaining--;
            cancelBtn.textContent = `Close (${remaining}s)`;
            if (remaining <= 0) {
                clearInterval(autoCloseTimer);
                autoCloseTimer = null;
                closeModal();
            }
        }, 1000);
    }

    function showSuccess(filename) {
        document.querySelector('.bkp-icon-wrap').textContent = '✅';
        document.querySelector('.bkp-title').textContent = 'Backup Complete!';
        document.querySelector('.bkp-subtitle').innerHTML =
            `<strong style="color:#2dd4bf;">${filename}</strong><br>has been downloaded successfully.`;
        document.querySelector('.bkp-info-box').style.display = 'none';
        confirmBtn.style.display = 'none';
        startAutoClose();
    }

    function showError(msg) {
        document.querySelector('.bkp-icon-wrap').textContent = '❌';
        document.querySelector('.bkp-title').textContent = 'Backup Failed';
        document.querySelector('.bkp-subtitle').innerHTML =
            `<span style="color:#fca5a5;">${msg || 'Something went wrong. Please try again.'}</span>`;
        document.querySelector('.bkp-info-box').style.display = 'none';
        confirmBtn.style.display = 'none';
        startAutoClose();
    }

    openBtn.addEventListener('click', openModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    confirmBtn.addEventListener('click', () => {
        confirmBtn.classList.add('running');

        fetch('index.php?page=backup', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'generate_backup=1'
        })
        .then(response => {
            if (!response.ok) throw new Error('Server error: ' + response.status);

            // Grab filename from Content-Disposition header if available
            const disposition = response.headers.get('Content-Disposition');
            let filename = 'backup.sql';
            if (disposition) {
                const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match) filename = match[1].replace(/['"]/g, '');
            }

            return response.blob().then(blob => ({ blob, filename }));
        })
        .then(({ blob, filename }) => {
            // Trigger the actual file download
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);

            confirmBtn.classList.remove('running');
            showSuccess(filename);
        })
        .catch(err => {
            confirmBtn.classList.remove('running');
            showError(err.message);
        });
    });
})();
// ── Import Modal ──────────────────────────────────────────────────────────
(function () {
    const overlay    = document.getElementById('impOverlay');
    const openBtn    = document.getElementById('impOpenBtn');
    const cancelBtn  = document.getElementById('impCancel');
    const confirmBtn = document.getElementById('impConfirm');
    const fileInput  = document.getElementById('impFileInput');
    const fileLabel  = document.getElementById('impFileLabelText');

    let autoCloseTimer = null;

    function openModal() {
        // Reset every time
        document.getElementById('impIcon').textContent   = '📂';
        document.getElementById('impTitle').textContent  = 'Import Database Backup';
        document.getElementById('impSubtitle').innerHTML =
            'Upload a <strong style="color:#2dd4bf;">.sql backup file</strong> to restore your database. ' +
            '<strong style="color:#ff4444;">This will overwrite all existing data.</strong>';
        document.getElementById('impInfoBox').style.display = '';
        document.getElementById('impFileWrap').style.display = '';
        document.getElementById('impActions').style.display = '';
        confirmBtn.style.display = '';
        confirmBtn.classList.remove('running');
        confirmBtn.style.opacity = '0.4';
        confirmBtn.style.pointerEvents = 'none';
        confirmBtn.disabled = true;
        fileInput.value = '';
        fileLabel.textContent = 'Click to select a .sql file';
        cancelBtn.textContent = 'Cancel';
        if (autoCloseTimer) { clearInterval(autoCloseTimer); autoCloseTimer = null; }
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (autoCloseTimer) { clearInterval(autoCloseTimer); autoCloseTimer = null; }
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function startAutoClose() {
        let remaining = 8;
        cancelBtn.textContent = `Close (${remaining}s)`;
        autoCloseTimer = setInterval(() => {
            remaining--;
            cancelBtn.textContent = `Close (${remaining}s)`;
            if (remaining <= 0) { clearInterval(autoCloseTimer); autoCloseTimer = null; closeModal(); }
        }, 1000);
    }

    function showResult(success, message, filename) {
        document.getElementById('impIcon').textContent  = success ? '✅' : '❌';
        document.getElementById('impTitle').textContent = success ? 'Import Successful!' : 'Import Failed';
        document.getElementById('impSubtitle').innerHTML = success
            ? `<strong style="color:#2dd4bf;">${filename}</strong><br>has been imported successfully.`
            : `<span style="color:#fca5a5;">${message}</span>`;
        document.getElementById('impInfoBox').style.display = 'none';
        document.getElementById('impFileWrap').style.display = 'none';
        confirmBtn.style.display = 'none';
        cancelBtn.style.flex = '1';
        startAutoClose();

        if (success) {
            // Refresh dashboard data after successful import
            setTimeout(() => window.fetchDashboard && window.fetchDashboard(), 500);
        }
    }

    // File selection
    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            fileLabel.textContent = `📄 ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
            confirmBtn.style.opacity = '1';
            confirmBtn.style.pointerEvents = 'auto';
            confirmBtn.disabled = false;
        } else {
            fileLabel.textContent = 'Click to select a .sql file';
            confirmBtn.style.opacity = '0.4';
            confirmBtn.style.pointerEvents = 'none';
            confirmBtn.disabled = true;
        }
    });

    openBtn.addEventListener('click', openModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    confirmBtn.addEventListener('click', () => {
        const file = fileInput.files[0];
        if (!file) return;

        confirmBtn.classList.add('running');

        const formData = new FormData();
        formData.append('sql_file', file);

        fetch('index.php?page=import', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            confirmBtn.classList.remove('running');
            showResult(data.success, data.message, data.filename || file.name);
        })
        .catch(err => {
            confirmBtn.classList.remove('running');
            showResult(false, 'Unexpected error: ' + err.message, '');
        });
    });
})();
// ── Sidebar Logic ─────────────────────────────────────────────────────────
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

// ── Dashboard Data ────────────────────────────────────────────────────────
function esc(str) { return String(str??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function fmtDate(str) { return new Date(str).toLocaleDateString('en-PH',{month:'short',day:'2-digit'}); }
function fmtTime(str) { return new Date(str).toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'}); }

let lastHash = '';
window.fetchDashboard = function() {
    fetch('app/ajax/ajax_dashboard.php')
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(d => { const h = JSON.stringify(d); if (h === lastHash) return; lastHash = h; renderDashboard(d); })
        .catch(() => {});
};

function renderDashboard(d) {

    const totalEl = document.getElementById('totalProducts');
    totalEl.textContent = Number(d.total_products).toLocaleString();
    totalEl.classList.remove('skeleton');
    document.getElementById('totalUnits').textContent = Number(d.total_items_in_stock).toLocaleString() + ' total units in stock';

    const total_attention = d.out_of_stock_count + d.low_stock_count;
    const attCard = document.getElementById('attentionCard');
    attCard.className = 'stat-card' + (total_attention > 0 ? ' alert-card' : '');
    const attCount = document.getElementById('attentionCount');
    attCount.textContent = total_attention;
    attCount.style.color = total_attention > 0 ? 'var(--danger)' : 'var(--success)';
    document.getElementById('attentionSub').textContent = d.out_of_stock_count + ' out, ' + d.low_stock_count + ' low';
    document.getElementById('inventoryValue').innerHTML = '<span style="color:var(--success);opacity:.7;">₱</span>' + esc(d.total_inventory_value);

    const catGrid = document.getElementById('categoryGrid');
    catGrid.innerHTML = !d.category_products.length
        ? '<div class="empty-state"><div class="empty-icon">📊</div><p>No categories found</p></div>'
        : d.category_products.map(c => `
            <div class="category-card">
                <div class="category-name">${esc(c.category_name)}</div>
                <div class="category-stats">
                    <div class="category-stat"><div class="category-stat-value">${c.product_count??0}</div><div class="category-stat-label">Products</div></div>
                    <div class="category-stat"><div class="category-stat-value">${c.total_stock??0}</div><div class="category-stat-label">Total Stock</div></div>
                </div>
                ${c.product_count > 0 ? `<div style="margin-top:1rem;text-align:center;"><a href="index.php?page=manage_products&category_id=${esc(String(c.id))}" style="background:rgba(255,215,0,.1);color:var(--primary);border:1px solid rgba(255,215,0,.3);padding:.25rem .75rem;border-radius:6px;text-decoration:none;font-size:.75rem;">View Products →</a></div>` : ''}
            </div>`).join('');

    const attSection = document.getElementById('attentionSection');
    if (total_attention > 0) {
        const extra = d.attention_total - d.attention_products.length;
        attSection.innerHTML = `
            <h3 class="section-title"><span>🚨</span> Attention Required</h3>
            <div class="dashboard-card card-critical">
                <div class="table-container">
                    <table class="table"><thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        ${d.attention_products.map(p => { const out = p.stock==0; return `<tr>
                            <td><strong style="color:#fff;">${esc(p.product_name)}</strong>${p.sku?`<div style="font-size:.75rem;color:var(--gray-light);">SKU: ${esc(p.sku)}</div>`:''}</td>
                            <td>${esc(p.category_name??'N/A')}</td>
                            <td><span class="stock-level ${out?'stock-critical':'stock-warning'}">${p.stock}</span></td>
                            <td><span class="badge-custom ${out?'badge-critical':'badge-warning'}">${out?'OUT OF STOCK':'LOW STOCK'}</span></td>
                            <td><a href="index.php?page=manage_stocks" class="btn-primary-custom" style="font-size:.8rem;padding:.4rem 1rem;">➕ Restock</a></td>
                        </tr>`; }).join('')}
                        ${extra > 0 ? `<tr><td colspan="5" class="text-center" style="padding:1rem;color:var(--gray-light);">+ ${extra} more <a href="index.php?page=manage_stocks" style="color:var(--primary);margin-left:.5rem;text-decoration:none;">View all</a></td></tr>` : ''}
                    </tbody></table>
                </div>
            </div>`;
    } else {
        attSection.innerHTML = `<div class="dashboard-card" style="margin-bottom:2rem;"><div class="success-state"><div class="success-icon">✅</div><div class="success-text">All inventory products are sufficiently stocked.</div><p style="font-size:.875rem;color:var(--gray-light);margin-top:.5rem;">No items require immediate attention</p></div></div>`;
    }

    const logsEl = document.getElementById('stockLogsContent');
    if (!d.recent_stock_logs.length) {
        logsEl.innerHTML = '<div class="empty-state"><div class="empty-icon">📋</div><p style="color:var(--gray-light);">No stock movements in the last 7 days</p></div>';
    } else {
        logsEl.innerHTML = `<table class="table"><thead><tr><th>Date & Time</th><th>Product</th><th>Action</th><th>Quantity</th><th>Remarks</th></tr></thead>
            <tbody>${d.recent_stock_logs.map(l => `<tr>
                <td style="white-space:nowrap;"><strong style="color:#fff;">${esc(fmtDate(l.created_at))}</strong><span style="color:var(--gray-light);display:block;font-size:.75rem;">${esc(fmtTime(l.created_at))}</span></td>
                <td><strong style="color:#fff;">${esc(l.product_name??'N/A')}</strong></td>
                <td><span class="badge-custom ${l.action==='IN'?'badge-in':'badge-out'}">${l.action==='IN'?'📥 IN':'📤 OUT'}</span></td>
                <td><strong style="font-size:1.1rem;color:var(--primary);">${esc(String(l.quantity))}</strong></td>
                <td>${esc(l.remarks??'-')}</td>
            </tr>`).join('')}</tbody></table>
            <div style="padding:1rem;text-align:center;border-top:1px solid rgba(255,215,0,.1);">
                <a href="index.php?page=stock_logs" style="color:var(--primary);text-decoration:none;font-size:.875rem;">View all stock logs →</a>
            </div>`;
    }
}

window.fetchDashboard();
setInterval(window.fetchDashboard, 10000);
</script>
<script src="/inventory/assets/js/ws.js"></script>
<?php if (isset($_SESSION['user_id'])): ?>
    <script src="assets/js/session-timeout.js"></script>
<?php endif; ?>
</body>
</html>