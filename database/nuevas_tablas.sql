-- ================================================================
--  TechStore POS — SQL para módulos nuevos
--  Ejecutar en phpMyAdmin sobre la BD existente `techstore`
-- ================================================================

USE techstore;

-- ── Configuración del negocio (para el ticket térmico) ──
CREATE TABLE IF NOT EXISTS configuracion (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    clave       VARCHAR(100) NOT NULL UNIQUE,
    valor       TEXT,
    descripcion VARCHAR(255)
);

INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES
('negocio_nombre',    'TechStore Bolivia',              'Nombre del negocio'),
('negocio_slogan',    'Tu tienda de tecnología',        'Slogan o subtítulo'),
('negocio_direccion', 'Av. Montes 456, Santa Cruz',     'Dirección del local'),
('negocio_telefono',  '+591 3 000-0000',                'Teléfono de contacto'),
('negocio_email',     'ventas@techstore.bo',            'Email de contacto'),
('negocio_nit',       '1234567890',                     'NIT de la empresa'),
('ticket_mensaje',    '¡Gracias por su compra!\nVuelva pronto :)',  'Mensaje al pie del ticket'),
('ticket_garantia',   'Conserve su comprobante para hacer válida la garantía.', 'Nota de garantía');

-- ── Agregar campo activo a proveedor si no existe ──
ALTER TABLE proveedor ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1;
