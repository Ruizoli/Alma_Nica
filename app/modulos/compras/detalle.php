<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var array $orden */
/** @var int $idOrden */
/** @var array $lineas */
/** @var array $recepciones */
defined('ALMANICA') || exit; ?>
<?php require __DIR__ . '/navegacion.php'; ?>
<div class="purchase-screen">
    <section class="card-box">
        <div class="section-heading">
            <div>
                <h2>Detalle de orden de compra</h2><p class="muted">OC-<?= str_pad($idOrden,5,'0',STR_PAD_LEFT) ?></p>
            </div>
            <span class="badge <?= $orden['Estado']==='Cancelada'?'red':($orden['Estado']==='Completada'?'green':'amber') ?>"><?= $orden['Estado']==='Completada'?'Recibida':($recepciones && $orden['Estado']==='Pendiente'?'Parcial':$orden['Estado']) ?></span>
        </div>
        <dl class="order-meta">
            <div>
                <dt>
                    Proveedor
                </dt>
                <dd>
                    <?= escapar($orden['Proveedor']) ?>
                </dd>
            </div>
            <div>
                <dt>
                    Fecha
                </dt>
                <dd>
                    <?= escapar(fechaCorta($orden['Fecha'])) ?>
                </dd>
            </div>
            <div>
                <dt>
                    Creada por
                </dt>
                <dd>
                    <?= escapar($orden['Empleado']) ?>
                </dd>
            </div>
            <div>
                <dt>
                    Total
                </dt>
                <dd>
                    <?= dinero($orden['Total']) ?>
                </dd>
            </div>
        </dl>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>
                            Producto
                        </th>
                        <th>
                            Pedido
                        </th>
                        <th>
                            Recibido
                        </th>
                        <th>
                            Pendiente
                        </th>
                        <th>
                            Costo
                        </th>
                        <th>
                            Subtotal
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lineas as $linea): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img class="product-thumb" src="<?= escapar(fotoProducto($linea['Imagen'])) ?>" alt="">
                                <div>
                                    <?= escapar($linea['Nombre']) ?><small><?= escapar($linea['Codigo']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?= numero($linea['Cantidad']) ?>
                        </td>
                        <td>
                            <?= $orden['Estado']==='Completada' && !$recepciones?'—':numero($linea['Recibida']) ?>
                        </td>
                        <td>
                            <?= $orden['Estado']==='Completada'?0:numero($linea['Cantidad']-$linea['Recibida']) ?>
                        </td>
                        <td>
                            <?= dinero($linea['Precio_Costo']) ?>
                        </td>
                        <td>
                            <?= dinero($linea['Subtotal']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($orden['Estado']==='Completada' && !$recepciones): ?><p class="muted mt">Esta orden se completó antes de habilitar el registro detallado de recepciones.</p><?php endif; ?>
        <div class="form-actions mt">
            <a class="button secondary" href="<?= escapar(ruta('compras')) ?>">Volver a órdenes</a><button type="button" class="button secondary" data-print>Imprimir</button>
            <?php if ($orden['Estado']==='Pendiente'): ?>
            <?php if (!$recepciones): ?>
            <form method="post" data-confirm="¿Cancelar esta orden de compra?">
                <?php campoCsrf(); ?><input type="hidden" name="accion_compra" value="cancelar"><input type="hidden" name="id_orden" value="<?= (int)$idOrden ?>"><button class="button danger-button">Cancelar orden</button>
            </form>
            <a class="button secondary" href="<?= escapar(ruta('compras',['accion'=>'editar','id'=>$idOrden])) ?>">Editar orden</a><?php endif; ?>
            <a class="button green-button" href="<?= escapar(ruta('recepcion',['id'=>$idOrden])) ?>">Registrar recepción</a>
            <?php endif; ?>
        </div>
        <?php if (esAdmin() && $orden['Estado']==='Pendiente'): ?>
        <form method="post" class="mt" data-confirm="¿Cerrar la orden? No se aceptarán más entregas; lo recibido se conserva.">
            <?php campoCsrf(); ?><input type="hidden" name="accion_compra" value="cerrar"><input type="hidden" name="id_orden" value="<?= (int)$idOrden ?>">
            <label class="field">
                <span>Motivo del cierre</span><input name="motivo" required maxlength="500" placeholder="Ej. El proveedor no entregará los productos restantes">
            </label>
            <button class="button danger-button mt">Cerrar orden</button>
        </form>
        <?php endif; ?>
    </section>
    <section class="card-box mt">
        <div class="section-heading">
            <h2>Recepciones de esta orden</h2>
        </div>
        <?php if (!$recepciones): ?><p class="muted">Todavía no hay recepciones registradas en este módulo.</p><?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>
                            Recepción
                        </th>
                        <th>
                            Fecha
                        </th>
                        <th>
                            Factura
                        </th>
                        <th>
                            Unidades
                        </th>
                        <th>
                            Registrada por
                        </th>
                        <th>
                            Observaciones / acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recepciones as $r): ?>
                    <tr>
                        <td>
                            RC-<?= str_pad($r['Id_Recepcion'],5,'0',STR_PAD_LEFT) ?>
                        </td>
                        <td>
                            <?= escapar(fechaCorta($r['Fecha'])) ?>
                        </td>
                        <td>
                            <?= escapar($r['Factura'] ?? '—') ?>
                        </td>
                        <td>
                            <?= numero($r['Unidades']) ?>
                        </td>
                        <td>
                            <?= escapar($r['Empleado']) ?>
                        </td>
                        <td class="notes-cell">
                            <?php if ($r['Reversion']): ?><span class="badge red">Revertida</span><p><?= escapar($r['Reversion']) ?></p>
                            <?php elseif (esAdmin() && in_array($orden['Estado'],['Pendiente','Completada'],true)): ?>
                            <form method="post" data-confirm="¿Revertir esta recepción y descontar sus unidades del inventario?">
                                <?php campoCsrf(); ?><input type="hidden" name="accion_compra" value="revertir"><input type="hidden" name="id_orden" value="<?= (int)$idOrden ?>"><input type="hidden" name="id_recepcion" value="<?= (int)$r['Id_Recepcion'] ?>">
                                <label class="field">
                                    <span>Motivo de reversión</span><input name="motivo" required maxlength="500">
                                </label>
                                <button class="button mini danger-button">Revertir recepción</button>
                            </form>
                            <?php endif; ?>
                            <?= escapar($r['Observaciones'] ?? '—') ?>
                            <details class="receipt-items">
                                <summary>
                                    Ver productos recibidos
                                </summary>
                                <ul>
                                    <?php foreach ($r['Detalles'] as $item): ?>
                                    <li>
                                        <?= escapar($item['Nombre']) ?>: <?= numero($item['Cantidad']) ?> uds.
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/impresion.php'; ?>
