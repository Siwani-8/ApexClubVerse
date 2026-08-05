<?php
require_once __DIR__ . '/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$protected_pages = ['vote-events.php', 'registration.php', 'admin.php'];

if (in_array($current_page, $protected_pages) && !isset($_SESSION['user_logged_in'])) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(url('assets/css/style.css')); ?>">
    <style>
        /* Taller Navbar Styles */
        .navbar {
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 60px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            min-height: 90px;
            position: relative;
            z-index: 1000;
            flex-shrink: 0;
            width: 100%;
        }

        .navbar-brand-container {
            display: flex;
            align-items: center;
        }

        .navbar-brand-container img.navbar-logo-square {
            height: 70px;
            width: auto;
            display: block;
            object-fit: contain;
        }

        .nav-toggle {
            display: none;
            background: none;
            border: 1px solid #e0ddd6;
            border-radius: 8px;
            width: 44px;
            height: 44px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 5px;
            padding: 0;
            flex-shrink: 0;
        }

        .nav-toggle span {
            display: block;
            width: 20px;
            height: 2px;
            background: #1e2530;
            border-radius: 2px;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .nav-toggle.is-open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .nav-toggle.is-open span:nth-child(2) {
            opacity: 0;
        }

        .nav-toggle.is-open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: #1e2530;
            font-weight: 600;
            font-size: 1rem;
            font-family: 'Segoe UI', sans-serif;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ff4500;
        }

        .btn-join {
            background: #990026;
            color: #ffffff !important;
            padding: 10px 24px;
            border-radius: 30px;
            transition: background 0.3s ease;
        }

        .btn-join:hover {
            background: #73001c;
        }

        .admin-link {
            color: #990026 !important;
            font-weight: bold;
        }

        .logout-link {
            color: #1e2530;
        }

        @media (max-width: 992px) {
            .navbar {
                padding: 14px 24px;
            }

            .nav-links {
                gap: 1.25rem;
            }

            .nav-links a {
                font-size: 0.92rem;
            }

            .navbar-brand-container img.navbar-logo-square {
                height: 58px;
            }
        }

        @media (max-width: 850px) {
            .navbar {
                padding: 12px 16px;
                min-height: auto;
                flex-wrap: wrap;
            }

            .navbar-brand-container img.navbar-logo-square {
                height: 48px;
            }

            .nav-toggle {
                display: flex;
            }

            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                margin-top: 12px;
                padding: 8px 0;
                border-top: 1px solid #eee;
            }

            .nav-links.is-open {
                display: flex;
            }

            .nav-links li {
                width: 100%;
            }

            .nav-links a {
                display: block;
                padding: 12px 8px;
                font-size: 0.95rem;
                border-radius: 8px;
            }

            .nav-links a:hover {
                background: #f5f3ef;
            }

            .nav-links .btn-join {
                text-align: center;
                margin-top: 4px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 10px 12px;
            }

            .navbar-brand-container img.navbar-logo-square {
                height: 42px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand-container">
        <a href="<?php echo htmlspecialchars(url('index.php')); ?>">
            <img src="<?php echo htmlspecialchars(url('images/logo.png')); ?>" alt="ApexClubVerse Logo" class="navbar-logo-square">
        </a>
    </div>
    <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navLinks">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <ul class="nav-links" id="navLinks">
        <li><a href="<?php echo htmlspecialchars(url('index.php')); ?>">Home</a></li>
        <li><a href="<?php echo htmlspecialchars(url('clubs.php')); ?>">Clubs</a></li>
        <li><a href="<?php echo htmlspecialchars(url('events.php')); ?>">Events Feed</a></li>
        <li><a href="<?php echo htmlspecialchars(url('vote-events.php')); ?>">Event Vote</a></li>

        <?php if (!empty($_SESSION['user_logged_in'])): ?>

            <?php if ($_SESSION['user_role'] == 'admin'): ?>

                <li><a href="<?php echo htmlspecialchars(url('admin.php?applications_only=1')); ?>">Club Intake</a></li>
                <li><a href="<?php echo htmlspecialchars(url('admin.php')); ?>" class="admin-link">&#9881; Admin</a></li>

            <?php else: ?>

                <li><a href="<?php echo htmlspecialchars(url('registration.php')); ?>">Club Intake</a></li>
                <li><a href="<?php echo htmlspecialchars(url('my_application.php')); ?>">My Applications</a></li>

            <?php endif; ?>

            <li><a href="<?php echo htmlspecialchars(url('logout.php')); ?>" class="logout-link">Logout</a></li>

        <?php else: ?>

            <li><a href="<?php echo htmlspecialchars(url('registration.php')); ?>">Club Intake</a></li>
            <li><a href="<?php echo htmlspecialchars(url('login.php')); ?>" class="btn-join">Sign In</a></li>

        <?php endif; ?>
    </ul>
</nav>
<script>
(function () {
    var toggle = document.getElementById('navToggle');
    var links = document.getElementById('navLinks');
    if (!toggle || !links) return;

    toggle.addEventListener('click', function () {
        var open = links.classList.toggle('is-open');
        toggle.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 850) {
            links.classList.remove('is-open');
            toggle.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>

<div class="content-wrapper">
