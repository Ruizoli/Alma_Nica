<?php
defined('ALMANICA') || exit;
[$label, $tipo, $requerido] = $def['campos'][$nombre];
$valor = $tipo==='password'?'':($registro[$nombre] ?? ($nombre==='Estado'?1:($nombre==='Stock_Minimo'?5:($tipo==='integer'?0:''))));
?>
<label class="field <?= $tipo==='textarea'?'wide':'' ?>">
<span><?= escapar($label) ?><?= $requerido?' *':'' ?>
</span>
<?php if (isset($opciones[$tipo])): ?>
<select name="<?= escapar($nombre) ?>" <?= $requerido?'required':'' ?>>
<?php if (!$requerido): ?>
<option value="">Sin asignar</option>
<?php endif; ?>
<?php foreach ($opciones[$tipo] as $key=>$texto): ?>
<option value="<?= escapar($key) ?>" <?= (string)$valor===(string)$key?'selected':'' ?>><?= escapar($texto) ?>
</option>
<?php endforeach; ?>
</select>
<?php elseif ($tipo==='textarea'): ?>
<textarea name="<?= escapar($nombre) ?>" rows="3" maxlength="5000"><?= escapar($valor) ?>
</textarea>
<?php else: ?>
<input name="<?= escapar($nombre) ?>" type="<?= in_array($tipo,['integer','money'],true)?'number':escapar($tipo) ?>" value="<?= escapar($valor) ?>" <?= $tipo==='password'?'autocomplete="new-password"':'' ?> <?= $requerido && !($tipo==='password' && $accion==='editar')?'required':'' ?> <?= in_array($tipo,['integer','money'],true)?'min="0" max="'.($tipo==='integer'?'2147483647':'99999999.99').'" step="'.($tipo==='money'?'0.01':'1').'"':'' ?> <?= isset($def['campos'][$nombre][3])?'maxlength="'.(int)$def['campos'][$nombre][3].'"':'' ?> <?= $nombre==='Stock_Actual' && $accion==='editar'?'readonly':'' ?>>
<?php endif; ?>
<?php if ($tipo==='password' && $accion==='editar'): ?>
<small>Déjala vacía para conservar la contraseña.</small>
<?php endif; ?>
<?php if ($nombre==='Stock_Actual'): ?>
<small>Después de crear el producto, el stock cambia con compras y ventas.</small>
<?php endif; ?>
</label>
