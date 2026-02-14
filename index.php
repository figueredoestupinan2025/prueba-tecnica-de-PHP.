<?php
/**
 * CONTODA - Sistema de Facturación
 * Lista de Productos - READ
 */

require_once 'funciones.php';

// Obtener parámetros de búsqueda
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$estado = isset($_GET['estado']) ? sanitize($_GET['estado']) : null;

// Obtener productos y categorías
$productos = getProductos($search, $categoria, $estado);
$categorias = getCategorias();
$stats = getEstadisticas();

// Mensaje de sesión
$message = '';
$messageType = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message'], $_SESSION['message_type']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - CONTODA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--dark-color) 0%, #374151 100%);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #e5e7eb;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
            font-weight: 500;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            border-left-color: #818cf8;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(139, 92, 246, 0.3));
            color: #a5b4fc;
            border-left-color: #6366f1;
            font-weight: 600;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                width: 280px;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .mobile-toggle {
                display: block !important;
            }
            
            .page-header {
                padding: 20px;
            }
            
            .page-header h2 {
                font-size: 1.5rem;
            }
            
            .stat-card {
                margin-bottom: 10px;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .btn-action {
                padding: 4px 6px;
            }
            
            .btn-action i {
                font-size: 0.8rem;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-toggle {
                display: none !important;
            }
        }
        
        .mobile-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        
        .sidebar .nav-link i {
            width: 24px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        
        .stat-card.total { border-color: var(--primary-color); }
        .stat-card.active { border-color: var(--success-color); }
        .stat-card.inactive { border-color: var(--danger-color); }
        .stat-card.warning { border-color: var(--warning-color); }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }
        
        .table-card {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .table th, .table td {
            vertical-align: middle;
        }
        
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-activo {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-inactivo {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-stock-bajo {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-sin-stock {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .btn-action {
            padding: 6px 10px;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .search-box {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 10px 15px;
        }
        
        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white fw-bold">
                        <i class="fas fa-store me-2"></i>CONTODA
                    </h4>
                    <p class="text-white-50 small">Sistema de Facturación</p>
                </div>
                
                <nav class="nav flex-column">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-boxes me-2"></i>Productos
                    </a>
                    <a href="categorias.php" class="nav-link">
                        <i class="fas fa-tags me-2"></i>Categorías
                    </a>
                    <a href="clientes.php" class="nav-link">
                        <i class="fas fa-users me-2"></i>Clientes
                    </a>
                    <a href="facturas.php" class="nav-link">
                        <i class="fas fa-file-invoice me-2"></i>Facturas
                    </a>
                    <a href="reportes.php" class="nav-link">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                    <hr class="text-white-50">
                    <a href="configuracion.php" class="nav-link">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </a>
                </nav>
            </div>
            
            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <!-- Encabezado -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold mb-1">
                                <i class="fas fa-boxes me-2"></i>Gestión de Productos
                            </h2>
                            <p class="mb-0 opacity-75">Administra el catálogo de productos de CONTODA</p>
                        </div>
                        <a href="create.php" class="btn btn-light btn-lg">
                            <i class="fas fa-plus me-2"></i>Nuevo Producto
                        </a>
                    </div>
                </div>
                
                <!-- Mensajes -->
                <?php if ($message): ?>
                    <?php echo showMessage($message, $messageType); ?>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card total p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 small">Total Productos</p>
                                    <h3 class="fw-bold mb-0"><?php echo $stats['total']; ?></h3>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-boxes fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card active p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 small">Activos</p>
                                    <h3 class="fw-bold mb-0 text-success"><?php echo $stats['activos']; ?></h3>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card warning p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 small">Stock Bajo</p>
                                    <h3 class="fw-bold mb-0 text-warning"><?php echo $stats['stock_bajo']; ?></h3>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card inactive p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 small">Sin Stock</p>
                                    <h3 class="fw-bold mb-0 text-danger"><?php echo $stats['sin_stock']; ?></h3>
                                </div>
                                <div class="text-danger">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros y Búsqueda -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Buscar</label>
                                <input type="text" name="search" class="form-control search-box" 
                                       placeholder="Nombre, código o descripción..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoría</label>
                                <select name="categoria" class="form-select">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>"
                                                <?php echo ($categoria == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="Activo" <?php echo ($estado == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Inactivo" <?php echo ($estado == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tabla de Productos -->
                <div class="card table-card">
                    <div class="card-body p-0">
                        <?php if (count($productos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $producto): ?>
                                            <tr>
                                                <td><?php echo $producto['id_producto']; ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($producto['codigo_producto'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($producto['nombre_producto']); ?></strong>
                                                    <?php if ($producto['descripcion']): ?>
                                                        <br><small class="text-muted"><?php echo substr(htmlspecialchars($producto['descripcion']), 0, 50); ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                                                <td><?php echo formatCurrency($producto['precio']); ?></td>
                                                <td>
                                                    <?php 
                                                        $stockClass = '';
                                                        $stockBadge = '';
                                                        if ($producto['stock'] == 0) {
                                                            $stockClass = 'badge-sin-stock';
                                                            $stockBadge = 'Sin Stock';
                                                        } elseif ($producto['stock'] <= $producto['stock_minimo']) {
                                                            $stockClass = 'badge-stock-bajo';
                                                            $stockBadge = 'Stock Bajo';
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $stockClass; ?>">
                                                        <?php echo $producto['stock']; ?>
                                                        <?php if ($stockBadge): ?>
                                                            <br><small><?php echo $stockBadge; ?></small>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge-estado badge-<?php echo strtolower($producto['estado']); ?>">
                                                        <?php echo $producto['estado']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="edit.php?id=<?php echo $producto['id_producto']; ?>" 
                                                       class="btn btn-warning btn-action" title="Editar">
                                                        <i class="fas fa-edit text-white"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?php echo $producto['id_producto']; ?>" 
                                                       class="btn btn-danger btn-action" title="Eliminar"
                                                       onclick="return confirm('¿Está seguro de eliminar este producto?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h4>No se encontraron productos</h4>
                                <p>Intenta con otros criterios de búsqueda o agrega un nuevo producto.</p>
                                <a href="create.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Producto
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Pie de tabla -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="text-muted mb-0">Mostrando <?php echo count($productos); ?> producto(s)</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
