<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var string $modulo */
/** @var string $estadoFiltro */
/** @var array $listado */
/** @var array $resumenCompras */
defined('ALMANICA') || exit; ?>
<?php require __DIR__ . '/navegacion.php'; ?>
<div class="purchase-management">
    <section class="card-box">
        <div class="section-heading">
            <div>
                <h2><?= $modulo==='recepcion'?'Órdenes por recibir':'Órdenes de compra' ?></h2><p class="muted"><?= $modulo==='recepcion'?'Selecciona una orden para registrar los productos que llegaron.':'Prepara tus pedidos y sigue cada entrega.' ?></p>
            </div>
        </div>
        <form class="filterbar" method="get" data-live-search="listado">
            <input type="hidden" name="modulo" value="<?= escapar($modulo) ?>"><?php if ($modulo==='compras'): ?><select name="estado" aria-label="Estado de la orden"><?php foreach ([''=>'Todos los estados','Pendiente'=>'Pendientes','Completada'=>'Recibidas','Cancelada'=>'Canceladas','Cerrada'=>'Cerradas'] as $estado=>$texto): ?><option value="<?= escapar($estado) ?>" <?= $estadoFiltro===$estado?'selected':'' ?>><?= escapar($texto) ?></option><?php endforeach; ?></select><?php else: ?><input type="hidden" name="estado" value="Pendiente"><?php endif; ?>
            <label class="search-field">
                <span class="sr-only">Buscar orden</span><input type="search" name="q" placeholder="Buscar por número de orden o proveedor…" value="<?= escapar($_GET['q'] ?? '') ?>">
            </label>
            <noscript><button class="button secondary">Buscar</button></noscript><a class="button secondary" href="<?= escapar(ruta($modulo,['estado'=>$estadoFiltro])) ?>">Limpiar</a>
        </form>
        <div data-live-region="listado">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>
                                N.º orden
                            </th>
                            <th>
                                Proveedor
                            </th>
                            <th>
                                Fecha
                            </th>
                            <th>
                                Estado
                            </th>
                            <th>
                                Total
                            </th>
                            <th>
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listado['filas'] as $fila): ?>
                        <tr>
                            <td>
                                <a class="order-number" href="<?= escapar(ruta('compras',['id'=>$fila['Id_OrdenCompra']])) ?>">OC-<?= str_pad($fila['Id_OrdenCompra'],5,'0',STR_PAD_LEFT) ?></a>
                            </td>
                            <td>
                                <?= escapar($fila['Proveedor']) ?>
                            </td>
                            <td>
                                <?= escapar(date('d/m/Y',strtotime($fila['Fecha']))) ?>
                            </td>
                            <td>
                                <span class="badge <?= $fila['Estado']==='Cancelada'?'red':($fila['Estado']==='Completada'?'green':'amber') ?>"><?= $fila['Estado']==='Completada'?'Recibida':($fila['Estado']==='Pendiente' && $fila['Recepciones']?'Parcial':$fila['Estado']) ?></span>
                            </td>
                            <td>
                                <?= dinero($fila['Total']) ?>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="button mini secondary" href="<?= escapar(ruta('compras',['id'=>$fila['Id_OrdenCompra']])) ?>">Ver detalle</a>
                                    <?php if ($fila['Estado']==='Pendiente'): ?><a class="button mini" href="<?= escapar(ruta('recepcion',['id'=>$fila['Id_OrdenCompra']])) ?>">Recibir</a><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$listado['filas']): ?>
                        <tr>
                            <td colspan="6" class="empty">
                                <strong>No hay órdenes en esta vista.</strong><p>Crea una orden para comenzar a organizar tus compras.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?>
        </div>
    </section>
    <aside class="card-box purchase-overview">
        <h2>Resumen de órdenes</h2>
        <div class="purchase-stats purchase-stats-side">
            <?php foreach (['Pendiente'=>['Por completar','amber'],'Completada'=>['Recibidas','green'],'Cancelada'=>['Canceladas','red'],'Cerrada'=>['Cerradas','red']] as $estadoResumen=>[$etiqueta,$color]): ?>
            <a class="card-box" href="<?= escapar(ruta('compras',['estado'=>$estadoResumen])) ?>"><span class="badge <?= $color ?>"><?= escapar($etiqueta) ?></span><strong><?= numero($resumenCompras[$estadoResumen] ?? 0) ?></strong></a>
            <?php endforeach; ?>
        </div>
        <hr><h3>Información</h3><p>Abre una orden para consultar, imprimir o editar su detalle.</p><p>Recibir registra las unidades entregadas y actualiza el inventario.</p><p>El administrador puede cerrar una orden pendiente o revertir una recepción desde el detalle.</p>
    </aside>
</div>
