<?php
// Punto común de inicialización de la aplicación.
define('ALMANICA', true);
date_default_timezone_set('America/Managua');
require_once __DIR__ . '/soporte/funciones.php';
require_once __DIR__ . '/soporte/sesion.php';
require_once __DIR__ . '/../config/conexion.php';

require_once __DIR__ . '/soporte/sistema.php';
require_once __DIR__ . '/soporte/imagenes.php';

require_once __DIR__ . '/soporte/iconos.php';
