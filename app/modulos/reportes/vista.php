<?php defined('ALMANICA') || exit; ?>
<div class="report-cards">
<?php foreach (['ventas'=>['↗','Ventas','Ingresos y métodos de pago.'],'productos'=>['▦','Productos','Existencias y precios actuales.'],'compras'=>['▣','Compras','Órdenes por fecha y estado.'],'clientes'=>['♧','Clientes','Frecuencia e importe de compra.']] as $key=>[$icon,$name,$description]): ?>
<a class="card-box report-card <?= $tipoReporte===$key?'chosen':'' ?>" href="<?= escapar(ruta('reportes',['tipo'=>$key])) ?>">
<span class="metric-icon"><?= $icon ?>
</span>
<div>
<h2><?= $name ?>
</h2>
<p class="muted"><?= $description ?>
</p>
</div>
</a>
<?php endforeach; ?>
</div>
<section class="card-box">
<form class="filterbar" method="get" data-live-search="listado">
<input type="hidden" name="modulo" value="reportes">
<input type="hidden" name="tipo" value="<?= escapar($tipoReporte) ?>">
<label class="field">
<span>Desde</span>
<input type="date" name="desde" required value="<?= escapar($desde) ?>" <?= $tipoReporte==='productos'?'disabled':'' ?>>
</label>
<label class="field">
<span>Hasta</span>
<input type="date" name="hasta" required value="<?= escapar($hasta) ?>" <?= $tipoReporte==='productos'?'disabled':'' ?>>
</label>
<label class="search-field"><span class="sr-only">Buscar en el reporte</span><input type="search" name="q" placeholder="Buscar en el reporte…" value="<?= escapar($_GET['q'] ?? '') ?>"></label><button class="button">Consultar</button>
<a class="button secondary" href="<?= escapar(ruta('reportes',['tipo'=>$tipoReporte,'desde'=>$desde,'hasta'=>$hasta,'q'=>($_GET['q'] ?? ''),'csv'=>1])) ?>">Exportar CSV</a>
<button class="button secondary" type="button" data-print>Imprimir</button>
</form>
<p class="muted">Hasta 5,000 registros por consulta. <?= $tipoReporte==='productos'?'Inventario actual, sin filtro de fechas.':'Filtra el período para revisar los resultados.' ?>
</p>
<div data-live-region="listado"><div class="table-wrap">
<table>
<?php if ($datosReporte): ?>
<thead>
<tr>
<?php foreach (array_keys($datosReporte[0]) as $col): ?>
<th><?= escapar($col) ?>
</th>
<?php endforeach; ?>
</tr>
</thead>
<tbody>
<?php foreach ($datosReporte as $fila): ?>
<tr>
<?php foreach ($fila as $key=>$v): ?>
<td><?= escapar(in_array($key,['Total','Precio'],true)?dinero($v):($v ?? '—')) ?>
</td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
<?php else: ?>
<tbody>
<tr>
<td class="empty">No hay registros para el período seleccionado.</td>
</tr>
</tbody>
<?php endif; ?>
</table>
</div></div>
</section>
