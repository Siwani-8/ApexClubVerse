<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/config.php';

if (!function_exists('apex_mail_define')) {
    function apex_mail_define(string $name, $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

if (!function_exists('apex_load_mail_config')) {
    function apex_load_mail_config(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $configFile = __DIR__ . '/mail_config.php';
        if (is_file($configFile)) {
            require $configFile;
        } elseif (is_file(__DIR__ . '/mail_config.example.php')) {
            require __DIR__ . '/mail_config.example.php';
        }

        apex_mail_define('MAIL_HOST', 'smtp-relay.brevo.com');
        apex_mail_define('MAIL_PORT', 587);
        apex_mail_define('MAIL_ENCRYPTION', 'tls');
        apex_mail_define('MAIL_USERNAME', '');
        apex_mail_define('MAIL_PASSWORD', '');
        apex_mail_define('MAIL_FROM_EMAIL', 'apexclubverse@gmail.com');
        apex_mail_define('MAIL_FROM_NAME', 'ApexClubVerse');
        apex_mail_define('MAIL_BREVO_API_KEY', '');
        apex_mail_define('MAIL_DEBUG_LOG', true);
        apex_mail_define('MAIL_SMTP_DEBUG', false);
    }
}

if (!function_exists('apex_require_phpmailer')) {
    function apex_require_phpmailer(): bool
    {
        static $loaded = false;
        if ($loaded) {
            return true;
        }
        $base = __DIR__ . '/../lib/PHPMailer/src';
        foreach (['Exception.php', 'PHPMailer.php', 'SMTP.php'] as $file) {
            if (!is_file($base . '/' . $file)) {
                return false;
            }
        }
        require_once $base . '/Exception.php';
        require_once $base . '/PHPMailer.php';
        require_once $base . '/SMTP.php';
        $loaded = true;
        return true;
    }
}

if (!function_exists('mail_app_password')) {
    function mail_app_password(): string
    {
        apex_load_mail_config();
        return preg_replace('/\s+/', '', (string) MAIL_PASSWORD);
    }
}

if (!function_exists('mail_is_placeholder')) {
    function mail_is_placeholder(string $value): bool
    {
        $value = strtolower(trim($value));
        return in_array($value, [
            '',
            'your-email@gmail.com',
            'your-16-char-app-password',
            'your-brevo-smtp-key',
            'your-brevo-api-key',
            'your_app_password',
            'changeme',
            'password',
        ], true);
    }
}

if (!function_exists('mail_has_brevo_api')) {
    function mail_has_brevo_api(): bool
    {
        apex_load_mail_config();
        if (!defined('MAIL_BREVO_API_KEY')) {
            return false;
        }
        $key = trim((string) MAIL_BREVO_API_KEY);
        return $key !== '' && !mail_is_placeholder($key) && strpos($key, 'xkeysib-') === 0;
    }
}

if (!function_exists('mail_has_credentials')) {
    function mail_has_credentials(): bool
    {
        apex_load_mail_config();

        if (mail_has_brevo_api()) {
            return defined('MAIL_FROM_EMAIL') && !mail_is_placeholder((string) MAIL_FROM_EMAIL);
        }

        if (!defined('MAIL_USERNAME') || mail_is_placeholder((string) MAIL_USERNAME)) {
            return false;
        }
        if (mail_app_password() === '' || mail_is_placeholder(mail_app_password())) {
            return false;
        }
        return defined('MAIL_HOST') && trim((string) MAIL_HOST) !== '';
    }
}

if (!function_exists('mail_log_error')) {
    function mail_log_error(string $message): void
    {
        apex_load_mail_config();
        if (!MAIL_DEBUG_LOG) {
            return;
        }
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents(__DIR__ . '/mail_error.log', $line, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('mail_from_email')) {
    function mail_from_email(): string
    {
        apex_load_mail_config();
        if (defined('MAIL_FROM_EMAIL') && !mail_is_placeholder((string) MAIL_FROM_EMAIL)) {
            return (string) MAIL_FROM_EMAIL;
        }
        return defined('MAIL_USERNAME') ? (string) MAIL_USERNAME : 'apexclubverse@gmail.com';
    }
}

if (!function_exists('apex_request_scheme')) {
    function apex_request_scheme(): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        return $https ? 'https' : 'http';
    }
}

if (!function_exists('apex_absolute_url')) {
    function apex_absolute_url(string $path = ''): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return apex_request_scheme() . '://' . $host . url($path);
    }
}

if (!function_exists('build_verification_url')) {
    function build_verification_url(string $token): string
    {
        return apex_absolute_url('verify.php?token=' . urlencode($token));
    }
}

if (!function_exists('build_verification_email_html')) {
    function build_verification_email_html(string $toEmail, string $verificationLink, string $recipientName = ''): string
    {
        $safeLink = htmlspecialchars($verificationLink, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($toEmail, ENT_QUOTES, 'UTF-8');
        $safeName = htmlspecialchars(trim($recipientName), ENT_QUOTES, 'UTF-8');
        $greeting = $safeName !== '' ? $safeName : 'there';
        $logoUrl = htmlspecialchars(apex_absolute_url('images/logo.png'), ENT_QUOTES, 'UTF-8');
        $siteUrl = htmlspecialchars(apex_absolute_url(''), ENT_QUOTES, 'UTF-8');
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify your ApexClubVerse account</title>
</head>
<body style="margin:0;padding:0;background:#f5f3ef;font-family:'Segoe UI',Arial,sans-serif;color:#1e2530;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f3ef;padding:32px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e8e4dc;">
          <tr>
            <td style="background:linear-gradient(110deg,#7a1028 0%,#b23417 55%,#d44000 100%);padding:28px 32px;text-align:center;">
              <img src="{$logoUrl}" alt="ApexClubVerse" width="72" style="display:block;margin:0 auto 12px;border:0;border-radius:12px;background:#fff;padding:6px;">
              <div style="font-size:22px;font-weight:700;color:#ffffff;letter-spacing:0.02em;">ApexClubVerse</div>
              <div style="margin-top:6px;font-size:12px;color:rgba(255,255,255,0.85);text-transform:uppercase;letter-spacing:0.12em;">Apex College · Student Clubs</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 32px 8px;">
              <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#1a1a1a;">Welcome, {$greeting}!</h1>
              <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#555;">
                Thanks for joining <strong>ApexClubVerse</strong>. Confirm
                <span style="color:#7a1028;font-weight:600;">{$safeEmail}</span>
                to activate your account and start exploring clubs, events, and votes.
              </p>
              <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#555;">
                Click the button below to verify your email. This link is unique to your account.
              </p>
              <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
                <tr>
                  <td align="center" style="border-radius:10px;background:#d44000;">
                    <a href="{$safeLink}"
                       style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">
                      Verify my account
                    </a>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 8px;font-size:13px;color:#777;">Button not working? Copy and paste this link:</p>
              <p style="margin:0 0 24px;padding:12px 14px;background:#faf8f4;border:1px solid #ebe6dc;border-radius:8px;font-size:12px;line-height:1.5;word-break:break-all;color:#7a1028;">
                <a href="{$safeLink}" style="color:#7a1028;text-decoration:none;">{$safeLink}</a>
              </p>
              <p style="margin:0;font-size:13px;line-height:1.5;color:#999;">
                If you did not create an ApexClubVerse account, you can ignore this email.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:24px 32px 28px;border-top:1px solid #f0ebe3;text-align:center;">
              <p style="margin:0 0 6px;font-size:12px;color:#999;">
                <a href="{$siteUrl}" style="color:#7a1028;text-decoration:none;font-weight:600;">ApexClubVerse</a>
                · Apex College Student Activity Portal
              </p>
              <p style="margin:0;font-size:11px;color:#bbb;">© {$year} ApexClubVerse. This is an automated message — please do not reply.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}

if (!function_exists('build_verification_email_text')) {
    function build_verification_email_text(string $verificationLink, string $recipientName = ''): string
    {
        $greeting = trim($recipientName) !== '' ? trim($recipientName) : 'there';
        return "Welcome to ApexClubVerse, {$greeting}!\n\n"
            . "Please verify your account by opening this link:\n"
            . "{$verificationLink}\n\n"
            . "If you did not create this account, ignore this email.\n";
    }
}

if (!function_exists('apex_send_brevo_api')) {
    /**
     * @return array{ok:bool,message_id?:string,profile?:string,error?:string}
     */
    function apex_send_brevo_api(
        string $toEmail,
        string $subject,
        string $htmlBody,
        string $altBody,
        bool $plainOnly = false
    ): array {
        apex_load_mail_config();

        if (!mail_has_brevo_api()) {
            return ['ok' => false, 'error' => 'Brevo API key not configured.'];
        }

        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'PHP cURL extension is required for Brevo API.'];
        }

        $fromEmail = mail_from_email();
        $fromName = defined('MAIL_FROM_NAME') ? (string) MAIL_FROM_NAME : 'ApexClubVerse';
        $apiKey = trim((string) MAIL_BREVO_API_KEY);

        $payload = [
            'sender' => [
                'name' => $fromName,
                'email' => $fromEmail,
            ],
            'to' => [
                ['email' => $toEmail],
            ],
            'subject' => $subject,
        ];

        if ($plainOnly) {
            $payload['textContent'] = $altBody !== '' ? $altBody : strip_tags($htmlBody);
        } else {
            $payload['htmlContent'] = $htmlBody;
            $payload['textContent'] = $altBody !== '' ? $altBody : strip_tags($htmlBody);
        }

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'api-key: ' . $apiKey,
                'content-type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $msg = 'Brevo API cURL error: ' . $curlError;
            mail_log_error($msg);
            return ['ok' => false, 'error' => $msg];
        }

        $data = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            $messageId = is_array($data) ? (string) ($data['messageId'] ?? '') : '';
            mail_log_error('OK via Brevo API → ' . $toEmail . ($messageId !== '' ? ' | ' . $messageId : ''));
            return [
                'ok' => true,
                'message_id' => $messageId,
                'profile' => 'Brevo API',
            ];
        }

        $apiMessage = is_array($data) ? (string) ($data['message'] ?? $response) : (string) $response;
        $msg = 'Brevo API HTTP ' . $httpCode . ': ' . $apiMessage;
        mail_log_error($msg);
        return ['ok' => false, 'error' => $msg];
    }
}

