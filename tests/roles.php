<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../app/soporte/sistema.php';
function check(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); echo "OK: $message\n"; }
foreach (['','Desconocido','administrador'] as $rol) {
    $_SESSION=['empleado'=>['rol'=>$rol]];
    foreach (['dashboard','productos','ventas','usuarios','configuracion'] as $m) check(!puedeVer($m) && !puedeEditar($m),"$rol sin acceso a $m");
}
$_SESSION=['empleado'=>['rol'=>'Inventarista']];
foreach (['productos','categorias','proveedores','compras','recepcion'] as $m) check(puedeVer($m)&&puedeEditar($m),"Inventarista administra $m");
foreach (['dashboard','ventas','clientes','usuarios','empleados','reportes'] as $m) check(!puedeVer($m)&&!puedeEditar($m),"Inventarista sin acceso a $m");
check(puedeVer('historial')&&!puedeEditar('historial'),'Historial de solo lectura');
check(moduloInicial()==='productos','Inicio de Inventarista en productos');
foreach (['Vendedor','Cajero'] as $rol) { $_SESSION['empleado']['rol']=$rol; check(puedeEditar('ventas')&&!puedeEditar('productos')&&!puedeVer('usuarios'),"Permisos conservados para $rol"); }
$_SESSION['empleado']['rol']='Administrador';check(puedeEditar('usuarios')&&!puedeVer('modulo_inventado'),'Administrador con módulos explícitos');
