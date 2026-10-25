<?php
require_once __DIR__ . '/../../config/catalogos.php';
require_once __DIR__ . '/../../config/roles.php';
function opciones(PDO $db): array
{
    return [
        'categoria'=>$db->query('SELECT Id_Categoria, Nombre FROM Categoria ORDER BY Nombre')->fetchAll(PDO::FETCH_KEY_PAIR),
        'proveedor'=>$db->query('SELECT Id_Proveedor, Nombre FROM Proveedor ORDER BY Nombre')->fetchAll(PDO::FETCH_KEY_PAIR),
        'empleado'=>$db->query('SELECT Id_Empleado, Nombre FROM Empleado ORDER BY Nombre')->fetchAll(PDO::FETCH_KEY_PAIR),
        'rol'=>rolesDisponibles(),
        'estado'=>[1=>'Activo',0=>'Inactivo'],
    ];
}
function listarCatalogo(PDO $db, array $def, string $busqueda, int $pagina, array $filtros): array
{
    $where = []; $params = [];
    if ($busqueda !== '') {
        $partes = [];
        foreach ($def['buscar'] as $campo) { $partes[] = "`$campo` LIKE ?"; $params[] = '%' . $busqueda . '%'; }
        $where[] = '(' . implode(' OR ', $partes) . ')';
    }
    if ($def['tabla'] === 'Producto') {
        foreach (['Id_Categoria','Id_Proveedor'] as $campo) {
            if (!empty($filtros[$campo])) { $where[] = "`$campo` = ?"; $params[] = entero($filtros[$campo], 1); }
        }
    }
    if ($def['tabla']==='Producto' && !empty($filtros['bajo'])) $where[]='Stock_Actual <= COALESCE(Stock_Minimo,5)';
    $sql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $q = $db->prepare("SELECT COUNT(*) FROM `{$def['tabla']}`" . $sql); $q->execute($params); $total = (int) $q->fetchColumn();
    $paginas = max(1, (int) ceil($total / 10)); $pagina = min($paginas, max(1, $pagina)); $offset = ($pagina - 1) * 10;
    $campos = array_unique(array_merge([$def['id']], array_keys($def['campos'])));
    if ($def['tabla'] === 'Producto') { $campos[] = 'Imagen'; }
    $campos = array_filter($campos, fn($c) => $c !== 'Contrasena');
    $seleccion = implode(', ', array_map(fn($c) => "`$c`", $campos));
    $q = $db->prepare("SELECT $seleccion FROM `{$def['tabla']}` $sql ORDER BY `{$def['id']}` DESC LIMIT 10 OFFSET $offset"); $q->execute($params);
    return ['filas'=>$q->fetchAll(),'total'=>$total,'pagina'=>$pagina,'paginas'=>$paginas];
}
function registroCatalogo(PDO $db, array $def, int $id): array
{
    $q = $db->prepare("SELECT * FROM `{$def['tabla']}` WHERE `{$def['id']}` = ?"); $q->execute([$id]);
    $fila = $q->fetch();
    if (!$fila) { throw new InvalidArgumentException('El registro solicitado no existe.'); }
    unset($fila['Contrasena']);
    return $fila;
}
function guardarCatalogo(PDO $db, string $modulo, array $entrada, int $id, ?string $imagen = null): int
{
    $def = catalogos()[$modulo]; $datos = []; $opts = opciones($db);
    foreach ($def['campos'] as $nombre => $campo) {
        [$etiqueta,$tipo,$requerido] = $campo;
        if ($modulo === 'productos' && $id && $nombre === 'Stock_Actual') { continue; }
        $valor = trim((string) ($entrada[$nombre] ?? ''));
        if ($tipo === 'password' && $id && $valor === '') { continue; }
        if ($requerido && $valor === '') { throw new InvalidArgumentException("Completa el campo $etiqueta."); }
        if ($valor === '') { $datos[$nombre] = null; continue; }
        if (isset($campo[3]) && mb_strlen($valor) > $campo[3]) { throw new InvalidArgumentException("El campo $etiqueta es demasiado largo."); }
        if ($tipo === 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) { throw new InvalidArgumentException('El correo no es válido.'); }
        if (isset($opts[$tipo]) && !array_key_exists($valor, $opts[$tipo])) { throw new InvalidArgumentException("Selecciona un valor válido en $etiqueta."); }
        if ($tipo === 'integer') { $valor = entero($valor); }
        if ($tipo === 'money') { $valor = decimal(centavos($valor)); }
        if ($tipo === 'password') {
            if (strlen($valor) < 8 || strlen($valor) > 72) { throw new InvalidArgumentException('La contraseña debe tener entre 8 y 72 caracteres ASCII o bytes UTF-8.'); }
            $valor = password_hash($valor, PASSWORD_DEFAULT);
        }
        $datos[$nombre] = $valor;
    }
    if ($modulo === 'productos' && $imagen !== null) { $datos['Imagen'] = $imagen ?: null; }
    $db->beginTransaction();
    try {
        // Serializa los cambios de cuentas para conservar al menos un administrador activo.
        if (in_array($modulo, ['usuarios','empleados'], true)) {
            $db->query('SELECT Id_Empleado FROM Empleado ORDER BY Id_Empleado FOR UPDATE')->fetchAll();
            $db->query('SELECT Id_Usuario FROM Usuario ORDER BY Id_Usuario FOR UPDATE')->fetchAll();
        }
        if ($id) { registroCatalogo($db, $def, $id); }
        if ($modulo === 'usuarios' && $id === (int) $_SESSION['empleado']['usuario_id'] && ((int) $datos['Estado'] !== 1 || $datos['Rol'] !== 'Administrador' || (int) $datos['Id_Empleado'] !== (int) $_SESSION['empleado']['id'])) { throw new InvalidArgumentException('No puedes desactivar tu propia cuenta ni cambiar su rol o empleado desde aquí.'); }
        if ($modulo === 'empleados' && $id === (int) $_SESSION['empleado']['id'] && (int) $datos['Estado'] !== 1) { throw new InvalidArgumentException('No puedes desactivar tu propio empleado.'); }
        if ($id) {
            $set = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($datos)));
            $q = $db->prepare("UPDATE `{$def['tabla']}` SET $set WHERE `{$def['id']}` = ?"); $q->execute([...array_values($datos), $id]);
        } else {
            $cols = implode('`, `', array_keys($datos)); $marcas = implode(',', array_fill(0, count($datos), '?'));
            $q = $db->prepare("INSERT INTO `{$def['tabla']}` (`$cols`) VALUES ($marcas)"); $q->execute(array_values($datos));
        }
        if (in_array($modulo, ['usuarios','empleados'], true) && !(int) $db->query("SELECT COUNT(*) FROM Usuario u JOIN Empleado e ON e.Id_Empleado=u.Id_Empleado WHERE u.Rol='Administrador' AND u.Estado=1 AND e.Estado=1")->fetchColumn()) { throw new InvalidArgumentException('Debe quedar al menos un administrador activo.'); }
        $guardado = $id ?: (int) $db->lastInsertId(); $db->commit(); return $guardado;
    } catch (Throwable $e) { $db->rollBack(); throw $e; }
}
function eliminarCatalogo(PDO $db, string $modulo, int $id): void
{
    if (in_array($modulo, ['usuarios','empleados'], true)) { throw new InvalidArgumentException('Usa Editar para desactivar este registro y conservar su historial.'); }
    $def = catalogos()[$modulo];
    $q = $db->prepare("DELETE FROM `{$def['tabla']}` WHERE `{$def['id']}` = ?"); $q->execute([$id]);
}