if (!function_exists('apex_send_mail_smtp')) {
    /**
     * Fallback SMTP (Brevo SMTP or other). Prefer API on ProFreeHost.
     *
     * @return array{ok:bool,message_id?:string,profile?:string,error?:string}
     */
    function apex_send_mail_smtp(
        string $toEmail,
        string $subject,
        string $htmlBody,
        string $altBody,
        bool $plainOnly = false
    ): array {
        apex_load_mail_config();

        if (!apex_require_phpmailer()) {
            return ['ok' => false, 'error' => 'PHPMailer library missing.'];
        }

        if (!mail_has_credentials() || mail_has_brevo_api()) {
            // When API key exists, callers should use API only.
        }

        if (mail_is_placeholder((string) MAIL_USERNAME) || mail_is_placeholder(mail_app_password())) {
            return ['ok' => false, 'error' => 'SMTP credentials not configured.'];
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = (string) MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = (string) MAIL_USERNAME;
            $mail->Password = mail_app_password();
            $mail->Port = (int) MAIL_PORT;
            $mail->Timeout = 25;
            $mail->CharSet = 'UTF-8';

            $enc = strtolower((string) MAIL_ENCRYPTION);
            if ($enc === 'ssl' || (int) MAIL_PORT === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $fromEmail = mail_from_email();
            $fromName = defined('MAIL_FROM_NAME') ? (string) MAIL_FROM_NAME : 'ApexClubVerse';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);
            $mail->addReplyTo($fromEmail, $fromName);
            $mail->Subject = $subject;

            if ($plainOnly) {
                $mail->isHTML(false);
                $mail->Body = $altBody !== '' ? $altBody : strip_tags($htmlBody);
            } else {
                $mail->isHTML(true);
                $mail->Body = $htmlBody;
                $mail->AltBody = $altBody;
            }

            $mail->send();
            mail_log_error('OK via SMTP ' . MAIL_HOST . ':' . MAIL_PORT . ' → ' . $toEmail);
            return [
                'ok' => true,
                'message_id' => (string) $mail->getLastMessageID(),
                'profile' => 'SMTP ' . MAIL_HOST,
            ];
        } catch (Exception $e) {
            $msg = 'SMTP failed: ' . $mail->ErrorInfo;
            mail_log_error($msg);
            return ['ok' => false, 'error' => $msg];
        }
    }
}

