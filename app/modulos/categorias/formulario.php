<?php defined('ALMANICA') || exit; ?>
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
<?php $nombre = 'Nombre'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Descripcion'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<div class="wide form-actions">
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Cancelar</a>
<button class="button" type="submit">Guardar registro</button>
</div>
</form>
</section>
