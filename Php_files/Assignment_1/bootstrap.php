<?php
// Start the session before sending any page content.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite' => 'Lax']);
if (!session_start()) {
    http_response_code(503);
    exit('Session storage is unavailable. Please try again later.');
}
// Database mode is private; public portfolio visitors use the isolated demo.
if (getenv('APP_MODE') === 'database') {
    $adminUser = getenv('ADMIN_USERNAME');
    $adminPassword = getenv('ADMIN_PASSWORD');
    if (!$adminUser || !$adminPassword) {
        http_response_code(503);
        exit('Configure administrator credentials before using database mode.');
    }
    if (!hash_equals($adminUser, $_SERVER['PHP_AUTH_USER'] ?? '') || !hash_equals($adminPassword, $_SERVER['PHP_AUTH_PW'] ?? '')) {
        header('WWW-Authenticate: Basic realm="Student Portal"');
        http_response_code(401);
        exit('Administrator sign-in required.');
    }
}
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self' data:; script-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
header('Cache-Control: no-store');

require_once __DIR__ . '/validate.php';
require_once __DIR__ . '/crud.php';

function escape($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function input(array $source, string $key): string {
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : '';
}
function redirect(string $url): void {
    header('Location: ' . $url, true, 303);
    exit;
}
function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function verifyPost(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('Use the form to make this change.');
    }
    if (!hash_equals(csrfToken(), input($_POST, 'csrf'))) {
        http_response_code(403);
        exit('Your form expired. Reload the page and try again.');
    }
}
function demoMode(): bool {
    return getenv('APP_MODE') !== 'database';
}
function flash(string $message): void {
    $_SESSION['message'] = $message;
}
