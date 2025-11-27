<?php
/**
 * database.php
 * Gestiona la conexión a la base de datos MySQL y proporciona métodos para verificar la conexión
 */

// Constantes de configuración de la base de datos
define('DB_HOST', 'localhost');      // Host del servidor MySQL
define('DB_USER', 'root');           // Usuario de MySQL
define('DB_PASS', '');               // Contraseña de MySQL
define('DB_NAME', 'spotify detemu'); // Nombre de la base de datos
define('DB_CHARSET', 'utf8mb4');     // Conjunto de caracteres

/**
 * Obtiene la conexión a la base de datos usando PDO
 * Utiliza patrón singleton para reutilizar la misma conexión
 * @return PDO Objeto de conexión a la base de datos
 * @throws Exception Si hay error de conexión
 */
function getDBConnection() {
    static $conn = null;
    
    // Si la conexión ya existe, la reutiliza
    if ($conn === null) {
        try {
            // Crear DSN (Data Source Name) para PDO
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            // Opciones de PDO para mejor manejo de errores
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,     // Lanzar excepciones en errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,           // Retornar arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                      // Usar prepared statements reales
            ];
            
            // Crear la conexión PDO
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Registrar el error en el log
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            error_log("Host: " . DB_HOST . ", Database: " . DB_NAME . ", User: " . DB_USER);
            
            // Obtener el mensaje de error para mostrar información más útil
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'Access denied') !== false) {
                $message = "Error de autenticación. Verifica el usuario y contraseña en database.php";
            } elseif (strpos($errorMsg, 'Unknown database') !== false) {
                $message = "La base de datos '" . DB_NAME . "' no existe. Crea la base de datos en phpMyAdmin o importa database.sql";
            } elseif (strpos($errorMsg, 'Connection refused') !== false || strpos($errorMsg, 'No connection') !== false) {
                $message = "MySQL no está corriendo. Inicia MySQL desde el Panel de Control de XAMPP";
            } else {
                $message = "Error de conexión: " . $errorMsg;
            }
            
            // Lanzar excepción con mensaje descriptivo
            throw new Exception($message);
        }
    }
    
    return $conn;
}

/**
 * Prueba la conexión a la base de datos
 * @return bool true si la conexión es exitosa, false en caso contrario
 */
function testConnection() {
    try {
        // Intentar obtener la conexión
        $conn = getDBConnection();
        return true;
    } catch (Exception $e) {
        // Si hay error, retornar falso
        return false;
    }
}
?>

