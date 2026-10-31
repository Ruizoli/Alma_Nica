<?php
// Variables preparadas por el sistema antes de incluir este archivo.
/** @var array|null $orden */
/** @var int $idOrden */
/** @var string $accion */
/** @var array $lineas */
/** @var array $productos */
/** @var array $proveedores */
/** @var string $tokenOperacion */

defined('ALMANICA') || exit;
$filasFormulario=[];
if ($_SERVER['REQUEST_METHOD']==='POST' && is_array($_POST['producto'] ?? null)) {
    foreach ($_POST['producto'] as $i=>$producto) $filasFormulario[]=['Id_Producto'=>$producto,'Cantidad'=>$_POST['cantidad'][$i] ?? 1,'Precio_Costo'=>$_POST['costo'][$i] ?? '', 'Precio_Venta_Referencia'=>$_POST['precio_venta'][$i] ?? ''];
} else $filasFormulario=$lineas;
require __DIR__ . '/navegacion.php';
?>
<h2 class="purchase-title"><?= $accion==='editar'?'EDITAR ORDEN DE COMPRA':'NUEVA ORDEN DE COMPRA' ?></h2>
<form method="post" data-purchase-editor>
    <?php campoCsrf(); ?>
    <input type="hidden" name="accion_compra" value="guardar"><input type="hidden" name="id_orden" value="<?= $accion==='editar'?(int)$idOrden:0 ?>"><input type="hidden" name="operacion_token" value="<?= escapar($tokenOperacion) ?>">
    <div class="purchase-workspace">
        <div>
            <section class="card-box">
                <h2>Datos de la orden</h2>
                <div class="purchase-order-fields">
                    <label class="field">
                        <span>Número de orden</span><input readonly value="<?= $orden?'OC-'.str_pad($idOrden,5,'0',STR_PAD_LEFT):'Se genera al guardar' ?>">
                    </label>
                    <label class="field">
                        <span>Fecha</span><input readonly value="<?= escapar(fechaCorta($orden['Fecha'] ?? date('Y-m-d'))) ?>">
                    </label>
                    <label class="field">
                        <span>Proveedor *</span><select name="proveedor" required><option value="">Busca un proveedor</option><?php foreach ($proveedores as $p): ?><option value="<?= (int)$p['Id_Proveedor'] ?>" <?= (string)($_POST['proveedor'] ?? $orden['Id_Proveedor'] ?? '')===(string)$p['Id_Proveedor']?'selected':'' ?>><?= escapar($p['Nombre']) ?></option><?php endforeach; ?></select>
                    </label>
                </div>
            </section>
            <section class="card-box mt">
                <h2>Agregar producto</h2>
                <div class="purchase-entry">
                    <label class="field">
                        <span>Producto</span><select name="producto_editor" data-entry-product><option value="">Busca un producto</option><?php foreach ($productos as $p): ?><option value="<?= (int)$p['Id_Producto'] ?>" data-price="<?= escapar($p['Precio_Venta']) ?>"><?= escapar($p['Codigo'].' · '.$p['Nombre']) ?></option><?php endforeach; ?></select>
                    </label>
                    <label class="field">
                        <span>Cantidad</span><input data-entry-quantity type="number" min="1" max="2147483647" step="1" value="1">
                    </label>
                    <label class="field">
                        <span>Precio compra C$</span><input data-entry-cost type="number" min="0" max="99999999.99" step=".01">
                    </label>
                    <label class="field">
                        <span>Precio venta C$</span><input data-entry-sale type="number" min="0" max="99999999.99" step=".01">
                    </label>
                    <button type="button" class="button" data-entry-add>Agregar</button>
                </div>
                <p data-entry-status role="status" class="muted"></p>
                <p class="muted">El precio de venta se guarda como referencia de esta orden. El total se calcula con el precio de compra.</p>
            </section>
            <section class="card-box mt">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    Producto
                                </th>
                                <th>
                                    Cantidad
                                </th>
                                <th>
                                    Unidad
                                </th>
                                <th>
                                    Precio compra
                                </th>
                                <th>
                                    Precio venta
                                </th>
                                <th>
                                    Subtotal
                                </th>
                                <th>
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody data-purchase-rows>
                        </tbody>
                    </table>
                </div>
                <p data-purchase-empty class="empty">Agrega los productos de tu pedido.</p>
            </section>
        </div>
        <aside class="card-box purchase-summary">
            <h2>Resumen</h2><p>Productos <strong data-purchase-products>0</strong></p><p>Unidades <strong data-purchase-units>0</strong></p>
            <div class="purchase-total">
                <span>Total de compra</span><strong data-total>C$ 0.00</strong>
            </div>
            <button class="button green-button full" type="submit">Guardar orden</button><a class="button secondary full mt" href="<?= escapar(ruta('compras')) ?>">Volver</a><p class="muted">Puedes editar una orden pendiente mientras no tenga recepciones. El stock cambia al recibirla.</p>
        </aside>
    </div>
    <script type="application/json" data-purchase-initial><?= json_encode($filasFormulario,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) ?></script>
    <noscript>Activa JavaScript para agregar y editar los productos de la orden.</noscript>
</form>
