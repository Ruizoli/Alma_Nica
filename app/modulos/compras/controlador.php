<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var PDO $conexion */
/** @var string $modulo */
/** @var string $accion */

// Se incluye desde sistema.php después de verificar sesión, permisos y CSRF.
defined('ALMANICA') || exit;
require_once __DIR__ . '/modelo.php';
require_once __DIR__ . '/esquema.php';
comprobarEsquemaCompras($conexion);
$idOrden=empty($_GET['id'])?0:entero($_GET['id'],1);
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $operacion=(string)($_POST['accion_compra'] ?? '');
    $idPost=empty($_POST['id_orden'])?0:entero($_POST['id_orden'],1);
    if (in_array($operacion,['cerrar','revertir'],true)) {
        if ($modulo!=='compras' || !esAdmin()) { http_response_code(403); throw new InvalidArgumentException('Solo el administrador puede cerrar o revertir compras.'); }
        if ($operacion==='cerrar') cerrarOrden($conexion,$idPost,(int)$_SESSION['empleado']['id'],(string)($_POST['motivo'] ?? ''));
        else revertirRecepcion($conexion,$idPost,entero($_POST['id_recepcion'] ?? '',1),(int)$_SESSION['empleado']['id'],(string)($_POST['motivo'] ?? ''));
        $_SESSION['mensaje']=$operacion==='cerrar'?'Orden cerrada. Se conserva lo recibido.':'Recepción revertida. Stock actualizado y orden pendiente.';
        header('Location: '.ruta('compras',['id'=>$idPost])); exit;
    }
    if ($operacion==='cancelar') {
        cancelarOrden($conexion,$idPost);
        volver('compras','Orden cancelada.');
    }
    $token=(string)($_POST['operacion_token'] ?? '');
    if (($_SESSION['operaciones'][$token] ?? '') !== $modulo) throw new InvalidArgumentException('Este formulario ya se procesó o venció. Recarga la página.');
    if ($operacion==='recibir' && $modulo==='recepcion') {
        $recepcion=recibirOrden($conexion,$idPost,$_POST,(int)$_SESSION['empleado']['id'],$token);
        unset($_SESSION['operaciones'][$token]);
        $_SESSION['mensaje']='Recepción #'.$recepcion.' guardada. El stock ya está actualizado.';
        header('Location: '.ruta('compras',['id'=>$idPost])); exit;
    }
    if ($operacion==='guardar' && $modulo==='compras') {
        if ($idPost) { editarOrden($conexion,$idPost,$_POST); $nuevoId=$idPost; }
        else $nuevoId=crearOperacion($conexion,true,$_POST,(int)$_SESSION['empleado']['id']);
        unset($_SESSION['operaciones'][$token]);
        $_SESSION['mensaje']='Orden de compra guardada correctamente.';
        header('Location: '.ruta('compras',['id'=>$nuevoId])); exit;
    }
    throw new InvalidArgumentException('Acción de compra no válida.');
}
