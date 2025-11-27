<?php
/**
 * auth.php
 * Gestiona la autenticación de usuarios, login, registro y funciones de sesión
 */

require_once 'config.php';
require_once 'database.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario está autenticado
 * @return bool true si el usuario está autenticado, false en caso contrario
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Obtiene la información del usuario actualmente autenticado
 * @return array|null Array con datos del usuario o null si no está autenticado
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    try {
        $conn = getDBConnection();
        // Preparar consulta para obtener datos del usuario
        $stmt = $conn->prepare("SELECT usuario_id, nombre_usuario, email, fecha_registro FROM Usuarios WHERE usuario_id = ?");
        // Ejecutar con el ID del usuario de la sesión
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Retornar array con datos del usuario formateados
            return [
                'id' => $user['usuario_id'],
                'name' => $user['nombre_usuario'],
                'email' => $user['email'],
                'created_at' => $user['fecha_registro']
            ];
        }
    } catch (PDOException $e) {
        error_log("Error obteniendo usuario: " . $e->getMessage());
    }
    
    return null;
}

/**
 * Intenta hacer login con email y contraseña
 * @param string $email Email del usuario
 * @param string $password Contraseña del usuario
 * @return bool true si el login fue exitoso, false en caso contrario
 */
function login($email, $password) {
    try {
        $conn = getDBConnection();
        // Buscar el usuario por email
        $stmt = $conn->prepare("SELECT usuario_id, nombre_usuario, email, contrasena_hash FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $passwordValid = false;
            
            // Verificar si la contraseña está hasheada o en texto plano
            if (strlen($user['contrasena_hash']) > 50) {
                // Usar password_verify para contraseñas hasheadas
                $passwordValid = password_verify($password, $user['contrasena_hash']);
            } else {
                // Compatibilidad con contraseñas antiguas en texto plano
                $passwordValid = ($user['contrasena_hash'] === $password || password_verify($password, $user['contrasena_hash']));
            }
            
            if ($passwordValid) {
                // Crear sesión para el usuario
                $_SESSION['user_id'] = $user['usuario_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['nombre_usuario'];
                return true;
            }
        }
    } catch (PDOException $e) {
        error_log("Error en login: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Registra un nuevo usuario en la base de datos
 * @param string $name Nombre completo del usuario
 * @param string $email Email del usuario
 * @param string $password Contraseña sin hashear
 * @return array Array con 'success' (bool) y 'message' (string)
 */
function register($name, $email, $password) {
    try {
        $conn = getDBConnection();
        
        // Verificar si el email o nombre de usuario ya existe
        $stmt = $conn->prepare("SELECT usuario_id FROM Usuarios WHERE email = ? OR nombre_usuario = ?");
        $stmt->execute([$email, $name]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El correo electrónico o nombre de usuario ya está registrado'];
        }
        
        // Hashear la contraseña usando bcrypt
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar nuevo usuario en la base de datos
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, email, contrasena_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $passwordHash]);
        
        // Obtener el ID del nuevo usuario
        $userId = $conn->lastInsertId();
        
        // Crear sesión automáticamente después del registro
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        
        return ['success' => true, 'message' => 'Usuario registrado correctamente'];
    } catch (PDOException $e) {
        error_log("Error en registro: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al registrar usuario: ' . $e->getMessage()];
    }
}

/**
 * Cierra la sesión del usuario actual
 */
function logout() {
    // Destruir todas las variables de sesión
    session_unset();
    // Destruir la sesión completamente
    session_destroy();
}

/**
 * Requiere que el usuario esté autenticado
 * Si no está autenticado, redirige al login
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Redirige al usuario autenticado a la página principal
 * Evita que usuarios autenticados accedan a login/register
 */
function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        header('Location: index.php');
        exit();
    }
}
?>

