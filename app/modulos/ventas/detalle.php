<?php defined('ALMANICA') || exit; ?>
<div class="factura-acciones">
<a class="button secondary" href="<?= escapar(ruta('ventas')) ?>">Volver a ventas</a>
<button type="button" class="button" onclick="window.print()">Imprimir factura</button>
</div>
<article class="factura-venta">
<header class="factura-cabecera">
<div class="factura-marca"><img src="assets/img/almanica-emblema.png" alt="Logo AlmaNica"><div><h1>AlmaNica</h1><p>Sistema de ventas e inventario</p><p>El Corazón de Nicaragua</p></div></div>
<div class="factura-numero"><h2>Factura de venta</h2><strong>#FACT-<?= str_pad((string)$venta['Id_Venta'],5,'0',STR_PAD_LEFT) ?></strong><p><?= escapar(fechaCorta($venta['Fecha'])) ?></p></div>
</header>
<table class="factura-datos"><thead><tr><th>Cliente</th><th>Vendedor</th><th>Fecha</th></tr></thead>
<tbody><tr><td><?= escapar($venta['Cliente']) ?></td><td><?= escapar($venta['Vendedor']) ?></td><td><?= escapar(fechaCorta($venta['Fecha'])) ?></td></tr></tbody></table>
<table class="factura-productos"><thead><tr><th>Código</th><th>Descripción</th><th>Cant.</th><th>Unidad</th><th>Precio</th><th>Total</th></tr></thead><tbody>
<?php foreach ($detalle as $linea): ?>
<tr><td><?= escapar($linea['Codigo']) ?></td><td><?= escapar($linea['Nombre']) ?></td><td><?= (int)$linea['Cantidad'] ?></td><td>UND</td><td><?= dinero($linea['Precio_Unitario']) ?></td><td><?= dinero($linea['Subtotal']) ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<div class="factura-totales"><p><span>Subtotal</span><strong><?= dinero($venta['Total']) ?></strong></p><p><span>Total a pagar</span><strong><?= dinero($venta['Total']) ?></strong></p></div>
<p class="factura-pago">Método de pago: <?= escapar($venta['Metodo_Pago']) ?></p>
</article>
<?php if ($ventaRecienGuardada): ?><script>localStorage.removeItem('carrito_ventas');</script><?php endif; ?>
