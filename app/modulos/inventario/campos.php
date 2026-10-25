<?php
// Campos, etiquetas y validaciones del módulo.
return ['titulo'=>'Productos', 'singular'=>'producto', 'tabla'=>'Producto', 'id'=>'Id_Producto', 'buscar'=>['Codigo','Nombre','Marca'], 'campos'=>[
            'Codigo'=>['Código','text',true,50], 'Nombre'=>['Nombre','text',true,100],
            'Id_Categoria'=>['Categoría','categoria',false], 'Id_Proveedor'=>['Proveedor','proveedor',false],
            'Marca'=>['Marca','text',false,50], 'Talla'=>['Talla','text',false,10],
            'Stock_Actual'=>['Stock inicial','integer',true], 'Stock_Minimo'=>['Stock mínimo','integer',true], 'Precio_Venta'=>['Precio de venta (C$)','money',true],
        ]];
