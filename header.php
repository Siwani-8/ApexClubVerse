<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$protected_pages = ['vote-events.php', 'registration.php', 'admin.php', 'club_admin.php'];

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
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand-container">
        <img src="logo.png" alt="ApexClubVerse Logo" class="navbar-logo-square">
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="clubs.php">Clubs</a></li>
        <li><a href="events.php">Events Feed</a></li>
        <li><a href="vote-events.php">Event Vote</a></li>
        <li><a href="registration.php">Club Intake</a></li>

        <?php if (isset($_SESSION['user_logged_in'])): ?>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                <li><a href="admin.php" style="color:#fff; font-weight:bold;">&#9881; Admin</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'club_admin'): ?>
                <li><a href="club_admin.php" style="color:#fff; font-weight:bold;">&#127917; Club Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php" style="color:rgba(7, 0, 0, 0.85);">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="content-wrapper">