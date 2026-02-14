-- =====================================================
-- CONTODA - Sistema de Facturación
-- Modelo de Base de Datos
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS contoda;
USE contoda;

-- =====================================================
-- TABLA: Categorías
-- =====================================================
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Productos
-- =====================================================
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    codigo_producto VARCHAR(50) UNIQUE,
    nombre_producto VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    imagen VARCHAR(255),
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM('Cédula', 'NIT', 'Pasaporte', 'Cédula Extranjería') DEFAULT 'Cédula',
    numero_documento VARCHAR(20) UNIQUE NOT NULL,
    nombre_cliente VARCHAR(200) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    ciudad VARCHAR(100),
    departamento VARCHAR(100),
    tipo_cliente ENUM('Persona Natural', 'Empresa') DEFAULT 'Persona Natural',
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Facturas
-- =====================================================
CREATE TABLE IF NOT EXISTS facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    numero_factura VARCHAR(20) UNIQUE NOT NULL,
    fecha_factura DATE NOT NULL,
    hora_factura TIME DEFAULT CURRENT_TIME,
    subtotal DECIMAL(10,2) DEFAULT 0,
    descuento DECIMAL(10,2) DEFAULT 0,
    impuesto DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    forma_pago ENUM('Efectivo', 'Tarjeta Débito', 'Tarjeta Crédito', 'Transferencia', 'Consignación') DEFAULT 'Efectivo',
    estado ENUM('Pendiente', 'Pagada', 'Anulada') DEFAULT 'Pendiente',
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: Detalle Factura
-- =====================================================
CREATE TABLE IF NOT EXISTS detalle_factura (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- DATOS DE EJEMPLO - Categorías
-- =====================================================
INSERT INTO categorias (nombre_categoria, descripcion) VALUES
('Maquillaje', 'Productos de maquillaje para labios, ojos, rostro y uñas'),
('Bolsos', 'Bolsos de mano, carteras, mochilas y accesorios'),
('Zapatos', 'Zapatos, botas, sandals y calzado formal'),
('Accesorios', 'Joyería, relojes, cinturones, gafas y demás accesorios');

-- =====================================================
-- DATOS DE EJEMPLO - Productos
-- =====================================================
INSERT INTO productos (id_categoria, codigo_producto, nombre_producto, descripcion, precio, stock, stock_minimo) VALUES
(1, 'MAQ-001', 'Labial Rojo Intenso', 'Labial de larga duración color rojo intenso', 25000, 50, 10),
(1, 'MAQ-002', 'Base Liquida Mate', 'Base líquida acabado mate para todo tipo de piel', 45000, 30, 8),
(1, 'MAQ-003', 'Paleta de Sombras', 'Paleta de 12 sombras nude y vibrantes', 65000, 25, 5),
(2, 'BOL-001', 'Bolso Casual Cuero', 'Bolso de cuero sintético multiusos', 89000, 20, 5),
(2, 'BOL-002', 'Mochila Urbana', 'Mochila moderna para diario', 55000, 35, 10),
(2, 'BOL-003', 'Cartera Elegante', 'Cartera de mano formal', 120000, 15, 3),
(3, 'ZAP-001', 'Zapatos Tacón Alto', 'Zapatos de tacón 8cm ideal para oficina', 150000, 12, 3),
(3, 'ZAP-002', 'Sandalias Verano', 'Sandalias cómodas para el verano', 75000, 40, 10),
(3, 'ZAP-003', 'Botas Cuero Invierno', 'Botas de cuero para invierno', 180000, 8, 2),
(4, 'ACC-001', 'Reloj Analogico', 'Reloj de pulsera analogo elegante', 95000, 25, 5),
(4, 'ACC-002', 'Gafas de Sol', 'Gafas de sol con protección UV', 65000, 30, 8),
(4, 'ACC-003', 'Collar Dorado', 'Collar de acero dorado con piedras', 35000, 45, 10);

-- =====================================================
-- DATOS DE EJEMPLO - Clientes
-- =====================================================
INSERT INTO clientes (tipo_documento, numero_documento, nombre_cliente, email, telefono, direccion, ciudad, tipo_cliente) VALUES
('Cédula', '12345678', 'María García López', 'maria.garcia@email.com', '3001234567', 'Calle 45 #12-30', 'Bogotá', 'Persona Natural'),
('NIT', '900123456-1', 'Empresa Moda SAS', 'compras@modasas.com', '6012345678', 'Carrera 15 #80-15', 'Bogotá', 'Empresa'),
('Cédula', '87654321', 'Ana Martínez Soto', 'ana.martinez@email.com', '3209876543', 'Avenida 50 #23-45', 'Medellín', 'Persona Natural'),
('Cédula', '11223344', 'Carlos Pérez Ruiz', 'carlos.perez@email.com', '3155556666', 'Calle 10 #5-20', 'Cali', 'Persona Natural');

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================
-- Vista para ver productos con nombre de categoría
CREATE OR REPLACE VIEW vista_productos AS
SELECT 
    p.id_producto,
    p.codigo_producto,
    p.nombre_producto,
    p.descripcion,
    p.precio,
    p.stock,
    p.stock_minimo,
    p.estado,
    c.nombre_categoria,
    c.id_categoria
FROM productos p
INNER JOIN categorias c ON p.id_categoria = c.id_categoria;

-- Vista para ver facturas con nombre de cliente
CREATE OR REPLACE VIEW vista_facturas AS
SELECT 
    f.id_factura,
    f.numero_factura,
    f.fecha_factura,
    f.total,
    f.estado,
    f.forma_pago,
    c.nombre_cliente,
    c.numero_documento
FROM facturas f
INNER JOIN clientes c ON f.id_cliente = c.id_cliente;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================
DELIMITER //
CREATE PROCEDURE sp_generar_numero_factura()
BEGIN
    DECLARE numero INT;
    DECLARE nuevo_numero VARCHAR(20);
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero_factura, 5) AS UNSIGNED)), 0) + 1 
    INTO numero 
    FROM facturas;
    
    SET nuevo_numero = CONCAT('FAC-', LPAD(numero, 8, '0'));
    SELECT nuevo_numero AS numero_factura;
END //
DELIMITER ;

-- Procedimiento para actualizar stock después de una venta
DELIMITER //
CREATE PROCEDURE sp_actualizar_stock(IN p_id_producto INT, IN p_cantidad INT)
BEGIN
    UPDATE productos 
    SET stock = stock - p_cantidad 
    WHERE id_producto = p_id_producto AND stock >= p_cantidad;
END //
DELIMITER ;
