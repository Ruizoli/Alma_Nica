<?php defined('ALMANICA') || exit; ?>
<?php if ($detalle): ?>
<section class="card-box mb">
<div class="section-heading">
<h2>Detalle de venta #<?= (int)$_GET['id'] ?>
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
