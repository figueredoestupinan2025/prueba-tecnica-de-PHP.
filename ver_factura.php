<?php
/**
 * CONTODA - Sistema de Facturación
 * Ver Factura
 */

require_once 'funciones.php';

$id_factura = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $pdo = getConnection();
    
    // Obtener factura
    $stmt = $pdo->prepare("
        SELECT f.*, c.nombre_cliente, c.numero_documento, c.tipo_documento, c.direccion, c.telefono, c.email
        FROM facturas f
        LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
        WHERE f.id_factura = ?
    ");
    $stmt->execute([$id_factura]);
    $factura = $stmt->fetch();
    
    if (!$factura) {
        die('Factura no encontrada');
    }
    
    // Obtener detalles
    $stmt = $pdo->prepare("
        SELECT d.*, p.nombre_producto, p.codigo_producto
        FROM detalle_factura d
        LEFT JOIN productos p ON d.id_producto = p.id_producto
        WHERE d.id_factura = ?
    ");
    $stmt->execute([$id_factura]);
    $detalles = $stmt->fetchAll();
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?php echo $factura['numero_factura']; ?> - CONTODA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .invoice-box {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            background: white;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr.heading td {
            background: #6366f1;
            color: white;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.total td {
            border-top: 2px solid #6366f1;
            font-weight: bold;
            font-size: 18px;
        }
        .company-info {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    <h2>CONTODA</h2>
                                    <p>Sistema de Facturación</p>
                                </td>
                                <td class="company-info">
                                    <h3>FACTURA</h3>
                                    <b>Número:</b> <?php echo $factura['numero_factura']; ?><br>
                                    <b>Fecha:</b> <?php echo date('d/m/Y', strtotime($factura['fecha_factura'])); ?><br>
                                    <b>Hora:</b> <?php echo $factura['hora_factura']; ?><br>
                                    <b>Estado:</b> <?php echo $factura['estado']; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr class="details">
                    <td colspan="2">
                        <br>
                        <h4>Información del Cliente</h4>
                        <table>
                            <tr>
                                <td width="50%">
                                    <b>Cliente:</b> <?php echo htmlspecialchars($factura['nombre_cliente']); ?><br>
                                    <b><?php echo $factura['tipo_documento']; ?>:</b> <?php echo htmlspecialchars($factura['numero_documento']); ?><br>
                                    <b>Dirección:</b> <?php echo htmlspecialchars($factura['direccion'] ?? 'N/A'); ?>
                                </td>
                                <td width="50%">
                                    <b>Teléfono:</b> <?php echo htmlspecialchars($factura['telefono'] ?? 'N/A'); ?><br>
                                    <b>Email:</b> <?php echo htmlspecialchars($factura['email'] ?? 'N/A'); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr class="heading">
                    <td>Producto</td>
                    <td style="text-align: right;">Cantidad</td>
                    <td style="text-align: right;">Precio Unit.</td>
                    <td style="text-align: right;">Subtotal</td>
                </tr>
                
                <?php foreach ($detalles as $detalle): ?>
                <tr class="item">
                    <td>
                        <strong><?php echo htmlspecialchars($detalle['nombre_producto']); ?></strong><br>
                        <small><?php echo htmlspecialchars($detalle['codigo_producto'] ?? ''); ?></small>
                    </td>
                    <td style="text-align: right;"><?php echo $detalle['cantidad']; ?></td>
                    <td style="text-align: right;"><?php echo formatCurrency($detalle['precio_unitario']); ?></td>
                    <td style="text-align: right;"><?php echo formatCurrency($detalle['subtotal']); ?></td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="total">
                    <td colspan="3" style="text-align: right;"><b>TOTAL:</b></td>
                    <td style="text-align: right;"><?php echo formatCurrency($factura['total']); ?></td>
                </tr>
                
                <?php if ($factura['forma_pago']): ?>
                <tr>
                    <td colspan="4">
                        <br>
                        <p><b>Forma de Pago:</b> <?php echo $factura['forma_pago']; ?></p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php if ($factura['observaciones']): ?>
                <tr>
                    <td colspan="4">
                        <p><b>Observaciones:</b> <?php echo htmlspecialchars($factura['observaciones']); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
            
            <div class="text-center mt-4">
                <a href="facturas.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
                <a href="javascript:window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Imprimir
                </a>
            </div>
        </div>
    </div>
</body>
</html>
