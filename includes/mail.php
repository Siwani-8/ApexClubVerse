<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/mail_config.php';

if (!function_exists('sendVerificationEmail')) {
function sendVerificationEmail($toEmail, $token)
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

        $mail->addAddress($toEmail);

        $mail->isHTML(true);

        $mail->Subject = 'Verify your ApexClubVerse account';

        // Build the verification link dynamically so it always points
        // to the folder this app is actually running from.
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/')), '/');
        $link = $scheme . '://' . $host . $basePath . '/verify.php?token=' . $token;

        $mail->Body = "
        <h2>Welcome to ApexClubVerse!</h2>

        <p>Click the button below to verify your account.</p>

        <a href='$link'
           style='padding:12px 25px;
                  background:#7a1028;
                  color:white;
                  text-decoration:none;
                  border-radius:6px;'>
            Verify Account
        </a>

        <br><br>

        <p>If you didn't create this account, ignore this email.</p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;

    }

}
}