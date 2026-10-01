<?php
function icono(string $nombre): string
{
    $rutas = [
        'dashboard'=>'<path d="m3 10 9-7 9 7v10H3Z"/><path d="M9 20v-7h6v7"/>',
        'ventas'=>'<path d="M2 3h3l3 12h11l3-9H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>',
        'productos'=>'<path d="m3 7 9-4 9 4v11l-9 4-9-4Z"/><path d="m3 7 9 5 9-5M12 12v10M8 5l9 5"/>',
        'clientes'=>'<circle cx="9" cy="7" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M17 4a3 3 0 0 1 0 6M18 13a5 5 0 0 1 4 5v3"/>',
        'proveedores'=>'<path d="M2 5h12v13H2ZM14 10h5l3 4v4h-8"/><circle cx="6" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
        'compras'=>'<path d="M6 3h10l4 4v15H6Z"/><path d="M15 3v5h5M9 12h8M9 16h8"/>',
        'recepcion'=>'<path d="M2 5h12v13H2ZM14 10h5l3 4v4h-8M5 10l2 2 4-4"/><circle cx="6" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
        'historial'=>'<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 3"/>',
        'categorias'=>'<path d="M3 3h7l11 11-7 7L3 10Z"/><circle cx="7" cy="7" r="1"/>',
        'empleados'=>'<circle cx="12" cy="7" r="4"/><path d="M4 22v-3a8 8 0 0 1 16 0v3M9 15l3 4 3-4"/>',
        'usuarios'=>'<circle cx="9" cy="8" r="4"/><path d="M2 22v-3a7 7 0 0 1 12-5M17 14l2 2 4-4"/>',
        'reportes'=>'<path d="M4 20V10h4v10M10 20V4h4v16M16 20v-8h4v8"/>',
        'configuracion'=>'<circle cx="12" cy="12" r="3"/><path d="m9 3 1-2h4l1 2 3 2 2 1 2 4-2 2v3l1 2-3 3-3-1-2 2h-4l-1-2-3-1-2-4 2-2V9L3 7l3-3Z"/>',
        'buscar'=>'<circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/>',
        'menu'=>'<path d="M3 6h18M3 12h18M3 18h18"/>',
        'alertas'=>'<path d="M5 16h14l-2-3V8a5 5 0 0 0-10 0v5ZM9 20h6"/>',
    ];
    return '<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($rutas[$nombre] ?? $rutas['productos']).'</svg>';
}
