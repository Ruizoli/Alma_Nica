<?php 
defined('ALMANICA') || exit; 

// 1. Obtenemos las filas de productos de forma segura
$filasProductos = $listado['filas'] ?? [];

// 2. Obtenemos el mapa de categorías para relacionar el ID con el Nombre de la categoría
$mapaCategorias = $opciones['categoria'] ?? [];

// 3. Agrupamos los productos por el nombre de su categoría
$productosPorCategoria = [];
foreach ($filasProductos as $p) {
    $idCat = $p['Id_Categoria'] ?? 0;
    // Buscamos el nombre de la categoría en el array de opciones, si no existe ponemos "Sin Categoría"
    $nombreCat = $mapaCategorias[$idCat] ?? 'Sin Categoría';
    
    $productosPorCategoria[$nombreCat][] = $p;
}
?>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <!-- Cabecera del Módulo -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <h3 class="fw-bold mb-1">Inventario de Productos</h3>
            <p class="text-muted small mb-0">Productos organizados y separados por sus respectivas categorías.</p>
        </div>
        <a href="<?= escapar(ruta($modulo, ['accion' => 'nuevo'])) ?>" class="btn btn-primary fw-semibold rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Nuevo producto
        </a>
    </div>

    <!-- Barra de Búsqueda General -->
    <form method="get" class="mb-5">
        <input type="hidden" name="modulo" value="<?= escapar($modulo) ?>">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="search" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Buscar producto..." value="<?= escapar($_GET['q'] ?? '') ?>">
            <button type="submit" class="btn btn-secondary px-4 fw-semibold">Buscar</button>
        </div>
    </form>

    <?php if (!empty($productosPorCategoria)): ?>
        <?php foreach ($productosPorCategoria as $categoriaNombre => $items): ?>
            <!-- Sección por cada Categoría -->
            <div class="mb-5">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <div class="bg-primary-subtle text-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-folder-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-0"><?= escapar($categoriaNombre) ?></h4>
                    <span class="badge bg-light text-secondary border ms-3 rounded-pill px-3">
                        <?= count($items) ?> <?= count($items) === 1 ? 'producto' : 'productos' ?>
                    </span>
                </div>

                <!-- Cuadrícula de Cajas de Productos -->
                <div class="row g-3">
                    <?php foreach ($items as $p): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                        <div class="card border rounded-4 p-3 h-100 shadow-sm bg-white position-relative product-box transition-hover">
                            
                            <!-- Contenedor de Imagen o Icono del Producto -->
                            <!-- Contenedor de Imagen o Icono del Producto -->
                            <div class="bg-light rounded-3 mb-3 text-center d-flex align-items-center justify-content-center overflow-hidden border-0 position-relative" style="height: 120px;">
                            <?php if (!empty($p['Imagen'])): ?>
                            <!-- Apunta al script imagen.php de la raíz del sistema -->
                            <img src="imagen.php?archivo=<?= escapar($p['Imagen']) ?>" alt="<?= escapar($p['Nombre']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="position-absolute w-100 h-100 d-none align-items-center justify-content-center bg-light">
                            <i class="bi bi-box-seam display-5 text-secondary opacity-75"></i>
                            </div>
                            <?php else: ?>
                            <!-- Icono por defecto si no tiene imagen -->
                            <i class="bi bi-box-seam display-5 text-secondary opacity-75"></i>
                            <?php endif; ?>
                            </div>
                            <!-- Información Principal -->
                            <span class="badge bg-light text-dark border font-monospace align-self-start mb-1" style="font-size: 0.75rem;"><?= escapar($p['Codigo']) ?></span>
                            <h5 class="fw-bold text-dark mb-1 text-truncate" title="<?= escapar($p['Nombre']) ?>"><?= escapar($p['Nombre']) ?></h5>
                            <p class="text-muted small mb-2">Marca: <?= escapar($p['Marca']) ?> | Talla: <?= escapar($p['Talla']) ?></p>

                            <!-- Stock y Precio -->
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                <div>
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Stock actual</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                        <?= numero($p['Stock_Actual']) ?> un.
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Precio</span>
                                    <span class="fw-bold text-primary"><?= dinero($p['Precio_Venta']) ?></span>
                                </div>
                            </div>

                            <!-- Botones de Acción Rápidos -->
                            <div class="d-flex gap-1 mt-3 pt-2 border-top">
                                <a href="<?= escapar(ruta($modulo, ['accion' => 'editar', 'id' => $p['Id_Producto']])) ?>" class="btn btn-sm btn-outline-primary w-50 rounded-pill">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <a href="<?= escapar(ruta($modulo, ['accion' => 'eliminar', 'id' => $p['Id_Producto']])) ?>" class="btn btn-sm btn-outline-danger w-50 rounded-pill">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-box2 display-4 mb-3 d-block text-secondary"></i>
            <p class="fs-5">No hay productos registrados en el inventario.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    border-color: var(--bs-primary) !important;
}
</style>