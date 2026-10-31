<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var array $orden */
/** @var int $idOrden */
/** @var array $lineas */
/** @var array $recepciones */
defined('ALMANICA') || exit; ?>
<article class="purchase-document">
    <header class="doc-heading">
        <div class="doc-brand">
            <img src="assets/img/almanica-emblema.png" alt="Logo AlmaNica">
            <div>
                <h1>AlmaNica</h1><small>SISTEMA DE VENTAS E INVENTARIO</small>
            </div>
        </div>
        <div>
            <h2>Orden de compra</h2><strong>OC-<?= str_pad($idOrden,5,'0',STR_PAD_LEFT) ?></strong><p><?= escapar(fechaCorta($orden['Fecha'])) ?></p>
        </div>
    </header>
    <div class="doc-total">
        <small>TOTAL DE LA ORDEN</small><strong><?= dinero($orden['Total']) ?></strong>
    </div>
    <section class="doc-memo">
        <b>ESTADO</b><p><?= escapar($orden['Estado']==='Completada'?'Recibida':$orden['Estado']) ?></p>
    </section>
    <table class="doc-meta">
        <thead>
            <tr>
                <th>
                    Comprador
                </th>
                <th>
                    Proveedor
                </th>
                <th>
                    Fecha
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= escapar($orden['Empleado']) ?>
                </td>
                <td>
                    <?= escapar($orden['Proveedor']) ?>
                </td>
                <td>
                    <?= escapar(fechaCorta($orden['Fecha'])) ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="doc-lines">
        <thead>
            <tr>
                <th>
                    Código
                </th>
                <th>
                    Artículo
                </th>
                <th>
                    Cantidad
                </th>
                <th>
                    Precio
                </th>
                <th>
                    Importe
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lineas as $l): ?>
            <tr>
                <td>
                    <?= escapar($l['Codigo']) ?>
                </td>
                <td>
                    <?= escapar($l['Nombre']) ?>
                </td>
                <td>
                    <?= numero($l['Cantidad']) ?> UND
                </td>
                <td>
                    <?= dinero($l['Precio_Costo']) ?>
                </td>
                <td>
                    <?= dinero($l['Subtotal']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="doc-totals">
        <p><span>Subtotal</span><b><?= dinero($orden['Total']) ?></b></p><p><span>Total</span><b><?= dinero($orden['Total']) ?></b></p>
    </div>
    <?php $vigentes=array_values(array_filter($recepciones,fn($r)=>!$r['Reversion'])); ?>
    <?php if ($vigentes): ?>
    <footer class="doc-receipts">
        <?php foreach ($vigentes as $r): ?><p>Recibido por: <?= escapar($r['Empleado']) ?><br>Factura proveedor: <?= escapar($r['Factura'] ?: '—') ?></p><?php endforeach; ?>
    </footer>
    <?php endif; ?>
</article>
