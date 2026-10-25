<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

require_once __DIR__ . '/modelo.php';
$tipoReporte = (string) ($_GET['tipo'] ?? 'ventas'); $desde = (string) ($_GET['desde'] ?? date('Y-m-01')); $hasta = (string) ($_GET['hasta'] ?? date('Y-m-d'));
$datosReporte = reporte($conexion,$tipoReporte,$desde,$hasta);
$textoReporte = trim((string)($_GET['q'] ?? ''));
if ($textoReporte !== '') $datosReporte = array_values(array_filter($datosReporte, function ($fila) use ($textoReporte) {
    if (isset($fila['Fecha'])) $fila['Fecha'] = fechaCorta($fila['Fecha']);
    return mb_stripos(implode(' ', $fila), $textoReporte) !== false;
}));
foreach ($datosReporte as &$filaReporte) if (isset($filaReporte['Fecha'])) $filaReporte['Fecha'] = fechaCorta($filaReporte['Fecha']);
unset($filaReporte);
if (isset($_GET['csv'])) {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="almanica-' . $tipoReporte . '.csv"');
    $salida = fopen('php://output','w'); fwrite($salida,"\xEF\xBB\xBF");
    if ($datosReporte) { fputcsv($salida,array_keys($datosReporte[0]),';'); }
    foreach ($datosReporte as $fila) { fputcsv($salida,array_map(fn($v) => preg_match('/^[=+@\-\t\r\n]/',(string) $v) ? "'" . $v : $v,$fila),';'); }
    fclose($salida); exit;
}
$vista = 'reportes';
