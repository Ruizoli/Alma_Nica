<?php defined('ALMANICA') || exit; ?>
<form method="post" class="operation-form" data-operation="venta">
<?php campoCsrf(); ?>
<input type="hidden" name="operacion_token" value="<?= escapar($tokenOperacion) ?>">
<div class="operation-grid">
<section class="card-box">
<div class="section-heading">
<div>
<h2>Registrar venta
</h2>
<p class="muted">Los precios se verifican al guardar.
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
<button type="button" class="product-card" data-add-product="<?= (int)$p['Id_Producto'] ?>" data-search="<?= escapar(mb_strtolower($p['Codigo'].' '.$p['Nombre'])) ?>" <?= (int)$p['Stock_Actual']<=0?'disabled':'' ?>>
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
<h2>Detalle de venta
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
<span class="line-subtotal">C$ 0.00</span>
<button type="button" class="remove-line" aria-label="Quitar producto">×</button>
</div>
</div>
<?php endforeach; ?>
</div>
<button type="button" class="button secondary full" data-add-line>＋ Agregar fila</button>
<label class="field mt">
<span>Cliente (opcional)
</span>
<select name="cliente" >
<option value="">Consumidor final
</option>
<?php foreach ($terceros as $t): ?>
<option value="<?= (int)$t['id'] ?>" <?= (string)($_POST['cliente'] ?? '')===(string)$t['id']?'selected':'' ?>><?= escapar($t['Nombre']) ?>
</option>
<?php endforeach; ?>
</select>
</label>
<label class="field mt">
<span>Método de pago</span>
<select name="metodo">
<?php foreach (['Efectivo','Tarjeta','Transferencia'] as $m): ?>
<option <?= ($_POST['metodo'] ?? '')===$m?'selected':'' ?>><?= $m ?>
</option>
<?php endforeach; ?>
</select>
</label>
<div class="order-total">
<span>Total estimado</span>
<strong data-total>C$ 0.00</strong>
</div>
<button class="button full" type="submit" <?= !$productos?'disabled':'' ?>>Registrar venta
</button>
</section>
</div>
</form>
