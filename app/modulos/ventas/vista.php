<?php 
defined('ALMANICA') || exit; 

$listaProductos = $productos ?? [];
$listaClientes  = $terceros ?? [];
$historialVentas = $listado['filas'] ?? [];
?>

<script>
<?php if (!empty($_GET['id'])): ?>
localStorage.removeItem('carrito_ventas');
<?php endif; ?>

window.carrito = JSON.parse(localStorage.getItem('carrito_ventas') || '[]');

window.guardarCarritoLocal = function() {
    localStorage.setItem('carrito_ventas', JSON.stringify(window.carrito));
};

window.agregarAlCarrito = function(id, nombre, precio, stock) {
    let index = window.carrito.findIndex(item => item.id === id);
    if (index !== -1) {
        if (window.carrito[index].cantidad < stock) {
            window.carrito[index].cantidad++;
        } else {
            alert('No hay suficiente stock disponible.');
            return;
        }
    } else {
        window.carrito.push({ id: id, nombre: nombre, precio: precio, cantidad: 1, stock: stock });
    }
    window.guardarCarritoLocal();
    window.renderizarCarrito();
};

window.cambiarCantidad = function(id, cantidad) {
    let index = window.carrito.findIndex(item => item.id === id);
    if (index !== -1) {
        let nuevaCant = parseInt(cantidad);
        if (isNaN(nuevaCant) || nuevaCant < 1) nuevaCant = 1;
        if (nuevaCant > window.carrito[index].stock) {
            alert('La cantidad supera el stock disponible (' + window.carrito[index].stock + ').');
            window.carrito[index].cantidad = window.carrito[index].stock;
        } else {
            window.carrito[index].cantidad = nuevaCant;
        }
    }
    window.guardarCarritoLocal();
    window.renderizarCarrito();
};

window.eliminarItem = function(id) {
    window.carrito = window.carrito.filter(item => item.id !== id);
    window.guardarCarritoLocal();
    window.renderizarCarrito();
};

window.limpiarCarrito = function() {
    window.carrito = [];
    localStorage.removeItem('carrito_ventas');
    window.renderizarCarrito();
};

window.renderizarCarrito = function() {
    let contenedor = document.getElementById('contenedor-items-carrito');
    let totalEstimadoEl = document.getElementById('total-estimado');
    let btnRegistrar = document.getElementById('btn-registrar');
    let contenedorInputs = document.getElementById('inputs-ocultos-detalle');
    let campoJson = document.getElementById('detalle-json');
    
    if (!contenedor) return;
    contenedor.innerHTML = '';
    if (contenedorInputs) contenedorInputs.innerHTML = '';
    
    if (window.carrito.length === 0) {
        contenedor.innerHTML = `
            <div class="text-center text-muted py-4" id="carrito-vacio">
                <i class="bi bi-cart-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                <p class="small mb-0">No hay productos agregados.</p>
            </div>`;
        if (totalEstimadoEl) totalEstimadoEl.innerText = 'C$ 0.00';
        if (btnRegistrar) btnRegistrar.disabled = true;
        if (campoJson) campoJson.value = '';
        return;
    }

    let totalGeneral = 0;

    window.carrito.forEach((item, index) => {
        let subtotal = item.precio * item.cantidad;
        totalGeneral += subtotal;

        let htmlItem = `
            <div class="card border bg-light p-3 mb-2 rounded-3 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">${item.nombre}</h6>
                        <small class="text-muted">Precio: C$ ${item.precio.toFixed(2)}</small>
                    </div>
                    <button type="button" class="btn-close ms-2" aria-label="Eliminar" onclick="window.eliminarItem(${item.id})"></button>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white">Cant.</span>
                            <input type="number" class="form-control text-center" value="${item.cantidad}" min="1" max="${item.stock}" onchange="window.cambiarCantidad(${item.id}, this.value)">
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <span class="fw-bold text-dark">C$ ${subtotal.toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `;
        contenedor.innerHTML += htmlItem;

        if (contenedorInputs) {
            contenedorInputs.innerHTML += `
                <input type="hidden" name="detalle[${index}][Id_Producto]" value="${item.id}">
                <input type="hidden" name="detalle[${index}][Cantidad]" value="${item.cantidad}">
            `;
        }
    });

    if (campoJson) {
        campoJson.value = JSON.stringify(
            window.carrito.map(i => ({ Id_Producto: i.id, Cantidad: i.cantidad }))
        );
    }

    if (totalEstimadoEl) totalEstimadoEl.innerText = 'C$ ' + totalGeneral.toFixed(2);
    if (btnRegistrar) btnRegistrar.disabled = false;
};

document.addEventListener("DOMContentLoaded", function() {
    window.renderizarCarrito();
});
</script>

