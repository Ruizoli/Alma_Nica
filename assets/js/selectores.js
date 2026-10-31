'use strict';
// Mejora progresiva: PHP sigue recibiendo los identificadores del select original.
(() => {
    let sequence = 0;
    const names = new Set(['producto_editor', 'proveedor', 'cliente', 'producto[]', 'Id_Proveedor', 'Id_Categoria', 'Id_Empleado']);
    const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase().trim();
    function enhance(select) {
        if (select.dataset.searchReady || !names.has(select.name) || select.form?.method !== 'post') return;
        select.dataset.searchReady = 'true';
        const required = select.required;
        const options = [...select.options].map(option => ({value: option.value, text: option.textContent.trim(), disabled: option.disabled}));
        const id = `buscar-opcion-${++sequence}`;
        const wrapper = document.createElement('div'); wrapper.className = 'search-choice';
        const input = document.createElement('input'); input.type = 'text'; input.autocomplete = 'off';
        input.required = required; input.disabled = select.disabled;
        input.placeholder = 'Escribe para buscar…';
        input.setAttribute('role', 'combobox'); input.setAttribute('aria-autocomplete', 'list');
        input.setAttribute('aria-expanded', 'false'); input.setAttribute('aria-controls', id);
        input.setAttribute('aria-label', select.closest('label')?.querySelector('span')?.textContent.trim() || 'Buscar registro');
        const list = document.createElement('div'); list.className = 'search-choice-list'; list.id = id;
        list.setAttribute('role', 'listbox'); list.hidden = true;
        const hint = document.createElement('small'); hint.className = 'search-choice-hint'; hint.id = `${id}-estado`; hint.setAttribute('role','status');
        input.setAttribute('aria-describedby', hint.id);
        select.before(wrapper); wrapper.append(input, list, hint, select);
        select.hidden = true; select.required = false; select.tabIndex = -1;
        let matches = [], active = -1;
        function close() { list.hidden = true; input.setAttribute('aria-expanded', 'false'); input.removeAttribute('aria-activedescendant'); }
        function sync() {
            input.value = select.value ? select.selectedOptions[0]?.textContent.trim() || '' : '';
            input.setCustomValidity('');
            hint.textContent = select.value ? 'Registro seleccionado' : required ? 'Escribe y elige una coincidencia.' : (options.find(o => !o.value)?.text || 'Opcional');
            close();
        }
        function choose(option) {
            select.value = option.value;
            select.dispatchEvent(new Event('change', {bubbles:true}));
            input.focus(); close();
        }
        function highlight() {
            [...list.querySelectorAll('[role="option"]')].forEach((item,index) => {
                item.setAttribute('aria-selected', String(index === active));
                if (index === active) { input.setAttribute('aria-activedescendant', item.id); item.scrollIntoView({block:'nearest'}); }
            });
        }
        function render() {
            const q = normalize(input.value);
            const all = options.filter(o => !o.disabled && (!o.value ? !required && !q : normalize(o.text).includes(q)));
            matches = all.slice(0,40); active = -1; list.replaceChildren();
            input.removeAttribute('aria-activedescendant');
            matches.forEach((option,index) => {
                const item = document.createElement('div'); item.setAttribute('role','option'); item.id = `${id}-${index}`;
                item.setAttribute('aria-selected','false'); item.textContent = option.text;
                item.addEventListener('mousedown', event => event.preventDefault());
                item.addEventListener('click', event => { event.preventDefault(); choose(option); });
                list.append(item);
            });
            if (!matches.length) { const empty = document.createElement('div'); empty.className='search-choice-empty'; empty.textContent='No hay coincidencias. Prueba otro nombre o código.'; list.append(empty); }
            hint.textContent = `${all.length} coincidencias${all.length>40?' · Escribe más para acotar la búsqueda.':''}`;
            list.hidden = false; input.setAttribute('aria-expanded','true');
        }
        input.addEventListener('focus', () => { input.select(); render(); });
        input.addEventListener('input', () => {
            // Nunca conservar el identificador anterior si se cambia el texto.
            select.value = ''; select.dispatchEvent(new Event('input',{bubbles:true}));
            input.setCustomValidity(input.value.trim() ? 'Elige una coincidencia de la lista.' : ''); render();
        });
        input.addEventListener('keydown', event => {
            if (event.key === 'Escape') { close(); return; }
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault(); if (list.hidden) render();
                active = matches.length ? (active + (event.key==='ArrowDown'?1:-1) + matches.length) % matches.length : -1; highlight();
            } else if (event.key === 'Enter' && !list.hidden) {
                event.preventDefault(); if (matches[active]) choose(matches[active]); else if (matches.length===1) choose(matches[0]);
            }
        });
        input.addEventListener('blur', close);
        select.addEventListener('change', sync);
        select.form?.addEventListener('reset', () => setTimeout(sync,0));
        sync();
    }
    function scan(root) { root.querySelectorAll('select').forEach(enhance); }
    scan(document);
    new MutationObserver(records => records.forEach(record => record.addedNodes.forEach(node => {
        if (node.nodeType!==1) return;
        if (node.matches('select')) enhance(node);
        scan(node);
    }))).observe(document.body,{childList:true,subtree:true});
})();
