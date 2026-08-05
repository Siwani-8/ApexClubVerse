<?php
/**
 * Mail credentials for ApexClubVerse.
 *
 * ON THE LIVE SERVER:
 *   1. Copy this file to mail_config.php (same folder).
 *   2. Fill in a Gmail address + App Password (not your normal password):
 *      https://myaccount.google.com/apppasswords
 *   3. Keep mail_config.php off Git (it is gitignored).
 *
 * ProFreeHost notes:
 *   - Outbound SMTP is allowed on port 587; if Gmail still fails, try 465 below.
 *   - Spaces in App Passwords are optional; the mailer strips them automatically.
 */
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);                 // try 465 if 587 fails
define('MAIL_ENCRYPTION', 'tls');         // 'tls' for 587, 'ssl' for 465
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-16-char-app-password');
define('MAIL_FROM_EMAIL', 'your-email@gmail.com');
define('MAIL_FROM_NAME', 'ApexClubVerse');
define('MAIL_ALLOW_PHP_MAIL', true);      // last-resort fallback on shared hosts
define('MAIL_DEBUG_LOG', true);           // writes includes/mail_error.log when send fails
