<?php defined('ALMANICA') || exit; ?>
<section class="card-box mt">
<div class="section-heading">
<div>
<h2>Ventas registradas
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
<th>Cliente
</th>
<th>Pago
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
<?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?></div>
</section>
