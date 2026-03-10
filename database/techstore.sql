-- ============================================================
--  TechStore POS — Base de Datos Completa
--  Motor: MySQL 5.7+ / MariaDB 10.3+
--  Basado en el Modelo Conceptual del cliente
--  Ejecutar en phpMyAdmin o MySQL CLI:
--    mysql -u root -p < techstore.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS techstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techstore;

SET FOREIGN_KEY_CHECKS = 0;

-- ─────────────────────────────────────────────
--  USUARIOS Y ROLES
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50) NOT NULL,
    descripcion TEXT,
    creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuarios (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL,
    apellido        VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    rol_id          INT NOT NULL,
    activo          TINYINT(1) DEFAULT 1,
    ultimo_acceso   DATETIME,
    creado_en       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- ─────────────────────────────────────────────
--  CATÁLOGO (Modelo Conceptual)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pais (
    ID_Pais     INT AUTO_INCREMENT PRIMARY KEY,
    Nombre      VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS marca (
    ID_Marca    INT AUTO_INCREMENT PRIMARY KEY,
    Nombre      VARCHAR(100) NOT NULL,
    ID_Pais     INT NOT NULL,
    FOREIGN KEY (ID_Pais) REFERENCES pais(ID_Pais)
);

CREATE TABLE IF NOT EXISTS modelo (
    ID_Modelo   INT AUTO_INCREMENT PRIMARY KEY,
    Nombre      VARCHAR(150) NOT NULL,
    ID_Marca    INT NOT NULL,
    FOREIGN KEY (ID_Marca) REFERENCES marca(ID_Marca)
);

CREATE TABLE IF NOT EXISTS tipo_producto (
    ID_Tipo     INT AUTO_INCREMENT PRIMARY KEY,
    Nombre      VARCHAR(100) NOT NULL UNIQUE
);

-- ─────────────────────────────────────────────
--  PRODUCTO
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS producto (
    ID_Producto         INT AUTO_INCREMENT PRIMARY KEY,
    SKU                 VARCHAR(50) UNIQUE,
    Codigo_Barras       VARCHAR(100),
    Nombre              VARCHAR(200) NOT NULL,
    Descripcion         TEXT,
    Voltaje             VARCHAR(50),
    Potencia_Watts      VARCHAR(50),
    Garantia_Meses      INT DEFAULT 12,
    Precio_Costo        DECIMAL(12,2) DEFAULT 0,
    Precio_Venta        DECIMAL(12,2) NOT NULL,
    ID_Tipo             INT NOT NULL,
    ID_Modelo           INT NOT NULL,
    activo              TINYINT(1) DEFAULT 1,
    creado_en           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Tipo)   REFERENCES tipo_producto(ID_Tipo),
    FOREIGN KEY (ID_Modelo) REFERENCES modelo(ID_Modelo)
);

-- ─────────────────────────────────────────────
--  INVENTARIO
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS inventario (
    ID_Inventario   INT AUTO_INCREMENT PRIMARY KEY,
    Stock_Actual    INT NOT NULL DEFAULT 0,
    Stock_Minimo    INT NOT NULL DEFAULT 5,
    ID_Producto     INT NOT NULL UNIQUE,
    actualizado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Producto) REFERENCES producto(ID_Producto)
);

CREATE TABLE IF NOT EXISTS movimiento_inventario (
    ID_Movimiento   INT AUTO_INCREMENT PRIMARY KEY,
    Fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Tipo            ENUM('entrada','salida','ajuste') NOT NULL,
    Cantidad        INT NOT NULL,
    Referencia      VARCHAR(100),
    Notas           TEXT,
    ID_Producto     INT NOT NULL,
    ID_Usuario      INT,
    FOREIGN KEY (ID_Producto) REFERENCES producto(ID_Producto),
    FOREIGN KEY (ID_Usuario)  REFERENCES usuarios(id)
);

