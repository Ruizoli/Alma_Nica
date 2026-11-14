<?php
defined('ALMANICA') || exit;

/**
 * Genera el script SQL completo (estructura y datos) de toda la base de datos.
 */
function generarSqlCompleto(PDO $conexion): string
{
    $nombreBD = $conexion->query('SELECT DATABASE()')->fetchColumn();

    $sql  = "-- ==========================================================\n";
    $sql .= "-- RESPALDO DE BASE DE DATOS: {$nombreBD}\n";
    $sql .= "-- SISTEMA: AlmaNica\n";
    $sql .= "-- FECHA: " . date('d/m/Y H:i:s') . "\n";
    $sql .= "-- ==========================================================\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sql .= "SET time_zone = \"-06:00\";\n\n";

    $tablas = $conexion->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tablas as $tabla) {
        // 1. Estructura de la tabla
        $sql .= "-- Estructura de la tabla `{$tabla}`\n";
        $sql .= "DROP TABLE IF EXISTS `{$tabla}`;\n";
        $create = $conexion->query("SHOW CREATE TABLE `{$tabla}`")->fetch(PDO::FETCH_ASSOC);
        $sql .= $create['Create Table'] . ";\n\n";

        // 2. Registros de la tabla
        $filas = $conexion->query("SELECT * FROM `{$tabla}`")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($filas)) {
            $columnas = '`' . implode('`, `', array_keys($filas[0])) . '`';
            foreach ($filas as $fila) {
                $valores = [];
                foreach ($fila as $val) {
                    if ($val === null) {
                        $valores[] = 'NULL';
                    } elseif (is_int($val) || is_float($val)) {
                        $valores[] = $val;
                    } else {
                        $valores[] = $conexion->quote((string)$val);
                    }
                }
                $sql .= "INSERT INTO `{$tabla}` ({$columnas}) VALUES (" . implode(', ', $valores) . ");\n";
            }
            $sql .= "\n";
        }
    }

    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    return $sql;
}

/**
 * Guarda el respaldo en Alma_Nica/backups/ y devuelve la ruta del archivo.
 */
function guardarArchivoRespaldo(PDO $conexion, string $tipo = 'auto'): string
{
    $carpetaBackups = dirname(__DIR__, 2) . '/backups';

    if (!is_dir($carpetaBackups)) {
        mkdir($carpetaBackups, 0755, true);
        file_put_contents($carpetaBackups . '/.htaccess', "Deny from all\n");
    }

    $nombreArchivo = 'almanica_' . $tipo . '_' . date('Y-m-d_H-i-s') . '.sql';
    $rutaCompleta = $carpetaBackups . '/' . $nombreArchivo;

    file_put_contents($rutaCompleta, generarSqlCompleto($conexion));

    return $rutaCompleta;
}

/**
 * Ejecuta un respaldo silencioso si han pasado 24 horas desde el último.
 */
function verificarRespaldoAutomatico24h(PDO $conexion): void
{
    $carpetaBackups = dirname(__DIR__, 2) . '/backups';
    $archivoControl = $carpetaBackups . '/ultimo_respaldo_24h.txt';
    $intervalo24h = 86400; // 24 horas en segundos

    $ultimo = is_file($archivoControl) ? (int)file_get_contents($archivoControl) : 0;

    if ((time() - $ultimo) >= $intervalo24h) {
        guardarArchivoRespaldo($conexion, 'auto');
        file_put_contents($archivoControl, (string)time());

        // Conservar solo los últimos 14 respaldos automáticos para no llenar el disco
        $archivosAuto = glob($carpetaBackups . '/almanica_auto_*.sql');
        if ($archivosAuto && count($archivosAuto) > 14) {
            usort($archivosAuto, fn($a, $b) => filemtime($a) <=> filemtime($b));
            foreach (array_slice($archivosAuto, 0, count($archivosAuto) - 14) as $viejo) {
                @unlink($viejo);
            }
        }
    }
}

/**
 * Descarga el archivo .sql al navegador.
 */
function descargarRespaldoManual(PDO $conexion): void
{
    $rutaArchivo = guardarArchivoRespaldo($conexion, 'manual');

    if (is_file($rutaArchivo)) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
        header('Content-Length: ' . filesize($rutaArchivo));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($rutaArchivo);
        exit;
    }
}