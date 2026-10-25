<?php defined('ALMANICA') || exit; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escapar($titulo) ?> · AlmaNica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
    <link href="assets/css/sistema.css" rel="stylesheet">
    <link href="assets/css/comercial.css?v=3" rel="stylesheet">
    <script src="assets/js/sistema.js?v=5" defer></script>
<link href="assets/css/marca.css?v=1" rel="stylesheet">
    <script src="assets/js/busqueda.js?v=1" defer></script>
    <link href="assets/css/busqueda.css?v=2" rel="stylesheet">
    <script src="assets/js/selectores.js?v=1" defer></script>
    <link href="assets/css/selectores.css?v=1" rel="stylesheet">
</head>
<body class="sistema">
<aside class="sidebar" id="menu-principal">
    <a class="brand" href="panel.php"><?php require __DIR__ . '/marca.php'; ?></a>
    <nav aria-label="Navegación principal">
        <?php $menu=['dashboard'=>'Dashboard','ventas'=>'Ventas','productos'=>'Productos','categorias'=>'Categorías','clientes'=>'Clientes','proveedores'=>'Proveedores','compras'=>'Compras','empleados'=>'Empleados','usuarios'=>'Usuarios','reportes'=>'Reportes','configuracion'=>'Configuración']; ?>
        <?php foreach ($menu as $key=>$label): if (!puedeVer($key)) continue; ?>
        <?php if ($key==='compras'): ?>
            <details class="nav-group" <?= in_array($modulo,['compras','recepcion','historial'],true)?'open':'' ?>>
                <summary><?= icono('ventas') ?><span>Compras</span><span class="chevron">⌄</span></summary>
                <?php foreach (['compras'=>'Órdenes de compra','recepcion'=>'Recepción de compras','historial'=>'Historial de compras'] as $sub=>$texto): ?>
                <a href="<?= escapar(ruta($sub)) ?>" class="<?= $modulo===$sub?'selected':'' ?>" <?= $modulo===$sub?'aria-current="page"':'' ?>><?= icono($sub) ?><span><?= $texto ?></span></a>
                <?php endforeach; ?>
            </details>
        <?php else: ?>
            <a href="<?= escapar(ruta($key)) ?>" class="<?= $modulo===$key?'selected':'' ?>" <?= $modulo===$key?'aria-current="page"':'' ?>><?= icono($key) ?><span><?= $label ?></span></a>
        <?php endif; endforeach; ?>
    </nav>
    <div class="sidebar-bottom"><form method="post" action="salir.php"><?php campoCsrf(); ?><button class="logout" type="submit">↪ Cerrar sesión</button></form></div>
</aside>
<div class="workspace">
    <header class="topbar">
        <button class="icon-button menu-toggle" type="button" aria-label="Mostrar u ocultar menú" aria-controls="menu-principal" aria-expanded="true" data-toggle-menu><?= icono('menu') ?></button>
        <form class="global-search" method="get" data-global-search><input type="hidden" name="modulo" value="buscar"><?= icono('buscar') ?><input name="q" type="search" aria-label="Buscar en el sistema" placeholder="Buscar en los módulos permitidos…" value="<?= $modulo==='buscar'?escapar($_GET['q'] ?? ''):'' ?>"><button type="submit" class="sr-only">Buscar</button></form>
        <a class="notification-link" href="<?= escapar(ruta('productos',['bajo'=>1])) ?>" aria-label="Consultar productos con stock bajo"><?= icono('alertas') ?></a>
        <a class="account" href="<?= escapar(ruta('configuracion')) ?>"><span class="avatar"><?= escapar(mb_substr($_SESSION['empleado']['nombre'],0,1)) ?></span><div><strong><?= escapar($_SESSION['empleado']['nombre']) ?></strong><small><?= escapar($_SESSION['empleado']['rol']) ?></small></div><span class="account-arrow">⌄</span></a>
    </header>
    <main class="page">
        <?php if ($modulo!=='dashboard'): ?><div class="page-heading"><div><h1><?= escapar($titulo) ?></h1><p class="muted">SISTEMA DE VENTAS E INVENTARIO · ALMANICA</p></div><span class="date-pill"><?= date('d/m/Y') ?></span></div><?php endif; ?>
        <?php if ($mensaje): ?><div class="notice success" role="status"><?= escapar($mensaje) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="notice danger" role="alert"><?= escapar($error) ?></div><?php endif; ?>
