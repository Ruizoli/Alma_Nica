<?php
if (!defined('ALMANICA_ENTRADA')) { http_response_code(404); exit; }
require_once __DIR__ . '/../inicio.php';
require_once __DIR__ . '/../middleware/autenticacion.php';
require_once __DIR__ . '/../soporte/sistema.php';
require_once __DIR__ . '/../modelos/catalogo.php';
$modulo = (string) ($_GET['modulo'] ?? 'dashboard');
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
        if (!puedeEditar($modulo) && $modulo !== 'configuracion') { http_response_code(403); throw new InvalidArgumentException('No tienes permiso para modificar este módulo.'); }
    }
    if (isset($catalogos[$modulo])) {
        $def = $catalogos[$modulo]; $opciones = opciones($conexion); $registro = [];
        $id = empty($_GET['id']) ? 0 : entero($_GET['id'],1);
        if ($accion === 'editar') { $registro = registroCatalogo($conexion,$def,$id); }
        if (in_array($accion,['nuevo','editar'],true) && !puedeEditar($modulo)) { http_response_code(403); throw new InvalidArgumentException('Este catálogo es de solo consulta para tu rol.'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = empty($_POST['id']) ? 0 : entero($_POST['id'],1);
            if (($_POST['accion'] ?? '') === 'eliminar') { eliminarCatalogo($conexion,$modulo,$id); volver($modulo,'Registro eliminado.'); }
            $registro = $_POST;
            $fotoNueva = null;
            try {
                if ($modulo === 'productos') $fotoNueva = guardarFotoProducto($_FILES['foto'] ?? []);
                $foto = $fotoNueva ?? ($modulo === 'productos' && isset($_POST['quitar_foto']) ? '' : null);
                guardarCatalogo($conexion,$modulo,$_POST,$id,$foto);
            } catch (Throwable $e) { descartarFotoNueva($fotoNueva); throw $e; }
            volver($modulo,'Registro guardado correctamente.');
        }
        $listado = listarCatalogo($conexion,$def,trim((string) ($_GET['q'] ?? '')),max(1,(int) ($_GET['pagina'] ?? 1)),$_GET);
        $vista = 'catalogo';
    } elseif (in_array($modulo,['compras','recepcion','historial'],true)) {
        require __DIR__ . '/compras.php';
    } elseif ($modulo === 'ventas') {
        require_once __DIR__ . '/../modelos/operacion.php';
        $compra = $modulo === 'compras';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $token = (string) ($_POST['operacion_token'] ?? '');
            if (!isset($_SESSION['operaciones'][$token]) || $_SESSION['operaciones'][$token] !== $modulo) { throw new InvalidArgumentException('Este formulario ya se procesó o venció. Abre una nueva operación.'); }
            $nuevoId = crearOperacion($conexion,$compra,$_POST,(int) $_SESSION['empleado']['id']);
            unset($_SESSION['operaciones'][$token]);
            $_SESSION['mensaje'] = ($compra ? 'Orden de compra' : 'Venta') . ' #' . $nuevoId . ' registrada correctamente.';
            header('Location: ' . ruta($modulo,['id'=>$nuevoId])); exit;
        }
        $vista = 'operaciones';
    } elseif ($modulo==='buscar') {
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
    } elseif ($modulo === 'reportes') {
        require_once __DIR__ . '/../modelos/reporte.php';
        $tipoReporte = (string) ($_GET['tipo'] ?? 'ventas'); $desde = (string) ($_GET['desde'] ?? date('Y-m-01')); $hasta = (string) ($_GET['hasta'] ?? date('Y-m-d'));
        $datosReporte = reporte($conexion,$tipoReporte,$desde,$hasta);
        $textoReporte = trim((string)($_GET['q'] ?? ''));
        if ($textoReporte !== '') $datosReporte = array_values(array_filter($datosReporte, function ($fila) use ($textoReporte) {
            if (isset($fila['Fecha'])) $fila['Fecha'] = fechaCorta($fila['Fecha']);
            return mb_stripos(implode(' ', $fila), $textoReporte) !== false;
        }));
        foreach ($datosReporte as &$filaReporte) if (isset($filaReporte['Fecha'])) $filaReporte['Fecha'] = fechaCorta($filaReporte['Fecha']);
        unset($filaReporte);
        if (isset($_GET['csv'])) {
            header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="almanica-' . $tipoReporte . '.csv"');
            $salida = fopen('php://output','w'); fwrite($salida,"\xEF\xBB\xBF");
            if ($datosReporte) { fputcsv($salida,array_keys($datosReporte[0]),';'); }
            foreach ($datosReporte as $fila) { fputcsv($salida,array_map(fn($v) => preg_match('/^[=+@\-\t\r\n]/',(string) $v) ? "'" . $v : $v,$fila),';'); }
            fclose($salida); exit;
        }
        $vista = 'reportes';
    } elseif ($modulo === 'configuracion') {
        $vista = 'configuracion';
        require_once __DIR__ . '/../modelos/perfil.php';
        $perfil = obtenerPerfil($conexion, (int)$_SESSION['empleado']['usuario_id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../modelos/seguridad.php';
            $q = $conexion->prepare('SELECT Usuario FROM Usuario WHERE Id_Usuario=?'); $q->execute([$_SESSION['empleado']['usuario_id']]); $usuarioActual = $q->fetchColumn();
            $resultado = autenticar($conexion,$usuarioActual,(string) ($_POST['actual'] ?? ''),$_SERVER['REMOTE_ADDR'] ?? 'local');
            if (!$resultado['cuenta']) { throw new InvalidArgumentException($resultado['espera'] ? 'Espera ' . $resultado['espera'] . ' segundos antes de intentarlo nuevamente.' : 'La contraseña actual no es correcta.'); }
            if (($_POST['accion_cuenta'] ?? '') === 'perfil') {
                guardarPerfil($conexion,(int)$_SESSION['empleado']['usuario_id'],(string)($_POST['usuario'] ?? ''),(string)($_POST['nombre'] ?? ''));
                $_SESSION['empleado']['nombre']=trim((string)$_POST['nombre']);
                session_regenerate_id(true);
                volver($modulo,'Nombre y usuario actualizados. Utiliza tu nuevo usuario la próxima vez que inicies sesión.');
            }
            $nueva = (string) ($_POST['nueva'] ?? '');
            if (strlen($nueva)<8 || strlen($nueva)>72 || $nueva !== ($_POST['confirmar'] ?? '')) { throw new InvalidArgumentException('Usa entre 8 y 72 bytes y confirma la misma contraseña.'); }
            $nuevoHash = password_hash($nueva,PASSWORD_DEFAULT);
            $q = $conexion->prepare('UPDATE Usuario SET Contrasena=? WHERE Id_Usuario=?'); $q->execute([$nuevoHash,$_SESSION['empleado']['usuario_id']]);
            $_SESSION['credencial'] = hash('sha256',$nuevoHash);
            session_regenerate_id(true); volver($modulo,'Contraseña actualizada.');
        }
        $vista = 'configuracion';
    } else {
        require_once __DIR__ . '/../modelos/dashboard.php';
        $datos = obtenerDashboard($conexion); $vista = 'resumen';
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
    $vista = 'operaciones'; $compra = $modulo === 'compras';
    try {
        $productos = $conexion->query('SELECT Id_Producto,Codigo,Nombre,Precio_Venta,Stock_Actual,Imagen FROM Producto ORDER BY Nombre')->fetchAll();
        $terceros = $conexion->query($compra ? 'SELECT Id_Proveedor AS id,Nombre FROM Proveedor ORDER BY Nombre' : 'SELECT Id_Cliente AS id,Nombre FROM Cliente ORDER BY Nombre')->fetchAll();
        $listado = historialOperaciones($conexion,$compra,max(1,(int) ($_GET['pagina'] ?? 1)),trim((string)($_GET['q'] ?? '')));
        $detalle = empty($_GET['id']) ? [] : detalleOperacion($conexion,$compra,entero($_GET['id'],1));
        $tokenOperacion = bin2hex(random_bytes(24));
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
        $_SESSION['operaciones'][$tokenOperacion] = $modulo;
        $_SESSION['operaciones'] = array_slice($_SESSION['operaciones'],-20,null,true);
        }
    } catch (Throwable $e) { error_log($e->getMessage()); $error = 'No se pudieron cargar los datos de la operación.'; $vista = 'error'; }
}
if (in_array($modulo,['compras','recepcion','historial'],true)) {
    require_once __DIR__ . '/../modelos/compra.php';
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
}
$vista = $vista ?? 'error';
require __DIR__ . '/../vistas/parciales/cabecera.php';
require __DIR__ . '/../vistas/' . $vista . '.php';
require __DIR__ . '/../vistas/parciales/pie.php';
