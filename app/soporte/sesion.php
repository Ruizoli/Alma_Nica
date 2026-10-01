<?php
defined('ALMANICA') || exit;
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();
if (isset($_SESSION['actividad']) && time() - $_SESSION['actividad'] > 1800) { $_SESSION = []; session_regenerate_id(true); }
$_SESSION['actividad'] = time();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
