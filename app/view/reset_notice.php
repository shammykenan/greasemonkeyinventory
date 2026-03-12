<?php
session_start();

if (!isset($_SESSION['reset_message'])) {
    header("Location: index.php?page=landing_page");
    exit;
}

$message    = $_SESSION['reset_message'];
$is_limited = isset($_SESSION['reset_limited']) && $_SESSION['reset_limited'] === true;
unset($_SESSION['reset_message'], $_SESSION['reset_limited']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/logo.png">
    <title>Password Reset — Grease Monkey</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Hard reset — fights Bootstrap and landingpage.css */
        #rn-root, #rn-root *, #rn-root *::before, #rn-root *::after {
            box-sizing: border-box !important;
            font-family: 'Poppins', sans-serif !important;
        }

        /* Full viewport takeover */
        #rn-root {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: #000 !important;
            z-index: 999999 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            /* Center child */
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 1.5rem !important;
        }

        /* Orbs */
        .rn-orb {
            position: absolute !important;
            border-radius: 50% !important;
            filter: blur(80px) !important;
            pointer-events: none !important;
            z-index: 0 !important;
        }
        .rn-orb-1 {
            top: -10% !important; right: -5% !important;
            width: 500px !important; height: 500px !important;
            background: radial-gradient(circle, rgba(251,191,36,0.1) 0%, transparent 70%) !important;
            animation: rnOrbFloat 18s ease-in-out infinite !important;
        }
        .rn-orb-2 {
            bottom: -10% !important; left: -5% !important;
            width: 420px !important; height: 420px !important;
            background: radial-gradient(circle, rgba(251,191,36,0.07) 0%, transparent 70%) !important;
            animation: rnOrbFloat 14s ease-in-out infinite reverse !important;
        }
        @keyframes rnOrbFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(20px,-20px) scale(1.08); }
        }

        /* Card */
        .rn-card {
            position: relative !important;
            z-index: 10 !important;
            width: 100% !important;
            max-width: 460px !important;
            background: rgba(12,12,12,0.97) !important;
            border: 1.5px solid rgba(251,191,36,0.45) !important;
            border-radius: 24px !important;
            overflow: hidden !important;
            box-shadow: 0 0 70px rgba(251,191,36,0.2), 0 30px 60px rgba(0,0,0,0.9) !important;
            animation: rnCardIn 0.55s cubic-bezier(0.34,1.56,0.64,1) both !important;
            /* No margin auto needed — parent flexbox centers it */
        }
        @keyframes rnCardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0)    scale(1);    }
        }

        /* Header */
        .rn-header {
            padding: 1.75rem 2.25rem 1.25rem !important;
            border-bottom: 1px solid rgba(251,191,36,0.15) !important;
            text-align: center !important;
        }
        .rn-logo-row {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.6rem !important;
            margin-bottom: 1rem !important;
        }
        .rn-logo-row img {
            width: 38px !important; height: 38px !important;
            object-fit: contain !important;
            filter: drop-shadow(0 0 10px rgba(251,191,36,0.5)) !important;
            animation: rnLogoFloat 3s ease-in-out infinite !important;
        }
        @keyframes rnLogoFloat {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-5px); }
        }
        .rn-brand {
            font-size: 1rem !important;
            font-weight: 800 !important;
            color: #fbbf24 !important;
            text-shadow: 0 0 16px rgba(251,191,36,0.5) !important;
            margin: 0 !important;
        }
        .rn-title {
            font-size: 1.5rem !important;
            font-weight: 800 !important;
            color: #fff !important;
            letter-spacing: -0.4px !important;
            margin: 0 0 0.3rem !important;
        }
        .rn-subtitle {
            font-size: 0.82rem !important;
            color: rgba(251,191,36,0.6) !important;
            font-weight: 500 !important;
            margin: 0 !important;
        }

        /* Body */
        .rn-body {
            padding: 2rem 2.25rem !important;
            text-align: center !important;
        }

        /* Envelope */
        .rn-env-wrap {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 88px !important; height: 88px !important;
            margin-bottom: 1.5rem !important;
        }
        .rn-env-icon {
            font-size: 3rem !important;
            position: relative !important;
            z-index: 2 !important;
            animation: rnEnvBounce 0.9s cubic-bezier(0.34,1.56,0.64,1) 0.2s both !important;
        }
        @keyframes rnEnvBounce {
            0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
            70%  { transform: scale(1.18) rotate(4deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .rn-ping {
            position: absolute !important; inset: 0 !important;
            border-radius: 50% !important;
            border: 2px solid rgba(251,191,36,0.45) !important;
            animation: rnPing 1.8s ease-out 0.6s infinite !important;
        }
        @keyframes rnPing {
            0%   { transform: scale(0.75); opacity: 0.8; }
            100% { transform: scale(1.65); opacity: 0; }
        }

        .rn-msg {
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            color: #e5e7eb !important;
            line-height: 1.65 !important;
            margin: 0 0 0.75rem !important;
        }
        .rn-note {
            font-size: 0.8rem !important;
            color: rgba(251,191,36,0.5) !important;
            line-height: 1.7 !important;
            margin: 0 0 1.5rem !important;
        }
        .rn-note strong { color: #fbbf24 !important; }

        .rn-divider {
            height: 1px !important;
            background: rgba(251,191,36,0.12) !important;
            margin-bottom: 1.5rem !important;
        }

        /* Countdown */
        .rn-countdown-row {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.6rem !important;
            margin-bottom: 1.25rem !important;
            font-size: 0.85rem !important;
            color: rgba(251,191,36,0.6) !important;
            font-weight: 500 !important;
        }
        .rn-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 36px !important; height: 36px !important;
            border-radius: 50% !important;
            border: 2px solid rgba(251,191,36,0.5) !important;
            background: rgba(251,191,36,0.08) !important;
            font-size: 1rem !important;
            font-weight: 800 !important;
            color: #fbbf24 !important;
        }
        .rn-badge.tick { animation: rnTick 0.25s ease !important; }
        @keyframes rnTick {
            0%,100% { transform: scale(1); }
            50%      { transform: scale(1.3); }
        }

        /* Progress bar */
        .rn-progress-wrap {
            height: 3px !important;
            border-radius: 2px !important;
            background: rgba(251,191,36,0.12) !important;
            overflow: hidden !important;
            margin-bottom: 1.5rem !important;
        }
        .rn-progress-bar {
            height: 100% !important;
            border-radius: 2px !important;
            background: linear-gradient(90deg, #fbbf24, #f59e0b) !important;
            transform-origin: left !important;
            animation: rnDrain var(--drain-dur, 10s) linear 0.1s forwards !important;
        }
        @keyframes rnDrain {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }

        /* Button */
        .rn-btn {
            display: block !important;
            width: 100% !important;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
            border: none !important;
            color: #000 !important;
            padding: 0.9rem !important;
            font-size: 0.88rem !important;
            font-weight: 800 !important;
            border-radius: 10px !important;
            cursor: pointer !important;
            text-decoration: none !important;
            text-transform: uppercase !important;
            letter-spacing: 1.2px !important;
            box-shadow: 0 0 25px rgba(251,191,36,0.35) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
            text-align: center !important;
        }
        .rn-btn::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important; left: -100% !important;
            width: 100% !important; height: 100% !important;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent) !important;
            transition: left 0.5s ease !important;
        }
        .rn-btn:hover::before { left: 100% !important; }
        .rn-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 0 45px rgba(251,191,36,0.65) !important;
        }

        /* Rate-limited / blocked state */
        .rn-card--limited {
            border-color: rgba(239,68,68,0.4) !important;
            box-shadow: 0 0 70px rgba(239,68,68,0.12), 0 30px 60px rgba(0,0,0,0.9) !important;
        }
        .rn-card--limited .rn-header {
            border-bottom-color: rgba(239,68,68,0.15) !important;
        }
        .rn-card--limited .rn-logo-row + h2 { color: #f87171 !important; }
        .rn-card--limited .rn-subtitle { color: rgba(248,113,113,0.6) !important; }
        .rn-env-block {
            animation: rnBlockShake 0.6s cubic-bezier(0.36,0.07,0.19,0.97) 0.3s both !important;
        }
        @keyframes rnBlockShake {
            0%,100% { transform: translateX(0); }
            15%      { transform: translateX(-6px) rotate(-5deg); }
            30%      { transform: translateX(6px)  rotate(5deg); }
            45%      { transform: translateX(-4px) rotate(-3deg); }
            60%      { transform: translateX(4px)  rotate(3deg); }
            75%      { transform: translateX(-2px); }
        }
        .rn-msg--blocked {
            color: #f87171 !important;
        }
        .rn-btn--limited {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%) !important;
            color: #d1d5db !important;
            box-shadow: none !important;
        }
        .rn-btn--limited:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%) !important;
            box-shadow: 0 0 20px rgba(255,255,255,0.05) !important;
        }

        /* Mobile */
        @media (max-width: 480px) {
            .rn-header, .rn-body { padding-left: 1.25rem !important; padding-right: 1.25rem !important; }
            .rn-title { font-size: 1.25rem !important; }
            .rn-env-icon { font-size: 2.5rem !important; }
            .rn-env-wrap { width: 72px !important; height: 72px !important; }
            .rn-msg { font-size: 0.875rem !important; }
            .rn-btn { font-size: 0.82rem !important; padding: 0.8rem !important; }
        }
        @media (max-width: 360px) {
            .rn-header, .rn-body { padding-left: 1rem !important; padding-right: 1rem !important; }
            .rn-title { font-size: 1.1rem !important; }
        }
    </style>
