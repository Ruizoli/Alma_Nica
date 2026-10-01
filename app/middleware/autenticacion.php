<?php
// Solo se ejecuta después de inicializar la aplicación.
defined('ALMANICA') || exit;
require_once __DIR__ . '/../modelos/usuario.php';

if (!isset($_SESSION['empleado']['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $conexion = conectar();
    $actual = buscarUsuarioActivo($conexion, (int) $_SESSION['empleado']['usuario_id']);
    if (!$actual || (isset($_SESSION['credencial']) && !hash_equals($_SESSION['credencial'], hash('sha256', $actual['Contrasena'])))) {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php');
        exit;
    }
    $_SESSION['empleado']['id'] = (int) $actual['Id_Empleado'];
    $_SESSION['credencial'] = hash('sha256', $actual['Contrasena']);
    $_SESSION['empleado']['nombre'] = $actual['Nombre'];
    $_SESSION['empleado']['rol'] = $actual['Rol'];
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(503);
    exit('No se pudo verificar tu acceso. Inicia MySQL en XAMPP y recarga la página.');
}
