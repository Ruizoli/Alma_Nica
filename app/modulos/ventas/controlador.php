<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

require_once __DIR__ . '/../../compartido/operaciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['accion']) && $_GET['accion'] === 'guardar') {
        $token = (string) ($_POST['operacion_token'] ?? '');
        
        if (!isset($_SESSION['operaciones'][$token]) || $_SESSION['operaciones'][$token] !== $modulo) { 
            throw new InvalidArgumentException('Este formulario ya se procesó o venció. Abre una nueva operación.'); 
        }
        
        // Si el detalle tradicional viene vacío, lo rescatamos del JSON que comprobamos que sí viaja
        if (empty($_POST['detalle']) && !empty($_POST['detalle_json'])) {
            $d = json_decode((string) $_POST['detalle_json'], true);
            if (is_array($d)) {
                $_POST['detalle'] = array_values($d);
            }
        }

        // Validación estricta con el array ya asegurado
        if (empty($_POST['detalle']) || !is_array($_POST['detalle'])) {
            throw new InvalidArgumentException('Agrega entre 1 y 100 productos.');
        }

        // Creamos la operación de venta en la base de datos y descontamos stock
        $nuevoId = crearOperacion($conexion, false, $_POST, (int) $_SESSION['empleado']['id']);
        
        unset($_SESSION['operaciones'][$token]);
        $_SESSION['mensaje'] = 'Venta #' . $nuevoId . ' registrada correctamente.';
        
        header('Location: ' . ruta($modulo, ['id' => $nuevoId])); 
        exit;
    }
}

$vista = 'ventas'; 
try {
    $productos = $conexion->query('SELECT Id_Producto, Codigo, Nombre, Precio_Venta, Stock_Actual, Imagen FROM Producto ORDER BY Nombre')->fetchAll();
    $terceros = $conexion->query('SELECT Id_Cliente AS id, Nombre FROM Cliente ORDER BY Nombre')->fetchAll();
    $listado = historialOperaciones($conexion, false, max(1, (int) ($_GET['pagina'] ?? 1)), trim((string)($_GET['q'] ?? '')));
    $detalle = empty($_GET['id']) ? [] : detalleOperacion($conexion, false, entero($_GET['id'], 1));
    $tokenOperacion = bin2hex(random_bytes(24));
    
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
        $_SESSION['operaciones'][$tokenOperacion] = $modulo;
        $_SESSION['operaciones'] = array_slice($_SESSION['operaciones'], -20, null, true);
    }
} catch (Throwable $e) { 
    error_log($e->getMessage()); 
    $error = 'No se pudieron cargar los datos de la operación.'; 
    $vista = 'error'; 
}