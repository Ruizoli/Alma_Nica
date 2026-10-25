<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

require_once __DIR__ . '/../../compartido/operaciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = (string) ($_POST['operacion_token'] ?? '');
    if (!isset($_SESSION['operaciones'][$token]) || $_SESSION['operaciones'][$token] !== $modulo) { throw new InvalidArgumentException('Este formulario ya se procesó o venció. Abre una nueva operación.'); }
    $nuevoId = crearOperacion($conexion,false,$_POST,(int) $_SESSION['empleado']['id']);
    unset($_SESSION['operaciones'][$token]);
    $_SESSION['mensaje'] = 'Venta' . ' #' . $nuevoId . ' registrada correctamente.';
    header('Location: ' . ruta($modulo,['id'=>$nuevoId])); exit;
}
$vista = 'ventas';
