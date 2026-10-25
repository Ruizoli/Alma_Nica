<?php
// Campos, etiquetas y validaciones del módulo.
return ['titulo'=>'Proveedores', 'singular'=>'proveedor', 'tabla'=>'Proveedor', 'id'=>'Id_Proveedor', 'buscar'=>['Nombre','Telefono','Correo'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,100], 'Telefono'=>['Teléfono','text',false,20], 'Correo'=>['Correo','email',false,100], 'Direccion'=>['Dirección','textarea',false,5000],
        ]];
