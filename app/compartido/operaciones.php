<?php
function lineasOperacion(array $post, bool $compra): array
{
    $ids = $post['producto'] ?? []; $cantidades = $post['cantidad'] ?? []; $costos = $post['costo'] ?? [];
    if (!is_array($ids) || count($ids) < 1 || count($ids) > 100) { throw new InvalidArgumentException('Agrega entre 1 y 100 productos.'); }
    if ($compra && isset($post['precio_venta']) && !is_array($post['precio_venta'])) throw new InvalidArgumentException('Revisa los precios de venta.');
    $lineas = [];
    foreach ($ids as $i=>$valor) {
        if ($valor === '') { continue; }
        $id = entero($valor, 1); $cantidad = entero($cantidades[$i] ?? '', 1);
        if (isset($lineas[$id])) { throw new InvalidArgumentException('Un producto está repetido. Agrupa su cantidad en una sola fila.'); }
        $lineas[$id] = ['cantidad'=>$cantidad, 'venta'=>$compra && isset($post['precio_venta'][$i]) ? centavos((string)$post['precio_venta'][$i]) : null, 'costo'=>$compra ? centavos((string) ($costos[$i] ?? '')) : 0];
    }
    if (!$lineas) { throw new InvalidArgumentException('Agrega al menos un producto.'); }
    ksort($lineas); return $lineas;
}
function crearOperacion(PDO $db, bool $compra, array $post, int $empleado): int
{
    $lineas = lineasOperacion($post, $compra); $total = 0;
    $db->beginTransaction();
    try {
        foreach ($lineas as $id=>&$linea) {
            $q = $db->prepare('SELECT * FROM Producto WHERE Id_Producto=? FOR UPDATE'); $q->execute([$id]); $producto = $q->fetch();
            if (!$producto) { throw new InvalidArgumentException('Uno de los productos ya no existe.'); }
            if (!$compra && (int) $producto['Stock_Actual'] < $linea['cantidad']) { throw new InvalidArgumentException('Stock insuficiente para ' . $producto['Nombre'] . '.'); }
            $linea['venta'] ??= centavos($producto['Precio_Venta']);
            $linea['precio'] = $compra ? $linea['costo'] : centavos($producto['Precio_Venta']);
            $total += $linea['cantidad'] * $linea['precio'];
            if ($total > 9999999999) { throw new InvalidArgumentException('El importe supera el máximo permitido.'); }
        }
        unset($linea);
        if ($compra) {
            $proveedor = entero($post['proveedor'] ?? '', 1);
            $q = $db->prepare('INSERT INTO OrdenCompra (Id_Proveedor,Id_Empleado,Total) VALUES (?,?,?)'); $q->execute([$proveedor,$empleado,decimal($total)]);
        } else {
            $cliente = empty($post['cliente']) ? null : entero($post['cliente'], 1);
            $metodo = $post['metodo'] ?? '';
            if (!in_array($metodo, ['Efectivo','Tarjeta','Transferencia'], true)) { throw new InvalidArgumentException('Selecciona un método de pago válido.'); }
            $q = $db->prepare('INSERT INTO Venta (Id_Cliente,Id_Empleado,Total,Metodo_Pago) VALUES (?,?,?,?)'); $q->execute([$cliente,$empleado,decimal($total),$metodo]);
        }
        $idOperacion = (int) $db->lastInsertId();
        foreach ($lineas as $id=>$linea) {
            $sql = $compra ? 'INSERT INTO Detalle_OrdenCompra (Id_OrdenCompra,Id_Producto,Cantidad,Precio_Costo) VALUES (?,?,?,?)' : 'INSERT INTO Detalle_Venta (Id_Venta,Id_Producto,Cantidad,Precio_Unitario) VALUES (?,?,?,?)';
            $q = $db->prepare($sql); $q->execute([$idOperacion,$id,$linea['cantidad'],decimal($linea['precio'])]);
            if ($compra) { $q=$db->prepare('UPDATE Detalle_OrdenCompra SET Precio_Venta_Referencia=? WHERE Id_OrdenCompra=? AND Id_Producto=?'); $q->execute([decimal($linea['venta']),$idOperacion,$id]); }
            if (!$compra) { $q = $db->prepare('UPDATE Producto SET Stock_Actual=Stock_Actual-? WHERE Id_Producto=?'); $q->execute([$linea['cantidad'],$id]); }
        }
        $db->commit(); return $idOperacion;
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
}
function historialOperaciones(PDO $db, bool $compra, int $pagina, string $busqueda = ''): array
{
    $base = $compra ? "SELECT o.Id_OrdenCompra AS id,o.Fecha,o.Total,o.Estado AS estado,p.Nombre AS nombre FROM OrdenCompra o JOIN Proveedor p ON p.Id_Proveedor=o.Id_Proveedor" : "SELECT v.Id_Venta AS id,v.Fecha,v.Total,v.Metodo_Pago AS estado,COALESCE(c.Nombre,'Consumidor final') AS nombre FROM Venta v LEFT JOIN Cliente c ON c.Id_Cliente=v.Id_Cliente";
    $where = $busqueda === '' ? '' : " WHERE CONCAT_WS(' ',id,nombre,estado,DATE_FORMAT(Fecha,'%d/%m/%Y'),Total) LIKE ?";
    $params = $busqueda === '' ? [] : ['%'.$busqueda.'%'];
    $q=$db->prepare("SELECT COUNT(*) FROM ($base) h".$where); $q->execute($params); $total=(int)$q->fetchColumn();
    $paginas=max(1,(int)ceil($total/10)); $pagina=min($paginas,max(1,$pagina)); $offset=($pagina-1)*10;
    $q=$db->prepare("SELECT * FROM ($base) h".$where." ORDER BY id DESC LIMIT 10 OFFSET $offset"); $q->execute($params);
    return ['filas'=>$q->fetchAll(),'total'=>$total,'paginas'=>$paginas,'pagina'=>$pagina];
}
function detalleOperacion(PDO $db, bool $compra, int $id): array
{
    $sql = $compra ? 'SELECT p.Nombre,d.Cantidad,d.Precio_Costo AS precio,d.Subtotal FROM Detalle_OrdenCompra d JOIN Producto p ON p.Id_Producto=d.Id_Producto WHERE d.Id_OrdenCompra=?' : 'SELECT p.Nombre,d.Cantidad,d.Precio_Unitario AS precio,d.Subtotal FROM Detalle_Venta d JOIN Producto p ON p.Id_Producto=d.Id_Producto WHERE d.Id_Venta=?';
    $q = $db->prepare($sql); $q->execute([$id]); return $q->fetchAll();
}
