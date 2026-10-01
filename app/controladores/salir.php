<?php
if (!defined('ALMANICA_ENTRADA')) { http_response_code(404); exit; }
require_once __DIR__ . '/../inicio.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
    http_response_code(403);
    exit('Solicitud no válida.');
}
$_SESSION = [];
$parametros = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 42000,
    'path' => $parametros['path'],
    'domain' => $parametros['domain'],
    'secure' => $parametros['secure'],
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_destroy();
header('Location: index.php');
exit;
