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
<?php $nombre = 'Codigo'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Nombre'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Id_Categoria'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Id_Proveedor'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Marca'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Talla'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Stock_Actual'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Stock_Minimo'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Precio_Venta'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
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
