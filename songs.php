<?php
/**
 * songs.php
 * Página que muestra todas las canciones disponibles en la aplicación
 * Permite reproducir canciones y agregarlas a favoritos
 */

// Requerir autenticación para acceder a esta página
require_once 'auth.php';
requireAuth();

require_once 'config.php';
require_once 'music_functions.php';

// Obtener información del usuario autenticado
$currentUser = getCurrentUser();
$userId = $_SESSION['user_id'] ?? null;

// Obtener todas las canciones disponibles
$allSongs = getAllSongs($userId);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Canciones - Spotify Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Incluir barra lateral -->
        <?php include 'components/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Barra superior con navegación -->
            <header class="top-bar">
                <div class="nav-buttons">
                    <button onclick="history.back()">&#60;</button>
                    <button onclick="history.forward()">&#62;</button>
                </div>
                <div class="profile-button" id="profile-menu">
                    <?php 
                    $currentUser = getCurrentUser();
                    echo strtoupper(substr($currentUser['name'] ?? $currentUser['email'] ?? 'U', 0, 1)); 
                    ?>
                </div>
            </header>

            <!-- Contenido principal -->
            <main class="content-view">
                <h1>Todas las Canciones</h1>
                
                <!-- Lista de todas las canciones -->
                <div class="songs-list">
                    <?php foreach ($allSongs as $song): ?>
                        <!-- Elemento individual de canción -->
                        <div class="song-item" data-song-id="<?php echo $song['cancion_id']; ?>">
                            <!-- Imagen del álbum -->
                            <img src="<?php echo htmlspecialchars($song['album_imagen'] ?? $song['artista_imagen'] ?? 'https://via.placeholder.com/50'); ?>" 
                                 alt="<?php echo htmlspecialchars($song['album_titulo'] ?? 'Álbum'); ?>"
                                 class="song-album-image">
                            <!-- Información de la canción -->
                            <div class="song-info">
                                <span class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></span>
                                <!-- Enlace al artista -->
                                <span class="song-artist">
                                    <a href="artist.php?id=<?php echo $song['artista_id']; ?>" class="artist-link">
                                        <?php echo htmlspecialchars($song['artista_nombre'] ?? 'Artista desconocido'); ?>
                                    </a>
                                </span>
                            </div>
                            <!-- Acciones y duración -->
                            <div class="song-actions">
                                <!-- Botón de favorito -->
                                <button class="favorite-btn" 
                                        data-song-id="<?php echo $song['cancion_id']; ?>"
                                        onclick="toggleFavorite(<?php echo $song['cancion_id']; ?>, this)">
                                    <i class="fa-<?php echo $song['is_favorite'] ? 'solid' : 'regular'; ?> fa-heart" 
                                       style="color: <?php echo $song['is_favorite'] ? '#1db954' : '#b3b3b3'; ?>"></i>
                                </button>
                                <!-- Duración de la canción -->
                                <span class="song-duration"><?php echo gmdate('i:s', $song['duracion_segundos'] ?? 0); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Incluir barra de reproductor -->
    <?php include 'components/player-bar.php'; ?>
    
    <!-- Scripts de la aplicación -->
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Canciones - Spotify Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <?php include 'components/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="top-bar">
                <div class="nav-buttons">
                    <button onclick="history.back()">&#60;</button>
                    <button onclick="history.forward()">&#62;</button>
                </div>
                <div class="profile-button" id="profile-menu">
                    <?php 
                    $currentUser = getCurrentUser();
                    echo strtoupper(substr($currentUser['name'] ?? $currentUser['email'] ?? 'U', 0, 1)); 
                    ?>
                </div>
            </header>

            <main class="content-view">
                <h1>Todas las Canciones</h1>
                
                <div class="songs-list">
                    <?php foreach ($allSongs as $song): ?>
                        <div class="song-item" data-song-id="<?php echo $song['cancion_id']; ?>">
                            <img src="<?php echo htmlspecialchars($song['album_imagen'] ?? $song['artista_imagen'] ?? 'https://via.placeholder.com/50'); ?>" 
                                 alt="<?php echo htmlspecialchars($song['album_titulo'] ?? 'Álbum'); ?>"
                                 class="song-album-image">
                            <div class="song-info">
                                <span class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></span>
                                <span class="song-artist">
                                    <a href="artist.php?id=<?php echo $song['artista_id']; ?>" class="artist-link">
                                        <?php echo htmlspecialchars($song['artista_nombre'] ?? 'Artista desconocido'); ?>
                                    </a>
                                </span>
                            </div>
                            <div class="song-actions">
                                <button class="favorite-btn" 
                                        data-song-id="<?php echo $song['cancion_id']; ?>"
                                        onclick="toggleFavorite(<?php echo $song['cancion_id']; ?>, this)">
                                    <i class="fa-<?php echo $song['is_favorite'] ? 'solid' : 'regular'; ?> fa-heart" 
                                       style="color: <?php echo $song['is_favorite'] ? '#1db954' : '#b3b3b3'; ?>"></i>
                                </button>
                                <span class="song-duration"><?php echo gmdate('i:s', $song['duracion_segundos'] ?? 0); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>
    
    <?php include 'components/player-bar.php'; ?>
    
    <script src="assets/js/app.js"></script>
    <script>
        // Función para toggle favoritos
        async function toggleFavorite(songId, buttonElement) {
            try {
                const formData = new FormData();
                formData.append('song_id', songId);
                
                const response = await fetch('api/toggle-favorite.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const icon = buttonElement.querySelector('i');
                    if (data.favorited) {
                        icon.className = 'fa-solid fa-heart';
                        icon.style.color = '#1db954';
                    } else {
                        icon.className = 'fa-regular fa-heart';
                        icon.style.color = '#b3b3b3';
                    }
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar el favorito'));
                }
            } catch (error) {
                console.error('Error toggle favorito:', error);
                alert('Error al actualizar favorito');
            }
        }
        
        // Hacer función global
        window.toggleFavorite = toggleFavorite;
    </script>
</body>
</html>

