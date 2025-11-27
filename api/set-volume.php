<?php
/**
 * api/set-volume.php
 * Establece el nivel de volumen del reproductor
 * Valores válidos de 0 a 100
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Obtener datos JSON con el nivel de volumen
$input = json_decode(file_get_contents('php://input'), true);
$volume = intval($input['volume'] ?? 50);
// Validar que el volumen esté entre 0 y 100
$volume = max(0, min(100, $volume));

// Cargar datos y actualizar volumen
$data = loadData('music_data.json');
$data['currentTrack']['volume'] = $volume;  // Actualizar volumen
saveData('music_data.json', $data);

// Retornar volumen actualizado
echo json_encode([
    'success' => true,
    'volume' => $volume
]);
?>

