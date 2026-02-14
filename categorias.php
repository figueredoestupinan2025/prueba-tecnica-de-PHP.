<?php
/**
 * CONTODA - Sistema de Facturación
 * Gestión de Categorías
 */

require_once 'funciones.php';

$message = '';
$messageType = '';

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        $data = [
            'nombre_categoria' => sanitize($_POST['nombre_categoria']),
            'descripcion' => sanitize($_POST['descripcion']),
            'estado' => $_POST['estado'] ?? 'Activo'
        ];
        
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre_categoria, descripcion, estado) VALUES (?, ?, ?)");
            $stmt->execute([$data['nombre_categoria'], $data['descripcion'], $data['estado']]);
            $message = 'Categoría creada correctamente';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error al crear categoría: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
        $id = intval($_POST['id_categoria']);
        $data = [
            'nombre_categoria' => sanitize($_POST['nombre_categoria']),
            'descripcion' => sanitize($_POST['descripcion']),
            'estado' => $_POST['estado'] ?? 'Activo'
        ];
        
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("UPDATE categorias SET nombre_categoria = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");
            $stmt->execute([$data['nombre_categoria'], $data['descripcion'], $data['estado'], $id]);
            $message = 'Categoría actualizada correctamente';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error al actualizar categoría: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Eliminar categoría
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        $message = 'Categoría eliminada correctamente';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error al eliminar categoría: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Obtener todas las categorías
$categorias = getCategorias(true);

// Obtener categoría para editar
$categoriaEditar = null;
if (isset($_GET['editar'])) {
    $categoriaEditar = getCategoriaById(intval($_GET['editar']));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - CONTODA</title>
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
                    <a href="categorias.php" class="nav-link active"><i class="fas fa-tags me-2"></i>Categorías</a>
                    <a href="clientes.php" class="nav-link"><i class="fas fa-users me-2"></i>Clientes</a>
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
                            <h2 class="fw-bold mb-1"><i class="fas fa-tags me-2"></i>Gestión de Categorías</h2>
                            <p class="mb-0 opacity-75">Administra las categorías de productos</p>
                        </div>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#categoriaModal">
                            <i class="fas fa-plus me-2"></i>Nueva Categoría
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
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categorias as $cat): ?>
                                        <tr>
                                            <td><?php echo $cat['id_categoria']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($cat['nombre_categoria']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($cat['descripcion'] ?? 'Sin descripción'); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($cat['estado']); ?>">
                                                    <?php echo $cat['estado']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?editar=<?php echo $cat['id_categoria']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit text-white"></i>
                                                </a>
                                                <a href="?eliminar=<?php echo $cat['id_categoria']; ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta categoría?');">
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
    
    <!-- Modal Crear/Editar Categoría -->
    <div class="modal fade" id="categoriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                    <h5 class="modal-title"><?php echo $categoriaEditar ? 'Editar Categoría' : 'Nueva Categoría'; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="<?php echo $categoriaEditar ? 'editar' : 'crear'; ?>">
                        <?php if ($categoriaEditar): ?>
                            <input type="hidden" name="id_categoria" value="<?php echo $categoriaEditar['id_categoria']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre de Categoría</label>
                            <input type="text" name="nombre_categoria" class="form-control" required 
                                   value="<?php echo $categoriaEditar ? htmlspecialchars($categoriaEditar['nombre_categoria']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"><?php echo $categoriaEditar ? htmlspecialchars($categoriaEditar['descripcion']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="Activo" <?php echo ($categoriaEditar && $categoriaEditar['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="Inactivo" <?php echo ($categoriaEditar && $categoriaEditar['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
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
    
    <?php if ($categoriaEditar): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('categoriaModal'));
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
