<?php
defined('ALMANICA') || exit;
require_once __DIR__ . '/../../compartido/operaciones.php';
$vista = 'ventas';
try {
    $idVenta = empty($_GET['id']) ? 0 : entero($_GET['id'], 1);
    if ($idVenta) {
        $q = $conexion->prepare("SELECT v.*, COALESCE(c.Nombre,'Consumidor final') AS Cliente, e.Nombre AS Vendedor FROM Venta v LEFT JOIN Cliente c ON c.Id_Cliente=v.Id_Cliente JOIN Empleado e ON e.Id_Empleado=v.Id_Empleado WHERE v.Id_Venta=?");
        $q->execute([$idVenta]); $venta = $q->fetch();
        if (!$venta) throw new InvalidArgumentException('No se encontró la venta.');
        $q = $conexion->prepare('SELECT p.Codigo,p.Nombre,d.Cantidad,d.Precio_Unitario,d.Subtotal FROM Detalle_Venta d JOIN Producto p ON p.Id_Producto=d.Id_Producto WHERE d.Id_Venta=? ORDER BY d.Id_Detalle_Venta');
        $q->execute([$idVenta]); $detalle = $q->fetchAll();
        $ventaRecienGuardada = (int)($_SESSION['venta_guardada'] ?? 0) === $idVenta;
        unset($_SESSION['venta_guardada']);
        $vista = 'venta_detalle';
    } else {
        $productos = $conexion->query('SELECT Id_Producto,Codigo,Nombre,Precio_Venta,Stock_Actual,Imagen FROM Producto ORDER BY Nombre')->fetchAll();
        $terceros = $conexion->query('SELECT Id_Cliente AS id,Nombre FROM Cliente ORDER BY Nombre')->fetchAll();
        $listado = historialOperaciones($conexion,false,max(1,(int)($_GET['pagina'] ?? 1)),trim((string)($_GET['q'] ?? '')));
        $tokenOperacion = bin2hex(random_bytes(24));
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
            $_SESSION['operaciones'][$tokenOperacion] = $modulo;
            $_SESSION['operaciones'] = array_slice($_SESSION['operaciones'],-20,null,true);
        }
    }
} catch (InvalidArgumentException $e) { $error=$e->getMessage(); $vista='error'; }
catch (PDOException $e) { $error=errorBase($e); $vista='error'; }
