<?php
/** Lista única de roles y permisos. Un rol desconocido no recibe permisos. */
function permisosRoles(): array
{
    return [
        'Administrador' => [
            'ver'=>['dashboard','productos','categorias','clientes','proveedores','compras','recepcion','historial','ventas','empleados','usuarios','reportes','configuracion','buscar'],
            'editar'=>['productos','categorias','clientes','proveedores','compras','recepcion','ventas','empleados','usuarios','configuracion'],
        ],
        'Vendedor' => [
            'ver'=>['dashboard','productos','ventas','clientes','configuracion','buscar'],
            'editar'=>['ventas','clientes','configuracion'],
        ],
        'Cajero' => [
            'ver'=>['dashboard','productos','ventas','clientes','configuracion','buscar'],
            'editar'=>['ventas','clientes','configuracion'],
        ],
        'Inventarista' => [
            'ver'=>['productos','categorias','proveedores','compras','recepcion','historial','configuracion','buscar'],
            'editar'=>['productos','categorias','proveedores','compras','recepcion','configuracion'],
        ],
    ];
}
function rolValido(string $rol): bool { return array_key_exists($rol, permisosRoles()); }
function rolesDisponibles(): array { $roles=array_keys(permisosRoles()); return array_combine($roles,$roles); }
