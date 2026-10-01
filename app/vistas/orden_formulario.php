<?php defined('ALMANICA') || exit;
$filasFormulario=[];
if ($_SERVER['REQUEST_METHOD']==='POST' && is_array($_POST['producto'] ?? null)) {
    foreach ($_POST['producto'] as $i=>$producto) $filasFormulario[]=['Id_Producto'=>$producto,'Cantidad'=>$_POST['cantidad'][$i] ?? 1,'Precio_Costo'=>$_POST['costo'][$i] ?? '0.00'];
} else $filasFormulario=$lineas ?: [['Id_Producto'=>'','Cantidad'=>1,'Precio_Costo'=>'0.00']];
?>
<form method="post" data-operation="compra" class="order-form-page">
    <?php campoCsrf(); ?>
    <input type="hidden" name="accion_compra" value="guardar"><input type="hidden" name="id_orden" value="<?= $accion==='editar'?(int)$idOrden:0 ?>"><input type="hidden" name="operacion_token" value="<?= escapar($tokenOperacion) ?>">
    <section class="card-box">
        <div class="section-heading"><div><h2><?= $accion==='editar'?'Editar orden OC-'.str_pad($idOrden,5,'0',STR_PAD_LEFT):'Nueva orden de compra' ?></h2><p class="muted">Selecciona el proveedor y los productos que vas a solicitar.</p></div><span class="badge amber">Pendiente</span></div>
        <div class="fields-grid"><label class="field"><span>Proveedor *</span><select name="proveedor" required><option value="">Selecciona un proveedor</option><?php foreach ($proveedores as $proveedor): ?><option value="<?= (int)$proveedor['Id_Proveedor'] ?>" <?= (string)($_POST['proveedor'] ?? $orden['Id_Proveedor'] ?? '')===(string)$proveedor['Id_Proveedor']?'selected':'' ?>><?= escapar($proveedor['Nombre']) ?></option><?php endforeach; ?></select></label><div class="field"><span>Fecha de creación</span><input value="<?= $orden?escapar(date('d/m/Y',strtotime($orden['Fecha']))):date('d/m/Y') ?>" readonly></div></div>
    </section>
    <section class="card-box mt">
        <div class="section-heading"><div><h2>Productos de la orden</h2><p class="muted">El inventario se actualizará al registrar la recepción.</p></div><button type="button" class="button green-button" data-add-line>＋ Agregar producto</button></div>
        <div data-lines>
        <?php foreach ($filasFormulario as $fila): ?>
            <div class="order-line purchase-line">
                <label class="field"><span>Producto *</span><select name="producto[]" required><option value="">Selecciona un producto</option><?php foreach ($productos as $producto): ?><option value="<?= (int)$producto['Id_Producto'] ?>" data-price="<?= escapar($producto['Precio_Venta']) ?>" <?= (string)$fila['Id_Producto']===(string)$producto['Id_Producto']?'selected':'' ?>><?= escapar($producto['Codigo'].' · '.$producto['Nombre']) ?></option><?php endforeach; ?></select></label>
                <div class="line-numbers"><label class="field"><span>Cantidad *</span><input name="cantidad[]" type="number" min="1" max="2147483647" step="1" required value="<?= escapar($fila['Cantidad']) ?>"></label><label class="field"><span>Costo C$ *</span><input name="costo[]" type="number" min="0" max="99999999.99" step="0.01" required value="<?= escapar($fila['Precio_Costo']) ?>"></label><span class="line-subtotal">C$ 0.00</span><button type="button" class="remove-line" aria-label="Quitar producto">×</button></div>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="purchase-total"><span>Total de la orden</span><strong data-total>C$ 0.00</strong></div>
        <div class="form-actions"><a class="button secondary" href="<?= escapar(ruta('compras')) ?>">Volver</a><button class="button" type="submit">Guardar orden</button></div>
    </section>
</form>
