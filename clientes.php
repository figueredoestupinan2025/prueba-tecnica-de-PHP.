<?php
/**
 * CONTODA - Sistema de Facturación
 * Gestión de Clientes
 */

require_once 'funciones.php';

$message = '';
$messageType = '';

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $data = [
            'tipo_documento' => sanitize($_POST['tipo_documento']),
            'numero_documento' => sanitize($_POST['numero_documento']),
            'nombre_cliente' => sanitize($_POST['nombre_cliente']),
            'email' => sanitize($_POST['email']),
            'telefono' => sanitize($_POST['telefono']),
            'direccion' => sanitize($_POST['direccion']),
            'ciudad' => sanitize($_POST['ciudad']),
            'departamento' => sanitize($_POST['departamento']),
            'tipo_cliente' => $_POST['tipo_cliente'] ?? 'Persona Natural',
            'estado' => $_POST['estado'] ?? 'Activo'
        ];
        
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("INSERT INTO clientes (tipo_documento, numero_documento, nombre_cliente, email, telefono, direccion, ciudad, departamento, tipo_cliente, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['tipo_documento'], $data['numero_documento'], $data['nombre_cliente'], $data['email'], $data['telefono'], $data['direccion'], $data['ciudad'], $data['departamento'], $data['tipo_cliente'], $data['estado']]);
            $message = 'Cliente creado correctamente';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error al crear cliente: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
        $id = intval($_POST['id_cliente']);
        $data = [
            'tipo_documento' => sanitize($_POST['tipo_documento']),
            'numero_documento' => sanitize($_POST['numero_documento']),
            'nombre_cliente' => sanitize($_POST['nombre_cliente']),
            'email' => sanitize($_POST['email']),
            'telefono' => sanitize($_POST['telefono']),
            'direccion' => sanitize($_POST['direccion']),
            'ciudad' => sanitize($_POST['ciudad']),
            'departamento' => sanitize($_POST['departamento']),
            'tipo_cliente' => $_POST['tipo_cliente'] ?? 'Persona Natural',
            'estado' => $_POST['estado'] ?? 'Activo'
        ];
        
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("UPDATE clientes SET tipo_documento = ?, numero_documento = ?, nombre_cliente = ?, email = ?, telefono = ?, direccion = ?, ciudad = ?, departamento = ?, tipo_cliente = ?, estado = ? WHERE id_cliente = ?");
            $stmt->execute([$data['tipo_documento'], $data['numero_documento'], $data['nombre_cliente'], $data['email'], $data['telefono'], $data['direccion'], $data['ciudad'], $data['departamento'], $data['tipo_cliente'], $data['estado'], $id]);
            $message = 'Cliente actualizado correctamente';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error al actualizar cliente: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $message = 'Cliente eliminado correctamente';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error al eliminar cliente: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Obtener todos los clientes
try {
    $pdo = getConnection();
    $clientes = $pdo->query("SELECT * FROM clientes ORDER BY id_cliente DESC")->fetchAll();
} catch (Exception $e) {
    $clientes = [];
}

// Obtener cliente para editar
$clienteEditar = null;
if (isset($_GET['editar'])) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
        $stmt->execute([intval($_GET['editar'])]);
        $clienteEditar = $stmt->fetch();
    } catch (Exception $e) {
        $clienteEditar = null;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - CONTODA</title>
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
                    <a href="clientes.php" class="nav-link active"><i class="fas fa-users me-2"></i>Clientes</a>
                    <a href="facturas.php" class="nav-link"><i class="fas fa-file-invoice me-2"></i>Facturas</a>
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
                            <h2 class="fw-bold mb-1"><i class="fas fa-users me-2"></i>Gestión de Clientes</h2>
                            <p class="mb-0 opacity-75">Administra los clientes de la tienda</p>
                        </div>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#clienteModal">
                            <i class="fas fa-plus me-2"></i>Nuevo Cliente
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
                                        <th>ID</th>
                                        <th>Documento</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Ciudad</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clientes as $cli): ?>
                                        <tr>
                                            <td><?php echo $cli['id_cliente']; ?></td>
                                            <td><?php echo htmlspecialchars($cli['tipo_documento'] . ' ' . $cli['numero_documento']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($cli['nombre_cliente']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($cli['email'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($cli['telefono'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($cli['ciudad'] ?? 'N/A'); ?></td>
                                            <td><?php echo $cli['tipo_cliente']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($cli['estado']); ?>">
                                                    <?php echo $cli['estado']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?editar=<?php echo $cli['id_cliente']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit text-white"></i>
                                                </a>
                                                <a href="?eliminar=<?php echo $cli['id_cliente']; ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este cliente?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
    
    <!-- Modal Crear/Editar Cliente -->
    <div class="modal fade" id="clienteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                    <h5 class="modal-title"><?php echo $clienteEditar ? 'Editar Cliente' : 'Nuevo Cliente'; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="<?php echo $clienteEditar ? 'editar' : 'crear'; ?>">
                        <?php if ($clienteEditar): ?>
                            <input type="hidden" name="id_cliente" value="<?php echo $clienteEditar['id_cliente']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Documento</label>
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="Cédula" <?php echo ($clienteEditar && $clienteEditar['tipo_documento'] == 'Cédula') ? 'selected' : ''; ?>>Cédula</option>
                                    <option value="NIT" <?php echo ($clienteEditar && $clienteEditar['tipo_documento'] == 'NIT') ? 'selected' : ''; ?>>NIT</option>
                                    <option value="Pasaporte" <?php echo ($clienteEditar && $clienteEditar['tipo_documento'] == 'Pasaporte') ? 'selected' : ''; ?>>Pasaporte</option>
                                    <option value="Cédula Extranjería" <?php echo ($clienteEditar && $clienteEditar['tipo_documento'] == 'Cédula Extranjería') ? 'selected' : ''; ?>>Cédula Extranjería</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de Documento</label>
                                <input type="text" name="numero_documento" class="form-control" required 
                                       value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['numero_documento']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Cliente</label>
                            <input type="text" name="nombre_cliente" class="form-control" required 
                                   value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['nombre_cliente']) : ''; ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['email']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" 
                                       value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['telefono']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control" 
                                   value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['direccion']) : ''; ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" 
                                       value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['ciudad']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento</label>
                                <input type="text" name="departamento" class="form-control" 
                                       value="<?php echo $clienteEditar ? htmlspecialchars($clienteEditar['departamento']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Cliente</label>
                                <select name="tipo_cliente" class="form-select">
                                    <option value="Persona Natural" <?php echo ($clienteEditar && $clienteEditar['tipo_cliente'] == 'Persona Natural') ? 'selected' : ''; ?>>Persona Natural</option>
                                    <option value="Empresa" <?php echo ($clienteEditar && $clienteEditar['tipo_cliente'] == 'Empresa') ? 'selected' : ''; ?>>Empresa</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="Activo" <?php echo ($clienteEditar && $clienteEditar['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Inactivo" <?php echo ($clienteEditar && $clienteEditar['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php if ($clienteEditar): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('clienteModal'));
            myModal.show();
        });
    </script>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .badge-activo { background-color: #d1fae5; color: #065f46; }
        .badge-inactivo { background-color: #fee2e2; color: #991b1b; }
    </style>
</body>
</html>
