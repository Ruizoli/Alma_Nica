<?php
// Sin --aplicar, solo consulta. Nunca se ejecuta desde el navegador.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../config/conexion.php';
require __DIR__.'/../app/modulos/compras/esquema.php';
try {
    $db=conectar();
    echo 'Base conectada: '.$db->query('SELECT DATABASE()')->fetchColumn().PHP_EOL;
    echo 'Servidor: '.$db->getAttribute(PDO::ATTR_SERVER_VERSION).PHP_EOL;
    $faltantes=revisarEsquemaCompras($db);
    if (!$faltantes) { echo "Compras actualizada. Sin cambios.\n"; exit(0); }
    echo "Falta:\n - ".implode("\n - ",$faltantes)."\n";
    if (!in_array('--aplicar',$argv,true)) { echo "Para actualizar: php database/actualizar_compras.php --aplicar\n"; exit(1); }
    foreach (['Producto','Proveedor','Empleado','OrdenCompra','Detalle_OrdenCompra'] as $tabla) $db->query('SELECT 1 FROM '.$tabla.' LIMIT 0');
    foreach ($db->query('SELECT DISTINCT Estado FROM OrdenCompra')->fetchAll(PDO::FETCH_COLUMN) as $estado) {
        if ($estado!==null && !in_array($estado,['Pendiente','Completada','Cancelada','Cerrada'],true)) throw new RuntimeException('Hay estados personalizados. Revisarlos antes de modificar el ENUM.');
    }
    $columnas=$db->query('SHOW COLUMNS FROM Producto')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('Imagen',$columnas,true)) $db->exec('ALTER TABLE Producto ADD COLUMN Imagen VARCHAR(80) NULL');
    $sql=preg_replace('/ALTER TABLE Producto[^;]+;/','',file_get_contents(__DIR__.'/002_imagenes_recepciones.sql'));
    $db->exec($sql);
    echo "Aplicada 002: imágenes y recepciones.\n";
    $db->exec(file_get_contents(__DIR__.'/004_cierre_reversion.sql'));
    echo "Aplicada 004: cierre y reversión.\n";
    $columnas=$db->query('SHOW COLUMNS FROM Detalle_OrdenCompra')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('Precio_Venta_Referencia',$columnas,true)) $db->exec('ALTER TABLE Detalle_OrdenCompra ADD COLUMN Precio_Venta_Referencia DECIMAL(10,2) NULL');
    echo "Aplicada 005: precio de referencia.\n";
    $faltantes=revisarEsquemaCompras($db);
    if ($faltantes) throw new RuntimeException('Esquema distinto. Todavía falta: '.implode(', ',$faltantes));
    echo "Actualización terminada. Recarga el navegador.\n";
} catch (Throwable $e) {
    fwrite(STDERR,'No se completó: '.$e->getMessage().PHP_EOL);
    fwrite(STDERR,"Los cambios de estructura anteriores pueden haberse aplicado. Corrige el error y vuelve a ejecutar.\n");
    exit(1);
}
