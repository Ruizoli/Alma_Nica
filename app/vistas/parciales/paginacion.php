<div class="pagination-bar">
<span><?= numero($listado['total']) ?> registros · Página <?= $listado['pagina'] ?> de <?= $listado['paginas'] ?>
</span>
<div>
<?php if ($listado['pagina']>1): ?>
<a class="button mini secondary" href="<?= escapar(ruta($modulo,array_merge($_GET,['pagina'=>$listado['pagina']-1]))) ?>">← Anterior</a>
<?php endif; ?>
<?php if ($listado['pagina']<$listado['paginas']): ?>
<a class="button mini secondary" href="<?= escapar(ruta($modulo,array_merge($_GET,['pagina'=>$listado['pagina']+1]))) ?>">Siguiente →</a>
<?php endif; ?>
</div>
</div>
