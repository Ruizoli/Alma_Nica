<?php defined('ALMANICA') || exit; ?>
<?php if (!empty($datos['error'])): ?>
    <div class="alert alert-danger shadow-sm rounded-3" role="alert"><?= escapar($datos['error']) ?></div>
<?php else: $s=$datos['estadisticas']; ?>

<!-- Banner Superior Original -->
<section class="commercial-hero">
    <div><h1>¡Bienvenido, <?= escapar($_SESSION['empleado']['nombre']) ?>!</h1><p>SISTEMA DE VENTAS E INVENTARIO · ALMANICA</p><span>“El Corazón de Nicaragua”</span></div>
    <div class="hero-brand"><?php require __DIR__ . '/../../vistas/parciales/marca.php'; ?></div>
</section>

<!-- Tarjetas de Métricas (KPIs) con Bootstrap 5 -->
<div class="row g-3 mb-4">
    <?php 
    $metricas=[
        ['ventas','Ventas hoy',dinero($datos['ventaHoy']['importe']),numero($datos['ventaHoy']['cantidad']).' operaciones', 'success', 'bg-success-subtle text-success'],
        ['productos','Productos',numero($s['productos']),numero($s['unidades']).' unidades', 'primary', 'bg-primary-subtle text-primary'],
        ['clientes','Clientes',numero($s['clientes']),'Registrados', 'warning', 'bg-warning-subtle text-warning'],
        ['proveedores','Proveedores',numero($s['proveedores']),'Registrados', 'info', 'bg-info-subtle text-info']
    ]; 
    ?>
    <?php foreach ($metricas as [$icon,$name,$value,$caption,$color,$bgClass]): ?>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 <?= $bgClass ?> p-3 rounded-3 me-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <?= icono($icon) ?>
                </div>
                <div class="flex-grow-1">
                    <span class="text-muted fw-semibold small d-block"><?= $name ?></span>
                    <h4 class="fw-bold mb-0 text-dark"><?= $value ?></h4>
                    <small class="text-muted"><?= $caption ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Sección de Gráficos y Stock Bajo (Grid de 3 columnas) -->
