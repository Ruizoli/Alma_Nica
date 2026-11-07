<?php
defined('ALMANICA') || exit;
require_once __DIR__ . '/../../compartido/operaciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['accion']) && $_GET['accion'] === 'guardar') {
        $token = (string) ($_POST['operacion_token'] ?? '');
        
        if (!isset($_SESSION['operaciones'][$token]) || $_SESSION['operaciones'][$token] !== $modulo) { 
            throw new InvalidArgumentException('Este formulario ya se procesó o venció.'); 
        }
        
        if (empty($_POST['detalle']) && !empty($_POST['detalle_json'])) {
            $d = json_decode((string) $_POST['detalle_json'], true);
            if (is_array($d)) { $_POST['detalle'] = array_values($d); }
        }

        if (empty($_POST['detalle']) || !is_array($_POST['detalle'])) {
            throw new InvalidArgumentException('Agrega al menos un producto.');
        }

        $nuevoId = crearOperacion($conexion, false, $_POST, (int) $_SESSION['empleado']['id']);
        unset($_SESSION['operaciones'][$token]);
        $_SESSION['mensaje'] = 'Venta #' . $nuevoId . ' registrada correctamente.';
        
        // Redirige directamente al archivo de impresión independiente sin pasar por el enrutador
        header('Location: app/modulos/ventas/imprimir.php?id=' . $nuevoId); 
        exit;
    }
}

// Mantenemos $vista = 'ventas' para que el enrutador cargue la vista principal sin errores
$vista = 'ventas'; 
try {
    $idVenta = empty($_GET['id']) ? 0 : (int)$_GET['id'];
    
    // Si no hay ID, cargamos los datos habituales para el catálogo y el historial
    if ($idVenta === 0) {
        $productos = $conexion->query('SELECT Id_Producto, Codigo, Nombre, Precio_Venta, Stock_Actual, Imagen FROM Producto ORDER BY Nombre')->fetchAll();
        $terceros = $conexion->query('SELECT Id_Cliente AS id, Nombre FROM Cliente ORDER BY Nombre')->fetchAll();
        $listado = historialOperaciones($conexion, false, max(1, (int) ($_GET['pagina'] ?? 1)), trim((string)($_GET['q'] ?? '')));
    }

    $tokenOperacion = bin2hex(random_bytes(24));
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
        $_SESSION['operaciones'][$tokenOperacion] = $modulo;
        $_SESSION['operaciones'] = array_slice($_SESSION['operaciones'], -20, null, true);
    }
} catch (Throwable $e) { 
    error_log($e->getMessage()); 
    $error = 'No se pudieron cargar los datos.'; 
    $vista = 'error'; 
}