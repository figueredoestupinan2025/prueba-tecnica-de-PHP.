<?php
/**
 * CONTODA - Sistema de Facturación
 * Reportes
 */

require_once 'funciones.php';

// Obtener estadísticas
try {
    $pdo = getConnection();
    
    // Total ventas hoy
    $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) as total FROM facturas WHERE DATE(fecha_factura) = CURDATE() AND estado != 'Anulada'");
    $ventasHoy = $stmt->fetch()['total'];
    
    // Total ventas mes
    $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) as total FROM facturas WHERE MONTH(fecha_factura) = MONTH(CURDATE()) AND YEAR(fecha_factura) = YEAR(CURDATE()) AND estado != 'Anulada'");
    $ventasMes = $stmt->fetch()['total'];
    
    // Total facturas hoy
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM facturas WHERE DATE(fecha_factura) = CURDATE() AND estado != 'Anulada'");
    $facturasHoy = $stmt->fetch()['total'];
    
    // Productos con stock bajo
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock > 0 AND stock <= stock_minimo");
    $stockBajo = $stmt->fetch()['total'];
    
    // Productos sin stock
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock = 0");
    $sinStock = $stmt->fetch()['total'];
    
    // Productos por categoría
    $stmt = $pdo->query("
        SELECT c.nombre_categoria, COUNT(p.id_producto) as total, SUM(p.stock) as stock
        FROM categorias c
        LEFT JOIN productos p ON c.id_categoria = p.id_categoria
        GROUP BY c.id_categoria
    ");
    $productosCategoria = $stmt->fetchAll();
    
    // Top productos vendidos
    $stmt = $pdo->query("
        SELECT p.nombre_producto, SUM(d.cantidad) as cantidad_vendida, SUM(d.subtotal) as total_vendido
        FROM detalle_factura d
        INNER JOIN productos p ON d.id_producto = p.id_producto
        INNER JOIN facturas f ON d.id_factura = f.id_factura
        WHERE f.estado != 'Anulada'
        GROUP BY d.id_producto
        ORDER BY cantidad_vendida DESC
        LIMIT 10
    ");
    $topProductos = $stmt->fetchAll();
    
    // Últimas facturas
    $stmt = $pdo->query("
        SELECT f.*, c.nombre_cliente
        FROM facturas f
        LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
        WHERE f.estado != 'Anulada'
        ORDER BY f.fecha_factura DESC, f.id_factura DESC
        LIMIT 10
    ");
    $ultimasFacturas = $stmt->fetchAll();
    
} catch (Exception $e) {
    $ventasHoy = $ventasMes = $facturasHoy = $stockBajo = $sinStock = 0;
    $productosCategoria = $topProductos = $ultimasFacturas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - CONTODA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .stat-card {
            border-left: 4px solid;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white fw-bold"><i class="fas fa-store me-2"></i>CONTODA</h4>
                    <p class="text-white-50 small">Sistema de Facturación</p>
                </div>
                <nav class="nav flex-column">
                    <a href="index.php" class="nav-link"><i class="fas fa-boxes me-2"></i>Productos</a>
                    <a href="categorias.php" class="nav-link"><i class="fas fa-tags me-2"></i>Categorías</a>
                    <a href="clientes.php" class="nav-link"><i class="fas fa-users me-2"></i>Clientes</a>
                    <a href="facturas.php" class="nav-link"><i class="fas fa-file-invoice me-2"></i>Facturas</a>
                    <a href="reportes.php" class="nav-link active"><i class="fas fa-chart-bar me-2"></i>Reportes</a>
                    <hr class="text-white-50">
                    <a href="configuracion.php" class="nav-link"><i class="fas fa-cog me-2"></i>Configuración</a>
                </nav>
            </div>
            
            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="page-header bg-gradient mb-4" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 30px; border-radius: 12px; color: white;">
                    <h2 class="fw-bold mb-1"><i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas</h2>
                    <p class="mb-0 opacity-75">Resumen de ventas e inventario</p>
                </div>
                
                <!-- Estadísticas Generales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card p-3" style="border-color: var(--success-color);">
                            <p class="text-muted mb-0 small">Ventas Hoy</p>
                            <h3 class="fw-bold text-success"><?php echo formatCurrency($ventasHoy); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card p-3" style="border-color: var(--primary-color);">
                            <p class="text-muted mb-0 small">Ventas del Mes</p>
                            <h3 class="fw-bold"><?php echo formatCurrency($ventasMes); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card p-3" style="border-color: var(--warning-color);">
                            <p class="text-muted mb-0 small">Facturas Hoy</p>
                            <h3 class="fw-bold text-warning"><?php echo $facturasHoy; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card p-3" style="border-color: var(--danger-color);">
                            <p class="text-muted mb-0 small">Stock Bajo</p>
                            <h3 class="fw-bold text-danger"><?php echo $stockBajo; ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Productos por Categoría -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Productos por Categoría</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Categoría</th>
                                                <th>Productos</th>
                                                <th>Stock Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productosCategoria as $cat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($cat['nombre_categoria']); ?></td>
                                                    <td><?php echo $cat['total']; ?></td>
                                                    <td><?php echo $cat['stock'] ?? 0; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Productos Vendidos -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Productos Vendidos</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topProductos as $prod): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($prod['nombre_producto']); ?></td>
                                                    <td><?php echo $prod['cantidad_vendida']; ?></td>
                                                    <td><?php echo formatCurrency($prod['total_vendido']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Últimas Facturas -->
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Últimas Facturas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimasFacturas as $fac): ?>
                                        <tr>
                                            <td><strong><?php echo $fac['numero_factura']; ?></strong></td>
                                            <td><?php echo date('d/m/Y', strtotime($fac['fecha_factura'])); ?></td>
                                            <td><?php echo htmlspecialchars($fac['nombre_cliente']); ?></td>
                                            <td><strong><?php echo formatCurrency($fac['total']); ?></strong></td>
                                            <td>
                                                <?php 
                                                    $estadoClass = $fac['estado'] == 'Pagada' ? 'success' : ($fac['estado'] == 'Pendiente' ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?php echo $estadoClass; ?>"><?php echo $fac['estado']; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
