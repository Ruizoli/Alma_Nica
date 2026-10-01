<?php
// Sirve imágenes como datos, nunca como código ejecutable.
define('ALMANICA_ENTRADA', true);
require __DIR__ . '/app/inicio.php';
require __DIR__ . '/app/middleware/autenticacion.php';
$archivo = (string) ($_GET['archivo'] ?? '');
if (!preg_match('/^[a-f0-9]{48}\.bin$/D', $archivo)) { http_response_code(404); exit; }
$ruta = __DIR__ . '/storage/productos/' . $archivo;
if (!is_file($ruta)) { http_response_code(404); exit; }
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($ruta);
if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) { http_response_code(404); exit; }
session_write_close();
header('Content-Type: ' . $mime);
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'none'; sandbox");
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
