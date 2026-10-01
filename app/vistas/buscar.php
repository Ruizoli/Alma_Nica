<?php defined('ALMANICA') || exit; ?>
<section class="card-box"><h2>Resultados para “<?= escapar($busquedaGlobal) ?>”</h2><p class="muted mb">Hasta 10 coincidencias por sección.</p>
<?php foreach ($resultadosGlobales as $tipo=>$filas): ?><h3 class="mt"><?= escapar(catalogos()[$tipo]['titulo']) ?></h3><?php if (!$filas): ?><p class="muted">No se encontraron coincidencias.</p><?php else: ?><ul class="search-results"><?php foreach ($filas as $fila): ?><li><span><?= escapar($fila['Nombre']) ?></span><a class="button mini secondary" href="<?= escapar(ruta($tipo,['q'=>$fila['Nombre']])) ?>">Ver en <?= escapar($tipo) ?></a></li><?php endforeach; ?></ul><?php endif; endforeach; ?>
</section>