-- ─────────────────────────────────────────────
--  EMPLEADOS
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS empleado (
    ID_Empleado         INT AUTO_INCREMENT PRIMARY KEY,
    Nombre              VARCHAR(100) NOT NULL,
    Apellido            VARCHAR(100) NOT NULL,
    Cargo               VARCHAR(100),
    Salario             DECIMAL(12,2),
    Fecha_Contratacion  DATE,
    ID_Usuario          INT UNIQUE,
    activo              TINYINT(1) DEFAULT 1,
    FOREIGN KEY (ID_Usuario) REFERENCES usuarios(id)
);

-- ─────────────────────────────────────────────
--  CLIENTES
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cliente (
    ID_Cliente  INT AUTO_INCREMENT PRIMARY KEY,
    Nombre      VARCHAR(100) NOT NULL,
    Apellido    VARCHAR(100) NOT NULL,
    CI          VARCHAR(20),
    Telefono    VARCHAR(30),
    Email       VARCHAR(150),
    NIT         VARCHAR(20),
    Razon_Social VARCHAR(200),
    activo      TINYINT(1) DEFAULT 1,
    creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
--  PROVEEDORES
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS proveedor (
    ID_Proveedor    INT AUTO_INCREMENT PRIMARY KEY,
    Nombre          VARCHAR(200) NOT NULL,
    Telefono        VARCHAR(30),
    Email           VARCHAR(150),
    Direccion       TEXT,
    NIT             VARCHAR(20),
    Condicion_Pago  VARCHAR(100),
    ID_Pais         INT,
    activo          TINYINT(1) DEFAULT 1,
    creado_en       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Pais) REFERENCES pais(ID_Pais)
);

-- ─────────────────────────────────────────────
--  COMPRAS
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS compra_proveedor (
    ID_Compra       INT AUTO_INCREMENT PRIMARY KEY,
    Numero_Orden    VARCHAR(20) NOT NULL UNIQUE,
    Fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Subtotal        DECIMAL(14,2) DEFAULT 0,
    Descuento       DECIMAL(14,2) DEFAULT 0,
    Total           DECIMAL(14,2) NOT NULL DEFAULT 0,
    Estado          ENUM('pendiente','recibida','anulada') DEFAULT 'recibida',
    Notas           TEXT,
    ID_Proveedor    INT NOT NULL,
    ID_Empleado     INT,
    FOREIGN KEY (ID_Proveedor)  REFERENCES proveedor(ID_Proveedor),
    FOREIGN KEY (ID_Empleado)   REFERENCES empleado(ID_Empleado)
);

CREATE TABLE IF NOT EXISTS detalle_compra (
    ID_Detalle_Compra   INT AUTO_INCREMENT PRIMARY KEY,
    Cantidad            INT NOT NULL,
    Precio_Compra       DECIMAL(12,2) NOT NULL,
    Subtotal            DECIMAL(12,2) NOT NULL,
    ID_Compra           INT NOT NULL,
    ID_Producto         INT NOT NULL,
    FOREIGN KEY (ID_Compra)   REFERENCES compra_proveedor(ID_Compra),
    FOREIGN KEY (ID_Producto) REFERENCES producto(ID_Producto)
);

