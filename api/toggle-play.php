<?php
/**
 * api/toggle-play.php
 * Alterna entre reproducción y pausa
 * Actualiza el estado de reproducción en music_data.json
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Obtener datos JSON con el estado de reproducción
$input = json_decode(file_get_contents('php://input'), true);
$isPlaying = $input['isPlaying'] ?? false;

// Cargar datos y actualizar estado
$data = loadData('music_data.json');
$data['currentTrack']['isPlaying'] = $isPlaying;  // Actualizar estado
saveData('music_data.json', $data);

// Retornar estado actualizado
echo json_encode([
    'success' => true,
    'isPlaying' => $isPlaying,
    'track' => $data['currentTrack']
]);
?>

