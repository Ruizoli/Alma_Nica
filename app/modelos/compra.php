<?php
require_once __DIR__ . '/operacion.php';

function obtenerOrden(PDO $db, int $id, bool $bloquear = false): array
{
    $q = $db->prepare('SELECT o.*, p.Nombre AS Proveedor, e.Nombre AS Empleado FROM OrdenCompra o JOIN Proveedor p ON p.Id_Proveedor=o.Id_Proveedor JOIN Empleado e ON e.Id_Empleado=o.Id_Empleado WHERE o.Id_OrdenCompra=?' . ($bloquear ? ' FOR UPDATE' : ''));
    $q->execute([$id]);
    $orden = $q->fetch();
    if (!$orden) throw new InvalidArgumentException('No se encontró la orden de compra.');
    return $orden;
}

function lineasOrden(PDO $db, int $id): array
{
    $q = $db->prepare('SELECT d.*, p.Nombre, p.Codigo, p.Imagen,
        COALESCE((SELECT SUM(r.Cantidad) FROM Detalle_Recepcion r WHERE r.Id_Detalle_Orden=d.Id_Detalle_Orden),0) AS Recibida
        FROM Detalle_OrdenCompra d JOIN Producto p ON p.Id_Producto=d.Id_Producto
        WHERE d.Id_OrdenCompra=? ORDER BY d.Id_Producto');
    $q->execute([$id]);
    return $q->fetchAll();
}

function listarOrdenes(PDO $db, string $estado, string $buscar, int $pagina): array
{
    $where=[]; $params=[];
    if ($estado !== '') {
        if (!in_array($estado,['Pendiente','Completada','Cancelada'],true)) throw new InvalidArgumentException('Filtro de estado no válido.');
        $where[]='o.Estado=?'; $params[]=$estado;
    }
    if ($buscar !== '') {
        $where[]='(p.Nombre LIKE ? OR CAST(o.Id_OrdenCompra AS CHAR)=?)';
        $params[]='%'.$buscar.'%'; $params[]=preg_replace('/^OC-0*/i','',$buscar);
    }
    $filtro=$where?' WHERE '.implode(' AND ',$where):'';
    $base=' FROM OrdenCompra o JOIN Proveedor p ON p.Id_Proveedor=o.Id_Proveedor';
    $q=$db->prepare('SELECT COUNT(*)'.$base.$filtro); $q->execute($params); $total=(int)$q->fetchColumn();
    $paginas=max(1,(int)ceil($total/10)); $pagina=min(max(1,$pagina),$paginas); $offset=($pagina-1)*10;
    $q=$db->prepare('SELECT o.*,p.Nombre AS Proveedor,(SELECT COUNT(*) FROM RecepcionCompra r WHERE r.Id_OrdenCompra=o.Id_OrdenCompra) AS Recepciones'.$base.$filtro." ORDER BY o.Id_OrdenCompra DESC LIMIT 10 OFFSET $offset");
    $q->execute($params);
    return ['filas'=>$q->fetchAll(),'total'=>$total,'pagina'=>$pagina,'paginas'=>$paginas];
}

function editarOrden(PDO $db, int $id, array $post): void
{
    $lineas=lineasOperacion($post,true);
    $proveedor=entero($post['proveedor'] ?? '',1);
    $db->beginTransaction();
    try {
        $orden=obtenerOrden($db,$id,true);
        if ($orden['Estado']!=='Pendiente') throw new InvalidArgumentException('Solo se pueden editar órdenes pendientes.');
        $q=$db->prepare('SELECT COUNT(*) FROM RecepcionCompra WHERE Id_OrdenCompra=?'); $q->execute([$id]);
        if ($q->fetchColumn()) throw new InvalidArgumentException('No se puede editar una orden que ya tiene recepciones.');
        $total=0;
        foreach ($lineas as $producto=>$linea) {
            $q=$db->prepare('SELECT Id_Producto FROM Producto WHERE Id_Producto=? FOR UPDATE'); $q->execute([$producto]);
            if (!$q->fetchColumn()) throw new InvalidArgumentException('Uno de los productos ya no existe.');
            $total+=$linea['cantidad']*$linea['costo'];
            if ($total>9999999999) throw new InvalidArgumentException('El total excede el máximo permitido.');
        }
        $q=$db->prepare('DELETE FROM Detalle_OrdenCompra WHERE Id_OrdenCompra=?'); $q->execute([$id]);
        $q=$db->prepare('INSERT INTO Detalle_OrdenCompra (Id_OrdenCompra,Id_Producto,Cantidad,Precio_Costo) VALUES (?,?,?,?)');
        foreach ($lineas as $producto=>$linea) $q->execute([$id,$producto,$linea['cantidad'],decimal($linea['costo'])]);
        $q=$db->prepare('UPDATE OrdenCompra SET Id_Proveedor=?,Total=? WHERE Id_OrdenCompra=?'); $q->execute([$proveedor,decimal($total),$id]);
        $db->commit();
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
}

function cancelarOrden(PDO $db, int $id): void
{
    $db->beginTransaction();
    try {
        $orden=obtenerOrden($db,$id,true);
        $q=$db->prepare('SELECT COUNT(*) FROM RecepcionCompra WHERE Id_OrdenCompra=?'); $q->execute([$id]);
        if ($orden['Estado']!=='Pendiente' || $q->fetchColumn()) throw new InvalidArgumentException('Solo se puede cancelar una orden pendiente sin recepciones.');
        $q=$db->prepare("UPDATE OrdenCompra SET Estado='Cancelada' WHERE Id_OrdenCompra=?"); $q->execute([$id]);
        $db->commit();
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
}

function recibirOrden(PDO $db, int $id, array $post, int $empleado, string $token): int
{
    if (!preg_match('/^[a-f0-9]{48}$/D',$token)) throw new InvalidArgumentException('El formulario no es válido.');
    $factura=trim((string)($post['factura'] ?? ''));
    $observaciones=trim((string)($post['observaciones'] ?? ''));
    if (mb_strlen($factura)>80 || mb_strlen($observaciones)>2000) throw new InvalidArgumentException('Revisa la longitud de la factura y las observaciones.');
    $fecha=(string)($post['fecha'] ?? '');
    $dia=DateTimeImmutable::createFromFormat('!Y-m-d',$fecha);
    if (!$dia || $dia->format('Y-m-d')!==$fecha || $fecha>date('Y-m-d')) throw new InvalidArgumentException('Selecciona una fecha de recepción válida, no futura.');
    $cantidades=$post['recibida'] ?? [];
    if (!is_array($cantidades)) throw new InvalidArgumentException('Revisa las cantidades recibidas.');
    $db->beginTransaction();
    try {
        $orden=obtenerOrden($db,$id,true);
        if ($orden['Estado']!=='Pendiente') throw new InvalidArgumentException('Esta orden ya fue completada o cancelada.');
        if ($fecha<substr($orden['Fecha'],0,10)) throw new InvalidArgumentException('La recepción no puede ser anterior a la orden.');
        $q=$db->prepare('SELECT Id_Recepcion FROM RecepcionCompra WHERE Token=?'); $q->execute([$token]);
        if ($q->fetchColumn()) throw new InvalidArgumentException('Esta recepción ya fue guardada.');
        $lineas=lineasOrden($db,$id); $entrada=[]; $pendiente=0;
        $validos=array_map('strval',array_column($lineas,'Id_Detalle_Orden'));
        foreach (array_keys($cantidades) as $key) { if (!in_array((string)$key,$validos,true)) throw new InvalidArgumentException('La recepción contiene un producto ajeno a esta orden.'); }
        foreach ($lineas as $linea) {
            $cantidad=entero($cantidades[$linea['Id_Detalle_Orden']] ?? 0);
            $restante=(int)$linea['Cantidad']-(int)$linea['Recibida'];
            if ($cantidad>$restante) throw new InvalidArgumentException('La cantidad recibida de '.$linea['Nombre'].' supera lo pendiente.');
            $pendiente+=$restante-$cantidad;
            if ($cantidad>0) $entrada[]=['linea'=>$linea,'cantidad'=>$cantidad];
        }
        if (!$entrada) throw new InvalidArgumentException('Ingresa al menos una cantidad recibida mayor que cero.');
        $q=$db->prepare('INSERT INTO RecepcionCompra (Id_OrdenCompra,Id_Empleado,Fecha,Factura,Observaciones,Token) VALUES (?,?,?,?,?,?)');
        $q->execute([$id,$empleado,$fecha.' '.date('H:i:s'),$factura?:null,$observaciones?:null,$token]);
        $recepcion=(int)$db->lastInsertId();
        foreach ($entrada as $item) {
            $linea=$item['linea']; $cantidad=$item['cantidad'];
            $q=$db->prepare('SELECT Stock_Actual FROM Producto WHERE Id_Producto=? FOR UPDATE'); $q->execute([$linea['Id_Producto']]);
            $stock=(int)$q->fetchColumn()+$cantidad;
            if ($stock>2147483647) throw new InvalidArgumentException('La cantidad supera el stock máximo permitido.');
            $q=$db->prepare('INSERT INTO Detalle_Recepcion (Id_Recepcion,Id_Detalle_Orden,Cantidad) VALUES (?,?,?)'); $q->execute([$recepcion,$linea['Id_Detalle_Orden'],$cantidad]);
            $q=$db->prepare('UPDATE Producto SET Stock_Actual=? WHERE Id_Producto=?'); $q->execute([$stock,$linea['Id_Producto']]);
        }
        if ($pendiente===0) { $q=$db->prepare("UPDATE OrdenCompra SET Estado='Completada' WHERE Id_OrdenCompra=?"); $q->execute([$id]); }
        $db->commit(); return $recepcion;
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
}

function recepcionesOrden(PDO $db, int $id): array
{
    $q=$db->prepare('SELECT r.*,e.Nombre AS Empleado,(SELECT SUM(d.Cantidad) FROM Detalle_Recepcion d WHERE d.Id_Recepcion=r.Id_Recepcion) AS Unidades FROM RecepcionCompra r JOIN Empleado e ON e.Id_Empleado=r.Id_Empleado WHERE r.Id_OrdenCompra=? ORDER BY r.Id_Recepcion DESC');
    $q->execute([$id]); $recepciones=$q->fetchAll();
    $q=$db->prepare('SELECT d.Id_Recepcion,d.Cantidad,p.Nombre FROM Detalle_Recepcion d JOIN RecepcionCompra r ON r.Id_Recepcion=d.Id_Recepcion JOIN Detalle_OrdenCompra o ON o.Id_Detalle_Orden=d.Id_Detalle_Orden JOIN Producto p ON p.Id_Producto=o.Id_Producto WHERE r.Id_OrdenCompra=? ORDER BY d.Id_Detalle_Recepcion');
    $q->execute([$id]); $detalles=[];
    foreach ($q->fetchAll() as $linea) $detalles[$linea['Id_Recepcion']][]=$linea;
    foreach ($recepciones as &$recepcion) $recepcion['Detalles']=$detalles[$recepcion['Id_Recepcion']] ?? [];
    unset($recepcion);
    return $recepciones;
}

function historialRecepciones(PDO $db, int $pagina, string $busqueda = ''): array
{
    $from=' FROM RecepcionCompra r JOIN OrdenCompra o ON o.Id_OrdenCompra=r.Id_OrdenCompra JOIN Proveedor p ON p.Id_Proveedor=o.Id_Proveedor JOIN Empleado e ON e.Id_Empleado=r.Id_Empleado';
    $where=$busqueda===''?'':" WHERE CONCAT_WS(' ',CONCAT('RC-',LPAD(r.Id_Recepcion,5,'0')),CONCAT('OC-',LPAD(r.Id_OrdenCompra,5,'0')),p.Nombre,e.Nombre,r.Factura,DATE_FORMAT(r.Fecha,'%d/%m/%Y')) LIKE ?";
    $params=$busqueda===''?[]:['%'.$busqueda.'%'];
    $q=$db->prepare('SELECT COUNT(*)'.$from.$where); $q->execute($params); $total=(int)$q->fetchColumn();
    $paginas=max(1,(int)ceil($total/10)); $pagina=min(max(1,$pagina),$paginas); $offset=($pagina-1)*10;
    $q=$db->prepare('SELECT r.*,p.Nombre AS Proveedor,e.Nombre AS Empleado,(SELECT SUM(d.Cantidad) FROM Detalle_Recepcion d WHERE d.Id_Recepcion=r.Id_Recepcion) AS Unidades'.$from.$where.' ORDER BY r.Id_Recepcion DESC LIMIT 10 OFFSET '.$offset); $q->execute($params);
    return ['filas'=>$q->fetchAll(),'total'=>$total,'pagina'=>$pagina,'paginas'=>$paginas];
}
