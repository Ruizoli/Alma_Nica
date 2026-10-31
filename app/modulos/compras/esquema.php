<?php
// Consulta metadatos; no modifica la base desde la pantalla.
function revisarEsquemaCompras(PDO $db): array
{
    $tablas=[
        'Producto'=>['Id_Producto','Codigo','Nombre','Stock_Actual','Precio_Venta','Imagen'],
        'Proveedor'=>['Id_Proveedor','Nombre'],
        'Empleado'=>['Id_Empleado','Nombre'],
        'OrdenCompra'=>['Id_OrdenCompra','Id_Proveedor','Id_Empleado','Fecha','Estado','Total'],
        'Detalle_OrdenCompra'=>['Id_Detalle_Orden','Id_OrdenCompra','Id_Producto','Cantidad','Precio_Costo','Subtotal','Precio_Venta_Referencia'],
        'RecepcionCompra'=>['Id_Recepcion','Id_OrdenCompra','Id_Empleado','Fecha','Factura','Observaciones','Token'],
        'Detalle_Recepcion'=>['Id_Detalle_Recepcion','Id_Recepcion','Id_Detalle_Orden','Cantidad'],
        'CompraReversion'=>['Id_Recepcion','Id_Empleado','Fecha','Motivo'],
        'OrdenCierre'=>['Id_OrdenCompra','Id_Empleado','Fecha','Motivo'],
    ];
    $faltantes=[];
    foreach ($tablas as $tabla=>$campos) {
        try { $columnas=$db->query('SHOW COLUMNS FROM '.$tabla)->fetchAll(PDO::FETCH_UNIQUE); }
        catch (PDOException $e) {
            if ((int)($e->errorInfo[1] ?? 0)!==1146) throw $e;
            $faltantes[]='Tabla '.$tabla; continue;
        }
        foreach ($campos as $campo) if (!isset($columnas[$campo])) $faltantes[]=$tabla.'.'.$campo;
        if ($tabla==='OrdenCompra' && isset($columnas['Estado']) && !str_contains($columnas['Estado']['Type'],"'Cerrada'")) $faltantes[]='Estado Cerrada en OrdenCompra';
    }
    return $faltantes;
}
function comprobarEsquemaCompras(PDO $db): void
{
    $faltantes=revisarEsquemaCompras($db);
    if ($faltantes) throw new InvalidArgumentException('Actualiza la base de datos de Compras. Falta: '.implode(', ',$faltantes).'. Ejecuta en la terminal: php database/actualizar_compras.php --aplicar');
}
