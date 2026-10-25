<?php
function catalogos(): array
{
    return [
        'productos' => require __DIR__ . '/../app/modulos/inventario/campos.php',
        'categorias' => require __DIR__ . '/../app/modulos/categorias/campos.php',
        'clientes' => require __DIR__ . '/../app/modulos/clientes/campos.php',
        'proveedores' => require __DIR__ . '/../app/modulos/proveedores/campos.php',
        'empleados' => require __DIR__ . '/../app/modulos/empleados/campos.php',
        'usuarios' => require __DIR__ . '/../app/modulos/usuarios/campos.php',
    ];
}
