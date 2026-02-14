<?php
/**
 * CONTODA - Sistema de Facturación
 * Configuración
 */

require_once 'funciones.php';

$message = '';
$messageType = '';

// Procesar formulario de configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    // Aquí podrías guardar la configuración en un archivo o base de datos
    $message = 'Configuración guardada correctamente';
    $messageType = 'success';
}

// Obtener información del sistema
$stats = getEstadisticas();

try {
    $pdo = getConnection();
    
    // Total clientes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
    $totalClientes = $stmt->fetch()['total'];
    
    // Total facturas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM facturas WHERE estado != 'Anulada'");
    $totalFacturas = $stmt->fetch()['total'];
    
    // Valor inventario
    $stmt = $pdo->query("SELECT COALESCE(SUM(precio * stock), 0) as total FROM productos");
    $valorInventario = $stmt->fetch()['total'];
    
} catch (Exception $e) {
    $totalClientes = $totalFacturas = $valorInventario = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - CONTODA</title>
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
                    <a href="reportes.php" class="nav-link"><i class="fas fa-chart-bar me-2"></i>Reportes</a>
                    <hr class="text-white-50">
                    <a href="configuracion.php" class="nav-link active"><i class="fas fa-cog me-2"></i>Configuración</a>
                </nav>
            </div>
            
            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="page-header bg-gradient mb-4" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 30px; border-radius: 12px; color: white;">
                    <h2 class="fw-bold mb-1"><i class="fas fa-cog me-2"></i>Configuración</h2>
                    <p class="mb-0 opacity-75">Configura el sistema</p>
                </div>
                
                <?php if ($message): ?>
                    <?php echo showMessage($message, $messageType); ?>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Información del Sistema -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <td><strong>Nombre de la Empresa</strong></td>
                                        <td>CONTODA</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Versión del Sistema</strong></td>
                                        <td>1.0.0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Productos</strong></td>
                                        <td><?php echo $stats['total']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Clientes</strong></td>
                                        <td><?php echo $totalClientes; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Facturas</strong></td>
                                        <td><?php echo $totalFacturas; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Valor del Inventario</strong></td>
                                        <td><?php echo formatCurrency($valorInventario); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de la Empresa -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Datos de la Empresa</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="accion" value="guardar">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la Empresa</label>
                                        <input type="text" class="form-control" value="CONTODA" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">NIT</label>
                                        <input type="text" class="form-control" placeholder="123456789-1">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" placeholder="Dirección de la empresa">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" placeholder="3001234567">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="contacto@contoda.com">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Opciones Adicionales -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <a href="import.php" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-database me-2"></i>Reimportar Base de Datos
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="index.php?exportar=productos" class="btn btn-outline-success w-100">
                                            <i class="fas fa-file-excel me-2"></i>Exportar Productos
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="facturas.php" class="btn btn-outline-info w-100">
                                            <i class="fas fa-file-invoice me-2"></i>Ver Todas las Facturas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
