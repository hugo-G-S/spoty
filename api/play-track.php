<?php
/**
 * api/play-track.php
 * Inicia la reproducción de una canción específica
 * Guarda la canción como actual en music_data.json
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';

// Obtener datos JSON enviados en el cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);
$trackId = $input['trackId'] ?? null;

// Cargar datos de music_data.json
$data = loadData('music_data.json');

// Buscar la canción en el array de canciones
$track = null;
foreach ($data['songs'] ?? [] as $song) {
    if ($song['id'] == $trackId) {
        $track = $song;
        break;
    }
}

// Si no encuentra la canción, usar una por defecto
if (!$track) {
    $track = [
        'id' => 1,
        'title' => 'Canción de Ejemplo',
        'artist' => 'Artista Ejemplo',
        'image' => 'https://th.bing.com/th/id/R.e77f4b7034748db1a835d74daad07a4d?rik=t1D4FLdLs3viLg&pid=ImgRaw&r=0',
        'duration' => 180
    ];
}

// Actualizar canción actual con estado de reproducción
$data['currentTrack'] = [
    'id' => $track['id'],
    'title' => $track['title'],
    'artist' => $track['artist'],
    'image' => $track['image'],
    'isPlaying' => true,      // Activar reproducción
    'progress' => 0,           // Empezar desde el inicio
    'duration' => $track['duration'] ?? 180,
    'volume' => $data['currentTrack']['volume'] ?? 50  // Mantener volumen actual
];

// Guardar datos actualizados
saveData('music_data.json', $data);

// Retornar respuesta con la canción reproducida
echo json_encode([
    'success' => true,
    'track' => $data['currentTrack']
]);
?>

