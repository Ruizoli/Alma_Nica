# AlmaNica — Ventas e inventario

Aplicación local en PHP 8.2, PDO, MySQL/MariaDB, JavaScript y Bootstrap 5.

## Inicio con XAMPP

Iniciar Apache y MySQL en XAMPP y abrir:
http://localhost/Alma_Nica/index.php

En este equipo, C:/xampp/htdocs/Alma_Nica es un enlace de directorio a
C:/Users/Oliver/Desktop/Alma_Nica. Se edita el proyecto del Escritorio;
ambas rutas muestran los mismos archivos. No hace falta iniciar php -S.
Al trasladarlo a otro equipo, copiar el proyecto completo a la carpeta
htdocs/Alma_Nica e importar la base de datos.

La conexión se configura en config/base_datos.php.
Las carpetas internas bloquean el acceso directo mediante .htaccess.
Bootstrap se carga desde CDN; los estilos propios y buscadores son locales.

## Campos con búsqueda

En órdenes, ventas y formularios de productos puedes escribir para buscar
proveedor, cliente, producto o categoría. Elige una coincidencia con el mouse,
o usa flechas y Enter. Se conservan los identificadores originales al guardar.
Escribir un nombre sin elegir un registro no permite enviar un dato ambiguo.
Vaciar un cliente opcional utiliza Consumidor final. Sin JavaScript se conserva
el selector estándar.
## Módulos

- Dashboard: indicadores, gráfico semanal, alertas y últimas ventas.
- Productos: crear, editar datos, buscar, filtrar y eliminar si no existe historial relacionado.
- Categorías, clientes y proveedores: crear, editar, buscar y eliminar según relaciones.
- Ventas: productos, cantidades, cliente opcional, pago, registro, detalle e impresión.
- Compras: órdenes pendientes, detalle, recepción y cancelación de pendientes.
- Empleados y usuarios: crear, editar, activar/desactivar, asignar roles y contraseñas.
- Reportes: ventas, compras, productos y clientes; fechas, exportación CSV e impresión.
- Configuración: contraseña propia e información de los ajustes.

Catálogos e historiales paginados de diez en diez. Reportes de hasta 5,000 registros
por consulta. El reporte de productos refleja las existencias actuales.

## Inventario

El stock inicial se establece al crear el producto. Posteriormente cambia mediante
ventas y recepción de compras. Registrar una venta descuenta stock; crear una compra
pendiente no lo modifica. Cada recepción suma solo las cantidades recibidas; al recibir
todo se completa la orden. Las órdenes parcialmente recibidas no se cancelan ni editan.
Las operaciones usan transacciones y bloqueos de filas. El servidor valida existencias
y precios, calcula en centavos y rechaza el reenvío de formularios ya procesados.

No se implementan devoluciones, anulaciones de ventas, descuentos, impuestos ni ajustes
manuales en esta versión. Los productos admiten fotos JPG, PNG y WebP de hasta 2 MB. La recuperación de contraseña por correo está pendiente.

## Permisos y seguridad

- Administrador: todos los módulos.
- Inventarista: productos, categorías, proveedores, compras, recepciones e historial; sin eliminación de registros.
- Vendedor y Cajero: dashboard, ventas, clientes, consulta de productos y contraseña propia.
- Permisos verificados en PHP, no solo en el menú.
- Usuarios y empleados se desactivan para mantener el historial.
- Protección del administrador activo y de la cuenta propia.
- Tres intentos incorrectos de una cuenta bloquean el acceso durante 60 segundos.
- Límite adicional de quince fallos por IP. Los contadores persisten en LoginIntento.
- Variar mayúsculas o acentos de una cuenta no evita el contador.
- Sesión de 30 minutos de inactividad, regeneración de identificador, cookies HttpOnly/SameSite.
- password_hash/password_verify y tokens CSRF para operaciones que escriben datos.
- Cambiar una contraseña invalida las otras sesiones en su siguiente acceso.
- Recordarme guarda solo el nombre de usuario durante treinta días.

`database/001_seguridad.sql` agrega la tabla de intentos sin borrar tablas del inventario.
La migración ya está aplicada a la base local al entregar el proyecto.

