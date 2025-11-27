<?php
/**
 * artist.php
 * Página de detalle de un artista específico
 * Muestra información del artista y sus canciones
 */

// Requerir autenticación para acceder a esta página
require_once 'auth.php';
requireAuth();

require_once 'config.php';
require_once 'music_functions.php';

// Obtener información del usuario autenticado
$currentUser = getCurrentUser();
$userId = $_SESSION['user_id'] ?? null;

// Obtener ID del artista desde los parámetros GET
$artistId = intval($_GET['id'] ?? 0);

// Debug: Verificar qué ID se está recibiendo
error_log("DEBUG artist.php - ID recibido de GET: " . ($_GET['id'] ?? 'NO EXISTE'));
error_log("DEBUG artist.php - ID después de intval: " . $artistId);

// Validar que el ID del artista sea válido
if ($artistId <= 0) {
    // Si no hay ID válido, redirigir a inicio
    header('Location: index.php');
    exit;
}

// Obtener información del artista de la base de datos
$artist = getArtistById($artistId);

// Si el artista no existe, redirigir a inicio
if (!$artist) {
    header('Location: index.php');
    exit;
}

// Debug: Verificar que se está obteniendo el artista correcto
error_log("DEBUG artist.php - Artista ID recibido: " . $artistId);
error_log("DEBUG artist.php - Artista obtenido: " . print_r($artist, true));

