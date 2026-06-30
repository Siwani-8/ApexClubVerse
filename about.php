<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - Apex Clubs</title>
    <style>
        .container { max-width: 800px; margin: 4rem auto; padding: 2.5rem; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); line-height: 1.8; }
        h2 { color: var(--dark-red); margin-bottom: 1rem; }
    </style>
</head>
<body>


<div class="container">
    <h2>About Apex College Clubs Hub</h2>
    <p>The Apex College Clubs Hub is a unified platform designed to manage and enhance student involvement across our 6 core campus clubs. Our mission is to promote engagement, streamline recruitment drives, and make student governance transparent.</p>
    <p style="margin-top: 1rem;">Whether you want to showcase leadership skills by running for the Board of Directors, cast your vote on student-led initiatives, or join a brand-new community, this web application acts as your definitive campus portal.</p>
</div>
</body>
<?php include 'footer.php'; ?>
</html>