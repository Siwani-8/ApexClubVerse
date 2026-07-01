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
    <title>Contact Us - Apex Clubs</title>
    <style>
        .container { max-width: 600px; margin: 4rem auto; padding: 2.5rem; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { color: var(--dark-red); margin-bottom: 1rem; }
        .contact-info { margin-top: 1.5rem; padding: 1rem; background: #f9f9f9; border-left: 4px solid var(--primary-red); }
    </style>
</head>
<body>


<div class="container">
    <h2>Contact Student Welfare</h2>
    <p>Have questions regarding club registrations, eligibility criteria, or event proposals? Drop by the administration block or get in touch through our official channels:</p>
    
    <div class="contact-info">
        <p><strong> Location:</strong> Apex College Main Campus, Mid-Baneshwor, Kathmandu</p>
        <p><strong> Email:</strong> info@apexcollege.edu.np</p>
        <p><strong> Phone:</strong> +977-1-4467922</p>
    </div>
</div>
</body>
<?php include 'footer.php'; ?>
</html>