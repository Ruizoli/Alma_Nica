<?php
// Preparación de datos y procesamiento de guardado para ventas.
defined('ALMANICA') || exit;

require_once __DIR__ . '/../../compartido/operaciones.php';

// 1. PROCESAR EL ENVÍO DEL FORMULARIO (POST / Guardar venta)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_GET['accion'] ?? '') === 'guardar')) {
    $token = (string) ($_POST['operacion_token'] ?? '');
    
    if (!isset($_SESSION['operaciones'][$token]) || $_SESSION['operaciones'][$token] !== $modulo) { 
        throw new InvalidArgumentException('Este formulario ya se procesó o venció. Abre una nueva operación.'); 
    }

    // Capturar si se presionó "registrar" o "facturar"
// Capturar si se presionó "registrar" o "facturar" 
        $accionTipo = $_POST['accion_tipo'] ?? 'registrar';

    // ... (aquí se ejecuta crearOperacion como ya lo tienes) ...
    $nuevoId = crearOperacion($conexion, false, $_POST, (int) $_SESSION['empleado']['id']);

    unset($_SESSION['operaciones'][$token]);
    $_SESSION['mensaje'] = 'Venta #' . $nuevoId . ' registrada correctamente.';

    // Redirección inteligente según el botón seleccionado
    if ($accionTipo === 'facturar') {
    header('Location: ' . ruta($modulo, ['accion' => 'detalle', 'id' => $nuevoId]));
     } else {
        header('Location: ' . ruta($modulo, ['id' => $nuevoId])); 
     }
    exit;
    // MAPEO OBLIGATORIO: Transformamos 'detalle' al formato exacto que 'lineasOperacion' exige ($_POST['producto'] y $_POST['cantidad'])
    if (!empty($_POST['detalle']) && is_array($_POST['detalle'])) {
        $_POST['producto'] = [];
        $_POST['cantidad'] = [];
        foreach ($_POST['detalle'] as $item) {
            $idProd = $item['Id_Producto'] ?? $item['id_producto'] ?? 0;
            $cant   = $item['Cantidad'] ?? $item['cantidad'] ?? 1;
            if ($idProd > 0) {
                $_POST['producto'][] = $idProd;
                $_POST['cantidad'][] = $cant;
            }
        }
    }

    // Mapear el cliente y el método de pago a las claves que espera crearOperacion()
    if (isset($_POST['Id_Cliente'])) {
        $_POST['cliente'] = $_POST['Id_Cliente'];
    }
    if (isset($_POST['Metodo_Pago'])) {
        $_POST['metodo'] = $_POST['Metodo_Pago'];
    }

    // Crear la operación de venta en la BD y descontar stock automáticamente
    $nuevoId = crearOperacion($conexion, false, $_POST, (int) $_SESSION['empleado']['id']);
    
    unset($_SESSION['operaciones'][$token]);
    $_SESSION['mensaje'] = 'Venta #' . $nuevoId . ' registrada correctamente.';
    
    // Redirección inteligente según el botón seleccionado
    if ($accionTipo === 'facturar') {
        header('Location: ' . ruta($modulo, ['accion' => 'detalle', 'id' => $nuevoId]));
    } else {
        header('Location: ' . ruta($modulo, ['id' => $nuevoId])); 
    }
    exit;
}

// 2. PREPARACIÓN DE DATOS PARA LA VISTA (GET / Cargar catálogo, clientes e historial)
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