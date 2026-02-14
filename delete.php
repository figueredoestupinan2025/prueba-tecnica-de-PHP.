<?php
/**
 * CONTODA - Sistema de Facturación
 * Eliminar Producto - DELETE
 */

require_once 'funciones.php';

$message = '';
$messageType = '';

// Verificar que se recibió el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_producto = intval($_GET['id']);
$producto = getProductoById($id_producto);

// Si el producto no existe, redireccionar
if (!$producto) {
    header("Location: index.php");
    exit;
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = deleteProducto($id_producto);
    
    if ($result['success']) {
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = 'success';
        header("Location: index.php");
        exit;
    } else {
        $message = $result['message'];
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producto - CONTODA</title>
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
            --danger-color: #ef4444;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1f2937 0%, #374151 100%);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
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
        
        .delete-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px;
        }
        
        .product-info {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .product-info .row {
            margin-bottom: 10px;
        }
        
        .product-info .row:last-child {
            margin-bottom: 0;
        }
        
        .product-info label {
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
        }
        
        .product-info .value {
            color: #1f2937;
        }
        
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .warning-box i {
            color: #f59e0b;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, var(--danger-color));
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
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-boxes me-2"></i>Productos
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-tags me-2"></i>Categorías
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-users me-2"></i>Clientes
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-file-invoice me-2"></i>Facturas
                    </a>
                    <hr class="text-white-50">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </a>
                </nav>
            </div>
            
            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="delete-container">
                    <!-- Botón Volver -->
                    <div class="mb-3">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Productos
                        </a>
                    </div>
                    
                    <!-- Mensajes -->
                    <?php if ($message): ?>
                        <?php echo showMessage($message, $messageType); ?>
                    <?php endif; ?>
                    
                    <!-- Formulario de Eliminación -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-trash me-2"></i>Eliminar Producto
                            </h4>
                            <p class="mb-0 mt-1 opacity-75">Esta acción no se puede deshacer</p>
                        </div>
                        <div class="card-body p-4">
                            <!-- Información del Producto -->
                            <div class="product-info">
                                <div class="row">
                                    <div class="col-4">
                                        <label>ID:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <?php echo $producto['id_producto']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Código:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($producto['codigo_producto'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Nombre:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <strong><?php echo htmlspecialchars($producto['nombre_producto']); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Categoría:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Precio:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <?php echo formatCurrency($producto['precio']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Stock:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <?php echo $producto['stock']; ?> unidades
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <label>Estado:</label>
                                    </div>
                                    <div class="col-8 value">
                                        <span class="badge-estado badge-<?php echo strtolower($producto['estado']); ?>">
                                            <?php echo $producto['estado']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Caja de Advertencia -->
                            <div class="warning-box">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Advertencia:</strong> Si elimina este producto, no podrá recuperarlo. 
                                Asegúrese de que este producto no esté asociado a ninguna factura.
                            </div>
                            
                            <!-- Formulario -->
                            <form method="POST">
                                <!-- Botones -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Eliminar Producto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
