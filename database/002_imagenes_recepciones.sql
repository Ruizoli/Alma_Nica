-- Cambios aditivos: no se eliminan registros existentes.
ALTER TABLE Producto ADD COLUMN IF NOT EXISTS Imagen VARCHAR(80) NULL;
CREATE TABLE IF NOT EXISTS RecepcionCompra (
    Id_Recepcion INT AUTO_INCREMENT PRIMARY KEY,
    Id_OrdenCompra INT NOT NULL,
    Id_Empleado INT NOT NULL,
    Fecha DATETIME NOT NULL,
    Factura VARCHAR(80) NULL,
    Observaciones TEXT NULL,
    Token CHAR(48) NOT NULL UNIQUE,
    FOREIGN KEY (Id_OrdenCompra) REFERENCES OrdenCompra(Id_OrdenCompra),
    FOREIGN KEY (Id_Empleado) REFERENCES Empleado(Id_Empleado)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS Detalle_Recepcion (
    Id_Detalle_Recepcion INT AUTO_INCREMENT PRIMARY KEY,
    Id_Recepcion INT NOT NULL,
    Id_Detalle_Orden INT NOT NULL,
    Cantidad INT NOT NULL,
    FOREIGN KEY (Id_Recepcion) REFERENCES RecepcionCompra(Id_Recepcion),
    FOREIGN KEY (Id_Detalle_Orden) REFERENCES Detalle_OrdenCompra(Id_Detalle_Orden)
) ENGINE=InnoDB;
