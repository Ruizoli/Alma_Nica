<?php defined('ALMANICA') || exit;

$esNuevo = $accion === 'nuevo';
?>
<section class="card-box form-card mod-prov">
<div class="mod-form-head">
<div class="mod-title">
<span class="mod-badge"><?= icono('proveedores') ?></span>
<div>
<h2><?= $esNuevo ? 'Nuevo proveedor' : 'Editar proveedor' ?></h2>
<p>Completa los datos del proveedor.</p>
</div>
</div>
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">← Volver</a>
</div>
<form method="post" class="fields-grid mod-fields" enctype="multipart/form-data">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int) ($registro[$def['id']] ?? $_GET['id'] ?? 0) ?>">
<div class="wide form-section"><i>1</i> Datos del proveedor</div>
<div class="wide"><?php $nombre = 'Nombre'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?></div>
<div class="wide form-section"><i>2</i> Contacto</div>
<?php $nombre = 'Telefono'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Correo'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<div class="wide form-section"><i>3</i> Ubicación</div>
<?php $nombre = 'Direccion'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<div class="wide form-actions">
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Cancelar</a>
<button class="button" type="submit"><?= $esNuevo ? 'Crear proveedor' : 'Guardar cambios' ?></button>
</div>
</form>
</section>
