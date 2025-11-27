<?php
/**
 * api/get-item.php
 * Obtiene información de un elemento específico (artista, álbum o canción)
 * Basado en el tipo y ID proporcionados
 */

// Establecer tipo de contenido JSON
header('Content-Type: application/json; charset=utf-8');

require_once '../config.php';
require_once '../music_functions.php';

// Obtener parámetros: id (del elemento) y type (tipo de elemento)
$id = intval($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'artist';  // Por defecto es artista

// Variable para almacenar el elemento encontrado
$item = null;

// Obtener el elemento según su tipo
if ($type === 'artist') {
    // Obtener información del artista
    $item = getArtistById($id);
} elseif ($type === 'album') {
    // Obtener información del álbum
    $item = getAlbumById($id);
    // Si el álbum existe, obtener sus canciones
    if ($item) {
        $item['songs'] = getAlbumSongs($id);
    }
} elseif ($type === 'song') {
    // Obtener información de la canción
    $item = getSongById($id);
}

// Retornar respuesta indicando si encontró el elemento
echo json_encode([
    'success' => $item !== null,  // Éxito si encontró el elemento
    'item' => $item
]);
?>

