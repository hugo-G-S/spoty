<?php
/**
 * index.php
 * Página principal de la aplicación Spotify Clone
 * Muestra artistas populares, álbumes destacados y canciones disponibles
 */

// Requerir autenticación para acceder a esta página
require_once 'auth.php';
requireAuth();

require_once 'config.php';
require_once 'music_functions.php';

// Obtener información del usuario autenticado
$currentUser = getCurrentUser();
$userId = $_SESSION['user_id'] ?? null;

// Obtener datos de música para mostrar en la página principal
$allSongs = getAllSongs($userId);                      // Todas las canciones
$topArtists = getTopArtists($userId, 6);              // Artistas favoritos del usuario (máx 6)

// Si el usuario no tiene artistas favoritos, mostrar artistas generales
if (empty($topArtists)) {
    $topArtists = getTopArtists(null, 6);
}

$popularAlbums = getPopularAlbums(10);                 // Álbumes más populares
$libraryArtists = getAllArtists();                     // Todos los artistas

// Cargar datos de música del archivo JSON
$data = loadData('music_data.json');
$currentTrack = $data['currentTrack'] ?? null;         // Canción en reproducción actual
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Clone - PHP</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Incluir barra lateral con navegación -->
        <?php include 'components/sidebar.php'; ?>
        <!-- Incluir contenido principal con artistas y álbumes -->
        <?php include 'components/main-content.php'; ?>
    </div>
    
    <!-- Incluir barra de reproductor inferior -->
    <?php include 'components/player-bar.php'; ?>
    
    <!-- Script principal de la aplicación -->
    <script src="assets/js/app.js"></script>
    <script>
        /**
         * Alterna el estado favorito de una canción
         * @param {number} songId ID de la canción
         * @param {HTMLElement} buttonElement Elemento del botón clickeado
         */
        async function toggleFavorite(songId, buttonElement) {
            try {
                // Preparar datos para enviar al servidor
                const formData = new FormData();
                formData.append('song_id', songId);
                
                // Hacer petición POST al API
                const response = await fetch('api/toggle-favorite.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Procesar respuesta JSON
                const data = await response.json();
                
                if (data.success) {
                    // Obtener el icono del botón
                    const icon = buttonElement.querySelector('i');
                    if (data.favorited) {
                        // Marcar como favorita (corazón relleno)
                        icon.className = 'fa-solid fa-heart';
                        icon.style.color = '#1db954';
                    } else {
                        // Desmarcar favorita (corazón vacío)
                        icon.className = 'fa-regular fa-heart';
                        icon.style.color = '#b3b3b3';
                    }
                } else {
                    // Mostrar error si la operación falló
                    alert('Error: ' + (data.message || 'No se pudo actualizar el favorito'));
                }
            } catch (error) {
                console.error('Error toggle favorito:', error);
                alert('Error al actualizar favorito');
            }
        }
        
        // Hacer la función disponible globalmente
        window.toggleFavorite = toggleFavorite;
    </script>
</body>
</html>

