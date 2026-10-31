<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var string $modulo */
/** @var string $accion */
defined('ALMANICA') || exit; ?>
<nav class="purchase-nav card-box" aria-label="Secciones de compras">
    <div>
        <h2>Gestión de compras</h2>
    </div>
    <div class="row-actions">
        <?php if (puedeEditar('compras')): ?><a class="button" href="<?= escapar(ruta('compras',['accion'=>'nuevo'])) ?>">＋ Nueva orden</a><?php endif; ?>
        <?php foreach (['compras'=>'Gestión de órdenes','historial'=>'Historial de recepciones'] as $destino=>$texto): if (!puedeVer($destino)) continue; ?>
        <a class="button <?= $modulo===$destino && $accion!=='nuevo'?'':'secondary' ?>" href="<?= escapar(ruta($destino)) ?>"><?= escapar($texto) ?></a>
        <?php endforeach; ?>
    </div>
</nav>
