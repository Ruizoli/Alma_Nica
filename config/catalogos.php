<?php
/** Definiciones de campos; todos los nombres SQL salen de esta lista interna. */
function catalogos(): array
{
    return [
        'productos' => ['titulo'=>'Productos', 'singular'=>'producto', 'tabla'=>'Producto', 'id'=>'Id_Producto', 'buscar'=>['Codigo','Nombre','Marca'], 'campos'=>[
            'Codigo'=>['Código','text',true,50], 'Nombre'=>['Nombre','text',true,100],
            'Id_Categoria'=>['Categoría','categoria',false], 'Id_Proveedor'=>['Proveedor','proveedor',false],
            'Marca'=>['Marca','text',false,50], 'Talla'=>['Talla','text',false,10],
            'Stock_Actual'=>['Stock inicial','integer',true], 'Stock_Minimo'=>['Stock mínimo','integer',true], 'Precio_Venta'=>['Precio de venta (C$)','money',true],
        ]],
        'categorias' => ['titulo'=>'Categorías', 'singular'=>'categoría', 'tabla'=>'Categoria', 'id'=>'Id_Categoria', 'buscar'=>['Nombre'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,50], 'Descripcion'=>['Descripción','textarea',false,5000],
        ]],
        'clientes' => ['titulo'=>'Clientes', 'singular'=>'cliente', 'tabla'=>'Cliente', 'id'=>'Id_Cliente', 'buscar'=>['Nombre','Cedula','Telefono'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,100], 'Cedula'=>['Cédula','text',false,20], 'Correo'=>['Correo','email',false,100], 'Telefono'=>['Teléfono','text',false,20],
        ]],
        'proveedores' => ['titulo'=>'Proveedores', 'singular'=>'proveedor', 'tabla'=>'Proveedor', 'id'=>'Id_Proveedor', 'buscar'=>['Nombre','Telefono','Correo'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,100], 'Telefono'=>['Teléfono','text',false,20], 'Correo'=>['Correo','email',false,100], 'Direccion'=>['Dirección','textarea',false,5000],
        ]],
        'empleados' => ['titulo'=>'Empleados', 'singular'=>'empleado', 'tabla'=>'Empleado', 'id'=>'Id_Empleado', 'buscar'=>['Nombre'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,100], 'Estado'=>['Estado','estado',true],
        ]],
        'usuarios' => ['titulo'=>'Usuarios', 'singular'=>'usuario', 'tabla'=>'Usuario', 'id'=>'Id_Usuario', 'buscar'=>['Usuario'], 'campos'=>[
            'Id_Empleado'=>['Empleado','empleado',true], 'Usuario'=>['Usuario','text',true,50], 'Contrasena'=>['Contraseña','password',true,255], 'Rol'=>['Rol','rol',true], 'Estado'=>['Estado','estado',true],
        ]],
    ];
}
