<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Pruebas de integración. Crea una base AISLADA y no altera almanica.
require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../app/soporte/funciones.php';
require __DIR__ . '/../app/soporte/sistema.php';
require __DIR__ . '/../app/modelos/catalogo.php';
require __DIR__ . '/../app/modelos/operacion.php';
require __DIR__ . '/../app/modelos/compra.php';
require __DIR__ . '/../app/modelos/seguridad.php';
require __DIR__ . '/../app/modelos/dashboard.php';
require __DIR__ . '/../app/modelos/reporte.php';
require __DIR__ . '/../app/modelos/perfil.php';
function comprobar(bool $condicion, string $texto): void { if (!$condicion) throw new RuntimeException($texto); echo "OK: $texto\n"; }
function rechaza(callable $accion, string $texto): void { try { $accion(); } catch (InvalidArgumentException|PDOException $e) { comprobar(true,$texto); return; } throw new RuntimeException('No rechazo: ' . $texto); }
$origen = conectar();
$nombre = 'almanica_pruebas_' . bin2hex(random_bytes(5));
$origen->exec("CREATE DATABASE `$nombre` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$c = require __DIR__ . '/../config/base_datos.php';
$db = new PDO("mysql:host={$c['host']};port={$c['puerto']};dbname=$nombre;charset=utf8mb4",$c['usuario'],$c['contrasena'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
try {
    $db->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($origen->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $tabla) {
        $sql = $origen->query('SHOW CREATE TABLE `' . $tabla . '`')->fetch(PDO::FETCH_NUM)[1]; $db->exec($sql);
    }
    $db->exec('SET FOREIGN_KEY_CHECKS=1');
    $db->exec(file_get_contents(__DIR__ . '/../database/001_seguridad.sql'));
    $db->exec(file_get_contents(__DIR__ . '/../database/002_imagenes_recepciones.sql'));
    $db->exec("INSERT INTO Empleado (Nombre) VALUES ('Administrador de prueba')"); $empleado=(int)$db->lastInsertId();
    $q=$db->prepare('INSERT INTO Usuario (Id_Empleado,Usuario,Contrasena,Rol) VALUES (?,?,?,?)'); $q->execute([$empleado,'admin_test',password_hash('Prueba-Segura-2026!',PASSWORD_DEFAULT),'Administrador']); $admin=(int)$db->lastInsertId();
    $_SESSION=['empleado'=>['id'=>$empleado,'usuario_id'=>$admin,'rol'=>'Administrador']];
    $categoria=guardarCatalogo($db,'categorias',['Nombre'=>'Ropa','Descripcion'=>'Prueba'],0);
    
    $proveedor=guardarCatalogo($db,'proveedores',['Nombre'=>'Proveedor de prueba','Telefono'=>'88888888','Correo'=>'prueba@example.com','Direccion'=>'Local'],0); 
    $cliente=guardarCatalogo($db,'clientes',['Nombre'=>'Cliente de prueba','Cedula'=>'TEST-001','Correo'=>'cliente@example.com','Telefono'=>'88888888'],0); 
    $datos=['Codigo'=>'PRUEBA-001','Nombre'=>'Camisa de prueba','Id_Categoria'=>$categoria,'Id_Proveedor'=>$proveedor,'Marca'=>'almaNica','Talla'=>'M','Stock_Actual'=>'10','Stock_Minimo'=>'3','Precio_Venta'=>'125.55'];
    $producto=guardarCatalogo($db,'productos',$datos,0);
    comprobar((int)$db->query('SELECT COUNT(*) FROM Producto')->fetchColumn()===1,'crear catálogos');
    rechaza(fn()=>guardarCatalogo($db,'productos',$datos,0),'código duplicado');
    rechaza(fn()=>guardarCatalogo($db,'productos',array_replace($datos,['Precio_Venta'=>'-1']),$producto),'precio negativo');
    guardarCatalogo($db,'productos',array_replace($datos,['Stock_Actual'=>'999']),$producto);
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===10,'no alterar stock desde edición');
    $venta=crearOperacion($db,false,['producto'=>[$producto],'cantidad'=>[2],'cliente'=>$cliente,'metodo'=>'Efectivo'],$empleado);
    comprobar($db->query("SELECT Total FROM Venta WHERE Id_Venta=$venta")->fetchColumn()==='251.10','venta con cálculo exacto');
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===8,'descontar stock');
    rechaza(fn()=>crearOperacion($db,false,['producto'=>[$producto],'cantidad'=>[99],'metodo'=>'Efectivo'],$empleado),'venta sin stock');
    comprobar((int)$db->query('SELECT COUNT(*) FROM Venta')->fetchColumn()===1,'rollback de venta fallida');
    rechaza(fn()=>crearOperacion($db,false,['producto'=>[$producto,$producto],'cantidad'=>[1,1],'metodo'=>'Efectivo'],$empleado),'productos duplicados');
    $orden=crearOperacion($db,true,['producto'=>[$producto],'cantidad'=>[4],'costo'=>['90.25'],'proveedor'=>$proveedor],$empleado);
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===8,'orden pendiente no modifica stock');
    $lineaVieja=lineasOrden($db,$orden)[0]['Id_Detalle_Orden'];
    recibirOrden($db,$orden,['fecha'=>date('Y-m-d'),'recibida'=>[$lineaVieja=>4]],$empleado,bin2hex(random_bytes(24)));
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===12,'recepción suma stock');
    rechaza(fn()=>recibirOrden($db,$orden,['fecha'=>date('Y-m-d'),'recibida'=>[$lineaVieja=>4]],$empleado,bin2hex(random_bytes(24))),'no duplicar recepción');
    $cancelada=crearOperacion($db,true,['producto'=>[$producto],'cantidad'=>[2],'costo'=>['90.25'],'proveedor'=>$proveedor],$empleado); cancelarOrden($db,$cancelada);
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===12,'cancelación no modifica stock');
    rechaza(fn()=>eliminarCatalogo($db,'productos',$producto),'conservar producto con historial');
    rechaza(fn()=>guardarCatalogo($db,'usuarios',['Id_Empleado'=>$empleado,'Usuario'=>'admin_test','Contrasena'=>'','Rol'=>'Vendedor','Estado'=>1],$admin),'proteger administrador actual');
    for ($i=1;$i<=3;$i++) { $r=autenticar($db,'admin_test','incorrecta','127.0.0.99'); comprobar(!$r['cuenta'],'rechazar intento ' . $i); }
    comprobar($r['espera']>0,'bloqueo al tercer intento');
    $r=autenticar($db,'ADMIN_TEST','Prueba-Segura-2026!','127.0.0.100'); comprobar(!$r['cuenta'] && $r['espera']>0,'bloqueo persiste con otra IP y mayúsculas');
    $db->exec('UPDATE LoginIntento SET Bloqueado_Hasta=1 WHERE Bloqueado_Hasta>0');
    $r=autenticar($db,'admin_test','Prueba-Segura-2026!','127.0.0.99'); comprobar((bool)$r['cuenta'],'acceso tras vencer bloqueo');
    comprobar(count(reporte($db,'ventas','2020-01-01','2099-01-01'))===1,'reporte de ventas');
    comprobar(count(reporte($db,'compras','2020-01-01','2099-01-01'))===2,'reporte de compras');
    comprobar((int)obtenerDashboard($db)['estadisticas']['productos']===1,'indicadores de dashboard');
    $ordenNueva=crearOperacion($db,true,['producto'=>[$producto],'cantidad'=>[6],'costo'=>['80.10'],'proveedor'=>$proveedor],$empleado);
    editarOrden($db,$ordenNueva,['producto'=>[$producto],'cantidad'=>[7],'costo'=>['80.10'],'proveedor'=>$proveedor]);
    comprobar(obtenerOrden($db,$ordenNueva)['Total']==='560.70','editar orden pendiente');
    $lineas=lineasOrden($db,$ordenNueva); $detalleId=$lineas[0]['Id_Detalle_Orden'];
    $token=bin2hex(random_bytes(24));
    recibirOrden($db,$ordenNueva,['fecha'=>date('Y-m-d'),'factura'=>'F-001','observaciones'=>'Primera entrega','recibida'=>[$detalleId=>2]],$empleado,$token);
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===14,'recepción parcial aumenta únicamente lo recibido');
    comprobar(obtenerOrden($db,$ordenNueva)['Estado']==='Pendiente','orden parcial permanece pendiente');
    rechaza(fn()=>recibirOrden($db,$ordenNueva,['fecha'=>date('Y-m-d'),'recibida'=>[$detalleId=>2]],$empleado,$token),'token de recepción repetido');
    rechaza(fn()=>recibirOrden($db,$ordenNueva,['fecha'=>date('Y-m-d'),'recibida'=>[$detalleId=>8]],$empleado,bin2hex(random_bytes(24))),'recepción superior a lo pendiente');
    rechaza(fn()=>cancelarOrden($db,$ordenNueva),'no cancelar orden parcialmente recibida');
    rechaza(fn()=>editarOrden($db,$ordenNueva,['producto'=>[$producto],'cantidad'=>[2],'costo'=>['80.10'],'proveedor'=>$proveedor]),'no editar orden con recepciones');
    recibirOrden($db,$ordenNueva,['fecha'=>date('Y-m-d'),'factura'=>'F-002','recibida'=>[$detalleId=>5]],$empleado,bin2hex(random_bytes(24)));
    comprobar(obtenerOrden($db,$ordenNueva)['Estado']==='Completada','completar recepción restante');
    comprobar((int)$db->query("SELECT Stock_Actual FROM Producto WHERE Id_Producto=$producto")->fetchColumn()===19,'stock exacto tras dos recepciones');
    comprobar(count(recepcionesOrden($db,$ordenNueva))===2,'historial conserva ambas recepciones');
    $pendienteHTTP=crearOperacion($db,true,['producto'=>[$producto],'cantidad'=>[3],'costo'=>['80.10'],'proveedor'=>$proveedor],$empleado);
    // Cuenta de permisos limitados para verificación HTTP posterior.
    $db->exec("INSERT INTO Empleado (Nombre) VALUES ('Vendedor de prueba')"); $vendedor=(int)$db->lastInsertId();
    $q->execute([$vendedor,'vendedor_test',password_hash('Prueba-Segura-2026!',PASSWORD_DEFAULT),'Vendedor']);
    comprobar(fechaCorta('2026-09-28 20:08:01')==='28/09/2026','fecha corta sin hora');
    comprobar(fechaCorta('2026-02-30')==='—','fecha inválida');
    comprobar(historialOperaciones($db,false,1,'Cliente de prueba')['total']===1,'buscar ventas por cliente');
    comprobar(historialOperaciones($db,false,1,'SIN_COINCIDENCIA')['total']===0,'venta sin resultados');
    comprobar(historialRecepciones($db,1,'F-001')['total']===1,'buscar recepción por factura');
    comprobar(historialRecepciones($db,1,'Proveedor de prueba')['total']===3,'buscar recepción por proveedor');
    $claveAntes=$db->query("SELECT Contrasena FROM Usuario WHERE Id_Usuario=$admin")->fetchColumn();
    guardarPerfil($db,$admin,'admin_nuevo','Nombre actualizado');
    comprobar(obtenerPerfil($db,$admin)['Usuario']==='admin_nuevo','cambiar usuario propio');
    comprobar(obtenerPerfil($db,$admin)['Nombre']==='Nombre actualizado','cambiar nombre visible');
    comprobar($db->query("SELECT Contrasena FROM Usuario WHERE Id_Usuario=$admin")->fetchColumn()===$claveAntes,'perfil conserva contraseña');
    rechaza(fn()=>guardarPerfil($db,$admin,'vendedor_test','Nombre no guardado'),'usuario duplicado');
    comprobar(obtenerPerfil($db,$admin)['Nombre']==='Nombre actualizado','duplicado no altera perfil');
    rechaza(fn()=>guardarPerfil($db,$admin,'a','Nombre'),'usuario demasiado corto');
    guardarPerfil($db,$admin,'admin_test','Administrador de prueba');
    echo 'BASE_PRUEBAS=' . $nombre . "\n";
    if (getenv('ALMANICA_KEEP_TEST_DB') !== '1') { $origen->exec("DROP DATABASE `$nombre`"); echo "Base temporal eliminada.\n"; }
} catch (Throwable $e) { $origen->exec("DROP DATABASE `$nombre`"); throw $e; }
