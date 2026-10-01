<?php defined('ALMANICA') || exit; ?>
<div class="two-columns">
<section class="card-box">
<h2>Mi cuenta</h2>
<p class="muted">Personaliza tu nombre y tu usuario de acceso.</p>
<form method="post" class="stack">
<?php campoCsrf(); ?>
<input type="hidden" name="accion_cuenta" value="perfil">
<label class="field"><span>Nombre visible</span><input name="nombre" maxlength="100" required value="<?= escapar(($_POST['accion_cuenta'] ?? '')==='perfil'?($_POST['nombre'] ?? ''):($perfil['Nombre'] ?? '')) ?>"><small>Se mostrará en el saludo y en tus operaciones.</small></label>
<label class="field"><span>Usuario de acceso</span><input name="usuario" autocomplete="username" minlength="3" maxlength="50" pattern="[a-zA-Z0-9_.\-]{3,50}" required value="<?= escapar(($_POST['accion_cuenta'] ?? '')==='perfil'?($_POST['usuario'] ?? ''):($perfil['Usuario'] ?? '')) ?>"><small>Letras, números, puntos, guiones o guiones bajos.</small></label>
<label class="field"><span>Contraseña actual para confirmar</span><input name="actual" type="password" autocomplete="current-password" required></label>
<button class="button">Guardar mi perfil</button>
</form>
<hr><h2>Cambiar contraseña</h2>
<form method="post" class="stack">
<?php campoCsrf(); ?>
<label class="field">
<span>Contraseña actual</span>
<input name="actual" type="password" autocomplete="current-password" required>
</label>
<label class="field">
<span>Nueva contraseña</span>
<input name="nueva" type="password" autocomplete="new-password" minlength="8" maxlength="72" required>
</label>
<label class="field">
<span>Confirmar nueva contraseña</span>
<input name="confirmar" type="password" autocomplete="new-password" minlength="8" maxlength="72" required>
</label>
<button class="button">Actualizar contraseña</button>
</form>
</section>
<section class="card-box">
<h2>Configuración del sistema</h2>
<dl class="settings-list">
<dt>Nombre comercial</dt>
<dd>AlmaNica</dd>
<dt>Moneda</dt>
<dd>Córdoba nicaragüense · C$</dd>
<dt>Zona horaria</dt>
<dd>America/Managua</dd>
<dt>Protección del acceso</dt>
<dd>3 intentos incorrectos → bloqueo de 60 segundos.</dd>
<dt>Sesión</dt>
<dd>Se cierra tras 30 minutos sin actividad.</dd>
</dl>

</section>
</div>
