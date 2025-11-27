<?php
/**
 * components/sidebar.php
 * Barra lateral de navegación principal
 * Contiene navegación, información del usuario y biblioteca de artistas
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../music_functions.php';

// Obtener filtro activo de la URL o usar "Listas" por defecto
$activeFilter = $_GET['filter'] ?? 'Listas';
// Obtener información del usuario autenticado
$currentUser = getCurrentUser();
$userId = $_SESSION['user_id'] ?? null;

// Cargar artistas según el filtro seleccionado
if ($activeFilter === 'Artistas') {
    // Si está en vista "Artistas", mostrar todos los artistas
    $libraryArtists = getAllArtists();
} else {
    // Si está en vista "Listas", mostrar artistas favoritos del usuario
    $libraryArtists = getUserFavoriteArtists($userId);
    // Si no hay artistas favoritos, mostrar todos
    if (empty($libraryArtists)) {
        $libraryArtists = getAllArtists();
    }
}
?>
<!-- Barra lateral de navegación -->
<div class="sidebar">
    <!-- Sección de navegación principal -->
    <div class="nav-section">
        <ul>
            <!-- Enlace a inicio -->
            <li>
                <a href="index.php">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
            </li>
            <!-- Enlace a búsqueda -->
            <li>
                <a href="search.php">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </a>
            </li>
            <!-- Enlace a canciones favoritas -->
            <li>
                <a href="favorites.php">
                    <i class="fa-solid fa-heart"></i> Mis Favoritos
                </a>
            </li>
        </ul>
    </div>

    <!-- Sección con información del usuario -->
    <div class="user-section">
        <div class="user-info">
            <?php if (isset($currentUser)): ?>
                <!-- Avatar del usuario con inicial de su nombre -->
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'] ?? $currentUser['email'], 0, 1)); ?>
                </div>
                <!-- Información del usuario (nombre y email) -->
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Usuario'); ?></span>
                    <span class="user-email"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <!-- Botón de cerrar sesión -->
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Cerrar Sesión
        </a>
    </div>

    <!-- Sección de biblioteca del usuario -->
    <div class="library-section">
        <h2>
            <i class="fa-solid fa-book"></i> Tu biblioteca
        </h2>
        <!-- Botones para cambiar el filtro de visualización -->
        <div class="library-filters">
            <!-- Botón "Listas" (favoritos) -->
            <a href="?filter=Listas" class="filter-btn <?php echo $activeFilter === 'Listas' ? 'active' : ''; ?>">
                Listas
            </a>
            <!-- Botón "Artistas" (todos) -->
            <a href="?filter=Artistas" class="filter-btn <?php echo $activeFilter === 'Artistas' ? 'active' : ''; ?>">
                Artistas
            </a>
        </div>

        <!-- Lista de artistas según el filtro -->
        <div class="simple-list" id="library-list">
            <?php if ($activeFilter === 'Artistas'): ?>
                <!-- Mostrar todos los artistas -->
                <?php 
                error_log("DEBUG sidebar - Total artistas: " . count($libraryArtists));
                foreach ($libraryArtistas as $idx => $artist) {
                    error_log("DEBUG sidebar - Artista #$idx: ID=" . $artist['artista_id'] . ", Nombre=" . $artist['nombre']);
                }
                ?>
                <!-- Enlace para cada artista -->
                <?php foreach ($libraryArtists as $artist): ?>
                    <a href="artist.php?id=<?php echo intval($artist['artista_id']); ?>" 
                       class="library-item" 
                       data-id="<?php echo intval($artist['artista_id']); ?>" 
                       data-type="artist"
                       onclick="console.log('Click: <?php echo htmlspecialchars($artist['nombre']); ?> (ID: <?php echo intval($artist['artista_id']); ?>)');">
                        <?php echo htmlspecialchars($artist['nombre']); ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mostrar artistas favoritos -->
                <p>Artistas que te gustan</p>
                <?php foreach ($libraryArtists as $artist): ?>
                    <a href="artist.php?id=<?php echo $artist['artista_id']; ?>" class="library-item" data-id="<?php echo $artist['artista_id']; ?>" data-type="artist">
                        <?php echo htmlspecialchars($artist['nombre']); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
    <div class="nav-section">
        <ul>
            <li>
                <a href="index.php">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
            </li>
            <li>
                <a href="search.php">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </a>
            </li>
            <li>
                <a href="favorites.php">
                    <i class="fa-solid fa-heart"></i> Mis Favoritos
                </a>
            </li>
        </ul>
    </div>

    <div class="user-section">
        <div class="user-info">
            <?php if (isset($currentUser)): ?>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'] ?? $currentUser['email'], 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Usuario'); ?></span>
                    <span class="user-email"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Cerrar Sesión
        </a>
    </div>

    <div class="library-section">
        <h2>
            <i class="fa-solid fa-book"></i> Tu biblioteca
        </h2>
        <div class="library-filters">
            <a href="?filter=Listas" class="filter-btn <?php echo $activeFilter === 'Listas' ? 'active' : ''; ?>">
                Listas
            </a>
            <a href="?filter=Artistas" class="filter-btn <?php echo $activeFilter === 'Artistas' ? 'active' : ''; ?>">
                Artistas
            </a>
        </div>

        <div class="simple-list" id="library-list">
            <?php if ($activeFilter === 'Artistas'): ?>
                <?php 
                error_log("DEBUG sidebar - Total artistas: " . count($libraryArtists));
                foreach ($libraryArtists as $idx => $artist) {
                    error_log("DEBUG sidebar - Artista #$idx: ID=" . $artist['artista_id'] . ", Nombre=" . $artist['nombre']);
                }
                ?>
                <?php foreach ($libraryArtists as $artist): ?>
                    <a href="artist.php?id=<?php echo intval($artist['artista_id']); ?>" 
                       class="library-item" 
                       data-id="<?php echo intval($artist['artista_id']); ?>" 
                       data-type="artist"
                       onclick="console.log('Click: <?php echo htmlspecialchars($artist['nombre']); ?> (ID: <?php echo intval($artist['artista_id']); ?>)');">
                        <?php echo htmlspecialchars($artist['nombre']); ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Artistas que te gustan</p>
                <?php foreach ($libraryArtists as $artist): ?>
                    <a href="artist.php?id=<?php echo $artist['artista_id']; ?>" class="library-item" data-id="<?php echo $artist['artista_id']; ?>" data-type="artist">
                        <?php echo htmlspecialchars($artist['nombre']); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

