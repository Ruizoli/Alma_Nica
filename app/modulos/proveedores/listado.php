<<<<<<< HEAD
<?php defined('ALMANICA') || exit;
// Iconos pequeños en línea (sin librerías externas).
$svg = static function (string $nombre): string {
    $formas = [
        'buscar'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'mas'       => '<path d="M12 5v14M5 12h14"/>',
        'editar'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'borrar'    => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>',
        'correo'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'telefono'  => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'direccion' => '<path d="M12 21s7-5.6 7-11a7 7 0 0 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
    ];
    return '<svg class="mi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $formas[$nombre] . '</svg>';
};

?>
<section class="card-box mod-prov">
<header class="mod-head">
<div class="mod-title">
<span class="mod-badge"><?= icono('proveedores') ?></span>
<div>
<h2><?= escapar($titulo) ?> registrados</h2>
<p>Quién te surte y cómo contactarlo, todo en un solo lugar.</p>
</div>
</div>
<?php if (puedeEditar($modulo)): ?>
<a class="button mod-new" href="<?= escapar(ruta($modulo,['accion'=>'nuevo'])) ?>"><?= $svg('mas') ?> Nuevo proveedor</a>
<?php endif; ?>
</header>

<div class="mod-body">
<form class="filterbar mod-search" method="get" data-live-search="listado">
<input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
<label class="search-field">
<span class="sr-only">Buscar</span>
<?= $svg('buscar') ?>
<input type="search" name="q" placeholder="Buscar por nombre, teléfono o correo…" value="<?= escapar($_GET['q'] ?? '') ?>">
</label>
<button class="button secondary">Buscar</button>
</form>

<div data-live-region="listado">
<div class="prov-grid">
<?php if (!$listado['filas']): ?>
<div class="mod-empty">
<span class="mod-empty-icon"><?= icono('proveedores') ?></span>
<strong>No hay proveedores para mostrar</strong>
<p>Agrega tu primer proveedor o cambia el texto de búsqueda.</p>
</div>
<?php endif; ?>
<?php foreach ($listado['filas'] as $fila):
    $telefono  = trim((string) ($fila['Telefono'] ?? ''));
    $correo    = trim((string) ($fila['Correo'] ?? ''));
    $direccion = trim((string) ($fila['Direccion'] ?? ''));
?>
<article class="prov-card">
<div class="prov-top">
<span class="prov-logo"><?= icono('proveedores') ?></span>
<div>
<h3><?= escapar($fila['Nombre'] ?? '') ?></h3>
<small>PROVEEDOR N.º <?= (int) $fila[$def['id']] ?></small>
</div>
</div>
<ul class="prov-datos">
<li>
<span class="prov-ico"><?= $svg('telefono') ?></span>
<?php if ($telefono !== ''): ?>
<a class="texto" href="tel:<?= escapar(preg_replace('/[^0-9+]/', '', $telefono)) ?>"><?= escapar($telefono) ?></a>
<?php else: ?><span class="texto vacio">Sin teléfono</span><?php endif; ?>
</li>
<li>
<span class="prov-ico"><?= $svg('correo') ?></span>
<?php if ($correo !== ''): ?>
<a class="texto" href="mailto:<?= escapar($correo) ?>"><?= escapar($correo) ?></a>
<?php else: ?><span class="texto vacio">Sin correo</span><?php endif; ?>
</li>
<li>
<span class="prov-ico"><?= $svg('direccion') ?></span>
<?php if ($direccion !== ''): ?>
<span class="texto"><?= escapar($direccion) ?></span>
<?php else: ?><span class="texto vacio">Sin dirección</span><?php endif; ?>
</li>
</ul>
<?php if (puedeEditar($modulo)): ?>
<div class="prov-foot">
<div class="mod-actions">
<a class="button mini secondary" href="<?= escapar(ruta($modulo,['accion'=>'editar','id'=>$fila[$def['id']]])) ?>"><?= $svg('editar') ?> Editar</a>
<?php if (esAdmin()): ?>
<form method="post" data-confirm="¿Eliminar este registro? Esta acción no se puede deshacer.">
<?php campoCsrf(); ?>
<input type="hidden" name="id" value="<?= (int) $fila[$def['id']] ?>">
<input type="hidden" name="accion" value="eliminar">
<button class="button mini danger-button"><?= $svg('borrar') ?> Eliminar</button>
</form>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
</article>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?>
</div>
</div>
</section>
=======
<?php
defined('ALMANICA') || exit;

// Consulta directa de respaldo para garantizar que los datos se muestren
try {
    $busqueda = trim((string) ($_GET['q'] ?? ''));
    if ($busqueda !== '') {
        $stmt = $conexion->prepare("SELECT Id_Proveedor AS id, Nombre, Telefono, Correo, Direccion FROM proveedor WHERE Nombre LIKE ? OR Correo LIKE ? OR Telefono LIKE ? ORDER BY Nombre");
        $stmt->execute(['%'.$busqueda.'%', '%'.$busqueda.'%', '%'.$busqueda.'%']);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conexion->query("SELECT Id_Proveedor AS id, Nombre, Telefono, Correo, Direccion FROM proveedor ORDER BY Nombre");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    $datos = [];
}
?>
<section class="card-box">
    <div class="section-heading">
        <div>
            <h2>Proveedores</h2>
            <p class="muted">Gestión y listado de proveedores registrados.</p>
        </div>
        <a class="button" href="<?= escapar(ruta($modulo, ['accion' => 'nuevo'])) ?>">Nuevo proveedor</a>
    </div>

    <form method="get" class="search-bar" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, correo o teléfono..." value="<?= escapar($_GET['q'] ?? '') ?>" style="flex: 1; padding: 8px;">
        <button class="button secondary" type="submit">Buscar</button>
        <a class="button secondary" href="<?= escapar(ruta($modulo)) ?>">Limpiar</a>
    </form>

    <div class="table-responsive">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 10px;">Nombre</th>
                    <th style="text-align: left; padding: 10px;">Teléfono</th>
                    <th style="text-align: left; padding: 10px;">Correo</th>
                    <th style="text-align: left; padding: 10px;">Dirección</th>
                    <th style="text-align: right; padding: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos)): ?>
                    <?php foreach ($datos as $row): ?>
                        <tr>
                            <td style="padding: 10px;"><?= escapar($row['Nombre'] ?? '') ?></td>
                            <td style="padding: 10px;"><?= escapar($row['Telefono'] ?? '') ?></td>
                            <td style="padding: 10px;"><?= escapar($row['Correo'] ?? '') ?></td>
                            <td style="padding: 10px;"><?= escapar($row['Direccion'] ?? '') ?></td>
                            <td style="padding: 10px; text-align: right;">
                                <a class="button secondary" href="<?= escapar(ruta($modulo, ['accion' => 'editar', 'id' => $row['id']])) ?>" style="padding: 4px 8px; font-size: 12px;">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;" class="text-muted">No hay proveedores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
>>>>>>> bf4726a (Cambio dasbord)
