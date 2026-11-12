<?php
defined('ALMANICA') || exit;
require_once __DIR__ . '/../../compartido/operaciones.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['operacion_token'] ?? '');
    if (($_SESSION['operaciones'][$token] ?? '') !== $modulo) {
        throw new InvalidArgumentException('Este formulario ya se procesó o venció.');
    }
    $post = $_POST;
    if (empty($post['producto'])) {
        $detalle = $post['detalle'] ?? json_decode((string)($post['detalle_json'] ?? ''), true);
        if (!is_array($detalle)) throw new InvalidArgumentException('Agrega al menos un producto.');
        $post['producto'] = []; $post['cantidad'] = [];
        foreach ($detalle as $item) {
            if (!is_array($item)) throw new InvalidArgumentException('Detalle de venta inválido.');
            $post['producto'][] = $item['Id_Producto'] ?? $item['id_producto'] ?? $item['id'] ?? '';
            $post['cantidad'][] = $item['Cantidad'] ?? $item['cantidad'] ?? '';
        }
    }
    $post['cliente'] = $post['Id_Cliente'] ?? $post['cliente'] ?? '';
    $post['metodo'] = $post['Metodo_Pago'] ?? $post['metodo'] ?? '';
    $id = crearOperacion($conexion, false, $post, (int)$_SESSION['empleado']['id']);
    unset($_SESSION['operaciones'][$token]);
    $_SESSION['venta_guardada'] = $id;
    $_SESSION['mensaje'] = 'Venta registrada correctamente.';
    header('Location: '.ruta('ventas', ['id'=>$id]));
    exit;
}
