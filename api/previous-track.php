<?php
/**
 * api/previous-track.php
 * Reproduce la canción anterior en la lista de reproducción
 * Si es la primera canción, va a la última
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Cargar datos de music_data.json
$data = loadData('music_data.json');
$songs = $data['songs'] ?? [];  // Obtener lista de canciones
$currentId = $data['currentTrack']['id'] ?? 1;  // Obtener ID de canción actual

// Buscar la canción anterior
$previousTrack = null;
$lastTrack = null;  // Almacena la canción anterior a la actual
foreach ($songs as $song) {
    // Si es la canción actual
    if ($song['id'] == $currentId) {
        $previousTrack = $lastTrack;  // La anterior es la que se guardó
        break;
    }
    $lastTrack = $song;  // Guardar cada canción para cuando encuentre la actual
}

// Si no hay anterior (es la primera), ir a la última
if (!$previousTrack && count($songs) > 0) {
    $previousTrack = end($songs);  // Obtener la última del array
}

// Actualizar la canción actual si encontró una anterior
if ($previousTrack) {
    $data['currentTrack'] = [
        'id' => $previousTrack['id'],
        'title' => $previousTrack['title'],
        'artist' => $previousTrack['artist'],
        'image' => $previousTrack['image'],
        'isPlaying' => true,  // Iniciar reproducción
        'progress' => 0,      // Empezar desde el inicio
        'duration' => $previousTrack['duration'] ?? 180,
        'volume' => $data['currentTrack']['volume'] ?? 50  // Mantener volumen
    ];
    // Guardar cambios
    saveData('music_data.json', $data);
}

// Retornar la canción actual (anterior)
echo json_encode([
    'success' => true,
    'track' => $data['currentTrack']
]);
?>

