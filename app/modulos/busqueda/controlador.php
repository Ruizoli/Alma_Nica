<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

$busquedaGlobal=trim((string)($_GET['q'] ?? ''));
$resultadosGlobales=[];
if ($busquedaGlobal!=='') {
    foreach (['productos','clientes','proveedores'] as $tipo) {
        if (!puedeVer($tipo)) continue;
        $definicion=$catalogos[$tipo];
        $resultado=listarCatalogo($conexion,$definicion,$busquedaGlobal,1,[]);
        $resultadosGlobales[$tipo]=$resultado['filas'];
    }
}
$vista='buscar';
