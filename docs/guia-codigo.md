# Guía rápida del código

## Dónde trabajar

- app/modulos/clientes: formulario, campos y listado de clientes.
- app/modulos/inventario: formulario, campos y listado de productos.
- app/modulos/ventas: registro, detalle e historial de ventas.
- app/modulos/compras: órdenes, recepciones, historial e impresión.
- app/compartido: consultas y validaciones reutilizadas.
- app/vistas/parciales: cabecera, menú, pie y campos comunes.
- app/soporte: funciones de formato, imágenes y sesión.
- config: conexión, rutas y permisos.
- assets: estilos, JavaScript e imágenes.
- database: cambios de estructura de la base de datos.
- tests: pruebas que usan una base temporal.

## Cómo funciona Compras

1. panel.php carga app/controladores/sistema.php.
2. El sistema comprueba la sesión, el rol y el formulario.
3. compras/controlador.php procesa guardar, recibir, cerrar o revertir.
4. compras/modelo.php ejecuta las consultas y transacciones.
5. compras/datos.php prepara los datos de la pantalla.
6. El sistema carga formulario.php, listado.php, detalle.php o recepcion.php.
7. detalle.php incluye impresion.php para la hoja impresa.

## Variables que vienen de otro archivo

PHP permite que un archivo incluido con require use las variables del archivo que lo llamó.
Por ejemplo, la conexión PDO se crea en app/middleware/autenticacion.php y se utiliza en los módulos.

Los comentarios @var al inicio de las vistas indican su tipo a Intelephense.
No crean variables ni sustituyen las validaciones. Si PHP informa una variable indefinida al
abrir una pantalla, hay que revisar cómo se prepararon sus datos.

## Cambios habituales

- Cambiar etiquetas: campos.php del catálogo correspondiente.
- Cambiar el formulario de compras: app/modulos/compras/formulario.php.
- Cambiar el documento impreso: app/modulos/compras/impresion.php.
- Cambiar cálculos o stock: modelo.php y app/compartido/operaciones.php.
- Cambiar permisos: config/roles.php.
- Cambiar apariencia de compras: assets/css/compras.css.

Las carpetas tienen responsabilidades distintas. Los archivos .htaccess protegen las carpetas
internas; las migraciones y pruebas también se conservan porque se utilizan.
