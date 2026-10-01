<?php defined('ALMANICA') || exit; ?>
<?php if ($detalle): ?>
<section class="card-box mb">
<div class="section-heading">
<h2>Detalle de <?= $compra?'compra':'venta' ?> #<?= (int)$_GET['id'] ?>
</h2>
<button type="button" class="button secondary" data-print>Imprimir</button>
</div>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody>
<?php $totalDetalle=0; foreach ($detalle as $linea): $totalDetalle+=centavos($linea['Subtotal']); ?>
<tr>
<td><?= escapar($linea['Nombre']) ?>
</td>
<td><?= numero($linea['Cantidad']) ?>
</td>
<td><?= dinero($linea['precio']) ?>
</td>
<td><?= dinero($linea['Subtotal']) ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr>
<th colspan="3">Total</th>
<th><?= dinero(decimal($totalDetalle)) ?>
</th>
</tr>
</tfoot>
</table>
</div>
</section>
<?php endif; ?>
<form method="post" class="operation-form" data-operation="<?= $compra?'compra':'venta' ?>">
<?php campoCsrf(); ?>
<input type="hidden" name="operacion_token" value="<?= escapar($tokenOperacion) ?>">
<div class="operation-grid">
<section class="card-box">
<div class="section-heading">
<div>
<h2><?= $compra?'Nueva orden de compra':'Registrar venta' ?>
</h2>
<p class="muted"><?= $compra?'El stock aumenta al completar la orden.':'Los precios se verifican al guardar.' ?>
</p>
</div>
<span class="badge green"><?= count($productos) ?> productos</span>
</div>
<label class="field">
<span>Buscar productos</span>
<input type="search" placeholder="Código o nombre…" data-product-search>
</label>
<div class="product-grid">
<?php foreach ($productos as $p): ?>
<button type="button" class="product-card" data-add-product="<?= (int)$p['Id_Producto'] ?>" data-search="<?= escapar(mb_strtolower($p['Codigo'].' '.$p['Nombre'])) ?>" <?= !$compra && (int)$p['Stock_Actual']<=0?'disabled':'' ?>>
<img class="product-photo" src="<?= escapar(fotoProducto($p['Imagen'] ?? null)) ?>" alt="" loading="lazy">
<strong><?= escapar($p['Nombre']) ?>
</strong>
<small><?= escapar($p['Codigo']) ?>
</small>
<b><?= dinero($p['Precio_Venta']) ?>
</b>
<small>Stock: <?= numero($p['Stock_Actual']) ?>
</small>
</button>
<?php endforeach; ?>
<?php if (!$productos): ?>
<div class="empty">
<strong>Primero registra productos</strong>
<p>Los productos disponibles aparecerán aquí.</p>
</div>
<?php endif; ?>
</div>
<p class="muted product-no-results" hidden>No se encontraron productos.</p>
</section>
<section class="card-box order-box">
<div class="section-heading">
<h2>Detalle de <?= $compra?'compra':'venta' ?>
</h2>
<button type="button" class="button mini danger-button" data-clear>Limpiar</button>
</div>
<div data-lines>
<?php $anteriores = $_POST['producto'] ?? ['']; if (!is_array($anteriores)) $anteriores=['']; foreach ($anteriores as $i=>$seleccion): ?>
<div class="order-line">
<label class="field">
<span>Producto</span>
<select name="producto[]" class="line-product" required>
<option value="">Selecciona un producto</option>
<?php foreach ($productos as $p): ?>
<option value="<?= (int)$p['Id_Producto'] ?>" data-price="<?= escapar($p['Precio_Venta']) ?>" <?= (string)$seleccion===(string)$p['Id_Producto']?'selected':'' ?>><?= escapar($p['Codigo'].' · '.$p['Nombre']) ?>
</option>
<?php endforeach; ?>
</select>
</label>
<div class="line-numbers">
<label class="field">
<span>Cantidad</span>
<input name="cantidad[]" type="number" min="1" max="2147483647" step="1" required value="<?= escapar($_POST['cantidad'][$i] ?? 1) ?>">
</label>
<?php if ($compra): ?>
<label class="field">
<span>Costo C$</span>
<input name="costo[]" type="number" min="0" max="99999999.99" step="0.01" required value="<?= escapar($_POST['costo'][$i] ?? '0.00') ?>">
</label>
<?php endif; ?>
<span class="line-subtotal">C$ 0.00</span>
<button type="button" class="remove-line" aria-label="Quitar producto">×</button>
</div>
</div>
<?php endforeach; ?>
</div>
<button type="button" class="button secondary full" data-add-line>＋ Agregar fila</button>
<label class="field mt">
<span><?= $compra?'Proveedor':'Cliente (opcional)' ?>
</span>
<select name="<?= $compra?'proveedor':'cliente' ?>" <?= $compra?'required':'' ?>>
<option value=""><?= $compra?'Selecciona un proveedor':'Consumidor final' ?>
</option>
<?php foreach ($terceros as $t): ?>
<option value="<?= (int)$t['id'] ?>" <?= (string)($_POST[$compra?'proveedor':'cliente'] ?? '')===(string)$t['id']?'selected':'' ?>><?= escapar($t['Nombre']) ?>
</option>
<?php endforeach; ?>
</select>
</label>
<?php if (!$compra): ?>
<label class="field mt">
<span>Método de pago</span>
<select name="metodo">
<?php foreach (['Efectivo','Tarjeta','Transferencia'] as $m): ?>
<option <?= ($_POST['metodo'] ?? '')===$m?'selected':'' ?>><?= $m ?>
</option>
<?php endforeach; ?>
</select>
</label>
<?php endif; ?>
<div class="order-total">
<span>Total estimado</span>
<strong data-total>C$ 0.00</strong>
</div>
<button class="button full" type="submit" <?= !$productos?'disabled':'' ?>><?= $compra?'Guardar orden pendiente':'Registrar venta' ?>
</button>
</section>
</div>
</form>
<section class="card-box mt">
<div class="section-heading">
<div>
<h2><?= $compra?'Órdenes de compra':'Ventas registradas' ?>
</h2>
<p class="muted">Historial de operaciones</p>
</div>
</div>
<form class="filterbar" method="get" data-live-search="listado"><input type="hidden" name="modulo" value="<?= escapar($modulo) ?>"><label class="search-field"><span class="sr-only">Buscar en el historial</span><input type="search" name="q" placeholder="Buscar en el historial…" value="<?= escapar($_GET['q'] ?? '') ?>"></label><button class="button secondary">Buscar</button></form>
<div data-live-region="listado"><div class="table-wrap">
<table>
<thead>
<tr>
<th>Número</th>
<th>Fecha</th>
<th><?= $compra?'Proveedor':'Cliente' ?>
</th>
<th><?= $compra?'Estado':'Pago' ?>
</th>
<th>Total</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php foreach ($listado['filas'] as $f): ?>
<tr>
<td>#<?= (int)$f['id'] ?>
</td>
<td><?= escapar(fechaCorta($f['Fecha'])) ?>
</td>
<td><?= escapar($f['nombre']) ?>
</td>
<td>
<span class="badge <?= $f['estado']==='Pendiente'?'amber':'green' ?>"><?= escapar($f['estado']) ?>
</span>
</td>
<td><?= dinero($f['Total']) ?>
</td>
<td>
<div class="row-actions">
<a class="button mini secondary" href="<?= escapar(ruta($modulo,['id'=>$f['id']])) ?>">Detalle</a>
<?php if ($compra && $f['estado']==='Pendiente'): foreach (['Completada'=>'Completar','Cancelada'=>'Cancelar'] as $estado=>$label): ?>
<form method="post" data-confirm="<?= $estado==='Completada'?'¿Confirmar la recepción y aumentar el stock?':'¿Cancelar esta orden pendiente?' ?>">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
<input type="hidden" name="estado" value="<?= $estado ?>">
<button class="button mini <?= $estado==='Cancelada'?'danger-button':'' ?>"><?= $label ?>
</button>
</form>
<?php endforeach; endif; ?>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php if (!$listado['filas']): ?>
<tr>
<td colspan="6" class="empty">Todavía no hay operaciones registradas.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/parciales/paginacion.php'; ?></div>
</section>
