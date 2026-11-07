<?php defined('ALMANICA') || exit;

$esNuevo = $accion === 'nuevo';
?>
<section class="card-box form-card mod-cat">
<div class="mod-form-head">
<div class="mod-title">
<span class="mod-badge"><?= icono('categorias') ?></span>
<div>
<h2><?= $esNuevo ? 'Nueva categoría' : 'Editar categoría' ?></h2>
<p>Dale un nombre claro y, si quieres, una breve descripción.</p>
</div>
</div>
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">← Volver</a>
</div>
<form method="post" class="fields-grid mod-fields" enctype="multipart/form-data">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int) ($registro[$def['id']] ?? $_GET['id'] ?? 0) ?>">
<div class="wide form-section"><i>1</i> Información de la categoría</div>
<div class="wide"><?php $nombre = 'Nombre'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?></div>
<?php $nombre = 'Descripcion'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<div class="wide form-actions">
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Cancelar</a>
<button class="button" type="submit"><?= $esNuevo ? 'Crear categoría' : 'Guardar cambios' ?></button>
</div>
</form>
</section>
