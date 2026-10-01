'use strict';
// Las respuestas anteriores nunca deben reemplazar una búsqueda más reciente.
document.querySelectorAll('[data-live-search], [data-global-search]').forEach(form => {
    const global = form.hasAttribute('data-global-search');
    const input = form.querySelector('input[name="q"]');
    let timer, controller, version = 0;
    const status = document.createElement('span');
    status.className = 'search-status'; status.setAttribute('role', 'status');
    form.append(status);
    const popup = global ? document.createElement('div') : null;
    if (popup) { popup.className = 'search-popup'; popup.hidden = true; form.append(popup); }
    const region = () => document.querySelector(`[data-live-region="${form.dataset.liveSearch}"]`);
    async function search(href) {
        clearTimeout(timer); controller?.abort();
        const current = ++version;
        const url = new URL(href || form.action, location.href);
        if (!href) { url.search = new URLSearchParams(new FormData(form)).toString(); url.searchParams.delete('pagina'); }
        if (global && !input.value.trim()) { popup.hidden = true; status.textContent = ''; return; }
        controller = new AbortController(); status.textContent = 'Buscando…';
        region()?.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, {signal: controller.signal, headers: {'X-Requested-With':'fetch'}});
            if (!response.ok) throw new Error('response');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (current !== version) return;
            if (doc.querySelector('.login-form')) { status.textContent = 'Tu sesión venció. Recarga para iniciar sesión.'; return; }
            if (global) {
                const results = doc.querySelector('.page .card-box');
                if (!results) throw new Error('results');
                popup.replaceChildren(...results.childNodes); popup.hidden = false;
            } else {
                const updated = doc.querySelector(`[data-live-region="${form.dataset.liveSearch}"]`);
                const target = region();
                if (!updated || !target) throw new Error('results');
                target.replaceChildren(...updated.childNodes);
                history.replaceState(null, '', url);
                // Exportar conserva exactamente los filtros consultados.
                const exportLink = form.querySelector('a[href*="csv="]');
                if (exportLink) { const csv = new URL(url); csv.searchParams.set('csv','1'); exportLink.href = csv; }
            }
            status.textContent = 'Resultados actualizados';
        } catch (error) {
            if (error.name !== 'AbortError' && current === version) status.textContent = 'No se pudo buscar. Presiona Buscar para recargar.';
        } finally { if (current === version) region()?.removeAttribute('aria-busy'); }
    }
    const schedule = () => { clearTimeout(timer); controller?.abort(); version++; timer = setTimeout(() => search(), 280); };
    input?.addEventListener('input', schedule);
    form.querySelectorAll('select,input[type="date"]').forEach(field => field.addEventListener('change', () => { if (form.reportValidity()) search(); }));
    // El botón conserva la navegación normal como alternativa sin JavaScript o sin red.
    if (!global) document.addEventListener('click', event => {
        const link = event.target.closest('[data-live-region] .pagination-bar a');
        if (link && region()?.contains(link) && !event.ctrlKey && !event.metaKey) { event.preventDefault(); search(link.href); }
    });
    if (global) {
        document.addEventListener('click', event => { if (!form.contains(event.target)) popup.hidden = true; });
        input.addEventListener('keydown', event => { if (event.key === 'Escape') popup.hidden = true; });
    }
});
