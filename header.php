<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
    <a href="index.php" class="brand">
        <div class="logo-square">A</div>
        <div class="brand-text">
            <h2>ApexClubVerse</h2>
            <p>APEX COLLEGE PORTAL</p>
        </div>
    </a>
    <ul class="nav-links">
        <li><a href="index.php"> Home</a></li>
        <li><a href="clubs.php"> Clubs</a></li>
        <li><a href="events.php"> Events Feed</a></li>
        <li><a href="vote-events.php"> Event Vote</a></li>
        <li><a href="registration.php"> Club Intake</a></li>
     
    


        <?php if (isset($_SESSION['user_logged_in'])): ?>
            <li><a href="logout.php" style="color: var(--primary-crimson)">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Sign In</a></li>
            <li><a href="signup.php" class="btn-join">Join Portal</a></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="content-wrapper">