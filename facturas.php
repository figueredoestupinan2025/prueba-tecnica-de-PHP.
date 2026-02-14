<?php
/**
 * CONTODA - Sistema de Facturación
 * Gestión de Facturas
 */

require_once 'funciones.php';

$message = '';
$messageType = '';

// Crear factura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $id_cliente = intval($_POST['id_cliente']);
    $forma_pago = sanitize($_POST['forma_pago']);
    $observaciones = sanitize($_POST['observaciones']);
    $productos = $_POST['productos'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    
    try {
        $pdo = getConnection();
        $pdo->beginTransaction();
        
        // Generar número de factura
        $stmt = $pdo->query("SELECT COALESCE(MAX(CAST(SUBSTRING(numero_factura, 5) AS UNSIGNED)), 0) + 1 as siguiente FROM facturas");
        $siguiente = $stmt->fetch()['siguiente'];
        $numero_factura = 'FAC-' . str_pad($siguiente, 8, '0', STR_PAD_LEFT);
        
        // Calcular total
        $total = 0;
        for ($i = 0; $i < count($productos); $i++) {
            $id_producto = intval($productos[$i]);
            $cantidad = intval($cantidades[$i]);
            
            $stmt = $pdo->prepare("SELECT precio, stock FROM productos WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch();
            
            if ($producto && $producto['stock'] >= $cantidad) {
                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;
            }
        }
        
        // Insertar factura
        $stmt = $pdo->prepare("INSERT INTO facturas (id_cliente, numero_factura, fecha_factura, forma_pago, total, estado, observaciones) VALUES (?, ?, CURDATE(), ?, ?, 'Pagada', ?)");
        $stmt->execute([$id_cliente, $numero_factura, $forma_pago, $total, $observaciones]);
        $id_factura = $pdo->lastInsertId();
        
        // Insertar detalles y actualizar stock
        for ($i = 0; $i < count($productos); $i++) {
            $id_producto = intval($productos[$i]);
            $cantidad = intval($cantidades[$i]);
            
            $stmt = $pdo->prepare("SELECT precio, stock FROM productos WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch();
            
            if ($producto && $producto['stock'] >= $cantidad) {
                $subtotal = $producto['precio'] * $cantidad;
                
                $stmt = $pdo->prepare("INSERT INTO detalle_factura (id_factura, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$id_factura, $id_producto, $cantidad, $producto['precio'], $subtotal]);
                
                // Actualizar stock
                $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
                $stmt->execute([$cantidad, $id_producto]);
            }
        }
        
        $pdo->commit();
        $message = 'Factura creada correctamente: ' . $numero_factura;
        $messageType = 'success';
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = 'Error al crear factura: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Anular factura
if (isset($_GET['anular'])) {
    $id = intval($_GET['anular']);
    try {
        $pdo = getConnection();
        
        // Restaurar stock
        $stmt = $pdo->prepare("SELECT id_producto, cantidad FROM detalle_factura WHERE id_factura = ?");
        $stmt->execute([$id]);
        $detalles = $stmt->fetchAll();
        
        foreach ($detalles as $detalle) {
            $stmt = $pdo->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
            $stmt->execute([$detalle['cantidad'], $detalle['id_producto']]);
        }
        
        // Cambiar estado
        $stmt = $pdo->prepare("UPDATE facturas SET estado = 'Anulada' WHERE id_factura = ?");
        $stmt->execute([$id]);
        
        $message = 'Factura anulada correctamente';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error al anular factura: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Obtener facturas
try {
    $pdo = getConnection();
    $facturas = $pdo->query("
        SELECT f.*, c.nombre_cliente, c.numero_documento 
        FROM facturas f 
        LEFT JOIN clientes c ON f.id_cliente = c.id_cliente 
        ORDER BY f.id_factura DESC
    ")->fetchAll();
} catch (Exception $e) {
    $facturas = [];
}

// Obtener clientes y productos para el formulario
try {
    $clientes = $pdo->query("SELECT * FROM clientes WHERE estado = 'Activo' ORDER BY nombre_cliente")->fetchAll();
    $productos = $pdo->query("SELECT * FROM productos WHERE estado = 'Activo' AND stock > 0 ORDER BY nombre_producto")->fetchAll();
} catch (Exception $e) {
    $clientes = [];
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Facturas - CONTODA</title>
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
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
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
                    <a href="facturas.php" class="nav-link active"><i class="fas fa-file-invoice me-2"></i>Facturas</a>
                    <a href="reportes.php" class="nav-link"><i class="fas fa-chart-bar me-2"></i>Reportes</a>
                    <hr class="text-white-50">
                    <a href="configuracion.php" class="nav-link"><i class="fas fa-cog me-2"></i>Configuración</a>
                </nav>
            </div>
            
            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="page-header bg-gradient mb-4" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 30px; border-radius: 12px; color: white;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold mb-1"><i class="fas fa-file-invoice me-2"></i>Gestión de Facturas</h2>
                            <p class="mb-0 opacity-75">Crea y manage facturas</p>
                        </div>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#facturaModal">
                            <i class="fas fa-plus me-2"></i>Nueva Factura
                        </button>
                    </div>
                </div>
                
                <?php if ($message): ?>
                    <?php echo showMessage($message, $messageType); ?>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                                    <tr>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Forma de Pago</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($facturas as $fac): ?>
                                        <tr>
                                            <td><strong><?php echo $fac['numero_factura']; ?></strong></td>
                                            <td><?php echo date('d/m/Y', strtotime($fac['fecha_factura'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($fac['nombre_cliente']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($fac['numero_documento']); ?></small>
                                            </td>
                                            <td><strong><?php echo formatCurrency($fac['total']); ?></strong></td>
                                            <td><?php echo $fac['forma_pago']; ?></td>
                                            <td>
                                                <?php 
                                                    $estadoClass = '';
                                                    if ($fac['estado'] == 'Pagada') $estadoClass = 'success';
                                                    elseif ($fac['estado'] == 'Pendiente') $estadoClass = 'warning';
                                                    else $estadoClass = 'danger';
                                                ?>
                                                <span class="badge bg-<?php echo $estadoClass; ?>"><?php echo $fac['estado']; ?></span>
                                            </td>
                                            <td>
                                                <a href="ver_factura.php?id=<?php echo $fac['id_factura']; ?>" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
                                                <?php if ($fac['estado'] != 'Anulada'): ?>
                                                <a href="?anular=<?php echo $fac['id_factura']; ?>" class="btn btn-danger btn-sm" title="Anular" onclick="return confirm('¿Está seguro de anular esta factura?');">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                <?php endif; ?>
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
    
    <!-- Modal Nueva Factura -->
    <div class="modal fade" id="facturaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                    <h5 class="modal-title">Nueva Factura</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="facturaForm">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cliente</label>
                                <select name="id_cliente" class="form-select" required>
                                    <option value="">Seleccionar cliente</option>
                                    <?php foreach ($clientes as $cli): ?>
                                        <option value="<?php echo $cli['id_cliente']; ?>">
                                            <?php echo htmlspecialchars($cli['nombre_cliente'] . ' - ' . $cli['numero_documento']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Forma de Pago</label>
                                <select name="forma_pago" class="form-select" required>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta Débito">Tarjeta Débito</option>
                                    <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Consignación">Consignación</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <hr>
                        <h6>Productos</h6>
                        <div id="productos-container">
                            <div class="row producto-row mb-2">
                                <div class="col-md-6">
                                    <select name="productos[]" class="form-select producto-select" required>
                                        <option value="">Seleccionar producto</option>
                                        <?php foreach ($productos as $prod): ?>
                                            <option value="<?php echo $prod['id_producto']; ?>" data-precio="<?php echo $prod['precio']; ?>" data-stock="<?php echo $prod['stock']; ?>">
                                                <?php echo htmlspecialchars($prod['nombre_producto'] . ' - ' . formatCurrency($prod['precio']) . ' (Stock: ' . $prod['stock'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="cantidades[]" class="form-control cantidad-input" placeholder="Cantidad" min="1" value="1" required>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge bg-secondary subtotal-display">$0</span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-remove" disabled><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-secondary btn-sm" id="add-producto">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                        
                        <hr>
                        <div class="d-flex justify-content-end">
                            <h4>Total: <span id="total-factura">$0</span></h4>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Factura</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('productos-container');
            const addBtn = document.getElementById('add-producto');
            
            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.producto-row').forEach(row => {
                    const select = row.querySelector('.producto-select');
                    const cantidad = parseInt(row.querySelector('.cantidad-input').value) || 0;
                    const option = select.options[select.selectedIndex];
                    if (option && option.value) {
                        const precio = parseFloat(option.dataset.precio) || 0;
                        total += precio * cantidad;
                        row.querySelector('.subtotal-display').textContent = '$' + (precio * cantidad).toLocaleString();
                    }
                });
                document.getElementById('total-factura').textContent = '$' + total.toLocaleString();
            }
            
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('producto-select') || e.target.classList.contains('cantidad-input')) {
                    updateTotal();
                }
            });
            
            addBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'row producto-row mb-2';
                row.innerHTML = `
                    <div class="col-md-6">
                        <select name="productos[]" class="form-select producto-select" required>
                            <option value="">Seleccionar producto</option>
                            <?php foreach ($productos as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>" data-precio="<?php echo $prod['precio']; ?>" data-stock="<?php echo $prod['stock']; ?>">
                                    <?php echo htmlspecialchars($prod['nombre_producto'] . ' - ' . formatCurrency($prod['precio']) . ' (Stock: ' . $prod['stock'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="cantidades[]" class="form-control cantidad-input" placeholder="Cantidad" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-secondary subtotal-display">$0</span>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove"><i class="fas fa-times"></i></button>
                    </div>
                `;
                container.appendChild(row);
            });
            
            container.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove')) {
                    const row = e.target.closest('.producto-row');
                    if (container.querySelectorAll('.producto-row').length > 1) {
                        row.remove();
                        updateTotal();
                    }
                }
            });
        });
    </script>
</body>
</html>
