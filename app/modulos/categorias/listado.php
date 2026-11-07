<?php defined('ALMANICA') || exit;
// Iconos pequeños en línea (sin librerías externas).
$svg = static function (string $nombre): string {
    $formas = [
        'buscar' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'mas'    => '<path d="M12 5v14M5 12h14"/>',
        'editar' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'borrar' => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>',
    ];
    return '<svg class="mi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $formas[$nombre] . '</svg>';
};

?>
<section class="card-box mod-cat">
<header class="mod-head">
<div class="mod-title">
<span class="mod-badge"><?= icono('categorias') ?></span>
<div>
<h2><?= escapar($titulo) ?> registradas</h2>
<p>Agrupa tus productos para encontrarlos más rápido.</p>
</div>
</div>
<?php if (puedeEditar($modulo)): ?>
<a class="button mod-new" href="<?= escapar(ruta($modulo,['accion'=>'nuevo'])) ?>"><?= $svg('mas') ?> Nueva categoría</a>
<?php endif; ?>
</header>

<div class="mod-body">
<form class="filterbar mod-search" method="get" data-live-search="listado">
<input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
<label class="search-field">
<span class="sr-only">Buscar</span>
<?= $svg('buscar') ?>
<input type="search" name="q" placeholder="Buscar categorías…" value="<?= escapar($_GET['q'] ?? '') ?>">
</label>
<button class="button secondary">Buscar</button>
</form>

<div data-live-region="listado">
<div class="cat-grid">
<?php if (!$listado['filas']): ?>
<div class="mod-empty">
<span class="mod-empty-icon"><?= icono('categorias') ?></span>
<strong>No hay categorías para mostrar</strong>
<p>Crea tu primera categoría o cambia el texto de búsqueda.</p>
</div>
<?php endif; ?>
<?php foreach ($listado['filas'] as $fila):
    $descripcion = trim((string) ($fila['Descripcion'] ?? ''));
?>
<article class="cat-card">
<span class="cat-icon"><?= icono('categorias') ?></span>
<h3 class="cat-name"><?= escapar($fila['Nombre'] ?? '') ?></h3>
<p class="cat-desc<?= $descripcion === '' ? ' vacia' : '' ?>"><?= escapar($descripcion !== '' ? $descripcion : 'Sin descripción.') ?></p>
<div class="cat-foot">
<span class="cat-num">N.º <?= (int) $fila[$def['id']] ?></span>
<?php if (puedeEditar($modulo)): ?>
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
<?php endif; ?>
</div>
</article>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?>
</div>
</div>
</section>
