<?php
/**
 * Brevo / verification mail test
 *
 * Local:  http://localhost/ApexClubVerse/smtp_test.php?to=you@gmail.com
 * Live:   https://apexclubverse.unaux.com/smtp_test.php?to=you@gmail.com
 * Plain:  add &plain=1
 */
header('Content-Type: text/plain; charset=UTF-8');

$root = __DIR__;
require_once $root . '/includes/config.php';
require_once $root . '/includes/send_verification_email.php';

$to = trim((string) ($_GET['to'] ?? ''));
if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    echo "Usage: smtp_test.php?to=recipient@example.com\n";
    echo "Optional: &plain=1\n";
    exit(1);
}

$plainOnly = !empty($_GET['plain']);
echo "From      : " . mail_from_email() . "\n";
echo "Brevo API : " . (mail_has_brevo_api() ? 'configured (xkeysib-)' : 'not set') . "\n";
echo "Configured: " . (mail_has_credentials() ? 'yes' : 'no') . "\n";
echo "Mode      : " . ($plainOnly ? 'plain text' : 'branded HTML') . "\n\n";

if (!mail_has_credentials()) {
    echo "ERROR: Edit includes/mail_config.php — set MAIL_BREVO_API_KEY (xkeysib-...)\n";
    exit(1);
}

$subject = 'Verify your ApexClubVerse account (test)';
$link = apex_absolute_url('login.php');
$body = build_verification_email_html($to, $link, 'Test User');
$alt = build_verification_email_text($link, 'Test User');

$send = apex_send_mail($to, $subject, $body, $alt, $plainOnly);
if (!$send['ok']) {
    echo "FAIL: " . ($send['error'] ?? 'unknown') . "\n";
    echo "Check includes/mail_error.log\n";
    exit(1);
}

echo "OK: Sent to {$to} via " . ($send['profile'] ?? $send['via'] ?? 'mail') . ".\n";
if (!empty($send['message_id'])) {
    echo "Message-ID: " . $send['message_id'] . "\n";
}
echo "\nCheck inbox + Spam for the branded ApexClubVerse verification design.\n";
