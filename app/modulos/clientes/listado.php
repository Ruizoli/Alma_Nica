<?php defined('ALMANICA') || exit;
// Iconos pequeños en línea (sin librerías externas).
$svg = static function (string $nombre): string {
    $formas = [
        'buscar'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'mas'      => '<path d="M12 5v14M5 12h14"/>',
        'editar'   => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'borrar'   => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>',
        'correo'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'telefono' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
        'cedula'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.6-1.6 1.8-2.3 3-2.3s2.4.7 3 2.3M15 10h3M15 14h3"/>',
    ];
    return '<svg class="mi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $formas[$nombre] . '</svg>';
};

?>
<section class="card-box mod-cli">
<header class="mod-head">
<div class="mod-title">
<span class="mod-badge"><?= icono('clientes') ?></span>
<div>
<h2><?= escapar($titulo) ?> registrados</h2>
<p>Consulta y administra los datos de tus clientes.</p>
</div>
</div>
<?php if (puedeEditar($modulo)): ?>
<a class="button mod-new" href="<?= escapar(ruta($modulo,['accion'=>'nuevo'])) ?>"><?= $svg('mas') ?> Nuevo cliente</a>
<?php endif; ?>
</header>

<div class="mod-body">
<form class="filterbar mod-search" method="get" data-live-search="listado">
<input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
<label class="search-field">
<span class="sr-only">Buscar</span>
<?= $svg('buscar') ?>
<input type="search" name="q" placeholder="Buscar por nombre, cédula o teléfono…" value="<?= escapar($_GET['q'] ?? '') ?>">
</label>
<button class="button secondary">Buscar</button>
</form>

<div data-live-region="listado">
<div class="table-wrap">
<table class="cli-table">
<thead>
<tr>
<th>Cliente</th>
<th><?= escapar($def['campos']['Correo'][0]) ?></th>
<th><?= escapar($def['campos']['Telefono'][0]) ?></th>
<?php if (puedeEditar($modulo)): ?>
<th>Acciones</th>
<?php endif; ?>
</tr>
</thead>
<tbody>
<?php if (!$listado['filas']): ?>
<tr>
<td colspan="4" class="mod-empty">
<span class="mod-empty-icon"><?= icono('clientes') ?></span>
<strong>No hay clientes para mostrar</strong>
<p>Agrega tu primer cliente o cambia el texto de búsqueda.</p>
</td>
</tr>
<?php endif; ?>
<?php foreach ($listado['filas'] as $fila):
    $nombreCliente = trim((string) ($fila['Nombre'] ?? ''));
    $cedula   = trim((string) ($fila['Cedula'] ?? ''));
    $correo   = trim((string) ($fila['Correo'] ?? ''));
    $telefono = trim((string) ($fila['Telefono'] ?? ''));
    $inicial  = mb_strtoupper(mb_substr($nombreCliente !== '' ? $nombreCliente : '?', 0, 1));
?>
<tr>
<td class="cli-celda-persona" data-label="Cliente">
<div class="cli-persona">
<span class="cli-avatar" aria-hidden="true"><?= escapar($inicial) ?></span>
<div>
<strong><?= escapar($nombreCliente) ?></strong>
<small><?= $svg('cedula') ?> <?= $cedula !== '' ? escapar($cedula) : 'Sin cédula' ?></small>
</div>
</div>
</td>
<td data-label="<?= escapar($def['campos']['Correo'][0]) ?>">
<?php if ($correo !== ''): ?>
<a class="cli-dato" href="mailto:<?= escapar($correo) ?>"><?= $svg('correo') ?> <?= escapar($correo) ?></a>
<?php else: ?>
<span class="cli-dato vacio"><?= $svg('correo') ?> No registrado</span>
<?php endif; ?>
</td>
<td data-label="<?= escapar($def['campos']['Telefono'][0]) ?>">
<?php if ($telefono !== ''): ?>
<a class="cli-dato" href="tel:<?= escapar(preg_replace('/[^0-9+]/', '', $telefono)) ?>"><?= $svg('telefono') ?> <?= escapar($telefono) ?></a>
<?php else: ?>
<span class="cli-dato vacio"><?= $svg('telefono') ?> No registrado</span>
<?php endif; ?>
</td>
<?php if (puedeEditar($modulo)): ?>
<td data-label="Acciones">
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
</td>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?>
</div>
</div>
</section>