<form method="post" action="panel.php?modulo=ventas&accion=guardar" id="form-venta" onsubmit="return validarEnvioVenta(event);">
    <?php if (function_exists('campoCsrf')) campoCsrf(); ?>
    
    <?php if (!empty($tokenOperacion)): ?>
        <input type="hidden" name="operacion_token" value="<?= escapar($tokenOperacion) ?>">
    <?php endif; ?>

    <!-- Contenedores de respaldo para el detalle -->
    <div id="inputs-ocultos-detalle"></div>
    <input type="hidden" name="detalle_json" id="detalle-json">

    <div class="row g-4 mb-5">
        <!-- Columna Izquierda: Catálogo de Productos -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Registrar venta</h5>
                        <small class="text-muted">Selecciona los productos para agregarlos al detalle.</small>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <?= count($listaProductos) ?> productos disponibles
                    </span>
                </div>

                <div class="mb-4">
                    <label for="buscar-producto" class="form-label fw-semibold text-secondary small">Buscar productos</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" id="buscar-producto" class="form-control bg-light border-start-0 ps-0" placeholder="Código o nombre...">
                    </div>
                </div>

                <div class="row g-3" id="catalogo-productos">
                    <?php if (!empty($listaProductos)): ?>
                        <?php foreach ($listaProductos as $p): 
                            $idProd  = $p['Id_Producto'] ?? 0;
                            $codigo  = $p['Codigo'] ?? 'S/C';
                            $nombre  = $p['Nombre'] ?? 'Sin nombre';
                            $precio  = floatval($p['Precio_Venta'] ?? 0);
                            $stock   = intval($p['Stock_Actual'] ?? 0);
                            $imagen  = $p['Imagen'] ?? '';
                        ?>
                            <div class="col-12 col-sm-6 col-md-4 producto-item" data-nombre="<?= strtolower($nombre) ?>" data-codigo="<?= strtolower($codigo) ?>">
                                <div class="card border rounded-4 p-3 h-100 text-center shadow-sm position-relative product-card bg-white transition-hover">
                                    <div class="bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center overflow-hidden border-0 position-relative" style="height: 100px;">
                                        <?php if (!empty($imagen)): ?>
                                            <img src="imagen.php?archivo=<?= escapar($imagen) ?>" alt="<?= escapar($nombre) ?>" class="w-100 h-100 object-fit-cover">
                                        <?php else: ?>
                                            <i class="bi bi-box-seam display-5 text-secondary opacity-75"></i>
                                        <?php endif; ?>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark text-truncate" title="<?= escapar($nombre) ?>"><?= escapar($nombre) ?></h6>
                                    <span class="badge bg-light text-muted mb-2 font-monospace" style="font-size: 0.75rem;"><?= escapar($codigo) ?></span>
                                    <div class="fw-bold text-primary fs-5 mb-1"><?= dinero($precio) ?></div>
                                    <small class="text-muted d-block mb-3" style="font-size: 0.75rem;">Stock: <?= numero($stock) ?></small>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-auto fw-semibold" 
                                            onclick="window.agregarAlCarrito(<?= $idProd ?>, '<?= htmlspecialchars($nombre, ENT_QUOTES) ?>', <?= $precio ?>, <?= $stock ?>)">
                                        <i class="bi bi-plus-lg me-1"></i>Agregar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted py-4">
                            <p>No hay productos disponibles para la venta.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Detalle de Venta -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Detalle de venta</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="window.limpiarCarrito()">
                        <i class="bi bi-trash me-1"></i>Limpiar
                    </button>
                </div>

                <div class="cart-items mb-3" id="contenedor-items-carrito" style="max-height: 320px; overflow-y: auto;">
                    <div class="text-center text-muted py-4" id="carrito-vacio">
                        <i class="bi bi-cart-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="small mb-0">No hay productos agregados.</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="cliente-venta" class="form-label fw-semibold text-secondary small">Cliente (opcional)</label>
                    <select name="Id_Cliente" id="cliente-venta" class="form-select bg-light">
                        <option value="">Consumidor final</option>
                        <?php if (!empty($listaClientes)): ?>
                            <?php foreach ($listaClientes as $cli): ?>
                                <option value="<?= $cli['id'] ?>"><?= escapar($cli['Nombre']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="metodo-pago" class="form-label fw-semibold text-secondary small">Forma de pago</label>
                    <select name="Metodo_Pago" id="metodo-pago" class="form-select bg-light" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>
                </div>

                <div class="bg-warning-subtle p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center border border-warning-subtle">
                    <span class="fw-semibold text-warning-emphasis">Total estimado</span>
                    <span class="fs-4 fw-bold text-warning-emphasis" id="total-estimado">C$ 0.00</span>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 shadow-sm" id="btn-registrar" disabled>
                    <i class="bi bi-check-circle-fill me-2"></i>Registrar venta
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Historial de Ventas -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Ventas registradas</h5>
        <small class="text-muted">Historial de operaciones</small>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary">
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Estado / Pago</th>
                    <th>Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($historialVentas)): ?>
                    <?php foreach ($historialVentas as $v): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $v['id'] ?></td>
                            <td class="text-muted"><?= date('d/m/Y H:i', strtotime($v['Fecha'])) ?></td>
                            <td class="fw-semibold"><?= escapar($v['nombre'] ?? 'Consumidor Final') ?></td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <?= escapar($v['estado'] ?? 'Completado') ?>
                                </span>
                            </td>
                            <td class="fw-bold text-dark"><?= dinero($v['Total']) ?></td>
                            <td class="text-end">
                                <a href="<?= escapar(ruta($modulo, ['accion' => 'detalle', 'id' => $v['id']])) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    Detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No hay ventas registradas en el historial.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function validarEnvioVenta(e) {
    if (window.carrito.length === 0) {
        alert('Debes agregar al menos un producto al carrito antes de registrar la venta.');
        e.preventDefault();
        return false;
    }

    // Forzamos el renderizado de los inputs ocultos y del JSON
    window.renderizarCarrito();

    let btn = document.getElementById('btn-registrar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando venta...';
    
    // Permitimos el envío nativo del formulario hacia XAMPP
    return true;
}
document.addEventListener("DOMContentLoaded", function() {
    let inputBuscar = document.getElementById('buscar-producto');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', function(e) {
            let valor = e.target.value.toLowerCase().trim();
            let items = document.querySelectorAll('.producto-item');
            items.forEach(item => {
                let nombre = item.getAttribute('data-nombre');
                let codigo = item.getAttribute('data-codigo');
                if (nombre.includes(valor) || codigo.includes(valor)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>

<style>
.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    border-color: var(--bs-primary) !important;
}
</style>