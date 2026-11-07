<?php
defined('ALMANICA') || exit;

$def = [
    'singular' => 'proveedor',
    'plural' => 'proveedores',
    'id' => 'Id_Proveedor',
    'tabla' => 'proveedor'
];

$accion = $_GET['accion'] ?? 'listado';
$vista = ($accion === 'nuevo' || $accion === 'editar') ? 'formulario' : 'listado';

try {
    // 1. PROCESAR GUARDADO (INSERTAR O ACTUALIZAR)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['Nombre'] ?? '');
        $telefono = trim($_POST['Telefono'] ?? '');
        $correo = trim($_POST['Correo'] ?? '');
        $direccion = trim($_POST['Direccion'] ?? '');

        if ($nombre === '') {
            throw new InvalidArgumentException('El nombre del proveedor es obligatorio.');
        }

        if ($id > 0) {
            $stmt = $conexion->prepare("UPDATE proveedor SET Nombre = ?, Telefono = ?, Correo = ?, Direccion = ? WHERE Id_Proveedor = ?");
            $stmt->execute([$nombre, $telefono, $correo, $direccion, $id]);
            $_SESSION['mensaje'] = 'Proveedor actualizado correctamente.';
        } else {
            $stmt = $conexion->prepare("INSERT INTO proveedor (Nombre, Telefono, Correo, Direccion) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $telefono, $correo, $direccion]);
            $_SESSION['mensaje'] = 'Proveedor registrado correctamente.';
        }

        header('Location: ' . ruta($modulo));
        exit;
    }

    // 2. CARGAR REGISTRO PARA EDITAR
    $registro = [];
    if ($accion === 'editar' && isset($_GET['id'])) {
        $idEdit = (int) $_GET['id'];
        $stmt = $conexion->prepare("SELECT * FROM proveedor WHERE Id_Proveedor = ?");
        $stmt->execute([$idEdit]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. CARGAR LISTADO Y BÚSQUEDA
    $busqueda = trim((string) ($_GET['q'] ?? ''));
    if ($busqueda !== '') {
        $stmt = $conexion->prepare("SELECT Id_Proveedor AS id, Nombre, Telefono, Correo, Direccion FROM proveedor WHERE Nombre LIKE ? OR Correo LIKE ? OR Telefono LIKE ? ORDER BY Nombre");
        $stmt->execute(['%'.$busqueda.'%', '%'.$busqueda.'%', '%'.$busqueda.'%']);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conexion->query("SELECT Id_Proveedor AS id, Nombre, Telefono, Correo, Direccion FROM proveedor ORDER BY Nombre");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Throwable $e) {
    error_log($e->getMessage());
    $error = "Error: " . $e->getMessage();
    $datos = [];
    $registro = [];
}