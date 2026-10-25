# Guía para modificar AlmaNica

Las pantallas están organizadas por módulo dentro de app/modulos. Cada carpeta reúne las partes de su pantalla.

| Carpeta | Pantalla |
| --- | --- |
| clientes | Clientes |
| inventario | Productos, stock, precios y fotos |
| categorias | Categorías |
| proveedores | Proveedores |
| ventas | Registro, detalle e historial de ventas |
| compras | Órdenes, recepción e historial de compras |
| empleados | Empleados |
| usuarios | Cuentas y roles |
| configuracion | Perfil y contraseña |
| dashboard | Indicadores de inicio |
| reportes | Reportes y exportación |
| busqueda | Búsqueda global |

## Ejemplo: modificar clientes

- app/modulos/clientes/formulario.php: distribución del formulario y orden de campos. Cada campo aparece por su nombre.
- app/modulos/clientes/campos.php: etiquetas, obligatoriedad, longitud y columnas de Cliente.
- app/modulos/clientes/listado.php: tabla, búsqueda y botones.
- app/compartido/catalogo_acciones.php: guarda, edita y elimina los catálogos.
- app/controladores/sistema.php: elige entre formulario y listado.

Para cambiar “Teléfono” por “Celular”, cambia la etiqueta en campos.php; conserva Telefono como nombre de columna. Para mover el campo, mueve su línea en formulario.php. Agregar un campo nuevo también requiere crear su columna SQL.

Inventario, categorías, proveedores, empleados y usuarios siguen la misma estructura.

## Ventas y compras

controlador.php procesa las acciones; datos.php carga la información. formulario.php contiene el formulario, detalle.php presenta una operación e historial.php muestra registros anteriores. Compras también tiene listado.php y recepcion.php.

Las consultas y transacciones de ventas/compras siguen en app/compartido/operaciones.php y app/modulos/compras/modelo.php.

## Partes compartidas

- app/controladores/sistema.php: dirige las peticiones y valida sesión, roles y CSRF antes de ejecutar los módulos.
- app/compartido/catalogo_acciones.php y app/compartido/catalogo_modelo.php: acciones, consultas y validaciones compartidas por clientes, inventario y otros catálogos.
- app/vistas/parciales/campo_catalogo.php: dibuja un campo. Cada formulario decide cuáles incluir y en qué orden.
- app/vistas/parciales/: cabecera, pie, logo y paginación.
- config/catalogos.php: registra las definiciones de cada módulo.
- config/vistas.php: mapa de pantallas permitidas.
- config/roles.php: permisos por rol.
- assets/css/: estilos compartidos.
- assets/js/: comportamiento, búsquedas y selectores.
- database/: migraciones necesarias.
- tests/: comprobaciones de permisos e integración.

Las URLs siguen usando panel.php?modulo=clientes o panel.php?modulo=productos; inventario es el nombre de la carpeta del módulo de productos. No hay cambios en las tablas ni datos del negocio.

La organización utiliza funciones PHP y módulos; no es una conversión completa a clases de programación orientada a objetos.

## Estructura simplificada

Los catálogos solo tienen campos.php, formulario.php y listado.php. Se retiraron sus controladores y vistas intermediarios. Los modelos exclusivos están junto a su módulo, con el nombre modelo.php. app/compartido reúne operaciones, autenticación y acciones comunes. Esta es la única guía de estructura.
