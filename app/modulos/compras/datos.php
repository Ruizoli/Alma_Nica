<?php
// Preparación de datos, incluso después de errores de formulario.
defined('ALMANICA') || exit;

require_once __DIR__ . '/modelo.php';
try {
    $idOrden=empty($_GET['id'])?0:entero($_GET['id'],1);
    $orden=$idOrden?obtenerOrden($conexion,$idOrden):null;
    $lineas=$idOrden?lineasOrden($conexion,$idOrden):[];
    $recepciones=$idOrden?recepcionesOrden($conexion,$idOrden):[];
    $productos=$conexion->query('SELECT Id_Producto,Codigo,Nombre,Precio_Venta,Stock_Actual,Imagen FROM Producto ORDER BY Nombre')->fetchAll();
    $proveedores=$conexion->query('SELECT Id_Proveedor,Nombre FROM Proveedor ORDER BY Nombre')->fetchAll();
    if ($modulo==='historial') {
        $listado=historialRecepciones($conexion,(int)($_GET['pagina'] ?? 1),trim((string)($_GET['q'] ?? '')));
        $vista='historial_compras';
    } elseif ($modulo==='recepcion' && $orden) {
        $vista='recepcion';
    } elseif ($modulo==='compras' && in_array($accion,['nuevo','editar'],true)) {
        if ($accion==='editar' && (!$orden || $orden['Estado']!=='Pendiente' || $recepciones)) throw new InvalidArgumentException('Solo se editan órdenes pendientes sin recepciones.');
        $vista='orden_formulario';
    } elseif ($modulo==='compras' && $orden) {
        $vista='orden_detalle';
    } else {
        $estadoFiltro=$modulo==='recepcion'?'Pendiente':(string)($_GET['estado'] ?? '');
        $listado=listarOrdenes($conexion,$estadoFiltro,trim((string)($_GET['q'] ?? '')),(int)($_GET['pagina'] ?? 1));
        $vista='ordenes';
    }
    $tokenOperacion=bin2hex(random_bytes(24));
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
    $_SESSION['operaciones'][$tokenOperacion]=$modulo;
    $_SESSION['operaciones']=array_slice($_SESSION['operaciones'],-20,null,true);
    }
} catch (InvalidArgumentException $e) { $error=$e->getMessage(); $vista='error'; }
catch (PDOException $e) { $error=errorBase($e); $vista='error'; }
