# Compras: impresión, cierre y reversión

Aplicar database/004_cierre_reversion.sql después de 001, 002 y 003. La migración agrega tablas de auditoría y el estado Cerrada, sin borrar registros.

En Compras > Ver detalle > Imprimir se utiliza impresion.php y assets/css/compras.css para una hoja A4 con logo AlmaNica.

El administrador puede revertir una recepción vigente de una orden pendiente o completada. Se valida stock suficiente, se descuenta exactamente lo recibido y se reabre la orden. No se borra la recepción; se registra empleado, fecha y motivo. Una reversión repetida es rechazada.

Cerrar orden solo aplica a pendientes, incluso parcialmente recibidas; conserva stock e historial y bloquea nuevas entregas. Se registra motivo, fecha y empleado. Las órdenes cerradas no admiten reversión.

El historial y CSV señalan las recepciones revertidas. Los importes de esas filas corresponden al documento original, no a una entrada vigente.

Aplicar también 005_precio_venta_orden.sql. El formulario guarda el precio de venta de referencia en cada línea; no actualiza el precio de Producto. Las órdenes pendientes sin recepciones pueden editarse desde el listado o el detalle.
