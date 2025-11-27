<?php
/**
 * components/player-bar.php
 * Barra reproductora ubicada en el pie de página
 * Controles de reproducción, volumen y barra de progreso
 */

// Cargar datos de música si no están disponibles
if (!isset($data)) {
    require_once __DIR__ . '/../config.php';
    $data = loadData('music_data.json');
}

// Obtener información de la canción actual
$currentTrack = $data['currentTrack'] ?? [
    'title' => 'Canción Actual',
    'artist' => 'Artista',
    'image' => 'https://th.bing.com/th/id/R.e77f4b7034748db1a835d74daad07a4d?rik=t1D4FLdLs3viLg&pid=ImgRaw&r=0'
];
// Obtener estado y controles de la canción
$isPlaying = $currentTrack['isPlaying'] ?? false;      // ¿Se está reproduciendo?
$progress = $currentTrack['progress'] ?? 0;            // Progreso en porcentaje
$volume = $currentTrack['volume'] ?? 50;               // Volumen (0-100)
?>
<!-- Barra de reproducción en el pie de página -->
<footer class="player-bar">
    <!-- Información de la canción actual -->
    <div class="track-info">
        <!-- Imagen del álbum/artista actual -->
        <img src="<?php echo htmlspecialchars($currentTrack['image']); ?>" alt="Álbum Actual" id="current-track-image">
        <!-- Título y artista de la canción actual -->
        <span id="current-track-info"><?php echo htmlspecialchars($currentTrack['title'] . ' - ' . $currentTrack['artist']); ?></span>
    </div>

    <!-- Controles y barra de progreso -->
    <div class="controls">
        <!-- Botones de control de reproducción -->
        <div class="playback-buttons">
            <!-- Botón Shuffle (Aleatorio) -->
            <i class="fa-solid fa-shuffle" id="shuffle-btn" onclick="toggleShuffle()"></i>
            <!-- Botón Anterior -->
            <i class="fa-solid fa-backward-step" onclick="previousTrack()"></i>
            <!-- Botón Play/Pause -->
            <i class="fa-solid <?php echo $isPlaying ? 'fa-circle-pause' : 'fa-circle-play'; ?> main-play-btn" id="play-pause-btn" onclick="togglePlayPause()"></i>
            <!-- Botón Siguiente -->
            <i class="fa-solid fa-forward-step" onclick="nextTrack()"></i>
            <!-- Botón Repetir -->
            <i class="fa-solid fa-repeat" id="repeat-btn" onclick="toggleRepeat()"></i>
        </div>

        <!-- Barra de progreso de la canción -->
        <div class="progress-container">
            <!-- Tiempo actual -->
            <span class="time-display" id="current-time">0:00</span>
            <!-- Barra de progreso interactiva -->
            <div class="progress-bar" id="progress-bar" onclick="seek(event)">
                <!-- Relleno de progreso -->
                <div class="progress-fill" id="progress-fill" style="width: <?php echo $progress; ?>%"></div>
            </div>
            <!-- Tiempo total de la canción -->
            <span class="time-display" id="total-time">0:00</span>
        </div>
    </div>

    <!-- Control de volumen -->
    <div class="volume">
        <!-- Icono de volumen -->
        <i class="fa-solid fa-volume-high" id="volume-icon"></i>
        <!-- Slider de volumen -->
        <input type="range" min="0" max="100" value="<?php echo $volume; ?>" id="volume-slider" onchange="setVolume(this.value)">
    </div>
</footer>
    <div class="track-info">
        <img src="<?php echo htmlspecialchars($currentTrack['image']); ?>" alt="Álbum Actual" id="current-track-image">
        <span id="current-track-info"><?php echo htmlspecialchars($currentTrack['title'] . ' - ' . $currentTrack['artist']); ?></span>
    </div>

    <div class="controls">
        <div class="playback-buttons">
            <i class="fa-solid fa-shuffle" id="shuffle-btn" onclick="toggleShuffle()"></i>
            <i class="fa-solid fa-backward-step" onclick="previousTrack()"></i>
            <i class="fa-solid <?php echo $isPlaying ? 'fa-circle-pause' : 'fa-circle-play'; ?> main-play-btn" id="play-pause-btn" onclick="togglePlayPause()"></i>
            <i class="fa-solid fa-forward-step" onclick="nextTrack()"></i>
            <i class="fa-solid fa-repeat" id="repeat-btn" onclick="toggleRepeat()"></i>
        </div>

        <div class="progress-container">
            <span class="time-display" id="current-time">0:00</span>
            <div class="progress-bar" id="progress-bar" onclick="seek(event)">
                <div class="progress-fill" id="progress-fill" style="width: <?php echo $progress; ?>%"></div>
            </div>
            <span class="time-display" id="total-time">0:00</span>
        </div>
    </div>

    <div class="volume">
        <i class="fa-solid fa-volume-high" id="volume-icon"></i>
        <input type="range" min="0" max="100" value="<?php echo $volume; ?>" id="volume-slider" onchange="setVolume(this.value)">
    </div>
</footer>

