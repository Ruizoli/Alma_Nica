<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

require_once __DIR__ . '/modelo.php';
$datos = obtenerDashboard($conexion); $vista = 'dashboard';
