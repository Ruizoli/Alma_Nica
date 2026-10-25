<?php
// Campos, etiquetas y validaciones del módulo.
return ['titulo'=>'Usuarios', 'singular'=>'usuario', 'tabla'=>'Usuario', 'id'=>'Id_Usuario', 'buscar'=>['Usuario'], 'campos'=>[
            'Id_Empleado'=>['Empleado','empleado',true], 'Usuario'=>['Usuario','text',true,50], 'Contrasena'=>['Contraseña','password',true,255], 'Rol'=>['Rol','rol',true], 'Estado'=>['Estado','estado',true],
        ]];
