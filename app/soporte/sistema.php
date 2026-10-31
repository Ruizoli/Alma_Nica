<?php
require_once __DIR__ . '/../../config/roles.php';
function esAdmin(): bool { return ($_SESSION['empleado']['rol'] ?? '') === 'Administrador'; }
function puedeVer(string $modulo): bool
{
    return in_array($modulo, permisosRoles()[$_SESSION['empleado']['rol'] ?? '']['ver'] ?? [], true);
}
function puedeEditar(string $modulo): bool
{
    return puedeVer($modulo) && in_array($modulo, permisosRoles()[$_SESSION['empleado']['rol'] ?? '']['editar'] ?? [], true);
}
function moduloInicial(): string { return puedeVer('dashboard') ? 'dashboard' : 'productos'; }
function csrfValido(): bool { return hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? '')); }
function campoCsrf(): void { echo '<input type="hidden" name="csrf" value="' . escapar($_SESSION['csrf']) . '">'; }
function ruta(string $modulo, array $params = []): string { return 'panel.php?' . http_build_query(['modulo'=>$modulo] + $params); }
function volver(string $modulo, string $mensaje): never
{
    $_SESSION['mensaje'] = $mensaje;
    header('Location: ' . ruta($modulo));
    exit;
}
function dinero($valor): string { return 'C$ ' . importe($valor); }
function centavos(string $valor): int
{
    if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $valor)) { throw new InvalidArgumentException('Ingresa un importe positivo con un máximo de dos decimales.'); }
    $p = explode('.', $valor);
    return (int) $p[0] * 100 + (int) str_pad($p[1] ?? '', 2, '0');
}
function decimal(int $centavos): string { return intdiv($centavos, 100) . '.' . str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT); }
function entero($valor, int $min = 0): int
{
    $n = filter_var($valor, FILTER_VALIDATE_INT);
    if ($n === false || $n < $min || $n > 2147483647) { throw new InvalidArgumentException('Ingresa una cantidad entera válida.'); }
    return $n;
}
function errorBase(PDOException $e): string
{
    error_log($e->getMessage());
    return match ((int) ($e->errorInfo[1] ?? 0)) {
        1146, 1054 => 'Falta una tabla o columna. Ejecuta en la terminal: php database/actualizar_compras.php',
        1064 => 'La base rechazó una consulta por sintaxis. Revisa la versión de MySQL/MariaDB y el registro de errores.',
        1062 => 'Ya existe un registro con ese código, usuario o cédula.',
        1451 => 'No se puede eliminar: este registro está relacionado con otras operaciones.',
        1452 => 'El registro relacionado ya no está disponible. Actualiza la página.',
        default => 'No se pudo consultar o guardar la información. Revisa el registro de errores del servidor.',
    };
}
