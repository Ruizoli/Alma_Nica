<?php defined('ALMANICA') || exit; ?>
<?php if (!empty($datos['error'])): ?><div class="notice danger"><?= escapar($datos['error']) ?></div><?php else: $s=$datos['estadisticas']; ?>
<section class="commercial-hero">
    <div><h1>¡Bienvenido, <?= escapar($_SESSION['empleado']['nombre']) ?>!</h1><p>SISTEMA DE VENTAS E INVENTARIO · ALMANICA</p><span>“El Corazón de Nicaragua”</span></div>
    <div class="hero-brand"><?php require __DIR__ . "/parciales/marca.php"; ?></div>
</section>
<section class="metrics">
    <?php $metricas=[['ventas','Ventas hoy',dinero($datos['ventaHoy']['importe']),numero($datos['ventaHoy']['cantidad']).' operaciones'],['productos','Productos',numero($s['productos']),numero($s['unidades']).' unidades'],['clientes','Clientes',numero($s['clientes']),'Registrados'],['proveedores','Proveedores',numero($s['proveedores']),'Registrados']]; ?>
    <?php foreach ($metricas as [$icon,$name,$value,$caption]): ?><article class="card-box metric"><span class="metric-icon"><?= icono($icon) ?></span><div><p><?= $name ?></p><strong><?= $value ?></strong><small><?= $caption ?></small></div></article><?php endforeach; ?>
</section>
<div class="dashboard-three">
    <section class="card-box"><h2>Ventas en los últimos 7 días</h2><p class="muted">Importes en córdobas</p>
        <?php $max=max(1,...array_column($datos['serie'],'total')); ?>
        <div class="chart"><?php foreach ($datos['serie'] as $dia): ?><div class="chart-column"><small><?= importe($dia['total']) ?></small><div class="chart-track"><span style="height:<?= max(0,min(100,$dia['total']/$max*100)) ?>%"></span></div><small><?= $dia['fecha'] ?></small></div><?php endforeach; ?></div>
    </section>
    <section class="card-box"><h2>Productos más vendidos</h2><p class="muted">Unidades · últimos 30 días</p>
        <?php $totalTop=array_sum(array_column($datos['populares'],'Unidades')); $colores=['#247fe5','#ff9c35','#33b47e','#10aabd']; $segmentos=[]; $acumulado=0; foreach ($datos['populares'] as $i=>$p) { $inicio=$acumulado; $acumulado+=(float)$p['Unidades']/max(1,$totalTop)*100; $segmentos[]=$colores[$i].' '.$inicio.'% '.$acumulado.'%'; } ?>
        <div class="donut-layout"><div class="donut" style="background:<?= $segmentos?'conic-gradient('.implode(',',$segmentos).')':'#e7eef5' ?>" role="img" aria-label="Distribución de los cuatro productos más vendidos"><div><strong><?= numero($totalTop) ?></strong><small>unidades</small></div></div>
            <ul class="donut-legend"><?php foreach ($datos['populares'] as $i=>$p): ?><li><span style="background:<?= $colores[$i] ?>"></span><b><?= escapar($p['Nombre']) ?></b><small><?= round($p['Unidades']/max(1,$totalTop)*100) ?>%</small></li><?php endforeach; ?><?php if (!$datos['populares']): ?><li class="muted">Aún no hay ventas en este período.</li><?php endif; ?></ul>
        </div>
    </section>
    <section class="card-box"><div class="section-heading"><h2>Stock bajo</h2><a class="button mini danger-button" href="<?= escapar(ruta('productos',['bajo'=>1])) ?>">Ver todos</a></div>
        <div class="table-wrap"><table class="stock-table"><thead><tr><th>Producto</th><th>Stock</th><th>Mínimo</th></tr></thead><tbody><?php foreach ($datos['stockBajo'] as $p): ?><tr><td><?= escapar($p['Nombre']) ?></td><td class="stock-warning"><?= numero($p['Stock_Actual']) ?></td><td><?= numero($p['Stock_Minimo']) ?></td></tr><?php endforeach; ?><?php if (!$datos['stockBajo']): ?><tr><td colspan="3" class="empty"><?= $s['productos']?'No hay productos por debajo del mínimo.':'Agrega productos para ver tus existencias.' ?></td></tr><?php endif; ?></tbody></table></div>
    </section>
</div>
<section class="quick-actions" aria-label="Accesos rápidos">
    <?php $accesos=[['ventas','Nueva venta',[], 'mint'],['compras','Crear orden de compra',['accion'=>'nuevo'],'sky'],['recepcion','Recepción de compra',[],'yellow'],['productos','Nuevo producto',['accion'=>'nuevo'],'purple'],['clientes','Nuevo cliente',['accion'=>'nuevo'],'cyan'],['empleados','Nuevo empleado',['accion'=>'nuevo'],'pink']]; foreach ($accesos as [$mod,$label,$params,$color]): if (!puedeVer($mod) || !puedeEditar($mod)) continue; ?>
    <a class="quick-action <?= $color ?>" href="<?= escapar(ruta($mod,$params)) ?>"><?= icono($mod) ?><span><?= $label ?></span></a>
    <?php endforeach; ?>
</section>
<section class="card-box mt"><div class="section-heading"><h2>Últimas ventas</h2><a class="text-link" href="<?= escapar(ruta('ventas')) ?>">Ver historial →</a></div><div class="table-wrap"><table><thead><tr><th>Venta</th><th>Cliente</th><th>Fecha</th><th>Pago</th><th>Total</th></tr></thead><tbody><?php foreach ($datos['ventas'] as $v): ?><tr><td>#<?= (int)$v['Id_Venta'] ?></td><td><?= escapar($v['Cliente']) ?></td><td><?= escapar(fechaCorta($v['Fecha'])) ?></td><td><?= escapar($v['Metodo_Pago']) ?></td><td><?= dinero($v['Total']) ?></td></tr><?php endforeach; ?><?php if (!$datos['ventas']): ?><tr><td colspan="5" class="empty">Las ventas registradas aparecerán aquí.</td></tr><?php endif; ?></tbody></table></div></section>
<?php endif; ?>
