<?php
if (!defined('ALMANICA_ENTRADA')) { http_response_code(404); exit; }
require_once __DIR__ . '/../inicio.php';
require_once __DIR__ . '/../middleware/autenticacion.php';
require_once __DIR__ . '/../soporte/sistema.php';
require_once __DIR__ . '/../compartido/catalogo_modelo.php';
$modulo = (string) ($_GET['modulo'] ?? moduloInicial());
$catalogos = catalogos();
$titulos = ['buscar'=>'Búsqueda','dashboard'=>'Dashboard','ventas'=>'Ventas','compras'=>'Órdenes de compra','recepcion'=>'Recepción de compras','historial'=>'Historial de compras','reportes'=>'Reportes','configuracion'=>'Configuración'];
foreach ($catalogos as $key=>$c) { $titulos[$key] = $c['titulo']; }
if (!isset($titulos[$modulo])) { http_response_code(404); exit('El módulo no existe.'); }
if (!puedeVer($modulo)) { http_response_code(403); exit('No tienes permiso para acceder a este módulo.'); }
$titulo = $titulos[$modulo]; $error = ''; $mensaje = $_SESSION['mensaje'] ?? ''; unset($_SESSION['mensaje']);
$accion = (string) ($_GET['accion'] ?? '');
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrfValido()) { http_response_code(403); throw new InvalidArgumentException('La sesión del formulario venció. Recarga la página.'); }
        if (!puedeEditar($modulo)) { http_response_code(403); throw new InvalidArgumentException('No tienes permiso para modificar este módulo.'); }
    }
    if (isset($catalogos[$modulo])) {
        require __DIR__ . '/../compartido/catalogo_acciones.php';
    } elseif (in_array($modulo,['compras','recepcion','historial'],true)) {
        require __DIR__ . '/../modulos/compras/controlador.php';
    } elseif ($modulo === 'ventas') {
        require __DIR__ . '/../modulos/ventas/controlador.php';
    } elseif ($modulo==='buscar') {
        require __DIR__ . '/../modulos/busqueda/controlador.php';
    } elseif ($modulo === 'reportes') {
        require __DIR__ . '/../modulos/reportes/controlador.php';
    } elseif ($modulo === 'configuracion') {
        require __DIR__ . '/../modulos/configuracion/controlador.php';
    } else {
        require __DIR__ . '/../modulos/dashboard/controlador.php';

    }
} catch (InvalidArgumentException $e) { $error = $e->getMessage(); }
catch (PDOException $e) { $error = errorBase($e); }
// Preparación de vistas incluso cuando un formulario contiene errores.
if (isset($catalogos[$modulo])) {
    $vista = 'catalogo';
    $def = $catalogos[$modulo]; $registro = $registro ?? []; $opciones = $opciones ?? [];
    $listado = $listado ?? ['filas'=>[],'total'=>0,'pagina'=>1,'paginas'=>1];
}
if ($modulo === 'ventas') {
    require __DIR__ . '/../modulos/ventas/datos.php';
}
if (in_array($modulo,['compras','recepcion','historial'],true)) {
    require __DIR__ . '/../modulos/compras/datos.php';
}
$vista = $vista ?? 'error';
require __DIR__ . '/../vistas/parciales/cabecera.php';
$rutasVistas = require __DIR__ . '/../../config/vistas.php';
if ($vista === 'catalogo') {
    $archivo = in_array($accion, ['nuevo', 'editar'], true) && puedeEditar($modulo)
        ? 'formulario.php' : 'listado.php';
    require __DIR__ . '/../modulos/' . ($modulo === 'productos' ? 'inventario' : $modulo) . '/' . $archivo;
} else {
    require __DIR__ . '/../vistas/' . ($rutasVistas[$vista] ?? 'error.php');
}
require __DIR__ . '/../vistas/parciales/pie.php';
