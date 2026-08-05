<?php
/**
 * ApexClubVerse – shared configuration
 *
 * Works for:
 *   - Local XAMPP:  http://localhost/apexclubverse/
 *   - ProFreeHost:  https://yoursite.profreehost.com/  (or a subfolder)
 *
 * On ProFreeHost, update DB_* below with the values from your control panel.
 */

if (defined('APEX_CONFIG_LOADED')) {
    return;
}
define('APEX_CONFIG_LOADED', true);

/* ── Database (ProFreeHost: replace these with panel credentials ON THE SERVER ONLY) ── */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'apex_club_db');

/* ── Paths ── */
define('ROOT_PATH', dirname(__DIR__));

/**
 * Web base path of the app (no trailing slash).
 * Example: "" when at domain root, "/apexclubverse" in a subfolder.
 */
if (!defined('BASE_PATH')) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = rtrim(dirname($scriptName), '/');
    // Normalize Windows-style empty dirname
    if ($dir === '' || $dir === '.' || $dir === '\\') {
        $dir = '';
    }
    define('BASE_PATH', $dir === '/' ? '' : $dir);
}

/**
 * Build an app-relative URL (works in subdirectory or at domain root).
 */
function url(string $path = ''): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    if ($path === '') {
        return BASE_PATH === '' ? '/' : BASE_PATH . '/';
    }
    return (BASE_PATH === '' ? '' : BASE_PATH) . '/' . $path;
}

/**
 * Absolute filesystem path under the project root.
 */
function root_path(string $path = ''): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    return ROOT_PATH . ($path === '' ? '' : DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
}

/**
 * Resolve an event/cover image stored in the DB to a usable web path.
 * Accepts full relative paths or bare filenames.
 */
function media_url(?string $image): string
{
    if ($image === null || trim($image) === '') {
        return '';
    }

    $image = str_replace('\\', '/', trim($image));

    if (preg_match('#^(https?:)?//#i', $image)) {
        return $image;
    }

    $image = ltrim($image, '/');

    // Already a project-relative path
    if (strpos($image, '/') !== false) {
        if (is_file(root_path($image))) {
            return url($image);
        }
        // Fall through to basename search if missing
        $image = basename($image);
    }

    $candidates = [
        'uploads/events/' . $image,
        'images/events/' . $image,
        'images/' . $image,
        'images/members/' . $image,
    ];

    foreach ($candidates as $rel) {
        if (is_file(root_path($rel))) {
            return url($rel);
        }
    }

    // Last resort: return as-is under images/
    return url('images/' . $image);
}

/**
 * Member photo web path (files live in images/members/).
 */
function member_photo_url(?string $photo): string
{
    if ($photo === null || trim($photo) === '') {
        return url('images/logo.png');
    }
    $photo = basename(str_replace('\\', '/', trim($photo)));
    $rel = 'images/members/' . $photo;
    if (is_file(root_path($rel))) {
        return url($rel);
    }
    return url('images/logo.png');
}

/**
 * Redirect to an app path and exit.
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * One-time flash messages shown in a page banner (not browser alerts).
 */
function flash_set(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type' => $type === 'error' ? 'error' : 'success',
        'message' => $message,
    ];
}

function flash_render(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['flash']['message'])) {
        return '';
    }
    $type = $_SESSION['flash']['type'] ?? 'success';
    $message = (string)$_SESSION['flash']['message'];
    unset($_SESSION['flash']);
    $class = $type === 'error' ? 'app-flash app-flash-error' : 'app-flash app-flash-success';
    return '<div class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" role="status">'
        . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
        . '</div>';
}
