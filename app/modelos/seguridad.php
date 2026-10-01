<?php
require_once __DIR__ . '/usuario.php';
/** Bloqueos persistentes por cuenta e IP, serializados para intentos concurrentes. */
function autenticar(PDO $db, string $usuario, string $clave, string $ip): array
{
    // El ID canónico evita evadir el contador variando mayúsculas o acentos.
    $cuenta = buscarUsuario($db, $usuario);
    $identidad = $cuenta ? 'id:' . $cuenta['Id_Usuario'] : 'nombre:' . mb_strtolower($usuario);
    $claves = [hash('sha256', 'cuenta:' . $identidad)=>3, hash('sha256', 'ip:' . $ip)=>15];
    ksort($claves);
    $ahora = time(); $filas = [];
    $db->beginTransaction();
    try {
        foreach ($claves as $key=>$limite) {
            $q = $db->prepare('INSERT IGNORE INTO LoginIntento (Clave, Actualizado) VALUES (?, ?)'); $q->execute([$key,$ahora]);
            $q = $db->prepare('SELECT * FROM LoginIntento WHERE Clave=? FOR UPDATE'); $q->execute([$key]); $filas[$key] = $q->fetch();
        }
        $espera = max(0, ...array_map(fn($r) => (int) $r['Bloqueado_Hasta'] - $ahora, array_values($filas)));
        if ($espera > 0) { $db->commit(); return ['cuenta'=>false,'espera'=>$espera]; }
        $hash = $cuenta['Contrasena'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $valida = password_verify($clave, $hash);
        if ($valida && $cuenta && (int) $cuenta['Estado'] === 1 && (int) $cuenta['Empleado_Activo'] === 1) {
            $q = $db->prepare('DELETE FROM LoginIntento WHERE Clave=?'); $q->execute([hash('sha256', 'cuenta:' . $identidad)]);
            $db->commit(); return ['cuenta'=>$cuenta,'espera'=>0];
        }
        foreach ($claves as $key=>$limite) {
            $r = $filas[$key];
            $fallos = ((int) $r['Bloqueado_Hasta'] > 0 || $ahora - (int) $r['Actualizado'] > 900) ? 1 : (int) $r['Fallos'] + 1;
            $bloqueo = $fallos >= $limite ? $ahora + 60 : 0;
            $q = $db->prepare('UPDATE LoginIntento SET Fallos=?, Bloqueado_Hasta=?, Actualizado=? WHERE Clave=?'); $q->execute([$fallos,$bloqueo,$ahora,$key]);
            $espera = max($espera, $bloqueo - $ahora);
        }
        $db->commit(); return ['cuenta'=>false,'espera'=>$espera];
    } catch (Throwable $e) { if ($db->inTransaction()) { $db->rollBack(); } throw $e; }
}
