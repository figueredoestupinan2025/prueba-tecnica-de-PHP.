<?php
/**
 * CONTODA - Sistema de Facturación
 * Editar Producto - UPDATE
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

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_categoria' => intval($_POST['id_categoria']),
        'codigo_producto' => sanitize($_POST['codigo_producto']),
        'nombre_producto' => sanitize($_POST['nombre_producto']),
        'descripcion' => sanitize($_POST['descripcion']),
        'precio' => floatval($_POST['precio']),
        'stock' => intval($_POST['stock']),
        'stock_minimo' => intval($_POST['stock_minimo']),
        'estado' => $_POST['estado'] ?? 'Activo'
    ];
    
    $result = updateProducto($id_producto, $data);
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
        // Recargar datos del producto
        $producto = getProductoById($id_producto);
        // Redireccionar después de 1 segundo
        header("Refresh: 1; url=index.php");
    } else {
        $message = $result['message'];
        $messageType = 'danger';
    }
}

// Obtener categorías para el formulario
$categorias = getCategorias();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - CONTODA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
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
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            border: none;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .select2-container--default .select2-selection--single {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            height: 44px;
            padding: 5px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
        }
        
        .input-group-text {
            border-radius: 8px 0 0 8px;
            background-color: #f3f4f6;
        }
        
        .invalid-feedback {
            font-size: 80%;
        }
        
        .help-text {
            font-size: 80%;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-box i {
            color: var(--primary-color);
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
                <div class="form-container">
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
                    
                    <!-- Info Box -->
                    <div class="info-box">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Editando:</strong> <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                        <span class="badge bg-secondary ms-2">ID: <?php echo $producto['id_producto']; ?></span>
                    </div>
                    
                    <!-- Formulario -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Editar Producto
                            </h4>
                            <p class="mb-0 mt-1 opacity-75">Modifica los datos del producto</p>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" id="productoForm" novalidate>
                                <div class="row">
                                    <!-- Categoría -->
                                    <div class="col-md-6 mb-3">
                                        <label for="id_categoria" class="form-label">
                                            Categoría <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_categoria" id="id_categoria" class="form-select" required>
                                            <option value="">Seleccionar categoría</option>
                                            <?php foreach ($categorias as $cat): ?>
                                                <option value="<?php echo $cat['id_categoria']; ?>"
                                                        <?php echo ($producto['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Por favor seleccione una categoría</div>
                                    </div>
                                    
                                    <!-- Código Producto -->
                                    <div class="col-md-6 mb-3">
                                        <label for="codigo_producto" class="form-label">Código</label>
                                        <input type="text" name="codigo_producto" id="codigo_producto" 
                                               class="form-control" placeholder="Ej: MAQ-001"
                                               value="<?php echo htmlspecialchars($producto['codigo_producto'] ?? ''); ?>">
                                        <div class="help-text">Código único del producto (opcional)</div>
                                        <div class="invalid-feedback" id="codigo-feedback"></div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Nombre Producto -->
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre_producto" class="form-label">
                                            Nombre del Producto <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="nombre_producto" id="nombre_producto" 
                                               class="form-control" placeholder="Ej: Labial Rojo Intenso" required
                                               maxlength="200"
                                               value="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                        <div class="invalid-feedback">El nombre del producto es obligatorio</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Descripción -->
                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea name="descripcion" id="descripcion" rows="3" 
                                                  class="form-control" placeholder="Descripción detallada del producto..."><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Precio -->
                                    <div class="col-md-4 mb-3">
                                        <label for="precio" class="form-label">
                                            Precio <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="precio" id="precio" 
                                                   class="form-control" placeholder="0" 
                                                   min="0" step="0.01" required
                                                   value="<?php echo $producto['precio']; ?>">
                                        </div>
                                        <div class="invalid-feedback">El precio es obligatorio</div>
                                    </div>
                                    
                                    <!-- Stock -->
                                    <div class="col-md-4 mb-3">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" name="stock" id="stock" 
                                               class="form-control" placeholder="0" 
                                               min="0" value="<?php echo $producto['stock']; ?>">
                                        <div class="help-text">Cantidad en inventario</div>
                                    </div>
                                    
                                    <!-- Stock Mínimo -->
                                    <div class="col-md-4 mb-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                        <input type="number" name="stock_minimo" id="stock_minimo" 
                                               class="form-control" placeholder="5" 
                                               min="0" value="<?php echo $producto['stock_minimo']; ?>">
                                        <div class="help-text">Alerta cuando alcance esta cantidad</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Estado -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Estado</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="estado" 
                                                       id="estado_activo" value="Activo"
                                                       <?php echo ($producto['estado'] == 'Activo') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="estado_activo">
                                                    <span class="badge bg-success">Activo</span>
                                                    El producto estará disponible para venta
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="estado" 
                                                       id="estado_inactivo" value="Inactivo"
                                                       <?php echo ($producto['estado'] == 'Inactivo') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="estado_inactivo">
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                    El producto no estará disponible
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botones -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning text-white" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Actualizar Producto
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('#id_categoria').select2({
                placeholder: 'Seleccionar categoría',
                allowClear: true,
                language: 'es'
            });
            
            // Validación del formulario
            const form = document.getElementById('productoForm');
            
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            
            // Validación en tiempo real del código (excluyendo el actual)
            let timeout = null;
            const codigoActual = $('#codigo_producto').val();
            
            $('#codigo_producto').on('input', function() {
                const codigo = $(this).val();
                const feedback = $('#codigo-feedback');
                
                clearTimeout(timeout);
                
                // Si el código no cambió, no validar
                if (codigo === codigoActual || codigo === '') {
                    $(this).removeClass('is-valid is-invalid');
                    feedback.text('');
                    return;
                }
                
                if (codigo.length < 3) {
                    $(this).removeClass('is-valid is-invalid');
                    feedback.text('');
                    return;
                }
                
                timeout = setTimeout(function() {
                    $.ajax({
                        url: 'validar.php',
                        method: 'GET',
                        data: { 
                            accion: 'validar_codigo', 
                            codigo: codigo,
                            exclude_id: <?php echo $id_producto; ?>
                        },
                        success: function(response) {
                            if (response.valido) {
                                $('#codigo_producto').removeClass('is-invalid').addClass('is-valid');
                                feedback.text('');
                            } else {
                                $('#codigo_producto').removeClass('is-valid').addClass('is-invalid');
                                feedback.text('El código ya existe');
                            }
                        }
                    });
                }, 500);
            });
            
            // Validar precio
            $('#precio').on('input', function() {
                const valor = parseFloat($(this).val());
                if (valor < 0) {
                    $(this).val(0);
                }
            });
            
            // Validar stock
            $('#stock, #stock_minimo').on('input', function() {
                if (parseInt($(this).val()) < 0) {
                    $(this).val(0);
                }
            });
        });
    </script>
</body>
</html>
