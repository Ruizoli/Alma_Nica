<?php defined('ALMANICA') || exit; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escapar($titulo) ?> · AlmaNica</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Tus hojas de estilo existentes -->
    <link href="assets/css/estilos.css" rel="stylesheet">
    <link href="assets/css/sistema.css" rel="stylesheet">
    <link href="assets/css/comercial.css?v=3" rel="stylesheet">
    <link href="assets/css/marca.css?v=1" rel="stylesheet">
    <link href="assets/css/busqueda.css?v=2" rel="stylesheet">
    <link href="assets/css/selectores.css?v=1" rel="stylesheet">

<?php if (in_array($modulo,['compras','recepcion','historial'],true)): ?><link href="assets/css/compras.css?v=4" rel="stylesheet"><?php endif; ?>
<?php if (in_array($modulo,['categorias','clientes','proveedores'],true)): ?><link href="assets/css/modulos-catalogo.css?v=1" rel="stylesheet"><?php endif; ?>
<script src="assets/js/compras.js?v=1" defer></script>

    <?php if (in_array($modulo,['compras','recepcion','historial'],true)): ?>
        <link href="assets/css/compras.css?v=4" rel="stylesheet">
    <?php endif; ?>

    <!-- Scripts con defer -->
    <script src="assets/js/sistema.js?v=6" defer></script>
    <script src="assets/js/busqueda.js?v=1" defer></script>
    <script src="assets/js/selectores.js?v=2" defer></script>
    <script src="assets/js/compras.js?v=1" defer></script>

</head>
<body class="sistema bg-light">

<aside class="sidebar shadow-sm" id="menu-principal">
    <a class="brand p-3 text-decoration-none" href="panel.php"><?php require __DIR__ . '/marca.php'; ?></a>
    <nav aria-label="Navegación principal" class="px-2">
        <?php $menu=['dashboard'=>'Dashboard','ventas'=>'Ventas','productos'=>'Productos','categorias'=>'Categorías','clientes'=>'Clientes','proveedores'=>'Proveedores','compras'=>'Compras','empleados'=>'Empleados','usuarios'=>'Usuarios','reportes'=>'Reportes','configuracion'=>'Configuración']; ?>
        <?php foreach ($menu as $key=>$label): if (!puedeVer($key)) continue; ?>
        <?php if ($key==='compras'): ?>
            <details class="nav-group mb-1" <?= in_array($modulo,['compras','recepcion','historial'],true)?'open':'' ?>>
                <summary class="py-2 px-3 rounded"><?= icono('ventas') ?><span>Compras</span><span class="chevron">⌄</span></summary>
                <?php foreach (['compras'=>'Órdenes de compra','recepcion'=>'Recepción de compras','historial'=>'Historial de compras'] as $sub=>$texto): ?>
                <a href="<?= escapar(ruta($sub)) ?>" class="py-2 px-3 rounded <?= $modulo===$sub?'selected':'' ?>" <?= $modulo===$sub?'aria-current="page"':'' ?>><?= icono($sub) ?><span><?= $texto ?></span></a>
                <?php endforeach; ?>
            </details>
        <?php else: ?>
            <a href="<?= escapar(ruta($key)) ?>" class="py-2 px-3 rounded mb-1 <?= $modulo===$key?'selected':'' ?>" <?= $modulo===$key?'aria-current="page"':'' ?>><?= icono($key) ?><span><?= $label ?></span></a>
        <?php endif; endforeach; ?>
    </nav>
    <div class="sidebar-bottom p-3">
        <form method="post" action="salir.php">
            <?php campoCsrf(); ?>
            <button class="logout btn btn-outline-danger w-150 btn-sm rounded-pill" type="submit"><i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión</button>
        </form>
    </div>
</aside>

<div class="workspace">
    <header class="topbar navbar bg-white border-bottom px-4 shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <button class="icon-button menu-toggle btn btn-light border-0" type="button" aria-label="Mostrar u ocultar menú" aria-controls="menu-principal" aria-expanded="true" data-toggle-menu><?= icono('menu') ?></button>
            <form class="global-search input-group input-group-sm" method="get" data-global-search style="max-width: 350px;">
                <input type="hidden" name="modulo" value="buscar">
                <span class="input-group-text bg-light border-end-0"><?= icono('buscar') ?></span>
                <input name="q" type="search" class="form-control bg-light border-start-0 ps-0" aria-label="Buscar en el sistema" placeholder="Buscar módulos..." value="<?= $modulo==='buscar'?escapar($_GET['q'] ?? ''):'' ?>">
                <button type="submit" class="sr-only">Buscar</button>
            </form>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <a class="notification-link text-secondary position-relative p-2" href="<?= escapar(ruta('productos',['bajo'=>1])) ?>" aria-label="Consultar productos con stock bajo">
                <?= icono('alertas') ?>
            </a>
            <a class="account text-decoration-none d-flex align-items-center gap-2 border-start ps-3" href="<?= escapar(ruta('configuracion')) ?>">
                <span class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;"><?= escapar(mb_substr($_SESSION['empleado']['nombre'],0,1)) ?></span>
                <div class="d-none d-sm-block text-start lh-sm">
                    <strong class="d-block text-dark fs-6"><?= escapar($_SESSION['empleado']['nombre']) ?></strong>
                    <small class="text-muted fs-7"><?= escapar($_SESSION['empleado']['rol']) ?></small>
                </div>
                <span class="account-arrow text-muted">⌄</span>
            </a>
        </div>
    </header>

    <main class="page p-4">
        <?php if ($modulo!=='dashboard'): ?>
            <div class="page-heading d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1"><?= escapar($titulo) ?></h1>
                    <p class="text-muted small mb-0">SISTEMA DE VENTAS E INVENTARIO · ALMANICA</p>
                </div>
                <span class="date-pill badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill fw-semibold"><?= date('d/m/Y') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= escapar($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= escapar($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>