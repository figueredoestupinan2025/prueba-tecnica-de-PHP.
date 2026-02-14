the config.php file:<?php
/**
 * CONTODA - Sistema de Facturación
 * Configuración de Base de Datos
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'contoda');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración del sitio
define('SITE_NAME', 'CONTODA - Sistema de Facturación');
define('SITE_URL', 'http://localhost/contoda');

// Zona horaria
date_default_timezone_set('America/Bogota');

/**
 * Conexión a la base de datos usando PDO
 */
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        throw new Exception("Error de conexión a la base de datos");
    }
}

/**
 * Función para limpiar datos de entrada
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Función para formatear moneda
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 0, ',', '.');
}

/**
 * Función para mostrar mensajes
 */
function showMessage($message, $type = 'info') {
    $colors = [
        'success' => 'success',
        'error' => 'danger',
        'warning' => 'warning',
        'info' => 'info'
    ];
    $color = isset($colors[$type]) ? $colors[$type] : 'info';
    
    return '<div class="alert alert-' . $color . ' alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($message) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
}

/**
 * Redireccionar con mensaje
 */
function redirect($url, $message = null, $type = 'info') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit;
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
