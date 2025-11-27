<?php
/**
 * api/next-track.php
 * Reproduce la siguiente canción en la lista de reproducción
 * Si es la última canción, vuelve a la primera
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Cargar datos de music_data.json
$data = loadData('music_data.json');
$songs = $data['songs'] ?? [];  // Obtener lista de canciones
$currentId = $data['currentTrack']['id'] ?? 1;  // Obtener ID de canción actual

// Buscar la siguiente canción
$nextTrack = null;
$found = false;
foreach ($songs as $song) {
    // Si ya encontró la canción actual y aún no tiene la siguiente
    if ($found && !$nextTrack) {
        $nextTrack = $song;  // Esta es la siguiente
        break;
    }
    // Verificar si es la canción actual
    if ($song['id'] == $currentId) {
        $found = true;  // Marca que encontró la actual
    }
}

// Si no hay siguiente, volver a la primera canción
if (!$nextTrack && count($songs) > 0) {
    $nextTrack = $songs[0];
}

// Actualizar la canción actual si encontró una siguiente
if ($nextTrack) {
    $data['currentTrack'] = [
        'id' => $nextTrack['id'],
        'title' => $nextTrack['title'],
        'artist' => $nextTrack['artist'],
        'image' => $nextTrack['image'],
        'isPlaying' => true,  // Iniciar reproducción
        'progress' => 0,      // Empezar desde el inicio
        'duration' => $nextTrack['duration'] ?? 180,
        'volume' => $data['currentTrack']['volume'] ?? 50  // Mantener volumen
    ];
    // Guardar cambios
    saveData('music_data.json', $data);
}

// Retornar la canción actual (siguiente)
echo json_encode([
    'success' => true,
    'track' => $data['currentTrack']
]);
?>

