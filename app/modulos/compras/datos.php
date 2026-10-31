<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var PDO $conexion */
/** @var string $modulo */
/** @var string $accion */

// Preparación de datos, incluso después de errores de formulario.
defined('ALMANICA') || exit;

require_once __DIR__ . '/modelo.php';
require_once __DIR__ . '/esquema.php';
try {
    comprobarEsquemaCompras($conexion);
    $resumenCompras = $conexion->query("SELECT Estado, COUNT(*) AS Cantidad FROM OrdenCompra GROUP BY Estado")->fetchAll(PDO::FETCH_KEY_PAIR);
    $idOrden=empty($_GET['id'])?0:entero($_GET['id'],1);
    $orden=$idOrden?obtenerOrden($conexion,$idOrden):null;
    $lineas=$idOrden?lineasOrden($conexion,$idOrden):[];
    $recepciones=$idOrden?recepcionesOrden($conexion,$idOrden):[];
    $productos=$conexion->query('SELECT Id_Producto,Codigo,Nombre,Precio_Venta,Stock_Actual,Imagen FROM Producto ORDER BY Nombre')->fetchAll();
    $proveedores=$conexion->query('SELECT Id_Proveedor,Nombre FROM Proveedor ORDER BY Nombre')->fetchAll();
    if ($modulo==='historial') {
        $listado=historialRecepciones($conexion,(int)($_GET['pagina'] ?? 1),trim((string)($_GET['q'] ?? '')));
        $vista='historial_compras';
        if (isset($_GET['csv']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="recepciones-almanica.csv"');
            $salida=fopen('php://output','w'); fwrite($salida,"\xEF\xBB\xBF");
            fputcsv($salida,['Recepción','Orden','Proveedor','Factura','Responsable','Fecha','Unidades','Importe original C$','Estado'],';');
            $paginaExportar=1;
            do {
                $lote=historialRecepciones($conexion,$paginaExportar,trim((string)($_GET['q'] ?? '')));
                foreach ($lote['filas'] as $r) {
                    $valores=['RC-'.str_pad($r['Id_Recepcion'],5,'0',STR_PAD_LEFT),'OC-'.str_pad($r['Id_OrdenCompra'],5,'0',STR_PAD_LEFT),$r['Proveedor'],$r['Factura'] ?? '',$r['Empleado'],fechaCorta($r['Fecha']),$r['Unidades'],$r['Total_Recibido'],$r['Reversion']?'Revertida':'Vigente'];
                    fputcsv($salida,array_map(fn($v)=>preg_match('/^[=+@\-\t\r\n]/',(string)$v) ? "'".$v : $v,$valores),';');
                }
            } while (++$paginaExportar <= $lote['paginas']);
            fclose($salida); exit;
        }
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
