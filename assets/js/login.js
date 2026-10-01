const clave = document.querySelector('#contrasena');
const verClave = document.querySelector('.ver-clave');
verClave.addEventListener('click', () => {
    const mostrar = clave.type === 'password';
    clave.type = mostrar ? 'text' : 'password';
    verClave.setAttribute('aria-pressed', String(mostrar));
    verClave.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
    verClave.querySelector('.tachado').style.display = mostrar ? 'none' : '';
});
const recordar = document.querySelector('[name="recordar"]');
const aviso = document.querySelector('.ayuda-recordar');
const actualizarAviso = () => { aviso.hidden = !recordar.checked; };
recordar.addEventListener('change', actualizarAviso);
actualizarAviso();
const recuperacion = document.querySelector('#recuperacion');
document.querySelector('.recuperar').addEventListener('click', () => recuperacion.showModal());
const lockMessage = document.querySelector('[data-wait]');
if (lockMessage && Number(lockMessage.dataset.wait) > 0) {
    let remaining = Number(lockMessage.dataset.wait);
    const timer = setInterval(() => {
        remaining--;
        lockMessage.textContent = remaining > 0 ? `Demasiados intentos. Vuelve a intentarlo en ${remaining} segundos.` : 'Ya puedes volver a intentar iniciar sesión.';
        if (remaining <= 0) { clearInterval(timer); document.querySelector('.entrar[type="submit"]').disabled = false; }
    }, 1000);
}
