<?php
if (!defined('ALMANICA_ENTRADA')) { http_response_code(404); exit; }
require_once __DIR__ . '/../inicio.php';
require_once __DIR__ . '/../compartido/seguridad.php';
if (isset($_SESSION['empleado']['usuario_id'])) { header('Location: panel.php'); exit; }
unset($_SESSION['empleado']);
$error = ''; $conectado = false; $espera = 0;
$nombreUsuario = trim((string) ($_POST['usuario'] ?? $_COOKIE['almanica_usuario'] ?? ''));
$recordar = $_SERVER['REQUEST_METHOD']==='POST' ? isset($_POST['recordar']) : isset($_COOKIE['almanica_usuario']);
try {
    $conexion = conectar();
    $conexion->query('SELECT Clave FROM LoginIntento LIMIT 1'); $conectado = true;
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!hash_equals($_SESSION['csrf'],(string) ($_POST['csrf'] ?? ''))) { http_response_code(403); $error='La sesión del formulario venció. Recarga la página.'; }
        elseif (mb_strlen($nombreUsuario)>50 || strlen((string) ($_POST['contrasena'] ?? ''))>255) { $error='Usuario o contraseña incorrectos.'; }
        else {
            $resultado=autenticar($conexion,$nombreUsuario,(string) ($_POST['contrasena'] ?? ''),$_SERVER['REMOTE_ADDR'] ?? 'local');
            $cuenta=$resultado['cuenta']; $espera=$resultado['espera'];
            if ($cuenta) {
                session_regenerate_id(true);
                $_SESSION['empleado']=['id'=>(int)$cuenta['Id_Empleado'],'usuario_id'=>(int)$cuenta['Id_Usuario'],'nombre'=>$cuenta['Nombre'],'rol'=>$cuenta['Rol']];
                $_SESSION['credencial']=hash('sha256',$cuenta['Contrasena']);
                $_SESSION['actividad']=time(); $_SESSION['csrf']=bin2hex(random_bytes(32));
                setcookie('almanica_usuario',$recordar?$nombreUsuario:'',['expires'=>$recordar?time()+2592000:time()-3600,'path'=>'/','httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax']);
                header('Location: panel.php'); exit;
            }
            $error = $espera>0 ? 'Demasiados intentos. Vuelve a intentarlo en ' . $espera . ' segundos.' : 'Usuario o contraseña incorrectos, o cuenta inactiva.';
            if ($espera>0) { http_response_code(429); header('Retry-After: ' . $espera); }
        }
    }
} catch (PDOException $e) { error_log($e->getMessage()); $error='No se pudo conectar al sistema. Revisa MySQL o solicita ayuda al administrador.'; }
require __DIR__ . '/../vistas/login.php';
