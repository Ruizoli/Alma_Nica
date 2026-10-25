<?php
function obtenerDashboard(PDO $conexion): array
{
    // Consultas de solo lectura para el dashboard.
    
    $hoy = new DateTimeImmutable('today');
    $desde = $hoy->modify('-6 days');
    $hasta = $hoy->modify('+1 day');
    $ventaHoy = ['importe' => 0, 'cantidad' => 0];
    $estadisticas = [];
    $ventas = [];
    $stockBajo = [];
    $serie = [];
    $error = '';
    $populares = [];
    try {
        $estadisticas = $conexion->query('SELECT (SELECT COUNT(*) FROM Producto) AS productos, (SELECT COALESCE(SUM(Stock_Actual), 0) FROM Producto) AS unidades, (SELECT COUNT(*) FROM Cliente) AS clientes, (SELECT COUNT(*) FROM Proveedor) AS proveedores, (SELECT COUNT(*) FROM OrdenCompra WHERE Estado = "Pendiente") AS pendientes, (SELECT COUNT(*) FROM Producto WHERE Stock_Actual <= COALESCE(Stock_Minimo, 5)) AS alertas')->fetch();
        $consulta = $conexion->prepare('SELECT COALESCE(SUM(Total), 0) AS importe, COUNT(*) AS cantidad FROM Venta WHERE Fecha >= ? AND Fecha < ?');
        $consulta->execute([$hoy->format('Y-m-d'), $hasta->format('Y-m-d')]);
        $ventaHoy = $consulta->fetch();
        $consulta = $conexion->prepare('SELECT DATE(Fecha) AS dia, SUM(Total) AS total FROM Venta WHERE Fecha >= ? AND Fecha < ? GROUP BY DATE(Fecha) ORDER BY dia');
        $consulta->execute([$desde->format('Y-m-d'), $hasta->format('Y-m-d')]);
        $porDia = $consulta->fetchAll(PDO::FETCH_KEY_PAIR);
        for ($i = 0; $i < 7; $i++) {
            $fecha = $desde->modify("+$i days");
            $serie[] = ['fecha' => $fecha->format('d/m'), 'total' => (float) ($porDia[$fecha->format('Y-m-d')] ?? 0)];
        }
        $ventas = $conexion->query('SELECT v.Id_Venta, v.Fecha, v.Total, v.Metodo_Pago, COALESCE(c.Nombre, "Consumidor final") AS Cliente FROM Venta v LEFT JOIN Cliente c ON c.Id_Cliente = v.Id_Cliente ORDER BY v.Fecha DESC, v.Id_Venta DESC LIMIT 5')->fetchAll();
        $stockBajo = $conexion->query('SELECT Codigo, Nombre, Stock_Actual, COALESCE(Stock_Minimo, 5) AS Stock_Minimo FROM Producto WHERE Stock_Actual <= COALESCE(Stock_Minimo, 5) ORDER BY Stock_Actual, Nombre LIMIT 5')->fetchAll();
        $consulta=$conexion->prepare('SELECT p.Nombre,SUM(d.Cantidad) AS Unidades FROM Detalle_Venta d JOIN Venta v ON v.Id_Venta=d.Id_Venta JOIN Producto p ON p.Id_Producto=d.Id_Producto WHERE v.Fecha>=? AND v.Fecha<? GROUP BY p.Id_Producto,p.Nombre ORDER BY Unidades DESC LIMIT 4');
        $consulta->execute([$hoy->modify('-29 days')->format('Y-m-d'),$hasta->format('Y-m-d')]);
        $populares=$consulta->fetchAll();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $error = 'No se pudieron cargar los indicadores. Revisa la conexión y las tablas de almanica.';
    }
    return compact('hoy', 'estadisticas', 'ventaHoy', 'serie', 'ventas', 'stockBajo', 'populares', 'error');
}
