<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var string $modulo */
/** @var array $listado */
defined('ALMANICA') || exit; ?>
<?php require __DIR__ . '/navegacion.php'; ?>
<section class="card-box">
    <div class="section-heading">
        <div>
            <h2>Historial de recepciones</h2><p class="muted">Entregas recibidas, facturas y responsables.</p>
        </div>
    </div>
    <form class="filterbar" method="get" data-live-search="listado">
        <input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
        <label class="search-field">
            <span class="sr-only">Buscar en el historial</span><input type="search" name="q" placeholder="Recepción, orden, proveedor, factura o responsable…" value="<?= escapar($_GET['q'] ?? '') ?>">
        </label>
        <noscript><button class="button secondary">Buscar</button></noscript><a class="button green-button" href="<?= escapar(ruta('historial',['q'=>$_GET['q'] ?? '','csv'=>1])) ?>">Descargar CSV (Excel)</a>
    </form>
    <div data-live-region="listado">
        <p class="muted"><?= numero($listado['total']) ?> recepciones encontradas</p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>
                            Recepción
                        </th>
                        <th>
                            Orden
                        </th>
                        <th>
                            Fecha
                        </th>
                        <th>
                            Proveedor
                        </th>
                        <th>
                            Factura
                        </th>
                        <th>
                            Unidades
                        </th>
                        <th>
                            Total recibido
                        </th>
                        <th>
                            Responsable
                        </th>
                        <th>
                            Detalle
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listado['filas'] as $r): ?>
                    <tr>
                        <td>
                            <?php if ($r['Reversion']): ?><span class="badge red">Revertida</span><br><?php endif; ?>RC-<?= str_pad($r['Id_Recepcion'],5,'0',STR_PAD_LEFT) ?>
                        </td>
                        <td>
                            OC-<?= str_pad($r['Id_OrdenCompra'],5,'0',STR_PAD_LEFT) ?>
                        </td>
                        <td>
                            <?= escapar(fechaCorta($r['Fecha'])) ?>
                        </td>
                        <td>
                            <?= escapar($r['Proveedor']) ?>
                        </td>
                        <td>
                            <?= escapar($r['Factura'] ?? '—') ?>
                        </td>
                        <td>
                            <?= numero($r['Unidades']) ?>
                        </td>
                        <td>
                            <?= dinero($r['Total_Recibido']) ?>
                        </td>
                        <td>
                            <?= escapar($r['Empleado']) ?>
                        </td>
                        <td>
                            <a class="button mini secondary" href="<?= escapar(ruta('compras',['id'=>$r['Id_OrdenCompra']])) ?>">Ver orden</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$listado['filas']): ?>
                    <tr>
                        <td colspan="9" class="empty">
                            Las entregas aparecerán aquí después de guardar una recepción.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?>
    </div>
</section>
