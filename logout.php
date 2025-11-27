<?php
/**
 * logout.php
 * Cierra la sesión del usuario actual y lo redirige a la página de login
 */

require_once 'auth.php';

// Llamar a la función logout para destruir la sesión
logout();

// Redirigir a la página de login
header('Location: login.php');
exit();
?>

