<?php
// Campos, etiquetas y validaciones del módulo.
return ['titulo'=>'Categorías', 'singular'=>'categoría', 'tabla'=>'Categoria', 'id'=>'Id_Categoria', 'buscar'=>['Nombre'], 'campos'=>[
            'Nombre'=>['Nombre','text',true,50], 'Descripcion'=>['Descripción','textarea',false,5000],
        ]];
