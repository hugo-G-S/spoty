<?php
/**
 * api/get-current-track.php
 * Retorna la canción actualmente seleccionada para reproducción
 * Carga los datos de music_data.json o usa una canción por defecto
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Cargar datos de music_data.json
$data = loadData('music_data.json');
// Obtener canción actual o usar una por defecto
$currentTrack = $data['currentTrack'] ?? [
    'id' => 1,
    'title' => 'Canción Actual',
    'artist' => 'Artista',
    'image' => 'https://th.bing.com/th/id/R.e77f4b7034748db1a835d74daad07a4d?rik=t1D4FLdLs3viLg&pid=ImgRaw&r=0',
    'isPlaying' => false,
    'progress' => 0,
    'duration' => 180,
    'volume' => 50
];

// Retornar respuesta en JSON
echo json_encode([
    'success' => true,
    'track' => $currentTrack
]);
?>

