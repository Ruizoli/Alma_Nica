<?php
function conectar(): PDO
{
    static $conexion = null;
    if ($conexion === null) {
        $config = require __DIR__ . '/base_datos.php';
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'], $config['puerto'], $config['base_datos']
        );
        $conexion = new PDO($dsn, $config['usuario'], $config['contrasena'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $conexion;
}
