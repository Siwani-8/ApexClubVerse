<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/mail_config.php';

if (!function_exists('sendVerificationEmail')) {
function sendVerificationEmail($email, $token)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('apexclubverse@gmail.com', 'ApexClubVerse');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your ApexClubVerse Account';

        // Build the verification link dynamically so it always points
        // to the folder this app is actually running from.
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/')), '/');
        $verificationLink = $scheme . '://' . $host . $basePath . '/verify.php?token=' . $token;

        $mail->Body = "
        <h2>Welcome to ApexClubVerse</h2>

        <p>Thank you for registering.</p>

        <p>Please click the button below to verify your email.</p>

        <a href='$verificationLink'
        style='background:#7a1028;
               color:white;
               padding:12px 24px;
               text-decoration:none;
               border-radius:6px;'>
        Verify Account
        </a>

        <br><br>

        <p>If you did not create this account, simply ignore this email.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {

    return false;

}
}
}