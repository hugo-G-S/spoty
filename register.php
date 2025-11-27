<?php
/**
 * register.php
 * Página de registro de nuevos usuarios
 * Permite crear una nueva cuenta con nombre, email y contraseña
 */

require_once 'auth.php';

// Redirigir si el usuario ya está autenticado
redirectIfAuthenticated();

// Variables para almacenar mensajes de error o éxito
$error = '';
$success = '';

// Procesar el formulario de registro si se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar datos del formulario
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validar que todos los campos estén completos
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor, completa todos los campos';
    } 
    // Validar que las contraseñas coincidan
    elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } 
    // Validar longitud mínima de contraseña
    elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } 
    // Validar formato de email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido';
    } 
    // Si todas las validaciones pasan, intentar registrar el usuario
    else {
        $result = register($name, $email, $password);
        if ($result['success']) {
            // Si el registro es exitoso, redirigir a la página principal
            header('Location: index.php');
            exit();
        } else {
            // Si falla, mostrar mensaje de error
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Spotify Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <!-- Encabezado del formulario -->
            <div class="login-header">
                <i class="fa-brands fa-spotify"></i>
                <h1>Crear Cuenta</h1>
                <p>Únete a Spotify y descubre nueva música</p>
            </div>

            <!-- Mostrar mensaje de error si existe -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensaje de éxito si existe -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de registro -->
            <form method="POST" action="register.php" class="login-form">
                <!-- Campo de nombre completo -->
                <div class="form-group">
                    <label for="name">
                        <i class="fa-solid fa-user"></i>
                        Nombre completo
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder="Tu nombre"
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        required
                        autofocus
                    >
                </div>

                <!-- Campo de email -->
                <div class="form-group">
                    <label for="email">
                        <i class="fa-solid fa-envelope"></i>
                        Correo electrónico
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="nombre@ejemplo.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >
                </div>

                <!-- Campo de contraseña -->
                <div class="form-group">
                    <label for="password">
                        <i class="fa-solid fa-lock"></i>
                        Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Mínimo 6 caracteres"
                        required
                        minlength="6"
                    >
                </div>

                <!-- Campo de confirmar contraseña -->
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fa-solid fa-lock"></i>
                        Confirmar contraseña
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Repite tu contraseña"
                        required
                        minlength="6"
                    >
                </div>

                <!-- Botón de envío -->
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </form>

            <!-- Pie del formulario con enlace a login -->
            <div class="login-footer">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>

