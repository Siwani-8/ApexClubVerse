<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/config.php';

$mailConfigPath = __DIR__ . '/mail_config.php';
if (!is_file($mailConfigPath)) {
    // Fall back to example file only for missing defines (live must use mail_config.php)
    $example = __DIR__ . '/mail_config.example.php';
    if (is_file($example)) {
        require_once $example;
    }
} else {
    require_once $mailConfigPath;
}

if (!defined('MAIL_HOST')) {
    define('MAIL_HOST', 'smtp.gmail.com');
}
if (!defined('MAIL_PORT')) {
    define('MAIL_PORT', 587);
}
if (!defined('MAIL_ENCRYPTION')) {
    define('MAIL_ENCRYPTION', 'tls');
}
if (!defined('MAIL_FROM_EMAIL')) {
    define('MAIL_FROM_EMAIL', defined('MAIL_USERNAME') ? MAIL_USERNAME : 'noreply@apexclubverse.local');
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'ApexClubVerse');
}
if (!defined('MAIL_ALLOW_PHP_MAIL')) {
    define('MAIL_ALLOW_PHP_MAIL', true);
}
if (!defined('MAIL_DEBUG_LOG')) {
    define('MAIL_DEBUG_LOG', true);
}

if (!function_exists('mail_app_password')) {
    function mail_app_password(): string
    {
        return preg_replace('/\s+/', '', (string)(defined('MAIL_PASSWORD') ? MAIL_PASSWORD : ''));
    }
}

if (!function_exists('mail_log_error')) {
    function mail_log_error(string $message): void
    {
        if (!MAIL_DEBUG_LOG) {
            return;
        }
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents(__DIR__ . '/mail_error.log', $line, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('build_verification_url')) {
    function build_verification_url(string $token): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . url('verify.php?token=' . urlencode($token));
    }
}

if (!function_exists('configure_phpmailer_smtp')) {
    function configure_phpmailer_smtp(PHPMailer $mail, string $host, int $port, string $encryption): void
    {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = defined('MAIL_USERNAME') ? MAIL_USERNAME : '';
        $mail->Password = mail_app_password();
        $mail->Port = $port;
        $mail->Timeout = 20;
        $mail->CharSet = 'UTF-8';

        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Shared hosts often have incomplete CA bundles.
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    }
}

if (!function_exists('sendVerificationEmail')) {
function sendVerificationEmail($email, $token)
{
    if (!defined('MAIL_USERNAME') || MAIL_USERNAME === '' || MAIL_USERNAME === 'your-email@gmail.com') {
        mail_log_error('MAIL_USERNAME is not configured. Create includes/mail_config.php from the example.');
        return false;
    }
    if (mail_app_password() === '' || mail_app_password() === 'your-16-char-app-password') {
        mail_log_error('MAIL_PASSWORD is not configured.');
        return false;
    }

    $verificationLink = build_verification_url($token);
    $subject = 'Verify your ApexClubVerse Account';
    $body = "
        <h2>Welcome to ApexClubVerse</h2>
        <p>Thank you for registering.</p>
        <p>Please click the button below to verify your email.</p>
        <p><a href='{$verificationLink}'
           style='background:#7a1028;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;display:inline-block;'>
           Verify Account
        </a></p>
        <p>Or open this link:<br>{$verificationLink}</p>
        <p>If you did not create this account, ignore this email.</p>
    ";
    $alt = "Welcome to ApexClubVerse.\nVerify your account: {$verificationLink}\n";

    $attempts = [
        [
            'host' => MAIL_HOST,
            'port' => (int)MAIL_PORT,
            'enc'  => strtolower((string)MAIL_ENCRYPTION) === 'ssl' ? 'ssl' : 'tls',
        ],
    ];

    // Extra Gmail-friendly fallback if primary settings differ
    if (!(MAIL_HOST === 'smtp.gmail.com' && (int)MAIL_PORT === 587)) {
        $attempts[] = ['host' => 'smtp.gmail.com', 'port' => 587, 'enc' => 'tls'];
    }
    if (!(MAIL_HOST === 'smtp.gmail.com' && (int)MAIL_PORT === 465)) {
        $attempts[] = ['host' => 'smtp.gmail.com', 'port' => 465, 'enc' => 'ssl'];
    }

    $lastError = '';

    foreach ($attempts as $attempt) {
        $mail = new PHPMailer(true);
        try {
            configure_phpmailer_smtp($mail, $attempt['host'], $attempt['port'], $attempt['enc']);
            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->addReplyTo(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $alt;
            $mail->send();
            return true;
        } catch (Exception $e) {
            $lastError = 'SMTP ' . $attempt['host'] . ':' . $attempt['port'] . ' — ' . $mail->ErrorInfo;
            mail_log_error($lastError);
        }
    }

    // Last resort: PHP mail() (works on some shared hosts that block external SMTP auth oddly)
    if (MAIL_ALLOW_PHP_MAIL) {
        $mail = new PHPMailer(true);
        try {
            $mail->isMail();
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $alt;
            $mail->send();
            return true;
        } catch (Exception $e) {
            $lastError = 'PHP mail() — ' . $mail->ErrorInfo;
            mail_log_error($lastError);
        }
    }

    mail_log_error('All mail transports failed for ' . $email . '. Last: ' . $lastError);
    return false;
}
}
