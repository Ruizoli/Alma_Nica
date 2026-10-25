<?php
function reporte(PDO $db, string $tipo, string $desde, string $hasta): array
{
    foreach ([$desde,$hasta] as $fecha) { $d = DateTimeImmutable::createFromFormat('!Y-m-d',$fecha); if (!$d || $d->format('Y-m-d') !== $fecha) { throw new InvalidArgumentException('Selecciona fechas válidas.'); } }
    if ($desde > $hasta) { throw new InvalidArgumentException('La fecha inicial debe ser anterior a la final.'); }
    $fin = (new DateTimeImmutable($hasta))->modify('+1 day')->format('Y-m-d');
    $params = [];
    $sql = match ($tipo) {
        'ventas' => "SELECT v.Id_Venta AS Numero,v.Fecha,COALESCE(c.Nombre,'Consumidor final') AS Cliente,e.Nombre AS Empleado,v.Metodo_Pago AS Metodo,v.Total FROM Venta v LEFT JOIN Cliente c ON c.Id_Cliente=v.Id_Cliente JOIN Empleado e ON e.Id_Empleado=v.Id_Empleado WHERE v.Fecha>=? AND v.Fecha<? ORDER BY v.Fecha DESC",
        'compras' => 'SELECT o.Id_OrdenCompra AS Numero,o.Fecha,p.Nombre AS Proveedor,o.Estado,o.Total FROM OrdenCompra o JOIN Proveedor p ON p.Id_Proveedor=o.Id_Proveedor WHERE o.Fecha>=? AND o.Fecha<? ORDER BY o.Fecha DESC',
        'productos' => 'SELECT Codigo,Nombre,Marca,Talla,Stock_Actual AS Stock,Stock_Minimo AS Minimo,Precio_Venta AS Precio FROM Producto ORDER BY Nombre',
        'clientes' => 'SELECT c.Nombre,c.Cedula,c.Correo,COUNT(v.Id_Venta) AS Compras,COALESCE(SUM(v.Total),0) AS Total FROM Cliente c LEFT JOIN Venta v ON v.Id_Cliente=c.Id_Cliente AND v.Fecha>=? AND v.Fecha<? GROUP BY c.Id_Cliente,c.Nombre,c.Cedula,c.Correo ORDER BY Total DESC',
        default => throw new InvalidArgumentException('Reporte no válido.'),
    };
    if ($tipo !== 'productos') { $params = [$desde,$fin]; }
    $q = $db->prepare($sql . ' LIMIT 5000'); $q->execute($params); return $q->fetchAll();
}
