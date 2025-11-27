<?php
/**
 * api/seek-track.php
 * Cambia la posición actual de reproducción de la canción
 * Permite al usuario hacer clic en la barra de progreso
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Obtener datos JSON con el progreso
$input = json_decode(file_get_contents('php://input'), true);
$progress = floatval($input['progress'] ?? 0);
// Validar que el progreso esté entre 0 y 100
$progress = max(0, min(100, $progress));

// Cargar datos y actualizar progreso
$data = loadData('music_data.json');
$data['currentTrack']['progress'] = $progress;  // Actualizar posición
saveData('music_data.json', $data);

// Retornar progreso actualizado
echo json_encode([
    'success' => true,
    'progress' => $progress
]);
?>

