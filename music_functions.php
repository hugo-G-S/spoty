<?php
/**
 * music_functions.php
 * Contiene todas las funciones para gestionar artistas, álbumes, canciones y favoritos
 * Maneja las consultas a la base de datos relacionadas con contenido musical
 */

require_once 'database.php';

/**
 * Obtiene todos los artistas de la base de datos
 * @return array Array de artistas ordenados alfabéticamente
 */
function getAllArtists() {
    try {
        $conn = getDBConnection();
        // Consultar todos los artistas ordenados por nombre
        $stmt = $conn->query("SELECT artista_id, nombre, genero_principal, biografia, imagen_url FROM Artistas ORDER BY nombre");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error obteniendo artistas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los artistas favoritos de un usuario específico
 * @param int $userId ID del usuario
 * @return array Array de artistas favoritos del usuario
 */
function getUserFavoriteArtists($userId) {
    try {
        $conn = getDBConnection();
        // Obtener artistas cuyos álbumes contienen canciones favoritas del usuario
        $stmt = $conn->prepare("
            SELECT DISTINCT a.artista_id, a.nombre, a.genero_principal, a.imagen_url 
            FROM Artistas a
            INNER JOIN Albumes al ON a.artista_id = al.artista_id
            INNER JOIN Canciones c ON al.album_id = c.album_id
            INNER JOIN Canciones_Favoritas cf ON c.cancion_id = cf.cancion_id
            WHERE cf.usuario_id = ?
            UNION
            SELECT DISTINCT a.artista_id, a.nombre, a.genero_principal, a.imagen_url
            FROM Artistas a
            INNER JOIN Cancion_Artista ca ON a.artista_id = ca.artista_id
            INNER JOIN Canciones_Favoritas cf ON ca.cancion_id = cf.cancion_id
            WHERE cf.usuario_id = ?
            ORDER BY nombre
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error obteniendo artistas favoritos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los artistas más populares o favoritos
 * @param int|null $userId ID del usuario (si es null, obtiene todos los artistas)
 * @param int $limit Número máximo de artistas a retornar
 * @return array Array de artistas limitado por el parámetro limit
 */
function getTopArtists($userId = null, $limit = 6) {
    if ($userId) {
        // Si hay usuario, retornar sus favoritos
        return array_slice(getUserFavoriteArtists($userId), 0, $limit);
    }
    // Si no hay usuario, retornar artistas generales
    return array_slice(getAllArtists(), 0, $limit);
}

/**
 * Obtiene todos los álbumes de la base de datos
 * @return array Array de álbumes con información del artista
 */
function getAllAlbums() {
    try {
        $conn = getDBConnection();
        // Consultar todos los álbumes con información del artista
        $stmt = $conn->query("
            SELECT a.album_id, a.titulo, a.fecha_lanzamiento, a.imagen_url,
                   ar.artista_id, ar.nombre as artista_nombre
            FROM Albumes a
            INNER JOIN Artistas ar ON a.artista_id = ar.artista_id
            ORDER BY a.fecha_lanzamiento DESC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error obteniendo álbumes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los álbumes más populares basados en cantidad de favoritos
 * @param int $limit Número máximo de álbumes a retornar
 * @return array Array de álbumes populares con contador de favoritos
 */
function getPopularAlbums($limit = 10) {
    try {
        $conn = getDBConnection();
        // Obtener álbumes ordenados por cantidad de canciones favoritas
        $stmt = $conn->query("
            SELECT a.album_id, a.titulo, a.fecha_lanzamiento, a.imagen_url,
                   ar.artista_id, ar.nombre as artista_nombre,
                   COUNT(cf.cancion_id) as favoritos_count
            FROM Albumes a
            INNER JOIN Artistas ar ON a.artista_id = ar.artista_id
            LEFT JOIN Canciones c ON a.album_id = c.album_id
            LEFT JOIN Canciones_Favoritas cf ON c.cancion_id = cf.cancion_id
            GROUP BY a.album_id
            ORDER BY favoritos_count DESC, a.fecha_lanzamiento DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error obteniendo álbumes populares: " . $e->getMessage());
        return getAllAlbums();
    }
}

/**
 * Obtiene todas las canciones de un álbum específico
 * @param int $albumId ID del álbum
 * @return array Array de canciones del álbum
 */
function getAlbumSongs($albumId) {
    try {
        $conn = getDBConnection();
        // Obtener canciones del álbum ordenadas por ID
        $stmt = $conn->prepare("
            SELECT c.cancion_id, c.titulo, c.duracion_segundos
            FROM Canciones c
            WHERE c.album_id = ?
            ORDER BY c.cancion_id
        ");
        $stmt->execute([$albumId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error obteniendo canciones del álbum: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene todas las canciones favoritas de un usuario
 * @param int $userId ID del usuario
 * @return array Array de canciones favoritas con detalles
 */
function getUserFavoriteSongs($userId) {
    try {
        $conn = getDBConnection();
        // Obtener canciones favoritas con detalles del álbum y artista
        $stmt = $conn->prepare("
            SELECT c.cancion_id, c.titulo, c.duracion_segundos, c.album_id,
                   a.titulo as album_titulo, a.imagen_url as album_imagen,
                   ar.nombre as artista_nombre, ar.artista_id
            FROM Canciones_Favoritas cf
            INNER JOIN Canciones c ON cf.cancion_id = c.cancion_id
            LEFT JOIN Albumes a ON c.album_id = a.album_id
            LEFT JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE cf.usuario_id = ?
            ORDER BY cf.fecha_agregado DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error obteniendo canciones favoritas: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca música por término (artistas, álbumes y canciones)
 * @param string $query Término de búsqueda
 * @param int|null $userId ID del usuario (para marcar favoritos)
 * @return array Array con 'artists', 'albums' y 'songs' como resultados
 */
function searchMusic($query, $userId = null) {
    $results = [
        'artists' => [],
        'albums' => [],
        'songs' => []
    ];
    
    try {
        $conn = getDBConnection();
        // Preparar el término de búsqueda con comodines
        $searchTerm = "%{$query}%";
        
        // Buscar artistas por nombre o género
        $stmt = $conn->prepare("
            SELECT artista_id, nombre, genero_principal, imagen_url 
            FROM Artistas 
            WHERE nombre LIKE ? OR genero_principal LIKE ?
            ORDER BY nombre
        ");
        $stmt->execute([$searchTerm, $searchTerm]);
        $results['artists'] = $stmt->fetchAll();
        
        // Buscar álbumes por título o artista
        $stmt = $conn->prepare("
            SELECT a.album_id, a.titulo, a.fecha_lanzamiento, a.imagen_url,
                   ar.artista_id, ar.nombre as artista_nombre
            FROM Albumes a
            INNER JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE a.titulo LIKE ? OR ar.nombre LIKE ?
            ORDER BY a.fecha_lanzamiento DESC
        ");
        $stmt->execute([$searchTerm, $searchTerm]);
        $results['albums'] = $stmt->fetchAll();
        
        // Buscar canciones por título o artista
        $stmt = $conn->prepare("
            SELECT c.cancion_id, c.titulo, c.duracion_segundos, c.album_id,
                   a.titulo as album_titulo, a.imagen_url as album_imagen,
                   ar.nombre as artista_nombre, ar.artista_id
            FROM Canciones c
            LEFT JOIN Albumes a ON c.album_id = a.album_id
            LEFT JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE c.titulo LIKE ? OR ar.nombre LIKE ?
            ORDER BY c.titulo
        ");
        $stmt->execute([$searchTerm, $searchTerm]);
        $results['songs'] = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error en búsqueda: " . $e->getMessage());
    }
    
    return $results;
}

/**
 * Obtiene información de un artista específico
 * @param int $artistId ID del artista
 * @return array|null Array con datos del artista o null si no existe
 */
function getArtistById($artistId) {
    try {
        $conn = getDBConnection();
        // Validar que el ID sea un número entero
        $artistId = intval($artistId);
        
        // Consultar el artista por ID
        $sql = "SELECT artista_id, nombre, genero_principal, biografia, imagen_url 
                FROM Artistas 
                WHERE artista_id = :id
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $artistId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        return $result ? $result : null;
    } catch (PDOException $e) {
        error_log("Error obteniendo artista: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtiene información de un álbum específico
 * @param int $albumId ID del álbum
 * @return array|null Array con datos del álbum o null si no existe
 */
function getAlbumById($albumId) {
    try {
        $conn = getDBConnection();
        // Consultar el álbum con información del artista
        $stmt = $conn->prepare("
            SELECT a.album_id, a.titulo, a.fecha_lanzamiento, a.imagen_url,
                   ar.artista_id, ar.nombre as artista_nombre
            FROM Albumes a
            INNER JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE a.album_id = ?
        ");
        $stmt->execute([$albumId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error obteniendo álbum: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtiene información de una canción específica
 * @param int $songId ID de la canción
 * @return array|null Array con datos de la canción o null si no existe
 */
function getSongById($songId) {
    try {
        $conn = getDBConnection();
        // Consultar la canción con información del álbum y artista
        $stmt = $conn->prepare("
            SELECT c.cancion_id, c.titulo, c.duracion_segundos, c.album_id,
                   a.titulo as album_titulo, a.imagen_url as album_imagen,
                   ar.nombre as artista_nombre, ar.artista_id
            FROM Canciones c
            LEFT JOIN Albumes a ON c.album_id = a.album_id
            LEFT JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE c.cancion_id = ?
        ");
        $stmt->execute([$songId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error obteniendo canción: " . $e->getMessage());
        return null;
    }
}

/**
 * Alterna el estado favorito de una canción para un usuario
 * Si la canción es favorita, la elimina; si no lo es, la agrega
 * @param int $userId ID del usuario
 * @param int $songId ID de la canción
 * @return array Array con 'success' (bool) y 'favorited' (bool) o 'message' (string)
 */
function toggleFavoriteSong($userId, $songId) {
    try {
        $conn = getDBConnection();
        
        // Verificar si la canción ya está en favoritos
        $stmt = $conn->prepare("SELECT * FROM Canciones_Favoritas WHERE usuario_id = ? AND cancion_id = ?");
        $stmt->execute([$userId, $songId]);
        
        if ($stmt->fetch()) {
            // Si está en favoritos, eliminarla
            $stmt = $conn->prepare("DELETE FROM Canciones_Favoritas WHERE usuario_id = ? AND cancion_id = ?");
            $stmt->execute([$userId, $songId]);
            return ['success' => true, 'favorited' => false];
        } else {
            // Si no está en favoritos, agregarla
            $stmt = $conn->prepare("INSERT INTO Canciones_Favoritas (usuario_id, cancion_id) VALUES (?, ?)");
            $stmt->execute([$userId, $songId]);
            return ['success' => true, 'favorited' => true];
        }
    } catch (PDOException $e) {
        error_log("Error toggle favorito: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Verifica si una canción es favorita de un usuario
 * @param int $userId ID del usuario
 * @param int $songId ID de la canción
 * @return bool true si es favorita, false en caso contrario
 */
function isSongFavorite($userId, $songId) {
    try {
        $conn = getDBConnection();
        // Buscar la canción en favoritos del usuario
        $stmt = $conn->prepare("SELECT * FROM Canciones_Favoritas WHERE usuario_id = ? AND cancion_id = ?");
        $stmt->execute([$userId, $songId]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Error verificando favorito: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene todas las canciones disponibles en la aplicación
 * @param int|null $userId ID del usuario (para marcar favoritos personales)
 * @return array Array de canciones con detalles
 */
function getAllSongs($userId = null) {
    try {
        $conn = getDBConnection();
        // Consultar todas las canciones con información del álbum y artista
        $sql = "
            SELECT c.cancion_id, c.titulo, c.duracion_segundos, c.album_id,
                   a.titulo as album_titulo, a.imagen_url as album_imagen,
                   ar.nombre as artista_nombre, ar.artista_id, ar.imagen_url as artista_imagen
            FROM Canciones c
            LEFT JOIN Albumes a ON c.album_id = a.album_id
            LEFT JOIN Artistas ar ON a.artista_id = ar.artista_id
            ORDER BY c.titulo
        ";
        $stmt = $conn->query($sql);
        $songs = $stmt->fetchAll();
        
        // Si hay usuario, marcar sus canciones favoritas
        if ($userId) {
            foreach ($songs as &$song) {
                $song['is_favorite'] = isSongFavorite($userId, $song['cancion_id']);
            }
        }
        
        return $songs;
    } catch (PDOException $e) {
        error_log("Error obteniendo todas las canciones: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene las canciones favoritas de un usuario con detalles completos
 * @param int $userId ID del usuario
 * @return array Array de canciones favoritas con detalles
 */
function getUserFavoriteSongsWithDetails($userId) {
    try {
        $conn = getDBConnection();
        // Obtener canciones favoritas con toda la información
        $stmt = $conn->prepare("
            SELECT c.cancion_id, c.titulo, c.duracion_segundos, c.album_id,
                   a.titulo as album_titulo, a.imagen_url as album_imagen,
                   ar.nombre as artista_nombre, ar.artista_id, ar.imagen_url as artista_imagen,
                   cf.fecha_agregado
            FROM Canciones_Favoritas cf
            INNER JOIN Canciones c ON cf.cancion_id = c.cancion_id
            LEFT JOIN Albumes a ON c.album_id = a.album_id
            LEFT JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE cf.usuario_id = ?
            ORDER BY cf.fecha_agregado DESC
        ");
        $stmt->execute([$userId]);
        $songs = $stmt->fetchAll();
        
        // Marcar todas como favoritas (ya que vienen de la tabla de favoritos)
        foreach ($songs as &$song) {
            $song['is_favorite'] = true;
        }
        
        return $songs;
    } catch (PDOException $e) {
        error_log("Error obteniendo canciones favoritas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene todas las canciones de un artista específico
 * @param int $artistId ID del artista
 * @param int|null $userId ID del usuario (para marcar favoritos)
 * @return array Array de canciones del artista
 */
function getArtistSongs($artistId, $userId = null) {
    try {
        $conn = getDBConnection();
        // Consultar canciones del artista
        $sql = "
            SELECT c.cancion_id, c.titulo, c.duracion_segundos, c.album_id,
                   a.titulo as album_titulo, a.imagen_url as album_imagen,
                   ar.nombre as artista_nombre, ar.artista_id, ar.imagen_url as artista_imagen
            FROM Canciones c
            LEFT JOIN Albumes a ON c.album_id = a.album_id
            LEFT JOIN Artistas ar ON a.artista_id = ar.artista_id
            WHERE ar.artista_id = ?
            ORDER BY c.titulo
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$artistId]);
        $songs = $stmt->fetchAll();
        
        // Si hay usuario, marcar sus canciones favoritas
        if ($userId) {
            foreach ($songs as &$song) {
                $song['is_favorite'] = isSongFavorite($userId, $song['cancion_id']);
            }
        }
        
        return $songs;
    } catch (PDOException $e) {
        error_log("Error obteniendo canciones del artista: " . $e->getMessage());
        return [];
    }
}
?>

