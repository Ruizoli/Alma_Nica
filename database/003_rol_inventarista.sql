-- Conserva los roles existentes y agrega Inventarista. No cambia cuentas.
ALTER TABLE Usuario
MODIFY COLUMN Rol ENUM('Administrador','Vendedor','Cajero','Inventarista')
NOT NULL DEFAULT 'Vendedor';
