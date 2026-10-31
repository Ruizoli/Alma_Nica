# Actualizar Compras en otro equipo

Git no actualiza MySQL. Inicia MySQL y abre una terminal en la carpeta del proyecto.

PowerShell:
    C:\\xampp\\php\\php.exe database/actualizar_compras.php

Git Bash:
    /c/xampp/php/php.exe database/actualizar_compras.php

El comando muestra la base conectada, versión y lo que falta. No cambia datos sin --aplicar.
Confirma que sea la base correcta en config/base_datos.php.
Conserva un respaldo de la base y ejecuta el mismo comando agregando --aplicar para actualizar.

Aplica 002, 004 y 005. Requiere las tablas originales. No crea usuarios ni elimina registros.
Comprueba columnas antes de agregarlas para funcionar sin ADD COLUMN IF NOT EXISTS.
Si detecta un esquema distinto, muestra lo que queda pendiente. Puede repetirse.
Los cambios DDL no se deshacen todos juntos si una instrucción falla.

Comparte también los SQL de database, el actualizador y app/modulos/compras/esquema.php.
Si falla, comparte la salida del comando y la línea SQLSTATE del registro de Apache.
No compartas contraseñas.
