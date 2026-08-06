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
