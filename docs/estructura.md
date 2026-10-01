# Organización de AlmaNica

- Entradas públicas: index.php, panel.php, salir.php e imagen.php.
- app/controladores: coordinación de solicitudes.
- app/modelos: consultas y reglas del negocio.
- app/vistas: plantillas y parciales de interfaz.
- app/soporte y app/middleware: utilidades, sesiones y acceso.
- config: conexión y definición de catálogos.
- assets/css, assets/js, assets/img: presentación y recursos del navegador.
- database: migraciones necesarias para instalar el sistema.
- storage/productos: fotos cargadas; no eliminar.
- tests: comprobaciones de integración en una base aislada.
- docs: documentación del proyecto.

El acceso tiene una sola hoja específica: assets/css/login.css.
assets/css/marca.css comparte la marca entre pantallas.
El fondo fotográfico está en assets/img/login-nicaragua.png.
paisaje.svg sigue en uso en el dashboard.
Los archivos .htaccess protegen las carpetas internas en Apache.
