<?php defined('ALMANICA') || exit; ?>
<?php if ($orden['Estado']!=='Pendiente'): ?>
<section class="card-box empty"><h2>Esta orden no tiene una recepción pendiente</h2><a class="button" href="<?= escapar(ruta('compras',['id'=>$idOrden])) ?>">Ver orden</a></section>
<?php else: ?>
<form method="post" class="card-box">
    <?php campoCsrf(); ?><input type="hidden" name="accion_compra" value="recibir"><input type="hidden" name="id_orden" value="<?= (int)$idOrden ?>"><input type="hidden" name="operacion_token" value="<?= escapar($tokenOperacion) ?>">
    <div class="section-heading"><div><h2>Recepción de compra</h2><p class="muted">Registra únicamente las cantidades que llegaron.</p></div><span class="badge blue">OC-<?= str_pad($idOrden,5,'0',STR_PAD_LEFT) ?></span></div>
    <div class="fields-grid"><div class="field"><span>Proveedor</span><input value="<?= escapar($orden['Proveedor']) ?>" readonly></div><label class="field"><span>Fecha de recepción *</span><input type="date" name="fecha" required min="<?= escapar(substr($orden['Fecha'],0,10)) ?>" max="<?= date('Y-m-d') ?>" value="<?= escapar($_POST['fecha'] ?? date('Y-m-d')) ?>"></label><label class="field"><span>N.º factura del proveedor</span><input name="factura" maxlength="80" placeholder="Ej. FAC-0025" value="<?= escapar($_POST['factura'] ?? '') ?>"></label><label class="field"><span>Observaciones</span><textarea name="observaciones" maxlength="2000" rows="2" placeholder="Estado de la entrega, faltantes…"><?= escapar($_POST['observaciones'] ?? '') ?></textarea></label></div>
    <div class="section-heading mt"><div><h2>Productos de la orden</h2><p class="muted">Puedes guardar entregas parciales. El resto seguirá pendiente.</p></div><button type="button" class="button secondary" data-receive-all>Recibir todo lo pendiente</button></div>
    <div class="table-wrap"><table><thead><tr><th>Producto</th><th>Pedido</th><th>Recibido antes</th><th>Pendiente</th><th>Recibir ahora</th></tr></thead><tbody>
    <?php foreach ($lineas as $linea): $pendiente=(int)$linea['Cantidad']-(int)$linea['Recibida']; ?>
    <tr><td><div class="product-cell"><img class="product-thumb" src="<?= escapar(fotoProducto($linea['Imagen'])) ?>" alt=""><div><?= escapar($linea['Nombre']) ?><small><?= escapar($linea['Codigo']) ?></small></div></div></td><td><?= numero($linea['Cantidad']) ?></td><td><?= numero($linea['Recibida']) ?></td><td><span class="badge <?= $pendiente?'amber':'green' ?>"><?= numero($pendiente) ?></span></td><td><input class="receive-quantity" aria-label="Recibir <?= escapar($linea['Nombre']) ?>" type="number" name="recibida[<?= (int)$linea['Id_Detalle_Orden'] ?>]" min="0" max="<?= $pendiente ?>" step="1" required value="<?= escapar($_POST['recibida'][$linea['Id_Detalle_Orden']] ?? 0) ?>" <?= $pendiente===0?'readonly':'' ?>></td></tr>
    <?php endforeach; ?></tbody></table></div>
    <div class="form-actions mt"><a class="button secondary" href="<?= escapar(ruta('compras',['id'=>$idOrden])) ?>">Cancelar</a><button class="button green-button" type="submit">Guardar recepción</button></div>
</form>
<?php endif; ?>
