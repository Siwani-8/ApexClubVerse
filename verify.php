<?php

require_once __DIR__ . '/includes/config.php';
include 'includes/db.php';

function verify_render($title, $heading, $message, $showLogin = true) {
    $pageTitle = $title;
    include __DIR__ . '/includes/header.php';
    ?>
    <style>
        .verify-page {
            flex: 1 0 auto;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
            background: #f5f3ef;
        }
        .verify-box {
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 40px 24px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 28px rgba(0,0,0,.08);
            border: 0.5px solid #e0ddd6;
        }
        .verify-box h2 {
            margin: 0 0 12px;
            font-size: 1.4rem;
            line-height: 1.3;
            color: #1e2530;
        }
        .verify-box p {
            margin: 0;
            color: #555;
            line-height: 1.5;
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
        }
        .verify-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #7a1028;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-family: 'Segoe UI', sans-serif;
            font-weight: 600;
        }
        @media (max-width: 480px) {
            .verify-page { padding: 2rem 1rem; }
            .verify-box { padding: 28px 16px; }
            .verify-box h2 { font-size: 1.2rem; }
        }
    </style>
    <div class="verify-page">
        <div class="verify-box">
            <h2><?php echo htmlspecialchars($heading); ?></h2>
            <p><?php echo htmlspecialchars($message); ?></p>
            <?php if ($showLogin): ?>
                <a href="<?php echo htmlspecialchars(url('login.php')); ?>">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

if (!isset($_GET['token'])) {
    verify_render('Invalid Link', 'Invalid verification link', 'This verification link is missing or incomplete.', true);
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

$result = mysqli_query($conn,
"SELECT *
 FROM users
 WHERE verification_token='$token'
 LIMIT 1");

if (mysqli_num_rows($result) == 0) {
    verify_render('Invalid Link', 'Invalid or expired link', 'This verification link is invalid or has already been used.', true);
}

$user = mysqli_fetch_assoc($result);

if ($user['email_verified'] == 1) {
    verify_render('Already Verified', 'Account already verified', 'Your account is already verified. You can now log in.', true);
}

mysqli_query($conn,
"UPDATE users
 SET
 email_verified=1,
 verification_token=NULL
 WHERE id=".(int)$user['id']);

verify_render(
    'Email Verified',
    'Email Verified Successfully',
    'Your ApexClubVerse account is now active.',
    true
);
?>
