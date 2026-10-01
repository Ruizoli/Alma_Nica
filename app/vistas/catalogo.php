<?php defined('ALMANICA') || exit; ?>
<?php if (in_array($accion,['nuevo','editar'],true) && puedeEditar($modulo)): ?>
<section class="card-box form-card">
<div class="section-heading">
<div>
<h2><?= $accion==='nuevo'?'Nuevo registro':'Editar registro' ?>
</h2>
<p class="muted">Completa los datos de <?= escapar($def['singular']) ?>.</p>
</div>
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Volver</a>
</div>
<form method="post" class="fields-grid" enctype="multipart/form-data">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int) ($registro[$def['id']] ?? $_GET['id'] ?? 0) ?>">
<?php foreach ($def['campos'] as $nombre=>[$label,$tipo,$requerido]): $valor = $tipo==='password'?'':($registro[$nombre] ?? ($nombre==='Estado'?1:($nombre==='Stock_Minimo'?5:($tipo==='integer'?0:'')))); ?>
<label class="field <?= $tipo==='textarea'?'wide':'' ?>">
<span><?= escapar($label) ?><?= $requerido?' *':'' ?>
</span>
<?php if (isset($opciones[$tipo])): ?>
<select name="<?= escapar($nombre) ?>" <?= $requerido?'required':'' ?>>
<?php if (!$requerido): ?>
<option value="">Sin asignar</option>
<?php endif; ?>
<?php foreach ($opciones[$tipo] as $key=>$texto): ?>
<option value="<?= escapar($key) ?>" <?= (string)$valor===(string)$key?'selected':'' ?>><?= escapar($texto) ?>
</option>
<?php endforeach; ?>
</select>
<?php elseif ($tipo==='textarea'): ?>
<textarea name="<?= escapar($nombre) ?>" rows="3" maxlength="5000"><?= escapar($valor) ?>
</textarea>
<?php else: ?>
<input name="<?= escapar($nombre) ?>" type="<?= in_array($tipo,['integer','money'],true)?'number':escapar($tipo) ?>" value="<?= escapar($valor) ?>" <?= $tipo==='password'?'autocomplete="new-password"':'' ?> <?= $requerido && !($tipo==='password' && $accion==='editar')?'required':'' ?> <?= in_array($tipo,['integer','money'],true)?'min="0" max="'.($tipo==='integer'?'2147483647':'99999999.99').'" step="'.($tipo==='money'?'0.01':'1').'"':'' ?> <?= isset($def['campos'][$nombre][3])?'maxlength="'.(int)$def['campos'][$nombre][3].'"':'' ?> <?= $nombre==='Stock_Actual' && $accion==='editar'?'readonly':'' ?>>
<?php endif; ?>
<?php if ($tipo==='password' && $accion==='editar'): ?>
<small>Déjala vacía para conservar la contraseña.</small>
<?php endif; ?>
<?php if ($nombre==='Stock_Actual'): ?>
<small>Después de crear el producto, el stock cambia con compras y ventas.</small>
<?php endif; ?>
</label>
<?php endforeach; ?>
<?php if ($modulo==='productos'): ?>
<div class="wide image-editor">
    <img data-photo-preview src="<?= escapar(fotoProducto($registro['Imagen'] ?? null)) ?>" alt="Vista previa del producto">
    <div><label class="field"><span>Foto del producto</span><input type="file" name="foto" accept="image/jpeg,image/png,image/webp" data-photo-input><small>JPG, PNG o WebP · Máximo 2 MB. La foto se mostrará en productos y ventas.</small></label>
    <?php if (!empty($registro['Imagen'])): ?><label class="remove-photo"><input type="checkbox" name="quitar_foto" value="1"> Quitar foto actual</label><?php endif; ?></div>
</div>
<?php endif; ?>
<div class="wide form-actions">
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Cancelar</a>
<button class="button" type="submit">Guardar registro</button>
</div>
</form>
</section>
<?php else: ?>
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
<?php if (!in_array($modulo,['usuarios','empleados'],true)): ?>
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
<?php require __DIR__ . '/parciales/paginacion.php'; ?></div>
</section>
<?php endif; ?>
