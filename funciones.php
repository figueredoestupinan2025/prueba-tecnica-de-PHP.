<?php
/**
 * CONTODA - Sistema de Facturación
 * Funciones Auxiliares
 */

require_once 'config.php';

/**
 * Obtener todas las categorías
 */
function getCategorias($includeInactive = false) {
    try {
        $pdo = getConnection();
        $sql = "SELECT * FROM categorias";
        if (!$includeInactive) {
            $sql .= " WHERE estado = 'Activo'";
        }
        $sql .= " ORDER BY nombre_categoria";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error en getCategorias: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener una categoría por ID
 */
function getCategoriaById($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error en getCategoriaById: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener todos los productos
 */
function getProductos($search = null, $categoria = null, $estado = null) {
    try {
        $pdo = getConnection();
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE 1=1";
        
        $params = [];
        
        if ($search) {
            $sql .= " AND (p.nombre_producto LIKE ? OR p.codigo_producto LIKE ? OR p.descripcion LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($categoria) {
            $sql .= " AND p.id_categoria = ?";
            $params[] = $categoria;
        }
        
        if ($estado) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }
        
        $sql .= " ORDER BY p.id_producto DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error en getProductos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener un producto por ID
 */
function getProductoById($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, c.nombre_categoria 
            FROM productos p 
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
            WHERE p.id_producto = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error en getProductoById: " . $e->getMessage());
        return null;
    }
}

/**
 * Crear un nuevo producto
 */
function createProducto($data) {
    try {
        $pdo = getConnection();
        
        // Verificar código único
        if (!empty($data['codigo_producto'])) {
            $stmt = $pdo->prepare("SELECT id_producto FROM productos WHERE codigo_producto = ?");
            $stmt->execute([$data['codigo_producto']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El código del producto ya existe'];
            }
        }
        
        $sql = "INSERT INTO productos (
                    id_categoria, codigo_producto, nombre_producto, descripcion,
                    precio, stock, stock_minimo, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_categoria'],
            $data['codigo_producto'] ?? null,
            $data['nombre_producto'],
            $data['descripcion'] ?? null,
            $data['precio'],
            $data['stock'] ?? 0,
            $data['stock_minimo'] ?? 5,
            $data['estado'] ?? 'Activo'
        ]);
        
        return ['success' => true, 'message' => 'Producto creado correctamente', 'id' => $pdo->lastInsertId()];
    } catch (Exception $e) {
        error_log("Error en createProducto: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al crear el producto: ' . $e->getMessage()];
    }
}

/**
 * Actualizar un producto
 */
function updateProducto($id, $data) {
    try {
        $pdo = getConnection();
        
        // Verificar código único (excluyendo el producto actual)
        if (!empty($data['codigo_producto'])) {
            $stmt = $pdo->prepare("SELECT id_producto FROM productos WHERE codigo_producto = ? AND id_producto != ?");
            $stmt->execute([$data['codigo_producto'], $id]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El código del producto ya existe'];
            }
        }
        
        $sql = "UPDATE productos SET 
                    id_categoria = ?,
                    codigo_producto = ?,
                    nombre_producto = ?,
                    descripcion = ?,
                    precio = ?,
                    stock = ?,
                    stock_minimo = ?,
                    estado = ?
                WHERE id_producto = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_categoria'],
            $data['codigo_producto'] ?? null,
            $data['nombre_producto'],
            $data['descripcion'] ?? null,
            $data['precio'],
            $data['stock'],
            $data['stock_minimo'],
            $data['estado'],
            $id
        ]);
        
        return ['success' => true, 'message' => 'Producto actualizado correctamente'];
    } catch (Exception $e) {
        error_log("Error en updateProducto: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar el producto: ' . $e->getMessage()];
    }
}

/**
 * Eliminar un producto
 */
function deleteProducto($id) {
    try {
        $pdo = getConnection();
        
        // Verificar si el producto está en alguna factura
        $stmt = $pdo->prepare("SELECT id_detalle FROM detalle_factura WHERE id_producto = ? LIMIT 1");
        $stmt->execute([$id]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'No se puede eliminar el producto porque está relacionado con facturas'];
        }
        
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->execute([$id]);
        
        return ['success' => true, 'message' => 'Producto eliminado correctamente'];
    } catch (Exception $e) {
        error_log("Error en deleteProducto: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al eliminar el producto: ' . $e->getMessage()];
    }
}

/**
 * Validar código de producto (para AJAX)
 */
function validarCodigoProducto($codigo, $excludeId = null) {
    try {
        $pdo = getConnection();
        
        if ($excludeId) {
            $stmt = $pdo->prepare("SELECT id_producto FROM productos WHERE codigo_producto = ? AND id_producto != ?");
            $stmt->execute([$codigo, $excludeId]);
        } else {
            $stmt = $pdo->prepare("SELECT id_producto FROM productos WHERE codigo_producto = ?");
            $stmt->execute([$codigo]);
        }
        
        return $stmt->fetch() ? false : true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Obtener estadísticas de productos
 */
function getEstadisticas() {
    try {
        $pdo = getConnection();
        
        $stats = [
            'total' => 0,
            'activos' => 0,
            'inactivos' => 0,
            'stock_bajo' => 0,
            'sin_stock' => 0
        ];
        
        // Total de productos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
        $stats['total'] = $stmt->fetch()['total'];
        
        // Productos activos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'Activo'");
        $stats['activos'] = $stmt->fetch()['total'];
        
        // Productos inactivos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'Inactivo'");
        $stats['inactivos'] = $stmt->fetch()['total'];
        
        // Stock bajo
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock > 0 AND stock <= stock_minimo");
        $stats['stock_bajo'] = $stmt->fetch()['total'];
        
        // Sin stock
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock = 0");
        $stats['sin_stock'] = $stmt->fetch()['total'];
        
        return $stats;
    } catch (Exception $e) {
        error_log("Error en getEstadisticas: " . $e->getMessage());
        return $stats;
    }
}
