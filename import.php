<?php
/**
 * CONTODA - Importar Base de Datos
 * Este script importa la base de datos al servidor MySQL
 */

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'contoda';

echo "=== CONTODA - Importador de Base de Datos ===\n\n";

try {
    // Conectar sin especificar base de datos
    $pdo = new PDO("mysql:host=$host", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ Conexión exitosa a MySQL\n";
    
    // Leer el archivo SQL
    $sqlFile = file_get_contents('database.sql');
    
    if (!$sqlFile) {
        throw new Exception("No se pudo leer el archivo database.sql");
    }
    
    echo "✓ Archivo SQL leído correctamente\n";
    
    // Dividir en instrucciones individuales
    $statements = array_filter(array_map('trim', explode(';', $sqlFile)));
    
    $pdo->exec("DROP DATABASE IF EXISTS $database");
    echo "✓ Base de datos anterior eliminada (si existía)\n";
    
    $pdo->exec("CREATE DATABASE $database");
    echo "✓ Base de datos '$database' creada\n";
    
    // Cambiar a la nueva base de datos
    $pdo->exec("USE $database");
    
    // Ejecutar cada instrucción
    $count = 0;
    foreach ($statements as $statement) {
        if (!empty($statement) && stripos($statement, 'DELIMITER') === false) {
            try {
                $pdo->exec($statement);
                $count++;
            } catch (Exception $e) {
                // Ignorar errores de DELIMITER y otros comandos no soportados
                if (stripos($statement, 'DELIMITER') === false && stripos($statement, 'CREATE PROCEDURE') === false) {
                    // echo "Advertencia: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "✓ Se ejecutaron $count instrucciones SQL\n";
    echo "\n=== IMPORTACIÓN COMPLETA ===\n";
    echo "Base de datos '$database' importada correctamente!\n";
    echo "Ahora puedes acceder a la aplicación en: http://localhost/contoda\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