-- ─────────────────────────────────────────────
--  VENTAS
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS metodo_pago (
    ID_Metodo   INT AUTO_INCREMENT PRIMARY KEY,
    Nombre      VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS venta (
    ID_Venta    INT AUTO_INCREMENT PRIMARY KEY,
    Numero      VARCHAR(20) NOT NULL UNIQUE,
    Fecha       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Subtotal    DECIMAL(14,2) DEFAULT 0,
    Descuento   DECIMAL(14,2) DEFAULT 0,
    IVA         DECIMAL(14,2) DEFAULT 0,
    Total       DECIMAL(14,2) NOT NULL DEFAULT 0,
    Estado      ENUM('completada','anulada','pendiente') DEFAULT 'completada',
    ID_Cliente  INT,
    ID_Empleado INT,
    ID_Metodo   INT NOT NULL,
    FOREIGN KEY (ID_Cliente)  REFERENCES cliente(ID_Cliente),
    FOREIGN KEY (ID_Empleado) REFERENCES empleado(ID_Empleado),
    FOREIGN KEY (ID_Metodo)   REFERENCES metodo_pago(ID_Metodo)
);

CREATE TABLE IF NOT EXISTS detalle_venta (
    ID_Detalle      INT AUTO_INCREMENT PRIMARY KEY,
    Cantidad        INT NOT NULL,
    Precio_Unitario DECIMAL(12,2) NOT NULL,
    Subtotal        DECIMAL(12,2) NOT NULL,
    ID_Venta        INT NOT NULL,
    ID_Producto     INT NOT NULL,
    FOREIGN KEY (ID_Venta)    REFERENCES venta(ID_Venta),
    FOREIGN KEY (ID_Producto) REFERENCES producto(ID_Producto)
);

-- ─────────────────────────────────────────────
--  FACTURAS
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS factura (
    ID_Factura      INT AUTO_INCREMENT PRIMARY KEY,
    Numero_Factura  VARCHAR(20) NOT NULL UNIQUE,
    Fecha_Emision   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    NIT             VARCHAR(20),
    Razon_Social    VARCHAR(200),
    Monto_Total     DECIMAL(14,2) NOT NULL,
    ID_Venta        INT NOT NULL UNIQUE,
    FOREIGN KEY (ID_Venta) REFERENCES venta(ID_Venta)
);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  DATOS INICIALES (SEED)
-- ============================================================

-- Roles
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Cajero',        'Acceso al POS y ventas'),
('Almacén',       'Acceso a inventario y compras');

-- Usuario admin (password: admin123)
INSERT INTO usuarios (nombre, apellido, email, password_hash, rol_id) VALUES
('Luis',  'Adolfo',   'admin@techstore.bo',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('María', 'Torres',   'cajero@techstore.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Pedro', 'Quispe',   'almacen@techstore.bo','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);

-- Empleados
INSERT INTO empleado (Nombre, Apellido, Cargo, Salario, Fecha_Contratacion, ID_Usuario) VALUES
('Luis',  'Adolfo', 'Administrador', 5000.00, '2023-01-15', 1),
('María', 'Torres', 'Cajera',        3500.00, '2023-03-01', 2),
('Pedro', 'Quispe', 'Almacenero',    3000.00, '2023-06-01', 3);

-- Países
INSERT INTO pais (Nombre) VALUES ('Bolivia'),('Estados Unidos'),('Corea del Sur'),('Japón'),('China'),('Alemania');

-- Marcas
INSERT INTO marca (Nombre, ID_Pais) VALUES
('Apple',   2),('Samsung', 3),('Sony',   4),
('LG',      3),('DJI',     5),('Google', 2),('Dell', 2);

-- Tipos de Producto
INSERT INTO tipo_producto (Nombre) VALUES
('Smartphone'),('Laptop'),('Tablet'),('Televisor'),('Audio'),('Accesorio'),('Drone');

-- Modelos
INSERT INTO modelo (Nombre, ID_Marca) VALUES
('iPhone 15 Pro Max', 1),('iPhone 15 Pro', 1),('MacBook Pro M3', 1),('iPad Pro M4', 1),('AirPods Pro 2', 1),('Apple Watch Ultra 2', 1),
('Galaxy S24 Ultra',  2),('Galaxy S24+',   2),
('WH-1000XM5',        3),('BRAVIA A95L',   3),
('C3 OLED',           4),
('Mini 3 Pro',        5),
('Pixel 8 Pro',       6),
('XPS 15',            7);

-- Productos
INSERT INTO producto (SKU, Nombre, Voltaje, Potencia_Watts, Garantia_Meses, Precio_Costo, Precio_Venta, ID_Tipo, ID_Modelo) VALUES
('IPH-15PM', 'iPhone 15 Pro Max 256GB',   '5V',      '27W',  12,  6200, 8999, 1, 1),
('IPH-15P',  'iPhone 15 Pro 128GB',       '5V',      '27W',  12,  4800, 6999, 1, 2),
('MBP-M3MX', 'MacBook Pro M3 Max 14"',   '20V',     '140W', 12, 12800,18500, 2, 3),
('IPD-PRO4', 'iPad Pro M4 13"',           '5V',      '30W',  12,  6500, 9200, 3, 4),
('APL-APP2', 'AirPods Pro 2da Gen',       '5V',      '5W',   12,  1400, 2200, 5, 5),
('APL-AWU2', 'Apple Watch Ultra 2',       '5V',      '8W',   12,  3200, 4800, 6, 6),
('SAM-S24U', 'Samsung Galaxy S24 Ultra',  '5V',      '45W',  12,  5400, 7850, 1, 7),
('SAM-S24P', 'Samsung Galaxy S24+',       '5V',      '45W',  12,  3800, 5600, 1, 8),
('SNY-WH5',  'Sony WH-1000XM5',           '5V',      '3W',   12,  1800, 2800, 5, 9),
('SNY-A95L', 'Sony BRAVIA A95L 65"',      '110-220V','200W', 24, 16000,22000, 4,10),
('LG-C3-55', 'LG C3 OLED 55"',            '110-220V','150W', 24, 10200,14500, 4,11),
('DJI-MV3P', 'DJI Mini 3 Pro',            '7.38V',   '10W',  12,  3500, 5200, 7,12),
('GOO-PIX8', 'Google Pixel 8 Pro',        '5V',      '30W',  12,  4300, 6400, 1,13),
('DEL-XPS5', 'Dell XPS 15 OLED',          '20V',     '130W', 12, 11000,15800, 2,14);

-- Inventario
INSERT INTO inventario (Stock_Actual, Stock_Minimo, ID_Producto) VALUES
(18,5,1),(12,5,2),(3,2,3),(7,3,4),(30,10,5),(11,5,6),
(12,5,7),(8,5,8),(22,8,9),(0,2,10),(5,3,11),(9,4,12),(15,5,13),(4,2,14);

-- Métodos de pago
INSERT INTO metodo_pago (Nombre) VALUES ('Efectivo'),('QR / Transferencia'),('Tarjeta de Débito'),('Tarjeta de Crédito');

-- Proveedores
INSERT INTO proveedor (Nombre, Telefono, Email, Direccion, NIT, Condicion_Pago, ID_Pais) VALUES
('TechDistrib Bolivia S.R.L.', '+591 2 123-4567', 'ventas@techdistrib.bo',  'Av. Montes 456, La Paz',         '1024567890', '30 días', 1),
('GlobalTech Import S.A.',     '+591 3 987-6543', 'compras@globaltech.bo',  'Calle 24 de Septiembre, SC',     '2031458900', 'Contado', 1),
('Apple Premium Reseller',     '+591 4 321-0987', 'b2b@applereseller.bo',   'Av. Heroínas 123, Cbba',         '3140256800', '60 días', 2),
('Samsung Business Center',    '+591 2 456-7890', 'corp@samsung-bo.com',    'Zona Sopocachi, La Paz',         '4052167900', '15 días', 3),
('AV Import Tech Ltda.',       '+591 3 654-3210', 'info@avimport.bo',       'Av. Cristo Redentor, SC',        '5063278000', 'Contado', 1);

-- Clientes
INSERT INTO cliente (Nombre, Apellido, CI, Telefono, Email, NIT, Razon_Social) VALUES
('Carlos',    'Mendoza Quispe',    '2345678 LP', '+591 70012345', 'carlos@email.com',    '2345678901', 'Carlos Mendoza'),
('Ana',       'Flores Gutiérrez',  '4567890 CB', '+591 71123456', 'ana@email.com',       '4567890123', 'Ana Flores'),
('Roberto',   'Vargas Lima',       '6789012 SC', '+591 72234567', 'roberto@empresa.com', '6789012345', 'Importadora Vargas S.R.L.'),
('María',     'Condori Alvarado',  '8901234 LP', '+591 73345678', 'maria@correo.com',    '8901234567', 'María Condori'),
('José',      'Mamani Torres',     '1234567 OR', '+591 74456789', 'jose@negocios.bo',    '1234567890', 'Distribuidora Mamani'),
('Consumidor','Final',             '—',          '—',             '—',                   '0',          'Sin Factura');
