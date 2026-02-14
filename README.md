# CONTODA - Sistema de Facturación

<p align="center">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
  <img src="https://img.shields.io/badge/PHP-8.0+-purple.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/MySQL-8.0+-blue.svg" alt="MySQL Version">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-orange.svg" alt="Bootstrap Version">
</p>

## 📋 Descripción

**CONTODA** es un sistema de facturación y gestión de inventario diseñado para empresas comerciales que venden productos como maquillaje, bolsos, zapatos y accesorios. El sistema permite administrar el catálogo de productos, gestionar clientes y crear facturas de manera eficiente.

### ✨ Características Principales

- 📦 **Gestión de Productos** - CRUD completo con código único, precio, stock y categorías
- 🏷️ **Categorías** - Administración de categorías de productos
- 👥 **Gestión de Clientes** - Registro de clientes con diferentes tipos de documento
- 🧾 **Facturación** - Creación de facturas con cálculo automático de totales
- 📊 **Reportes** - Visualización de estadísticas y reportes de ventas
- ⚙️ **Configuración** - Panel de configuración del sistema

## 🛠️ Tecnologías Utilizadas

| Tecnología | Descripción |
|------------|-------------|
| **PHP 8.0+** | Lenguaje de programación del lado del servidor |
| **MySQL 8.0+** | Sistema de gestión de base de datos |
| **Bootstrap 5.3** | Framework CSS para diseño responsivo |
| **Font Awesome 6.4** | Biblioteca de iconos |
| **PDO** | Acceso uniforme a bases de datos |

## 📁 Estructura del Proyecto

```
contoda/
├── config.php           # Configuración de base de datos
├── funciones.php        # Funciones auxiliares del sistema
├── index.php           # Listado de productos (Página principal)
├── create.php          # Formulario para crear productos
├── edit.php            # Formulario para editar productos
├── delete.php          # Eliminación de productos
├── categorias.php      # Gestión de categorías
├── clientes.php        # Gestión de clientes
├── facturas.php        # Gestión de facturas
├── ver_factura.php    # Visualización de facturas
├── reportes.php       # Reportes y estadísticas
├── configuracion.php   # Configuración del sistema
├── login.php          # Página de inicio de sesión
├── logout.php         # Cierre de sesión
├── validar.php        # Validación AJAX
├── import.php         # Importador de base de datos
├── database.sql       # Esquema de base de datos
└── README.md         # Este archivo
```

## 📊 Modelo de Base de Datos

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `categorias` | Categorías de productos (Maquillaje, Bolsos, Zapatos, Accesorios) |
| `productos` | Catálogo de productos con stock y precios |
| `clientes` | Registro de clientes con información de contacto |
| `facturas` | Encabezados de facturas |
| `detalle_factura` | Detalles de cada línea de factura |

### Diagrama de Relaciones

```
┌──────────────┐       ┌──────────────┐
│  categorias  │──1:N──│   productos  │
└──────────────┘       └──────────────┘
                                      │
                                      │ N:M
                               ┌──────┴──────┐
                               │detalle_factura│
                               └──────┬──────┘
                                      │
                      ┌───────────────┴───────────────┐
                      │                             │
                 ┌────┴────┐                  ┌──────┴─────┐
                 │productos │                  │  facturas  │
                 └──────────┘                  └──────┬─────┘
                                                      │
                                                 ┌────┴────┐
                                                 │ clientes │
                                                 └──────────┘
```

## 🚀 Instalación

### Requisitos Previos

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Servidor web (Apache/Nginx) o XAMPP/WAMP

### Pasos de Instalación

1. **Clonar o descargar el proyecto**

2. **Configurar la base de datos**
   
   - Abrir XAMPP Control Panel
   - Iniciar Apache y MySQL
   - Acceder a phpMyAdmin (http://localhost/phpmyadmin)
   - Crear una base de datos llamada `contoda`
   - Importar el archivo `database.sql`

   Opcionalmente, usar el importador integrado:
   
```
   http://localhost/contoda/import.php
   
```

3. **Configurar la conexión**
   
   Editar `config.php` si es necesario:
   
```
php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'contoda');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Contraseña de MySQL
   
```

4. **Ejecutar el proyecto**
   
   Acceder a: http://localhost/contoda

## 📖 Guía de Uso

### Gestión de Productos

1. Desde la página principal (index.php)可以看到 todos los productos
2. Click en "Nuevo Producto" para agregar uno nuevo
3. Click en el ícono de edición para modificar un producto
4. Click en el ícono de eliminación para borrar un producto

### Crear una Factura

1. Navegar a "Facturas" desde el menú lateral
2. Click en "Nueva Factura"
3. Seleccionar el cliente
4. Elegir la forma de pago
5. Agregar productos y cantidades
6. El sistema calculará el total automáticamente
7. Click en "Crear Factura" para guardar

### Generar Reportes

1. Navegar a "Reportes" desde el menú
2. Visualizar estadísticas de productos
3. Ver información de ventas y clientes

## 🔒 Seguridad

El sistema implementa las siguientes medidas de seguridad:

- ✅ **PDO Prepared Statements** - Previene inyección SQL
- ✅ **Sanitización de entrada** - Limpia datos del usuario
- ✅ **Gestión de sesiones** - Control de acceso seguro
- ✅ **Validación de datos** - Verificación de integridad

### Recomendaciones para Producción

- Cambiar la contraseña de root en MySQL
- Implementar autenticación de usuarios con login/contraseña
- Configurar HTTPS/SSL
- Realizar backups regulares de la base de datos
- Implementar logs de auditoría

## 📱 Diseño Responsivo

El sistema está diseñado para funcionar en múltiples dispositivos:

- 📱 **Móviles** - Menú adaptativo y diseño responsive
-  Tablet** - Interfaz optimizada para tablets
- 💻 **Escritorio** - Experiencia completa de usuario

## 🤝 Contribución

Para contribuir al proyecto:

1. Fork del repositorio
2. Crear una rama (`git checkout -b feature/nueva-caracteristica`)
3. Commit de los cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crear un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## 👤 Autor

**CONTODA - Sistema de Facturación**

Desarrollado para la empresa CONTODA, una compañía dedicada a la venta de productos de belleza, moda y accesorios.

---

<p align="center">
  <strong>¡Gracias por usar CONTODA!</strong>
  <br>
  <sub>Versión 1.0.0</sub>
</p>
