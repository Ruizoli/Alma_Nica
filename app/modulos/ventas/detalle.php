<?php 
defined('ALMANICA') || exit; 
$idVentaActual = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lineasFactura = isset($detalle) && !empty($detalle) ? $detalle : (function_exists('detalleOperacion') ? detalleOperacion($conexion, false, $idVentaActual) : []);
?>
<div class="container my-4">
    <!-- Botones superiores (ocultos al imprimir) -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="panel.php?modulo=ventas" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Volver a Ventas
        </a>
        <button type="button" class="btn btn-dark rounded-pill px-4 shadow-sm" onclick="window.print();">
            <i class="bi bi-printer me-2"></i>Imprimir Factura
        </button>
    </div>

    <!-- Contenedor Principal Estilo Factura -->
    <section class="card-box mb p-5 bg-white rounded-4 shadow-sm mx-auto" style="max-width: 800px;">
        
        <!-- Cabecera con Logo y Datos de la Factura -->
        <div class="row align-items-center border-bottom pb-4 mb-4" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dee2e6; padding-bottom: 20px; margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background-color: #0d6efd; color: white; border-radius: 50%; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.25rem;">
                    AN
                </div>
                <div>
                    <h3 style="margin: 0; font-weight: bold; color: #212529;">AlmaNica</h3>
                    <small style="color: #6c757d; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px;">EL CORAZÓN DE NICARAGUA</small>
                </div>
            </div>

            <div style="text-align: right;">
                <h5 style="margin: 0 0 5px 0; font-size: 0.9rem; text-transform: uppercase; color: #6c757d; letter-spacing: 1px;">Datos de la Factura</h5>
                <p style="margin: 0 0 2px 0; font-weight: bold; color: #212529; font-family: monospace;">Factura No: f-<?= str_pad($idVentaActual, 8, '0', STR_PAD_LEFT) ?></p>
                <p style="margin: 0; color: #6c757d; font-size: 0.85rem;">Fecha: <?= date('d/m/Y, g:i a') ?></p>
            </div>
        </div>

        <!-- Título de sección -->
        <div class="section-heading mb-4">
            <h2 style="font-size: 1.2rem; font-weight: bold; color: #333;">Detalle de venta #<?= $idVentaActual ?></h2>
        </div>

        <!-- Tabla de Productos -->
        <div class="table-wrap mb-4">
            <table class="table align-middle w-100" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #212529; color: white;">
                        <th style="padding: 12px; text-align: left; border-top-left-radius: 8px;">Producto</th>
                        <th style="padding: 12px; text-align: center; width: 100px;">Cantidad</th>
                        <th style="padding: 12px; text-align: right; width: 130px;">Precio</th>
                        <th style="padding: 12px; text-align: right; width: 130px; border-top-right-radius: 8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalDetalle = 0;
                    if (!empty($lineasFactura)): 
                        foreach ($lineasFactura as $linea): 
                            $subtotal = centavos($linea['Subtotal'] ?? ($linea['precio'] * $linea['Cantidad']));
                            $totalDetalle += $subtotal;
                    ?>
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 12px; text-align: left; color: #212529; font-weight: 500;"><?= escapar($linea['Nombre'] ?? 'Producto') ?></td>
                        <td style="padding: 12px; text-align: center; color: #495057; font-weight: bold;"><?= numero($linea['Cantidad'] ?? 1) ?></td>
                        <td style="padding: 12px; text-align: right; color: #495057;"><?= dinero($linea['precio'] ?? 0) ?></td>
                        <td style="padding: 12px; text-align: right; font-weight: bold; color: #212529;"><?= dinero($subtotal) ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">No hay productos en esta venta.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Recuadro del Total Estilo Tarjeta -->
        <div style="display: flex; justify-content: flex-end; margin-top: 30px;">
            <div style="background-color: #212529; color: white; padding: 20px 30px; border-radius: 12px; text-align: center; min-width: 220px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <span style="display: block; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #adb5bd; margin-bottom: 5px; font-weight: 600;">Total</span>
                <span style="font-size: 1.5rem; font-weight: bold; color: #ffc107; font-family: monospace;">
                    <?= dinero(decimal($totalDetalle)) ?>
                </span>
            </div>
        </div>

        <!-- Pie de página -->
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 0.85rem; font-style: italic;">
            ¡Gracias por su compra en AlmaNica!
        </div>

    </section>
</div>

<style>
@media print {
    body {
        background-color: #ffffff !important;
    }
    .d-print-none {
        display: none !important;
    }
    .card-box {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>