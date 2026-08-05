<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendVerificationEmail($toEmail, $token)
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

        $mail->addAddress($toEmail);

        $mail->isHTML(true);

        $mail->Subject = 'Verify your ApexClubVerse account';

        $link = "http://localhost/ApexClubVerse/verify.php?token=".$token;

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