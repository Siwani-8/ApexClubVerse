<?php
/**
 * Mail config — copy to mail_config.php (gitignored) and fill in real values.
 *
 * ── RECOMMENDED (ProFreeHost + local): Brevo API ──
 * 1. Create account: https://www.brevo.com
 * 2. Verify sender: apexclubverse@gmail.com (Senders → green check)
 * 3. SMTP & API → API Keys → generate key starting with xkeysib-
 * 4. If Brevo Security blocks your IP, authorize it or disable IP restriction
 *
 * Optional SMTP settings below are only used when MAIL_BREVO_API_KEY is empty.
 */

define('MAIL_HOST', 'smtp-relay.brevo.com');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_USERNAME', 'your-brevo-smtp-login@smtp-brevo.com');
define('MAIL_PASSWORD', 'your-brevo-smtp-key'); // starts with xsmtpsib-

define('MAIL_FROM_EMAIL', 'apexclubverse@gmail.com');
define('MAIL_FROM_NAME', 'ApexClubVerse');

// Primary transport on ProFreeHost — paste your xkeysib- API key:
define('MAIL_BREVO_API_KEY', 'your-brevo-api-key');

define('MAIL_DEBUG_LOG', true);
define('MAIL_SMTP_DEBUG', false);
