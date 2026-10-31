ALTER TABLE OrdenCompra MODIFY Estado ENUM('Pendiente','Completada','Cancelada','Cerrada') DEFAULT 'Pendiente';
CREATE TABLE IF NOT EXISTS CompraReversion (
 Id_Recepcion INT PRIMARY KEY, Id_Empleado INT NOT NULL,
 Fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, Motivo VARCHAR(500) NOT NULL,
 FOREIGN KEY (Id_Recepcion) REFERENCES RecepcionCompra(Id_Recepcion),
 FOREIGN KEY (Id_Empleado) REFERENCES Empleado(Id_Empleado)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS OrdenCierre (
 Id_OrdenCompra INT PRIMARY KEY, Id_Empleado INT NOT NULL,
 Fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, Motivo VARCHAR(500) NOT NULL,
 FOREIGN KEY (Id_OrdenCompra) REFERENCES OrdenCompra(Id_OrdenCompra),
 FOREIGN KEY (Id_Empleado) REFERENCES Empleado(Id_Empleado)
) ENGINE=InnoDB;
