<?php
// Ajustes locales de MySQL en XAMPP. No publicar credenciales reales.
return [
    'host' => '127.0.0.1',
    'puerto' => '3306',
    'base_datos' => getenv('ALMANICA_DB') ?: 'almanica',
    'usuario' => 'root',
    'contrasena' => '',
];
