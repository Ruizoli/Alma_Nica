<?php defined('ALMANICA') || exit;

$esNuevo = $accion === 'nuevo';
?>
<section class="card-box form-card mod-cli">
<div class="mod-form-head">
<div class="mod-title">
<span class="mod-badge"><?= icono('clientes') ?></span>
<div>
<h2><?= $esNuevo ? 'Nuevo cliente' : 'Editar cliente' ?></h2>
<p>Completa los datos del cliente.</p>
</div>
</div>
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">← Volver</a>
</div>
<form method="post" class="fields-grid mod-fields" enctype="multipart/form-data">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int) ($registro[$def['id']] ?? $_GET['id'] ?? 0) ?>">
<div class="wide form-section"><i>1</i> Datos personales</div>
<?php $nombre = 'Nombre'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Cedula'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<div class="wide form-section"><i>2</i> Contacto</div>
<?php $nombre = 'Correo'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<?php $nombre = 'Telefono'; require __DIR__ . '/../../vistas/parciales/campo_catalogo.php'; ?>
<div class="wide form-actions">
<a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Cancelar</a>
<button class="button" type="submit"><?= $esNuevo ? 'Crear cliente' : 'Guardar cambios' ?></button>
</div>
</form>
</section>
