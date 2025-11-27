<?php
/**
 * api/toggle-favorite.php
 * Alterna el estado favorito de una canción para el usuario autenticado
 * Requiere autenticación del usuario
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

// Requerir autenticación
require_once '../auth.php';
require_once '../music_functions.php';

// Verificar que el usuario esté autenticado
if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Obtener ID del usuario de la sesión
$userId = $_SESSION['user_id'];
// Obtener ID de la canción del POST o GET
$songId = intval($_POST['song_id'] ?? $_GET['song_id'] ?? 0);

// Validar que el ID de canción sea válido
if ($songId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de canción inválido']);
    exit;
}

// Ejecutar toggle favorito y retornar resultado
$result = toggleFavoriteSong($userId, $songId);
echo json_encode($result);
?>

