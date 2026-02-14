<?php
/**
 * CONTODA - Sistema de Facturación
 * Validaciones AJAX
 */

require_once 'funciones.php';

// Configurar headers para JSON
header('Content-Type: application/json');

// Verificar que la petición sea AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $accion = isset($_GET['accion']) ? $_GET['accion'] : (isset($_POST['accion']) ? $_POST['accion'] : '');
    
    switch ($accion) {
        case 'validar_codigo':
            // Validar código único de producto
            $codigo = isset($_GET['codigo']) ? sanitize($_GET['codigo']) : '';
            $excludeId = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
            
            if (empty($codigo)) {
                echo json_encode(['valido' => true, 'mensaje' => 'Código vacío es válido']);
                break;
            }
            
            $esUnico = validarCodigoProducto($codigo, $excludeId);
            echo json_encode([
                'valido' => $esUnico,
                'mensaje' => $esUnico ? 'Código disponible' : 'El código ya existe'
            ]);
            break;
            
        case 'validar_producto':
            // Validar datos del producto antes de guardar
            $data = [
                'id_categoria' => isset($_POST['id_categoria']) ? intval($_POST['id_categoria']) : 0,
                'codigo_producto' => isset($_POST['codigo_producto']) ? sanitize($_POST['codigo_producto']) : '',
                'nombre_producto' => isset($_POST['nombre_producto']) ? sanitize($_POST['nombre_producto']) : '',
                'precio' => isset($_POST['precio']) ? floatval($_POST['precio']) : 0
            ];
            
            $errores = [];
            
            // Validar categoría
            if ($data['id_categoria'] <= 0) {
                $errores[] = 'Debe seleccionar una categoría';
            }
            
            // Validar nombre
            if (empty(trim($data['nombre_producto']))) {
                $errores[] = 'El nombre del producto es obligatorio';
            } elseif (strlen($data['nombre_producto']) < 3) {
                $errores[] = 'El nombre debe tener al menos 3 caracteres';
            } elseif (strlen($data['nombre_producto']) > 200) {
                $errores[] = 'El nombre no puede exceder 200 caracteres';
            }
            
            // Validar precio
            if ($data['precio'] <= 0) {
                $errores[] = 'El precio debe ser mayor a 0';
            }
            
            // Validar código único si se proporcionó
            if (!empty($data['codigo_producto'])) {
                $esUnico = validarCodigoProducto($data['codigo_producto']);
                if (!$esUnico) {
                    $errores[] = 'El código del producto ya existe';
                }
            }
            
            echo json_encode([
                'valido' => count($errores) === 0,
                'errores' => $errores
            ]);
            break;
            
        case 'obtener_productos':
            // Obtener productos filtrados (para autocompletar)
            $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
            $categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
            
            $productos = getProductos($search, $categoria, 'Activo');
            
            $resultados = [];
            foreach ($productos as $p) {
                $resultados[] = [
                    'id' => $p['id_producto'],
                    'text' => $p['nombre_producto'] . ' - ' . formatCurrency($p['precio']),
                    'nombre' => $p['nombre_producto'],
                    'precio' => $p['precio'],
                    'stock' => $p['stock']
                ];
            }
            
            echo json_encode(['productos' => $resultados]);
            break;
            
        case 'obtener_categorias':
            // Obtener todas las categorías
            $categorias = getCategorias(true);
            
            $resultados = [];
            foreach ($categorias as $c) {
                $resultados[] = [
                    'id' => $c['id_categoria'],
                    'text' => $c['nombre_categoria']
                ];
            }
            
            echo json_encode(['categorias' => $resultados]);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no reconocida']);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