if (!function_exists('apex_send_mail')) {
    /**
     * Prefer Brevo API when configured (best on ProFreeHost).
     *
     * @return array{ok:bool,message_id?:string,profile?:string,via?:string,error?:string}
     */
    function apex_send_mail(
        string $toEmail,
        string $subject,
        string $htmlBody,
        string $altBody,
        bool $plainOnly = false
    ): array {
        if (mail_has_brevo_api()) {
            $api = apex_send_brevo_api($toEmail, $subject, $htmlBody, $altBody, $plainOnly);
            if ($api['ok']) {
                $api['via'] = 'Brevo API';
                return $api;
            }
            return [
                'ok' => false,
                'error' => $api['error'] ?? 'Brevo API request failed.',
                'via' => 'Brevo API',
            ];
        }

        $smtp = apex_send_mail_smtp($toEmail, $subject, $htmlBody, $altBody, $plainOnly);
        $smtp['via'] = $smtp['ok'] ? ($smtp['profile'] ?? 'SMTP') : 'SMTP';
        return $smtp;
    }
}

if (!function_exists('sendVerificationEmail')) {
    function sendVerificationEmail(string $email, string $token, string $recipientName = ''): bool
    {
        if (!mail_has_credentials()) {
            mail_log_error('Mail not configured. Set MAIL_BREVO_API_KEY (xkeysib-...) in includes/mail_config.php.');
            return false;
        }

        $verificationLink = build_verification_url($token);
        $subject = 'Verify your ApexClubVerse account';
        $body = build_verification_email_html($email, $verificationLink, $recipientName);
        $alt = build_verification_email_text($verificationLink, $recipientName);

        $result = apex_send_mail($email, $subject, $body, $alt);
        return !empty($result['ok']);
    }
}
