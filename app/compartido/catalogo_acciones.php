<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

$def = $catalogos[$modulo]; $opciones = opciones($conexion); $registro = [];
$id = empty($_GET['id']) ? 0 : entero($_GET['id'],1);
if ($accion === 'editar') { $registro = registroCatalogo($conexion,$def,$id); }
if (in_array($accion,['nuevo','editar'],true) && !puedeEditar($modulo)) { http_response_code(403); throw new InvalidArgumentException('Este catálogo es de solo consulta para tu rol.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = empty($_POST['id']) ? 0 : entero($_POST['id'],1);
    if (($_POST['accion'] ?? '') === 'eliminar') { if (!esAdmin()) { http_response_code(403); throw new InvalidArgumentException('Solo el administrador puede eliminar registros.'); } eliminarCatalogo($conexion,$modulo,$id); volver($modulo,'Registro eliminado.'); }
    $registro = $_POST;
    $fotoNueva = null;
    try {
        if ($modulo === 'productos') $fotoNueva = guardarFotoProducto($_FILES['foto'] ?? []);
        $foto = $fotoNueva ?? ($modulo === 'productos' && isset($_POST['quitar_foto']) ? '' : null);
        guardarCatalogo($conexion,$modulo,$_POST,$id,$foto);
    } catch (Throwable $e) { descartarFotoNueva($fotoNueva); throw $e; }
    volver($modulo,'Registro guardado correctamente.');
}
$listado = listarCatalogo($conexion,$def,trim((string) ($_GET['q'] ?? '')),max(1,(int) ($_GET['pagina'] ?? 1)),$_GET);
$vista = 'catalogo';
