<?php
// Preparación de datos, incluso después de errores de formulario.
defined('ALMANICA') || exit;

$vista = 'ventas'; 
try {
    $productos = $conexion->query('SELECT Id_Producto,Codigo,Nombre,Precio_Venta,Stock_Actual,Imagen FROM Producto ORDER BY Nombre')->fetchAll();
    $terceros = $conexion->query('SELECT Id_Cliente AS id,Nombre FROM Cliente ORDER BY Nombre')->fetchAll();
    $listado = historialOperaciones($conexion,false,max(1,(int) ($_GET['pagina'] ?? 1)),trim((string)($_GET['q'] ?? '')));
    $detalle = empty($_GET['id']) ? [] : detalleOperacion($conexion,false,entero($_GET['id'],1));
    $tokenOperacion = bin2hex(random_bytes(24));
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
    $_SESSION['operaciones'][$tokenOperacion] = $modulo;
    $_SESSION['operaciones'] = array_slice($_SESSION['operaciones'],-20,null,true);
    }
} catch (Throwable $e) { error_log($e->getMessage()); $error = 'No se pudieron cargar los datos de la operación.'; $vista = 'error'; }
