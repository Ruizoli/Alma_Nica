<?php
// Campos, etiquetas y validaciones del módulo.
return ['titulo'=>'Empleados', 'singular'=>'empleado', 'tabla'=>'Empleado', 'id'=>'Id_Empleado', 'buscar'=>['Nombre'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,100], 'Estado'=>['Estado','estado',true],
        ]];
