<?php
// Campos, etiquetas y validaciones del módulo.
return ['titulo'=>'Clientes', 'singular'=>'cliente', 'tabla'=>'Cliente', 'id'=>'Id_Cliente', 'buscar'=>['Nombre','Cedula','Telefono'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,100], 'Cedula'=>['Cédula','text',false,20], 'Correo'=>['Correo','email',false,100], 'Telefono'=>['Teléfono','text',false,20],
        ]];
