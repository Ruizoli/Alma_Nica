<?php
// Incluido por sistema.php después de validar sesión, permisos y CSRF.
defined('ALMANICA') || exit;

$vista = 'configuracion';
require_once __DIR__ . '/modelo.php';
$perfil = obtenerPerfil($conexion, (int)$_SESSION['empleado']['usuario_id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../compartido/seguridad.php';
    $q = $conexion->prepare('SELECT Usuario FROM Usuario WHERE Id_Usuario=?'); $q->execute([$_SESSION['empleado']['usuario_id']]); $usuarioActual = $q->fetchColumn();
    $resultado = autenticar($conexion,$usuarioActual,(string) ($_POST['actual'] ?? ''),$_SERVER['REMOTE_ADDR'] ?? 'local');
    if (!$resultado['cuenta']) { throw new InvalidArgumentException($resultado['espera'] ? 'Espera ' . $resultado['espera'] . ' segundos antes de intentarlo nuevamente.' : 'La contraseña actual no es correcta.'); }
    if (($_POST['accion_cuenta'] ?? '') === 'perfil') {
        guardarPerfil($conexion,(int)$_SESSION['empleado']['usuario_id'],(string)($_POST['usuario'] ?? ''),(string)($_POST['nombre'] ?? ''));
        $_SESSION['empleado']['nombre']=trim((string)$_POST['nombre']);
        session_regenerate_id(true);
        volver($modulo,'Nombre y usuario actualizados. Utiliza tu nuevo usuario la próxima vez que inicies sesión.');
    }
    $nueva = (string) ($_POST['nueva'] ?? '');
    if (strlen($nueva)<8 || strlen($nueva)>72 || $nueva !== ($_POST['confirmar'] ?? '')) { throw new InvalidArgumentException('Usa entre 8 y 72 bytes y confirma la misma contraseña.'); }
    $nuevoHash = password_hash($nueva,PASSWORD_DEFAULT);
    $q = $conexion->prepare('UPDATE Usuario SET Contrasena=? WHERE Id_Usuario=?'); $q->execute([$nuevoHash,$_SESSION['empleado']['usuario_id']]);
    $_SESSION['credencial'] = hash('sha256',$nuevoHash);
    session_regenerate_id(true); volver($modulo,'Contraseña actualizada.');
}
$vista = 'configuracion';
