<?php defined('ALMANICA') || exit; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AlmaNica | Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">

    <script src="assets/js/login.js" defer>
</script>
<link href="assets/css/marca.css?v=1" rel="stylesheet"><link href="assets/css/login.css?v=3" rel="stylesheet"></head>
<body class="pagina-login">
<div class="login-layout">
<section class="login-intro">
<?php require __DIR__ . "/parciales/marca.php"; ?>
<ul class="login-benefits" aria-label="Funciones del sistema">
<li><?= icono('ventas') ?><span>Ventas<br>más rápidas</span></li>
<li><?= icono('productos') ?><span>Control<br>de inventario</span></li>
<li><?= icono('compras') ?><span>Órdenes<br>de compra</span></li>
<li><?= icono('clientes') ?><span>Tu negocio<br>en un solo sistema</span></li>
</ul></section>
<main class="login-card" aria-labelledby="bienvenido">
    <header class="identidad">
<?php require __DIR__ . '/parciales/marca.php'; ?>
</header>
    <h1 id="bienvenido">Bienvenido</h1>
    <p class="introduccion">Inicia sesión para acceder al sistema</p>
    <?php if ($error): ?>
<div class="mensaje-error" role="alert" data-wait="<?= (int)$espera ?>"><?= escapar($error) ?>
</div>
<?php endif; ?>
    <form method="post" class="login-form">
        <input type="hidden" name="csrf" value="<?= escapar($_SESSION['csrf']) ?>">
        <div class="campo">
            <svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-5 0-9 3-9 7v1h18v-1c0-4-4-7-9-7Z"/>
</svg>
            <label class="solo-lector" for="usuario">Usuario</label>
            <input id="usuario" name="usuario" type="text" placeholder="Usuario" autocomplete="username" maxlength="50" required value="<?= escapar($nombreUsuario) ?>">
        </div>
        <div class="campo">
            <svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M6 9V6a6 6 0 0 1 12 0v3h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V11a2 2 0 0 1 2-2h1Zm3 0h6V6a3 3 0 0 0-6 0v3Zm3 4a2 2 0 0 0-1 3.73V19h2v-2.27A2 2 0 0 0 12 13Z"/>
</svg>
            <label class="solo-lector" for="contrasena">Contraseña</label>
            <input id="contrasena" name="contrasena" type="password" placeholder="Contraseña" autocomplete="current-password" required>
            <button class="ver-clave" type="button" aria-label="Mostrar contraseña" aria-pressed="false" aria-controls="contrasena">
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
<circle cx="12" cy="12" r="3"/>
<path class="tachado" d="m3 3 18 18"/>
</svg>
</button>
        </div>
        <div class="opciones">
            <label class="recordar" title="Guardar mi nombre de usuario durante 30 días">
<input type="checkbox" name="recordar" value="1" <?= $recordar ? 'checked' : '' ?>> Recordarme</label>
            <button class="recuperar" type="button">¿Olvidaste tu contraseña?</button>
        </div>
        <p class="ayuda-recordar" hidden>Se recordará tu usuario en este navegador, no tu contraseña.</p>
        <button class="entrar" type="submit" <?= (!$conectado || $espera > 0) ? 'disabled' : '' ?>>
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
<path d="M14 3h6v18h-6M3 12h12m-5-5 5 5-5 5"/>
</svg>Iniciar Sesión</button>
    </form>
    <footer class="pie-login">
<div class="login-trust">
<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m12 2 8 3v6c0 5-8 11-8 11S4 16 4 11V5Z"/><path d="m8 11 3 3 5-6"/></svg>Seguro</span>
<span><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m13 1-9 13h7l-1 9 10-14h-7Z"/></svg>Rápido</span>
<span><?= icono('reportes') ?>Confiable</span>
</div></footer>
</main>
</div>
<svg class="login-waves" viewBox="0 0 1440 130" preserveAspectRatio="none" aria-hidden="true">
<path fill="#efa119" d="M0 0C180 0 230 125 430 90S580 20 760 85L920 130H0Z"/>
<path fill="#07709b" d="M0 28C180 10 260 133 440 105S600 46 795 122L840 130H0Z"/>
<path fill="#073b59" d="M0 68C190 23 245 141 470 112S600 67 780 130H0Z"/>
</svg>
<dialog id="recuperacion" aria-labelledby="titulo-recuperacion">
    <h2 id="titulo-recuperacion">Recuperar acceso</h2>
    <p>Solicita al administrador del sistema que restablezca tu contraseña. Indícale tu nombre de usuario.</p>
    <p>La recuperación por correo todavía no está configurada.</p>
    <form method="dialog">
<button class="entrar">Entendido</button>
</form>
</dialog>
</body>
</html>
