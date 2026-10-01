'use strict';
document.addEventListener('submit', event => { const form = event.target.closest('form[data-confirm]'); if (form && !confirm(form.dataset.confirm)) event.preventDefault(); });
document.querySelectorAll('[data-print]').forEach(button => button.addEventListener('click', () => window.print()));
const form = document.querySelector('[data-operation]');
if (form) {
    const lines = form.querySelector('[data-lines]');
    const template = lines.querySelector('.order-line').cloneNode(true);
    const money = amount => 'C$ ' + (amount / 100).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    const cents = value => Math.round(Number(value || 0) * 100);
    const update = () => {
        let total = 0;
        lines.querySelectorAll('.order-line').forEach(line => {
            const option = line.querySelector('select').selectedOptions[0];
            const price = form.dataset.operation === 'compra' ? cents(line.querySelector('[name="costo[]"]').value) : cents(option?.dataset.price);
            const subtotal = price * Number(line.querySelector('[name="cantidad[]"]').value || 0);
            line.querySelector('.line-subtotal').textContent = money(subtotal);
            total += subtotal;
        });
        form.querySelector('[data-total]').textContent = money(total);
    };
    const add = (id = '') => {
        const line = template.cloneNode(true);
        line.querySelector('select').value = String(id);
        line.querySelector('[name="cantidad[]"]').value = 1;
        const cost = line.querySelector('[name="costo[]"]'); if (cost) cost.value = '0.00';
        lines.append(line); update(); return line;
    };
    form.querySelector('[data-add-line]').addEventListener('click', () => add());
    form.querySelector('[data-clear]')?.addEventListener('click', () => { if (confirm('¿Limpiar el detalle de esta operación?')) { lines.replaceChildren(); add(); } });
    form.querySelectorAll('[data-add-product]').forEach(button => button.addEventListener('click', () => {
        const id = button.dataset.addProduct;
        const existing = [...lines.querySelectorAll('select')].find(select => select.value === id);
        if (existing) { const qty = existing.closest('.order-line').querySelector('[name="cantidad[]"]'); qty.value = Number(qty.value) + 1; }
        else { const empty = [...lines.querySelectorAll('select')].find(select => !select.value); if (empty) { empty.value = id; empty.dispatchEvent(new Event('change', {bubbles:true})); } else add(id); }
        update();
    }));
    lines.addEventListener('click', event => { const button = event.target.closest('.remove-line'); if (button) { button.closest('.order-line').remove(); if (!lines.children.length) add(); update(); } });
    lines.addEventListener('input', update); lines.addEventListener('change', update);
    form.querySelector('[data-product-search]')?.addEventListener('input', event => {
        const q = event.target.value.toLocaleLowerCase().trim();
        const cards = [...form.querySelectorAll('[data-add-product]')];
        cards.forEach(card => { card.hidden = !card.dataset.search.includes(q); });
        form.querySelector('.product-no-results').hidden = !cards.length || cards.some(card => !card.hidden);
    });
    form.addEventListener('submit', () => { const button = form.querySelector('[type="submit"]'); button.disabled = true; button.textContent = 'Guardando…'; });
    update();
}
// Herramientas del diseño comercial.
document.querySelector('[data-toggle-menu]')?.addEventListener('click', event => {
    const hidden = document.body.classList.toggle('sidebar-collapsed');
    event.currentTarget.setAttribute('aria-expanded', String(!hidden));
});
document.querySelector('[data-receive-all]')?.addEventListener('click', () => {
    document.querySelectorAll('.receive-quantity').forEach(input => { input.value = input.max; });
});
const photoInput = document.querySelector('[data-photo-input]');
let photoUrl;
photoInput?.addEventListener('change', () => {
    const file = photoInput.files[0];
    if (!file) return;
    if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 2*1024*1024) {
        alert('Selecciona JPG, PNG o WebP de hasta 2 MB.'); photoInput.value = ''; return;
    }
    if (photoUrl) URL.revokeObjectURL(photoUrl);
    photoUrl = URL.createObjectURL(file);
    document.querySelector('[data-photo-preview]').src = photoUrl;
    const remove = document.querySelector('[name="quitar_foto"]'); if (remove) remove.checked = false;
});