</head>
<body>

<div id="rn-root">
    <div class="rn-orb rn-orb-1"></div>
    <div class="rn-orb rn-orb-2"></div>

    <div class="rn-card">

        <div class="rn-header">
            <div class="rn-logo-row">
                <img src="/inventory/assets/landingpage-images/logo.png" alt="Grease Monkey">
                <p class="rn-brand">Grease Monkey</p>
            </div>
            <h2 class="rn-title"><?= $is_limited ? '🚫 Slow Down' : '📬 Check Your Email' ?></h2>
            <p class="rn-subtitle"><?= $is_limited ? 'Too many requests from this email' : 'Password reset request received' ?></p>
        </div>

        <div class="rn-body">

            <div class="rn-env-wrap">
                <div class="rn-env-icon"><?= $is_limited ? '⏳' : '✉️' ?></div>
                <?php if (!$is_limited): ?><div class="rn-ping"></div><?php endif; ?>
            </div>

            <p class="rn-msg"><?= htmlspecialchars($message) ?></p>

            <?php if (!$is_limited): ?>
            <p class="rn-note">
                The link expires in <strong>10 minutes</strong>.<br>
                Check your spam or junk folder if you don't see it.
            </p>
            <?php endif; ?>

            <div class="rn-divider"></div>

            <div class="rn-countdown-row">
                <span>Redirecting in</span>
                <span class="rn-badge" id="rnBadge">10</span>
                <span>seconds…</span>
            </div>

            <div class="rn-progress-wrap">
                <div class="rn-progress-bar" style="--drain-dur: 10s;"></div>
            </div>

            <a href="index.php?page=landing_page" class="rn-btn">← Back to Home</a>

        </div>
    </div>
</div>

<script>
    const TOTAL = 60;
    let seconds = 10;
    const badge = document.getElementById('rnBadge');

    // Delay 1s before first tick so badge always shows full 60 first
    setTimeout(function tick() {
        seconds--;
        badge.textContent = seconds;
        badge.classList.remove('tick');
        void badge.offsetWidth;
        badge.classList.add('tick');
        if (seconds <= 0) {
            window.location.href = "index.php?page=landing_page";
        } else {
            setTimeout(tick, 1000);
        }
    }, 1000);
</script>

</body>
</html>