## Organización

```text
app/
  controladores/   acciones y coordinación de módulos
  modelos/        consultas y reglas de guardado
  vistas/         pantallas y parciales comunes
  middleware/     verificación de cuenta y sesión
  soporte/        funciones, permisos y formatos
  inicio.php      inicialización común
assets/
  css/            estilos generales, login y sistema
  js/             comportamiento del login y operaciones
  img/            emblema, fondo y recursos gráficos
config/
  base_datos.php  credenciales locales
  conexion.php    función conectar()
  catalogos.php   campos de los catálogos
database/        migraciones SQL
tests/           pruebas de integración por CLI
index.php         entrada del login
panel.php         sistema: ?modulo=productos, ventas, etc.
salir.php         cierre de sesión por POST
```

Para ampliar un catálogo, editar app/modulos/<modulo>/campos.php y formulario.php. Para un flujo nuevo, agregar
modelo y vista, y conectarlos al controlador del sistema. Las consultas dinámicas
usan nombres de tablas y columnas de la configuración interna, nunca del navegador.

## Pruebas

`/c/xampp/php/php.exe tests/integracion.php` crea una base aislada con nombre
`almanica_pruebas_<identificador>` y comprueba catálogos, stock, operaciones, reportes
y bloqueo del login. Las migraciones 001, 002 y 003 se instalan en esa base temporal. No copia datos reales. Requiere permiso de creación de bases.
La base temporal se elimina al terminar; puede conservarse expresamente con la variable
`ALMANICA_KEEP_TEST_DB=1` para pruebas HTTP. `ALMANICA_DB` permite seleccionar la base
de un servidor temporal. Los tests están bloqueados desde el navegador.

Antes de publicar fuera del equipo local: configurar HTTPS, un usuario MySQL limitado
y un servidor de producción. No publicar las credenciales locales.

## Compras y fotografías

- Compras > Órdenes de compra: crear, editar pedidos pendientes sin recepciones, ver detalle y cancelar.
- Compras > Recepción de compras: cantidades recibidas, fecha, factura y observaciones; admite entregas parciales.
- Compras > Historial de compras: registro de todas las recepciones y enlace al detalle de cada orden.
- Las recepciones usan un token único en la base de datos, transacciones y bloqueos para evitar duplicados y exceso de stock.
- Productos > Nuevo/Editar: subir, reemplazar o quitar la foto. La imagen aparece en productos, ventas y detalle de compras.
- Formatos admitidos: JPG, PNG y WebP, máximo 2 MB y 12 megapíxeles. Se comprueban MIME y dimensiones; no se admiten SVG ni archivos ejecutables.
- Las imágenes se guardan con nombres aleatorios .bin en storage/productos y se sirven por imagen.php con sesión y cabeceras de imagen.
- Respaldar tanto la base de datos como storage/productos. Las fotos sustituidas se conservan en disco para evitar pérdidas accidentales.
-database/002_imagenes_recepciones.sql agrega Imagen, RecepcionCompra y Detalle_Recepcion. No elimina registros existentes.
- La búsqueda superior consulta productos, clientes y proveedores según los permisos del usuario.
- El gráfico circular muestra la distribución de unidades entre los cuatro productos más vendidos en los últimos treinta días.
- comercial.css contiene los cambios visuales; las vistas de compras están separadas del formulario de ventas.

## Organización y diseño

La estructura está documentada en docs/estructura.md. El login utiliza una única hoja assets/css/login.css y el fondo assets/img/login-nicaragua.png. La referencia y el prompt están documentados en docs/fondo-login.md.


## Guía de código

Consulta [docs/estructura.md](docs/estructura.md) para localizar el controlador, modelo y vista de cada módulo. Ventas está en app/modulos/ventas/; clientes en app/modulos/clientes/; productos en app/modulos/inventario/; el dashboard en app/modulos/dashboard/. Los estilos de búsqueda están en assets/css/busqueda.css.

Pruebas de permisos: `/c/xampp/php/php.exe tests/roles.php`.

Los formularios se agrupan por módulo en app/modulos/. Consulta docs/estructura.md para aprender qué archivo modificar.
