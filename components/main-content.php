<?php
/**
 * components/main-content.php
 * Contenido principal de la página de inicio
 * Muestra la barra superior, lista de canciones y controles
 */
?>
<div class="main-content">
    <!-- Barra superior con controles de navegación -->
    <header class="top-bar">
        <!-- Botones para navegación atrás y adelante -->
        <div class="nav-buttons">
            <!-- Botón atrás -->
            <button onclick="history.back()">&#60;</button>
            <!-- Botón adelante -->
            <button onclick="history.forward()">&#62;</button>
        </div>
        <!-- Botón de perfil del usuario -->
        <div class="profile-button" id="profile-menu">
            <?php 
            // Mostrar inicial del nombre del usuario
            $currentUser = getCurrentUser();
            echo strtoupper(substr($currentUser['name'] ?? $currentUser['email'] ?? 'U', 0, 1)); 
            ?>
        </div>
    </header>

    <!-- Área de contenido principal -->
    <main class="content-view">
        <!-- Título de la sección -->
        <h1>Todas las Canciones</h1>
        
        <!-- Lista de canciones disponibles -->
        <div class="songs-list">
            <?php foreach ($allSongs as $song): ?>
                <!-- Elemento individual de canción -->
                <div class="song-item" data-song-id="<?php echo $song['cancion_id']; ?>">
                    <!-- Imagen del álbum/canción -->
                    <img src="<?php echo htmlspecialchars($song['album_imagen'] ?? $song['artista_imagen'] ?? 'https://via.placeholder.com/50'); ?>" 
                         alt="<?php echo htmlspecialchars($song['album_titulo'] ?? 'Álbum'); ?>"
                         class="song-album-image">
                    <!-- Información de la canción -->
                    <div class="song-info">
                        <!-- Título de la canción -->
                        <span class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></span>
                        <!-- Artista con enlace a su página -->
                        <span class="song-artist">
                            <a href="artist.php?id=<?php echo $song['artista_id']; ?>" class="artist-link">
                                <?php echo htmlspecialchars($song['artista_nombre'] ?? 'Artista desconocido'); ?>
                            </a>
                        </span>
                    </div>
                    <!-- Acciones y duración -->
                    <div class="song-actions">
                        <!-- Botón para marcar/desmarcar como favorita -->
                        <button class="favorite-btn" 
                                data-song-id="<?php echo $song['cancion_id']; ?>"
                                onclick="toggleFavorite(<?php echo $song['cancion_id']; ?>, this)">
                            <!-- Icono de corazón (relleno si es favorita, vacío si no) -->
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