<div class="row g-4 mb-4">
    <!-- Ventas últimos 7 días -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-1">Ventas en los últimos 7 días</h5>
            <p class="text-muted small mb-3">Importes en córdobas</p>
            <?php $max=max(1,...array_column($datos['serie'],'total')); ?>
            <div class="chart d-flex justify-content-between align-items-end pt-3" style="height: 180px;">
                <?php foreach ($datos['serie'] as $dia): ?>
                <div class="chart-column text-center flex-fill px-1">
                    <small class="d-block text-muted fs-7 mb-1" style="font-size: 0.7rem;"><?= importe($dia['total']) ?></small>
                    <div class="chart-track bg-light rounded-pill position-relative mx-auto" style="height: 100px; width: 12px;">
                        <span class="position-absolute bottom-0 start-0 end-0 bg-primary rounded-pill" style="height:<?= max(0,min(100,$dia['total']/$max*100)) ?>%"></span>
                    </div>
                    <small class="d-block text-muted mt-2" style="font-size: 0.75rem;"><?= $dia['fecha'] ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Productos más vendidos -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-1">Productos más vendidos</h5>
            <p class="text-muted small mb-3">Unidades · últimos 30 días</p>
            <?php 
            $totalTop=array_sum(array_column($datos['populares'],'Unidades')); 
            $colores=['#0d6efd','#ffc107','#198754','#0dcaf0']; 
            $segmentos=[]; 
            $acumulado=0; 
            foreach ($datos['populares'] as $i=>$p) { 
                $inicio=$acumulado; 
                $acumulado+=(float)$p['Unidades']/max(1,$totalTop)*100; 
                $segmentos[]=$colores[$i].' '.$inicio.'% '.$acumulado.'%'; 
            } 
            ?>
            <div class="donut-layout d-flex flex-column align-items-center justify-content-center h-100">
                <div class="donut rounded-circle d-flex align-items-center justify-content-center text-center shadow-sm mb-3" style="width: 130px; height: 130px; background:<?= $segmentos?'conic-gradient('.implode(',',$segmentos).')':'#e7eef5' ?>" role="img" aria-label="Distribución de los cuatro productos más vendidos">
                    <div class="bg-white rounded-circle d-flex flex-column align-items-center justify-content-center" style="width: 90px; height: 90px;">
                        <strong class="fs-4 fw-bold text-dark"><?= numero($totalTop) ?></strong>
                        <small class="text-muted fs-7">unidades</small>
                    </div>
                </div>
                <ul class="donut-legend list-unstyled w-100 small mb-0">
                    <?php foreach ($datos['populares'] as $i=>$p): ?>
                    <li class="d-flex align-items-center justify-content-between mb-1">
                        <span class="d-flex align-items-center"><span class="rounded-circle d-inline-block me-2" style="width: 10px; height: 10px; background:<?= $colores[$i] ?>"></span><b><?= escapar($p['Nombre']) ?></b></span>
                        <span class="text-muted"><?= round($p['Unidades']/max(1,$totalTop)*100) ?>%</span>
                    </li>
                    <?php endforeach; ?>
                    <?php if (!$datos['populares']): ?>
                    <li class="text-muted text-center">Aún no hay ventas en este período.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stock bajo -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Stock bajo</h5>
                <a class="btn btn-sm btn-outline-danger rounded-pill px-3" href="<?= escapar(ruta('productos',['bajo'=>1])) ?>">Ver todos</a>
            </div>
            <div class="table-responsive flex-grow-1">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos['stockBajo'] as $p): ?>
                        <tr>
                            <td class="fw-semibold"><?= escapar($p['Nombre']) ?></td>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= numero($p['Stock_Actual']) ?></span></td>
                            <td class="text-muted"><?= numero($p['Stock_Minimo']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$datos['stockBajo']): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4"><?= $s['productos']?'No hay productos por debajo del mínimo.':'Agrega productos para ver tus existencias.' ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <h5 class="fw-bold mb-3">Accesos rápidos</h5>
    <div class="row g-3" aria-label="Accesos rápidos">
        <?php 
        $accesos=[
            ['ventas','Nueva venta',[], 'btn-success', 'bi-cart-plus'],
            ['compras','Crear orden de compra',['accion'=>'nuevo'],'btn-primary', 'bi-file-earmark-text'],
            ['recepcion','Recepción de compra',[],'btn-warning text-dark', 'bi-truck'],
            ['productos','Nuevo producto',['accion'=>'nuevo'],'btn-info text-white', 'bi-box-seam'],
            ['clientes','Nuevo cliente',['accion'=>'nuevo'],'btn-secondary', 'bi-person-plus'],
            ['empleados','Nuevo empleado',['accion'=>'nuevo'],'btn-danger', 'bi-person-badge']
        ]; 
        foreach ($accesos as [$mod,$label,$params,$btnClass,$iconBootstrap]): if (!puedeVer($mod) || !puedeEditar($mod)) continue; ?>
        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
            <a class="btn <?= $btnClass ?> w-100 py-3 rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 shadow-sm text-decoration-none fw-semibold" href="<?= escapar(ruta($mod,$params)) ?>">
                <i class="bi <?= $iconBootstrap ?> fs-3"></i>
                <span class="small"><?= $label ?></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Últimas Ventas -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Últimas ventas</h5>
        <a class="text-decoration-none fw-semibold text-primary" href="<?= escapar(ruta('ventas')) ?>">Ver historial →</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Venta</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Pago</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['ventas'] as $v): ?>
                <tr>
                    <td class="fw-bold text-primary">#<?= (int)$v['Id_Venta'] ?></td>
                    <td><?= escapar($v['Cliente']) ?></td>
                    <td class="text-muted"><?= escapar(fechaCorta($v['Fecha'])) ?></td>
                    <td><span class="badge bg-light text-dark border px-2 py-1"><?= escapar($v['Metodo_Pago']) ?></span></td>
                    <td class="text-end fw-bold text-success"><?= dinero($v['Total']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$datos['ventas']): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Las ventas registradas aparecerán aquí.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>