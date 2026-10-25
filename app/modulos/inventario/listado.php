<?php defined('ALMANICA') || exit; ?>
<section class="card-box">
<div class="section-heading">
<div>
<h2><?= escapar($titulo) ?> registrados</h2>
<p class="muted">Consulta y administra la información de tu negocio.</p>
</div>
<?php if (puedeEditar($modulo)): ?>
<a class="button" href="<?= escapar(ruta($modulo,['accion'=>'nuevo'])) ?>">＋ Nuevo <?= escapar($def['singular']) ?>
</a>
<?php endif; ?>
</div>
<form class="filterbar" method="get" data-live-search="listado">
<input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
<label class="search-field">
<span class="sr-only">Buscar</span>
<input type="search" name="q" placeholder="Buscar <?= escapar(mb_strtolower($titulo)) ?>…" value="<?= escapar($_GET['q'] ?? '') ?>">
</label>
<?php if ($modulo==='productos'): foreach (['Id_Categoria'=>['categoria','Todas las categorías'],'Id_Proveedor'=>['proveedor','Todos los proveedores']] as $field=>[$opt,$text]): ?>
<select name="<?= $field ?>" aria-label="<?= escapar($text) ?>">
<option value=""><?= escapar($text) ?>
</option>
<?php foreach ($opciones[$opt] as $key=>$text): ?>
<option value="<?= (int)$key ?>" <?= (string)($_GET[$field] ?? '')===(string)$key?'selected':'' ?>><?= escapar($text) ?>
</option>
<?php endforeach; ?>
</select>
<?php endforeach; endif; ?>
<button class="button secondary">Buscar</button>
</form>
<div data-live-region="listado"><div class="table-wrap">
<table>
<thead>
<tr>
<?php if ($modulo==='productos'): ?><th>Imagen</th><?php endif; ?>
<?php foreach ($def['campos'] as $field=>[$label,$tipo]): if ($tipo==='password' || $tipo==='textarea') continue; ?>
<th><?= escapar($field==='Stock_Actual'?'Stock':$label) ?>
</th>
<?php endforeach; ?>
<?php if (puedeEditar($modulo)): ?>
<th>Acciones</th>
<?php endif; ?>
</tr>
</thead>
<tbody>
<?php if (!$listado['filas']): ?>
<tr>
<td colspan="20" class="empty">
<strong>No hay registros para mostrar</strong>
<p>Agrega tu primer registro o cambia la búsqueda.</p>
</td>
</tr>
<?php endif; ?>
<?php foreach ($listado['filas'] as $fila): ?>
<tr>
<?php if ($modulo==='productos'): ?><td><img class="product-thumb" src="<?= escapar(fotoProducto($fila['Imagen'] ?? null)) ?>" alt="<?= escapar($fila['Nombre']) ?>" loading="lazy"></td><?php endif; ?>
<?php foreach ($def['campos'] as $field=>[$label,$tipo]): if ($tipo==='password' || $tipo==='textarea') continue; ?>
<td>
<?php $v=$fila[$field] ?? ''; if ($tipo==='estado'): ?>
<span class="badge <?= $v?'green':'red' ?>"><?= $v?'Activo':'Inactivo' ?>
</span>
<?php elseif ($field==='Stock_Actual'): ?>
<span class="badge <?= (int)$v <= (int)($fila['Stock_Minimo'] ?? 5)?'amber':'green' ?>"><?= numero($v) ?>
</span>
<?php else: ?><?= escapar($tipo==='money'?dinero($v):($opciones[$tipo][$v] ?? ($v===''?'—':$v))) ?>
<?php endif; ?>
</td>
<?php endforeach; ?>
<?php if (puedeEditar($modulo)): ?>
<td>
<div class="row-actions">
<a class="button mini secondary" href="<?= escapar(ruta($modulo,['accion'=>'editar','id'=>$fila[$def['id']]])) ?>">Editar</a>
<?php if (esAdmin() && !in_array($modulo,['usuarios','empleados'],true)): ?>
<form method="post" data-confirm="¿Eliminar este registro? Esta acción no se puede deshacer.">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int)$fila[$def['id']] ?>">
<input type="hidden" name="accion" value="eliminar">
<button class="button mini danger-button">Eliminar</button>
</form>
<?php endif; ?>
</div>
</td>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?></div>
</section>
