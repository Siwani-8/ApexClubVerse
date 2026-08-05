<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendVerificationEmail($email, $token)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'apexclubverse@gmail.com';
        $mail->Password = 'owvn ymjm ruii lxsz';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('apexclubverse@gmail.com', 'ApexClubVerse');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your ApexClubVerse Account';

        $verificationLink =
            "http://localhost/ApexClubVerse/verify.php?token=".$token;

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