<?php
/**
 * search.php
 * Página de búsqueda de música
 * Permite a usuarios buscar artistas, álbumes y canciones
 */

require_once 'auth.php';
// Requerir autenticación para acceder a esta página
requireAuth();

require_once 'config.php';
require_once 'music_functions.php';

// Obtener información del usuario autenticado
$currentUser = getCurrentUser();
$userId = $_SESSION['user_id'] ?? null;

// Obtener término de búsqueda de los parámetros GET
$searchQuery = $_GET['q'] ?? '';
// Inicializar resultado con arrays vacíos
$results = [
    'artists' => [],
    'albums' => [],
    'songs' => []
];

// Si hay un término de búsqueda, realizar la búsqueda
if (!empty($searchQuery)) {
    $results = searchMusic($searchQuery, $userId);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - Spotify Clone</title>
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

            <!-- Contenido principal de búsqueda -->
            <main class="content-view">
                <h1>Buscar</h1>
                
                <!-- Formulario de búsqueda -->
                <form method="GET" action="search.php" style="margin-bottom: 30px;">
                    <input 
                        type="text" 
                        name="q" 
                        value="<?php echo htmlspecialchars($searchQuery); ?>" 
                        placeholder="¿Qué quieres escuchar?"
                        style="width: 100%; padding: 12px; background: #282828; border: none; border-radius: 4px; color: white; font-size: 16px;"
                        autofocus
                    >
                </form>

                <!-- Mostrar resultados si hay búsqueda -->
                <?php if (!empty($searchQuery)): ?>
                    <!-- Sección de artistas encontrados -->
                    <?php if (count($results['artists']) > 0): ?>
                        <h2>Artistas</h2>
                        <div class="simple-grid">
                            <?php foreach ($results['artists'] as $artist): ?>
                                <div class="card" onclick="selectItem(<?php echo $artist['artista_id']; ?>, 'artist')">
                                    <img src="<?php echo htmlspecialchars($artist['imagen_url'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($artist['nombre']); ?>">
                                    <p><?php echo htmlspecialchars($artist['nombre']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Sección de álbumes encontrados -->
                    <?php if (count($results['albums']) > 0): ?>
                        <h2>Álbumes</h2>
                        <div class="simple-grid">
                            <?php foreach ($results['albums'] as $album): ?>
                                <div class="card" onclick="selectItem(<?php echo $album['album_id']; ?>, 'album')">
                                    <img src="<?php echo htmlspecialchars($album['imagen_url'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($album['titulo']); ?>">
                                    <p><?php echo htmlspecialchars($album['titulo']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Sección de canciones encontradas -->
                    <?php if (count($results['songs']) > 0): ?>
                        <h2>Canciones</h2>
                        <div class="songs-list">
                            <?php foreach ($results['songs'] as $song): ?>
                                <div class="song-item" onclick="playSong(<?php echo $song['cancion_id']; ?>)">
                                    <div class="song-info">
                                        <span class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></span>
                                        <span class="song-artist"><?php echo htmlspecialchars($song['artista_nombre'] ?? 'Artista desconocido'); ?></span>
                                    </div>
                                    <span class="song-duration"><?php echo gmdate('i:s', $song['duracion_segundos'] ?? 0); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Mensaje si no hay resultados -->
                    <?php if (empty($results['artists']) && empty($results['albums']) && empty($results['songs'])): ?>
                        <p style="color: var(--color-texto-gris);">No se encontraron resultados para "<?php echo htmlspecialchars($searchQuery); ?>"</p>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <!-- Incluir barra de reproductor -->
    <?php include 'components/player-bar.php'; ?>
    
    <!-- Scripts de la aplicación -->
    <script src="assets/js/app.js"></script>
</body>
</html>

