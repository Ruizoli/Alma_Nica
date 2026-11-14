-- ==========================================================
-- RESPALDO DE BASE DE DATOS: almanica
-- SISTEMA: AlmaNica
-- FECHA: 14/11/2026 09:01:41
-- ==========================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-06:00";

-- Estructura de la tabla `categoria`
DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `Id_Categoria` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  PRIMARY KEY (`Id_Categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categoria` (`Id_Categoria`, `Nombre`, `Descripcion`) VALUES (1, 'Pantalón', 'kfewniknws');
INSERT INTO `categoria` (`Id_Categoria`, `Nombre`, `Descripcion`) VALUES (2, 'Camisa', 'Vestir');
INSERT INTO `categoria` (`Id_Categoria`, `Nombre`, `Descripcion`) VALUES (3, 'Zapatos', 'Zapatos');
INSERT INTO `categoria` (`Id_Categoria`, `Nombre`, `Descripcion`) VALUES (4, 'Gorras', 'Gorras');

-- Estructura de la tabla `cliente`
DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `Id_Cliente` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Cedula` varchar(20) DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Id_Cliente`),
  UNIQUE KEY `Cedula` (`Cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cliente` (`Id_Cliente`, `Nombre`, `Cedula`, `Correo`, `Telefono`) VALUES (1, 'Jose Luis', '408098645', 'luis@gmail.com', '68086058065');
INSERT INTO `cliente` (`Id_Cliente`, `Nombre`, `Cedula`, `Correo`, `Telefono`) VALUES (2, 'Kevin Arcia', '561123456', 'kevin@gmail.com', '35643434');

-- Estructura de la tabla `comprareversion`
DROP TABLE IF EXISTS `comprareversion`;
CREATE TABLE `comprareversion` (
  `Id_Recepcion` int(11) NOT NULL,
  `Id_Empleado` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `Motivo` varchar(500) NOT NULL,
  PRIMARY KEY (`Id_Recepcion`),
  KEY `Id_Empleado` (`Id_Empleado`),
  CONSTRAINT `comprareversion_ibfk_1` FOREIGN KEY (`Id_Recepcion`) REFERENCES `recepcioncompra` (`Id_Recepcion`),
  CONSTRAINT `comprareversion_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleado` (`Id_Empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de la tabla `detalle_ordencompra`
DROP TABLE IF EXISTS `detalle_ordencompra`;
CREATE TABLE `detalle_ordencompra` (
  `Id_Detalle_Orden` int(11) NOT NULL AUTO_INCREMENT,
  `Id_OrdenCompra` int(11) NOT NULL,
  `Id_Producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Costo` decimal(10,2) NOT NULL,
  `Subtotal` decimal(10,2) GENERATED ALWAYS AS (`Cantidad` * `Precio_Costo`) STORED,
  `Precio_Venta_Referencia` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Id_Detalle_Orden`),
  KEY `Id_OrdenCompra` (`Id_OrdenCompra`),
  KEY `Id_Producto` (`Id_Producto`),
  CONSTRAINT `detalle_ordencompra_ibfk_1` FOREIGN KEY (`Id_OrdenCompra`) REFERENCES `ordencompra` (`Id_OrdenCompra`) ON DELETE CASCADE,
  CONSTRAINT `detalle_ordencompra_ibfk_2` FOREIGN KEY (`Id_Producto`) REFERENCES `producto` (`Id_Producto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_ordencompra` (`Id_Detalle_Orden`, `Id_OrdenCompra`, `Id_Producto`, `Cantidad`, `Precio_Costo`, `Subtotal`, `Precio_Venta_Referencia`) VALUES (1, 1, 3, 27, '200.00', '5400.00', '500.00');
INSERT INTO `detalle_ordencompra` (`Id_Detalle_Orden`, `Id_OrdenCompra`, `Id_Producto`, `Cantidad`, `Precio_Costo`, `Subtotal`, `Precio_Venta_Referencia`) VALUES (3, 2, 3, 106, '700.00', '74200.00', '1500.00');

-- Estructura de la tabla `detalle_recepcion`
DROP TABLE IF EXISTS `detalle_recepcion`;
CREATE TABLE `detalle_recepcion` (
  `Id_Detalle_Recepcion` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Recepcion` int(11) NOT NULL,
  `Id_Detalle_Orden` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  PRIMARY KEY (`Id_Detalle_Recepcion`),
  KEY `Id_Recepcion` (`Id_Recepcion`),
  KEY `Id_Detalle_Orden` (`Id_Detalle_Orden`),
  CONSTRAINT `detalle_recepcion_ibfk_1` FOREIGN KEY (`Id_Recepcion`) REFERENCES `recepcioncompra` (`Id_Recepcion`),
  CONSTRAINT `detalle_recepcion_ibfk_2` FOREIGN KEY (`Id_Detalle_Orden`) REFERENCES `detalle_ordencompra` (`Id_Detalle_Orden`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_recepcion` (`Id_Detalle_Recepcion`, `Id_Recepcion`, `Id_Detalle_Orden`, `Cantidad`) VALUES (1, 1, 1, 27);

-- Estructura de la tabla `detalle_venta`
DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE `detalle_venta` (
  `Id_Detalle_Venta` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Venta` int(11) NOT NULL,
  `Id_Producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(10,2) NOT NULL,
  `Subtotal` decimal(10,2) GENERATED ALWAYS AS (`Cantidad` * `Precio_Unitario`) STORED,
  PRIMARY KEY (`Id_Detalle_Venta`),
  KEY `Id_Venta` (`Id_Venta`),
  KEY `Id_Producto` (`Id_Producto`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`Id_Venta`) REFERENCES `venta` (`Id_Venta`) ON DELETE CASCADE,
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`Id_Producto`) REFERENCES `producto` (`Id_Producto`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (1, 1, 1, 4, '800.00', '3200.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (2, 2, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (3, 3, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (4, 4, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (5, 5, 3, 1, '100.00', '100.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (6, 6, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (7, 7, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (8, 8, 3, 1, '100.00', '100.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (9, 9, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (10, 10, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (11, 11, 1, 1, '800.00', '800.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (12, 12, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (13, 13, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (14, 14, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (15, 15, 3, 1, '100.00', '100.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (16, 16, 3, 1, '100.00', '100.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (17, 17, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (18, 18, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (19, 19, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (20, 20, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (21, 21, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (22, 22, 4, 1, '2000.00', '2000.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (23, 23, 3, 1, '100.00', '100.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (24, 24, 2, 1, '900.00', '900.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (25, 24, 3, 1, '100.00', '100.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (26, 25, 2, 2, '900.00', '1800.00');
INSERT INTO `detalle_venta` (`Id_Detalle_Venta`, `Id_Venta`, `Id_Producto`, `Cantidad`, `Precio_Unitario`, `Subtotal`) VALUES (27, 25, 3, 2, '100.00', '200.00');

-- Estructura de la tabla `empleado`
DROP TABLE IF EXISTS `empleado`;
CREATE TABLE `empleado` (
  `Id_Empleado` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`Id_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `empleado` (`Id_Empleado`, `Nombre`, `Estado`) VALUES (1, 'Administrador de prueba', 1);
INSERT INTO `empleado` (`Id_Empleado`, `Nombre`, `Estado`) VALUES (2, 'Kevin Ismael', 1);
INSERT INTO `empleado` (`Id_Empleado`, `Nombre`, `Estado`) VALUES (3, 'Luis arcia', 0);

-- Estructura de la tabla `loginintento`
DROP TABLE IF EXISTS `loginintento`;
CREATE TABLE `loginintento` (
  `Clave` char(64) NOT NULL,
  `Fallos` int(11) NOT NULL DEFAULT 0,
  `Bloqueado_Hasta` bigint(20) NOT NULL DEFAULT 0,
  `Actualizado` bigint(20) NOT NULL,
  PRIMARY KEY (`Clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `loginintento` (`Clave`, `Fallos`, `Bloqueado_Hasta`, `Actualizado`) VALUES ('227a139b2d0f158050b5542327a01af02caf27028d0e765c5c3b769a71257468', 7, 0, 1793475193);
INSERT INTO `loginintento` (`Clave`, `Fallos`, `Bloqueado_Hasta`, `Actualizado`) VALUES ('508cc0fe26185d71343b0aaa99a7a8c8a45392f278d5f16b37faef9bf114cc58', 0, 0, 1790908377);
INSERT INTO `loginintento` (`Clave`, `Fallos`, `Bloqueado_Hasta`, `Actualizado`) VALUES ('7ab77a3029900f2a0c215135666860449e594a5441b2cc30bac2f9fd32ec1a07', 2, 0, 1793475193);
INSERT INTO `loginintento` (`Clave`, `Fallos`, `Bloqueado_Hasta`, `Actualizado`) VALUES ('b77fcf58866bd5bd08760a74a7d2168645eb587a77d57e30514ac07df338416d', 2, 0, 1793475065);
INSERT INTO `loginintento` (`Clave`, `Fallos`, `Bloqueado_Hasta`, `Actualizado`) VALUES ('de4644073d07f795d58e119d82b6e5f63f1df9be033120d5e649c629945d7fa0', 1, 0, 1790905652);

-- Estructura de la tabla `ordencierre`
DROP TABLE IF EXISTS `ordencierre`;
CREATE TABLE `ordencierre` (
  `Id_OrdenCompra` int(11) NOT NULL,
  `Id_Empleado` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `Motivo` varchar(500) NOT NULL,
  PRIMARY KEY (`Id_OrdenCompra`),
  KEY `Id_Empleado` (`Id_Empleado`),
  CONSTRAINT `ordencierre_ibfk_1` FOREIGN KEY (`Id_OrdenCompra`) REFERENCES `ordencompra` (`Id_OrdenCompra`),
  CONSTRAINT `ordencierre_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleado` (`Id_Empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de la tabla `ordencompra`
DROP TABLE IF EXISTS `ordencompra`;
CREATE TABLE `ordencompra` (
  `Id_OrdenCompra` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Proveedor` int(11) NOT NULL,
  `Id_Empleado` int(11) NOT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Estado` enum('Pendiente','Completada','Cancelada','Cerrada') DEFAULT 'Pendiente',
  `Total` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`Id_OrdenCompra`),
  KEY `Id_Proveedor` (`Id_Proveedor`),
  KEY `Id_Empleado` (`Id_Empleado`),
  KEY `idx_orden_fecha` (`Fecha`),
  CONSTRAINT `ordencompra_ibfk_1` FOREIGN KEY (`Id_Proveedor`) REFERENCES `proveedor` (`Id_Proveedor`),
  CONSTRAINT `ordencompra_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleado` (`Id_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ordencompra` (`Id_OrdenCompra`, `Id_Proveedor`, `Id_Empleado`, `Fecha`, `Estado`, `Total`) VALUES (1, 2, 1, '2026-11-14 08:38:08', 'Completada', '5400.00');
INSERT INTO `ordencompra` (`Id_OrdenCompra`, `Id_Proveedor`, `Id_Empleado`, `Fecha`, `Estado`, `Total`) VALUES (2, 2, 1, '2026-11-14 08:46:56', 'Cancelada', '74200.00');

-- Estructura de la tabla `producto`
DROP TABLE IF EXISTS `producto`;
CREATE TABLE `producto` (
  `Id_Producto` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(50) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Id_Categoria` int(11) DEFAULT NULL,
  `Id_Proveedor` int(11) DEFAULT NULL,
  `Marca` varchar(50) DEFAULT NULL,
  `Talla` varchar(10) DEFAULT NULL,
  `Stock_Actual` int(11) NOT NULL DEFAULT 0,
  `Stock_Minimo` int(11) DEFAULT 5,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Imagen` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`Id_Producto`),
  UNIQUE KEY `Codigo` (`Codigo`),
  KEY `Id_Categoria` (`Id_Categoria`),
  KEY `Id_Proveedor` (`Id_Proveedor`),
  KEY `idx_producto_codigo` (`Codigo`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`Id_Categoria`) REFERENCES `categoria` (`Id_Categoria`) ON DELETE SET NULL,
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`Id_Proveedor`) REFERENCES `proveedor` (`Id_Proveedor`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `producto` (`Id_Producto`, `Codigo`, `Nombre`, `Id_Categoria`, `Id_Proveedor`, `Marca`, `Talla`, `Stock_Actual`, `Stock_Minimo`, `Precio_Venta`, `Imagen`) VALUES (1, '34543545', 'Pantalón', 1, NULL, 'Levias', '36', 8, 5, '800.00', NULL);
INSERT INTO `producto` (`Id_Producto`, `Codigo`, `Nombre`, `Id_Categoria`, `Id_Proveedor`, `Marca`, `Talla`, `Stock_Actual`, `Stock_Minimo`, `Precio_Venta`, `Imagen`) VALUES (2, '5656567', 'Camisa de vestir', 2, NULL, 'Levias', 'M', 5, 5, '900.00', '13be69bf6993f61e1ed277ba0c675167f74a4fb0c306ceb9.bin');
INSERT INTO `producto` (`Id_Producto`, `Codigo`, `Nombre`, `Id_Categoria`, `Id_Proveedor`, `Marca`, `Talla`, `Stock_Actual`, `Stock_Minimo`, `Precio_Venta`, `Imagen`) VALUES (3, '8989880', 'Camisa Hawaiana', 2, NULL, 'Levias', 'L M S XXL', 49, 5, '100.00', '9d6075677f0be30f952560ef933628c6e534aa90a9f73aaa.bin');
INSERT INTO `producto` (`Id_Producto`, `Codigo`, `Nombre`, `Id_Categoria`, `Id_Proveedor`, `Marca`, `Talla`, `Stock_Actual`, `Stock_Minimo`, `Precio_Venta`, `Imagen`) VALUES (4, '754756', 'Jordan 1', 3, NULL, 'Jordán', '35-40', 22, 6, '2000.00', '10c1f1a100c368e0453c1e704f4733a3e854af40d9aa341b.bin');

-- Estructura de la tabla `proveedor`
DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE `proveedor` (
  `Id_Proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  PRIMARY KEY (`Id_Proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `proveedor` (`Id_Proveedor`, `Nombre`, `Telefono`, `Correo`, `Direccion`) VALUES (2, 'Ismael Mora', '0359345', 'Mora@gmail.com', 'Vendedor de gorras');

-- Estructura de la tabla `recepcioncompra`
DROP TABLE IF EXISTS `recepcioncompra`;
CREATE TABLE `recepcioncompra` (
  `Id_Recepcion` int(11) NOT NULL AUTO_INCREMENT,
  `Id_OrdenCompra` int(11) NOT NULL,
  `Id_Empleado` int(11) NOT NULL,
  `Fecha` datetime NOT NULL,
  `Factura` varchar(80) DEFAULT NULL,
  `Observaciones` text DEFAULT NULL,
  `Token` char(48) NOT NULL,
  PRIMARY KEY (`Id_Recepcion`),
  UNIQUE KEY `Token` (`Token`),
  KEY `Id_OrdenCompra` (`Id_OrdenCompra`),
  KEY `Id_Empleado` (`Id_Empleado`),
  CONSTRAINT `recepcioncompra_ibfk_1` FOREIGN KEY (`Id_OrdenCompra`) REFERENCES `ordencompra` (`Id_OrdenCompra`),
  CONSTRAINT `recepcioncompra_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleado` (`Id_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `recepcioncompra` (`Id_Recepcion`, `Id_OrdenCompra`, `Id_Empleado`, `Fecha`, `Factura`, `Observaciones`, `Token`) VALUES (1, 1, 1, '2026-11-14 08:47:51', NULL, NULL, '1fc52da356129b93f339bdc6369769d4ea1807f348dd5e49');

-- Estructura de la tabla `usuario`
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `Id_Usuario` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Empleado` int(11) NOT NULL,
  `Usuario` varchar(50) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Rol` enum('Administrador','Vendedor','Cajero') NOT NULL DEFAULT 'Vendedor',
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  `Fecha_Creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`Id_Usuario`),
  UNIQUE KEY `Usuario` (`Usuario`),
  KEY `Id_Empleado` (`Id_Empleado`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleado` (`Id_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuario` (`Id_Usuario`, `Id_Empleado`, `Usuario`, `Contrasena`, `Rol`, `Estado`, `Fecha_Creacion`) VALUES (1, 1, 'admin_demo', '$2y$10$lGNvDFzM/sYtmlDQCNu9DeB73ZkakcONDK.ibsMmWifO8Azh/0sGO', 'Administrador', 1, '2026-10-01 19:48:07');
INSERT INTO `usuario` (`Id_Usuario`, `Id_Empleado`, `Usuario`, `Contrasena`, `Rol`, `Estado`, `Fecha_Creacion`) VALUES (3, 2, 'admin', '123456789', 'Administrador', 1, '2026-10-31 13:30:42');
INSERT INTO `usuario` (`Id_Usuario`, `Id_Empleado`, `Usuario`, `Contrasena`, `Rol`, `Estado`, `Fecha_Creacion`) VALUES (4, 2, 'Kevin15', '$2y$10$Oba49IHGB8iyS4LLyWDLOODrrtK6KDncweDUzYfPpGZ6eT8PtDNUC', 'Cajero', 0, '2026-11-14 08:49:13');

-- Estructura de la tabla `venta`
DROP TABLE IF EXISTS `venta`;
CREATE TABLE `venta` (
  `Id_Venta` int(11) NOT NULL AUTO_INCREMENT,
  `Id_Cliente` int(11) DEFAULT NULL,
  `Id_Empleado` int(11) NOT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Metodo_Pago` enum('Efectivo','Tarjeta','Transferencia') NOT NULL DEFAULT 'Efectivo',
  PRIMARY KEY (`Id_Venta`),
  KEY `Id_Cliente` (`Id_Cliente`),
  KEY `Id_Empleado` (`Id_Empleado`),
  KEY `idx_venta_fecha` (`Fecha`),
  CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`Id_Cliente`) REFERENCES `cliente` (`Id_Cliente`) ON DELETE SET NULL,
  CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleado` (`Id_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (1, 1, 1, '2026-10-31 13:42:24', '3200.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (2, 1, 1, '2026-11-04 15:18:21', '900.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (3, 2, 1, '2026-11-04 15:19:22', '900.00', 'Transferencia');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (4, 2, 1, '2026-11-04 15:36:16', '2000.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (5, NULL, 1, '2026-11-04 15:36:29', '100.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (6, 2, 1, '2026-11-07 12:22:55', '900.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (7, NULL, 1, '2026-11-07 12:23:06', '900.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (8, 1, 1, '2026-11-07 12:57:24', '100.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (9, 1, 1, '2026-11-07 13:32:08', '900.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (10, 1, 1, '2026-11-07 13:32:31', '900.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (11, 2, 1, '2026-11-07 13:39:12', '800.00', 'Transferencia');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (12, 1, 1, '2026-11-07 13:42:58', '2000.00', 'Transferencia');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (13, 1, 1, '2026-11-07 13:46:03', '900.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (14, 2, 1, '2026-11-07 13:48:35', '2000.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (15, 1, 1, '2026-11-07 13:50:17', '100.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (16, NULL, 1, '2026-11-07 13:52:36', '100.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (17, 2, 1, '2026-11-07 13:57:30', '2000.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (18, 2, 1, '2026-11-07 14:07:02', '2000.00', 'Transferencia');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (19, NULL, 1, '2026-11-07 14:10:42', '2000.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (20, 2, 1, '2026-11-07 14:13:30', '900.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (21, NULL, 1, '2026-11-07 14:21:24', '2000.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (22, NULL, 1, '2026-11-07 14:27:41', '2000.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (23, 2, 1, '2026-11-14 08:08:14', '100.00', 'Efectivo');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (24, 1, 1, '2026-11-14 08:10:35', '1000.00', 'Tarjeta');
INSERT INTO `venta` (`Id_Venta`, `Id_Cliente`, `Id_Empleado`, `Fecha`, `Total`, `Metodo_Pago`) VALUES (25, 2, 1, '2026-11-14 08:41:56', '2000.00', 'Transferencia');

SET FOREIGN_KEY_CHECKS=1;
