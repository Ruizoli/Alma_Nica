<?php defined('ALMANICA') || exit; ?>
<section class="card-box">
    <div class="section-heading">
        <div><h2><?= $modulo==='recepcion'?'Órdenes por recibir':'Órdenes de compra' ?></h2><p class="muted"><?= $modulo==='recepcion'?'Selecciona una orden para registrar los productos que llegaron.':'Prepara tus pedidos y sigue cada entrega.' ?></p></div>
        <a class="button" href="<?= escapar(ruta('compras',['accion'=>'nuevo'])) ?>">＋ Nueva orden</a>
    </div>
    <?php if ($modulo==='compras'): ?>
    <div class="status-tabs">
        <?php foreach ([''=>'Todas','Pendiente'=>'Pendientes','Completada'=>'Recibidas','Cancelada'=>'Canceladas'] as $estado=>$texto): ?>
        <a class="<?= $estadoFiltro===$estado?'active':'' ?>" href="<?= escapar(ruta('compras',['estado'=>$estado])) ?>"><?= $texto ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form class="filterbar" method="get" data-live-search="listado">
        <input type="hidden" name="modulo" value="<?= escapar($modulo) ?>"><input type="hidden" name="estado" value="<?= escapar($estadoFiltro) ?>">
        <label class="search-field"><span class="sr-only">Buscar orden</span><input type="search" name="q" placeholder="Buscar por número de orden o proveedor…" value="<?= escapar($_GET['q'] ?? '') ?>"></label>
        <button class="button secondary">Buscar</button>
    </form>
    <div data-live-region="listado"><div class="table-wrap"><table><thead><tr><th>N.º orden</th><th>Proveedor</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Acciones</th></tr></thead><tbody>
    <?php foreach ($listado['filas'] as $fila): ?>
        <tr><td><a class="order-number" href="<?= escapar(ruta('compras',['id'=>$fila['Id_OrdenCompra']])) ?>">OC-<?= str_pad($fila['Id_OrdenCompra'],5,'0',STR_PAD_LEFT) ?></a></td>
        <td><?= escapar($fila['Proveedor']) ?></td><td><?= escapar(date('d/m/Y',strtotime($fila['Fecha']))) ?></td>
        <td><span class="badge <?= $fila['Estado']==='Cancelada'?'red':($fila['Estado']==='Completada'?'green':'amber') ?>"><?= $fila['Estado']==='Completada'?'Recibida':($fila['Estado']==='Pendiente' && $fila['Recepciones']?'Parcial':$fila['Estado']) ?></span></td>
        <td><?= dinero($fila['Total']) ?></td><td><div class="row-actions"><a class="button mini secondary" href="<?= escapar(ruta('compras',['id'=>$fila['Id_OrdenCompra']])) ?>">Ver detalle</a>
        <?php if ($fila['Estado']==='Pendiente'): ?><a class="button mini" href="<?= escapar(ruta('recepcion',['id'=>$fila['Id_OrdenCompra']])) ?>">Recibir</a><?php endif; ?></div></td></tr>
    <?php endforeach; ?>
    <?php if (!$listado['filas']): ?><tr><td colspan="6" class="empty"><strong>No hay órdenes en esta vista.</strong><p>Crea una orden para comenzar a organizar tus compras.</p></td></tr><?php endif; ?>
    </tbody></table></div>
    <?php require __DIR__ . '/../../vistas/parciales/paginacion.php'; ?></div>
</section>
