<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$protected_pages = ['vote-events.php', 'registration.php', 'admin.php'];

if (in_array($current_page, $protected_pages) && !isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Taller Navbar Styles */
        .navbar {
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 60px; /* Increased top/bottom padding for extra height */
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            min-height: 90px; /* Ensures a consistent, taller header height */
        }

        .navbar-brand-container {
            display: flex;
            align-items: center;
        }

        .navbar-brand-container img.navbar-logo-square {
            height: 70px; /* Increased from 55px so the logo is crisp and clear */
            width: auto;
            display: block;
            object-fit: contain;
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
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand-container">
        <a href="index.php">
            <img src="images/logo.png" alt="ApexClubVerse Logo" class="navbar-logo-square">
        </a>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="clubs.php">Clubs</a></li>
        <li><a href="events.php">Events Feed</a></li>
        <li><a href="vote-events.php">Event Vote</a></li>

        <?php if (!empty($_SESSION['user_logged_in'])): ?>

            <?php if ($_SESSION['user_role'] == 'admin'): ?>

                <li><a href="admin.php?applications_only=1">Club Intake</a></li>
                <li><a href="admin.php" class="admin-link">&#9881; Admin</a></li>

            <?php else: ?>

                <li><a href="registration.php">Club Intake</a></li>
                <li><a href="my_application.php">My Applications</a></li>

            <?php endif; ?>

            <li><a href="logout.php" class="logout-link">Logout</a></li>

        <?php else: ?>

            <li><a href="registration.php">Club Intake</a></li>
            <li><a href="login.php" class="btn-join">Sign In</a></li>

        <?php endif; ?>
    </ul>
</nav>

<div class="content-wrapper">