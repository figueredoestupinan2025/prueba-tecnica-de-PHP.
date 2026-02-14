# Modelo Entidad-Relación - CONTODA

## Diagrama ER

```
mermaid
erDiagram
    CATEGORIAS ||--o{ PRODUCTOS : "contiene"
    CLIENTES ||--o{ FACTURAS : "genera"
    FACTURAS ||--o{ DETALLE_FACTURA : "contiene"
    PRODUCTOS ||--o{ DETALLE_FACTURA : "aparece_en"

    CATEGORIAS {
        int id_categoria PK
        varchar nombre_categoria UK
        text descripcion
        enum estado
        timestamp fecha_creacion
        timestamp fecha_actualizacion
    }

    PRODUCTOS {
        int id_producto PK
        int id_categoria FK
        varchar codigo_producto UK
        varchar nombre_producto
        text descripcion
        decimal precio
        int stock
        int stock_minimo
        varchar imagen
        enum estado
        timestamp fecha_creacion
        timestamp fecha_actualizacion
    }

    CLIENTES {
        int id_cliente PK
        enum tipo_documento
        varchar numero_documento UK
        varchar nombre_cliente
        varchar email
        varchar telefono
        text direccion
        varchar ciudad
        varchar departamento
        enum tipo_cliente
        enum estado
        timestamp fecha_creacion
        timestamp fecha_actualizacion
    }

    FACTURAS {
        int id_factura PK
        int id_cliente FK
        varchar numero_factura UK
        date fecha_factura
        time hora_factura
        decimal subtotal
        decimal descuento
        decimal impuesto
        decimal total
        enum forma_pago
        enum estado
        text observaciones
        timestamp fecha_creacion
        timestamp fecha_actualizacion
    }

    DETALLE_FACTURA {
        int id_detalle PK
        int id_factura FK
        int id_producto FK
        int cantidad
        decimal precio_unitario
        decimal descuento
        decimal subtotal
    }
```

## Descripción de Entidades

### 1. CATEGORIAS
Almacena las categorías de productos.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_categoria | INT | Identificador único (PK) |
| nombre_categoria | VARCHAR(100) | Nombre de la categoría (UK) |
| descripcion | TEXT | Descripción de la categoría |
| estado | ENUM | Estado (Activo/Inactivo) |
| fecha_creacion | TIMESTAMP | Fecha de creación |
| fecha_actualizacion | TIMESTAMP | Fecha de última actualización |

### 2. PRODUCTOS
Catálogo de productos de la empresa.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_producto | INT | Identificador único (PK) |
| id_categoria | INT | FK a categorías |
| codigo_producto | VARCHAR(50) | Código único del producto |
| nombre_producto | VARCHAR(200) | Nombre del producto |
| descripcion | TEXT | Descripción del producto |
| precio | DECIMAL(10,2) | Precio del producto |
| stock | INT | Cantidad en stock |
| stock_minimo | INT | Stock mínimo para alertas |
| imagen | VARCHAR(255) | Ruta de la imagen |
| estado | ENUM | Estado (Activo/Inactivo) |
| fecha_creacion | TIMESTAMP | Fecha de creación |
| fecha_actualizacion | TIMESTAMP | Fecha de última actualización |

### 3. CLIENTES
Registro de clientes de la empresa.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_cliente | INT | Identificador único (PK) |
| tipo_documento | ENUM | Tipo de documento |
| numero_documento | VARCHAR(20) | Número de documento (UK) |
| nombre_cliente | VARCHAR(200) | Nombre del cliente |
| email | VARCHAR(100) | Correo electrónico |
| telefono | VARCHAR(20) | Teléfono de contacto |
| direccion | TEXT | Dirección de entrega |
| ciudad | VARCHAR(100) | Ciudad |
| departamento | VARCHAR(100) | Departamento |
| tipo_cliente | ENUM | Tipo de cliente |
| estado | ENUM | Estado (Activo/Inactivo) |
| fecha_creacion | TIMESTAMP | Fecha de creación |
| fecha_actualizacion | TIMESTAMP | Fecha de última actualización |

### 4. FACTURAS
Encabezados de facturas de venta.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_factura | INT | Identificador único (PK) |
| id_cliente | INT | FK a clientes |
| numero_factura | VARCHAR(20) | Número de factura (UK) |
| fecha_factura | DATE | Fecha de la factura |
| hora_factura | TIME | Hora de la factura |
| subtotal | DECIMAL(10,2) | Subtotal sin impuestos |
| descuento | DECIMAL(10,2) | Descuento aplicado |
| impuesto | DECIMAL(10,2) | Monto de impuestos |
| total | DECIMAL(10,2) | Total de la factura |
| forma_pago | ENUM | Forma de pago |
| estado | ENUM | Estado (Pendiente/Pagada/Anulada) |
| observaciones | TEXT | Observaciones adicionales |
| fecha_creacion | TIMESTAMP | Fecha de creación |
| fecha_actualizacion | TIMESTAMP | Fecha de última actualización |

### 5. DETALLE_FACTURA
Líneas de detalle de cada factura.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_detalle | INT | Identificador único (PK) |
| id_factura | INT | FK a facturas |
| id_producto | INT | FK a productos |
| cantidad | INT | Cantidad del producto |
| precio_unitario | DECIMAL(10,2) | Precio unitario |
| descuento | DECIMAL(10,2) | Descuento por línea |
| subtotal | DECIMAL(10,2) | Subtotal (cantidad × precio) |

## Relaciones

| Relación | Tipo | Descripción |
|----------|------|-------------|
| CATEGORIAS → PRODUCTOS | 1:N | Una categoría puede tener muchos productos |
| CLIENTES → FACTURAS | 1:N | Un cliente puede tener muchas facturas |
| FACTURAS → DETALLE_FACTURA | 1:N | Una factura puede tener muchos detalles |
| PRODUCTOS → DETALLE_FACTURA | 1:N | Un producto puede aparecer en muchas líneas de detalle |

## Reglas de Negocio

1. **Eliminación de categorías**: No se puede eliminar una categoría que tenga productos asociados.
2. **Eliminación de productos**: No se puede eliminar un producto que aparezca en facturas.
3. **Stock**: El stock se descuenta automáticamente al crear una factura.
4. **Anulación de facturas**: Al anular una factura, el stock se restaura automáticamente.
5. **Código único**: Cada producto debe tener un código único.

## Notas Técnicas

- Motor de base de datos: InnoDB
- Charset: utf8mb4
- Todas las tablas tienen timestamps de creación y actualización
- Las FK tienen restricciones de integridad referencial