// Obtener todas las canciones del artista
$artistSongs = getArtistSongs($artistId, $userId);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artist['nombre']); ?> - Spotify Clone</title>
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
                    <!-- Botones de navegación atrás y adelante -->
                    <button onclick="history.back()">&#60;</button>
                    <button onclick="history.forward()">&#62;</button>
                </div>
                <!-- Botón de perfil de usuario -->
                <div class="profile-button" id="profile-menu">
                    <?php 
                    $currentUser = getCurrentUser();
                    echo strtoupper(substr($currentUser['name'] ?? $currentUser['email'] ?? 'U', 0, 1)); 
                    ?>
                </div>
            </header>

            <!-- Contenido principal -->
            <main class="content-view">
                <!-- Debug temporal: mostrar ID y nombre del artista -->
                <div style="background: #1db954; color: white; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                    <strong>DEBUG:</strong> ID recibido = <?php echo $artistId; ?>, 
                    Nombre = <?php echo htmlspecialchars($artist['nombre']); ?>, 
                    ID en BD = <?php echo $artist['artista_id']; ?>,
                    Imagen = <?php echo htmlspecialchars(substr($artist['imagen_url'] ?? 'N/A', 0, 50)); ?>...
                </div>
                
                <!-- Encabezado del artista con imagen e información -->
                <div class="artist-header">
                    <?php 
                    // Obtener URL de la imagen del artista
                    $artistImage = $artist['imagen_url'] ?? 'https://via.placeholder.com/300';
                    // Si la imagen es un placeholder, usar una imagen genérica con el nombre del artista
                    if (empty($artistImage) || strpos($artistImage, 'URL_IMAGEN') !== false) {
                        $artistImage = 'https://via.placeholder.com/300?text=' . urlencode($artist['nombre']);
                    }
                    ?>
                    <!-- Imagen del artista -->
                    <img src="<?php echo htmlspecialchars($artistImage); ?>" 
                         alt="<?php echo htmlspecialchars($artist['nombre']); ?>"
                         class="artist-image"
                         onerror="this.src='https://via.placeholder.com/300?text=<?php echo urlencode($artist['nombre']); ?>'"
                         style="border: 2px solid #1db954;">
                    <!-- Información del artista -->
                    <div class="artist-info">
                        <!-- Nombre del artista -->
                        <h1><?php echo htmlspecialchars($artist['nombre']); ?></h1>
                        <!-- Género principal del artista (si existe) -->
                        <?php if ($artist['genero_principal']): ?>
                            <p class="artist-genre"><?php echo htmlspecialchars($artist['genero_principal']); ?></p>
                        <?php endif; ?>
                        <!-- Cantidad de canciones del artista -->
                        <p class="song-count"><?php echo count($artistSongs); ?> canciones</p>
                    </div>
                </div>
                
                <!-- Título de sección de canciones -->
                <h2>Canciones</h2>
                <!-- Lista de canciones del artista -->
                <div class="songs-list">
                    <?php if (count($artistSongs) > 0): ?>
                        <!-- Mostrar cada canción del artista -->
                        <?php foreach ($artistSongs as $song): ?>
                            <!-- Elemento individual de canción -->
                            <div class="song-item" data-song-id="<?php echo $song['cancion_id']; ?>">
                                <!-- Imagen del álbum -->
                                <img src="<?php echo htmlspecialchars($song['album_imagen'] ?? $artist['imagen_url'] ?? 'https://via.placeholder.com/50'); ?>" 
                                     alt="<?php echo htmlspecialchars($song['album_titulo'] ?? 'Álbum'); ?>"
                                     class="song-album-image">
                                <!-- Información de la canción -->
                                <div class="song-info">
                                    <span class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></span>
                                    <!-- Álbum de la canción -->
                                    <span class="song-album"><?php echo htmlspecialchars($song['album_titulo'] ?? 'Sin álbum'); ?></span>
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
                    <?php else: ?>
                        <!-- Mensaje cuando no hay canciones -->
                        <p style="color: var(--color-texto-gris); padding: 20px;">No hay canciones disponibles para este artista.</p>
                    <?php endif; ?>
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
    <!-- Estilos específicos para esta página -->
    <style>
        /* Contenedor para el encabezado del artista */
        .artist-header {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
            align-items: flex-end;
        }
        
        /* Imagen del artista */
        .artist-image {
            width: 200px;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }
        
        /* Título del artista */
        .artist-info h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--color-texto-claro);
        }
        
        /* Género del artista */
        .artist-genre {
            color: var(--color-texto-gris);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        /* Cantidad de canciones */
        .song-count {
            color: var(--color-texto-gris);
            font-size: 14px;
        }
        
        /* Acciones de la canción */
        .song-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Botón de favorito */
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        
        .favorite-btn:hover {
            transform: scale(1.2);
        }
        
        .favorite-btn i {
            font-size: 18px;
            transition: color 0.2s;
        }
        
        /* Enlace al artista */
        .artist-link {
            color: var(--color-texto-gris);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .artist-link:hover {
            color: var(--color-texto-claro);
            text-decoration: underline;
        }
    </style>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artist['nombre']); ?> - Spotify Clone</title>
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
                <!-- Debug temporal: mostrar ID y nombre del artista -->
                <div style="background: #1db954; color: white; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                    <strong>DEBUG:</strong> ID recibido = <?php echo $artistId; ?>, 
                    Nombre = <?php echo htmlspecialchars($artist['nombre']); ?>, 
                    ID en BD = <?php echo $artist['artista_id']; ?>,
                    Imagen = <?php echo htmlspecialchars(substr($artist['imagen_url'] ?? 'N/A', 0, 50)); ?>...
                </div>
                
                <div class="artist-header">
                    <?php 
                    $artistImage = $artist['imagen_url'] ?? 'https://via.placeholder.com/300';
                    // Si la imagen es un placeholder, intentar usar la imagen del artista desde la base de datos
                    if (empty($artistImage) || strpos($artistImage, 'URL_IMAGEN') !== false) {
                        $artistImage = 'https://via.placeholder.com/300?text=' . urlencode($artist['nombre']);
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($artistImage); ?>" 
                         alt="<?php echo htmlspecialchars($artist['nombre']); ?>"
                         class="artist-image"
                         onerror="this.src='https://via.placeholder.com/300?text=<?php echo urlencode($artist['nombre']); ?>'"
                         style="border: 2px solid #1db954;">
                    <div class="artist-info">
                        <h1><?php echo htmlspecialchars($artist['nombre']); ?></h1>
                        <?php if ($artist['genero_principal']): ?>
                            <p class="artist-genre"><?php echo htmlspecialchars($artist['genero_principal']); ?></p>
                        <?php endif; ?>
                        <p class="song-count"><?php echo count($artistSongs); ?> canciones</p>
                    </div>
                </div>
                
                <h2>Canciones</h2>
                <div class="songs-list">
                    <?php if (count($artistSongs) > 0): ?>
                        <?php foreach ($artistSongs as $song): ?>
                            <div class="song-item" data-song-id="<?php echo $song['cancion_id']; ?>">
                                <img src="<?php echo htmlspecialchars($song['album_imagen'] ?? $artist['imagen_url'] ?? 'https://via.placeholder.com/50'); ?>" 
                                     alt="<?php echo htmlspecialchars($song['album_titulo'] ?? 'Álbum'); ?>"
                                     class="song-album-image">
                                <div class="song-info">
                                    <span class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></span>
                                    <span class="song-album"><?php echo htmlspecialchars($song['album_titulo'] ?? 'Sin álbum'); ?></span>
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
                    <?php else: ?>
                        <p style="color: var(--color-texto-gris); padding: 20px;">No hay canciones disponibles para este artista.</p>
                    <?php endif; ?>
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
    <style>
        .artist-header {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
            align-items: flex-end;
        }
        
        .artist-image {
            width: 200px;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }
        
        .artist-info h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--color-texto-claro);
        }
        
        .artist-genre {
            color: var(--color-texto-gris);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .song-count {
            color: var(--color-texto-gris);
            font-size: 14px;
        }
        
        .song-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        
        .favorite-btn:hover {
            transform: scale(1.2);
        }
        
        .favorite-btn i {
            font-size: 18px;
            transition: color 0.2s;
        }
        
        .artist-link {
            color: var(--color-texto-gris);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .artist-link:hover {
            color: var(--color-texto-claro);
            text-decoration: underline;
        }
    </style>
</body>
</html>

