<?php
/**
 * login.php
 * Página de inicio de sesión de la aplicación
 * Permite a usuarios registrados ingresar con email y contraseña
 */

require_once 'auth.php';

// Redirigir si el usuario ya está autenticado
redirectIfAuthenticated();

// Variables para almacenar mensajes de error o éxito
$error = '';
$success = '';

// Procesar el formulario de login si se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar datos del formulario
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos';
    } else {
        // Intentar hacer login
        if (login($email, $password)) {
            // Si el login es exitoso, redirigir a la página principal
            header('Location: index.php');
            exit();
        } else {
            // Si falla, mostrar mensaje de error
            $error = 'Correo electrónico o contraseña incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Spotify Clone</title>
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
                <h1>Iniciar Sesión</h1>
                <p>Continúa escuchando tu música favorita</p>
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

            <!-- Formulario de login -->
            <form method="POST" action="login.php" class="login-form">
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
                        autofocus
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
                        placeholder="Tu contraseña"
                        required
                    >
                </div>

                <!-- Botón de envío -->
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Pie del formulario con enlace a registro -->
            <div class="login-footer">
                <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="login-form">
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
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fa-solid fa-lock"></i>
                        Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingresa tu contraseña"
                        required
                    >
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" value="1">
                        <span>Recordarme</span>
                    </label>
                    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Iniciar Sesión
                </button>
            </form>

            <div class="login-footer">
                <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>

