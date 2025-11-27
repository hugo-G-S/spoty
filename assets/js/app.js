/**
 * app.js
 * Script principal de la aplicación Spotify Clone
 * Gestiona la reproducción de música, controles del reproductor y eventos del usuario
 */

// Objeto que almacena la información de la canción actual
let currentTrack = {
    id: null,                      // ID de la canción
    title: 'Canción Actual',       // Título de la canción
    artist: 'Artista',             // Nombre del artista
    image: '',                     // URL de la imagen (álbum/artista)
    isPlaying: false,              // Estado de reproducción
    progress: 0,                   // Progreso en porcentaje
    duration: 0,                   // Duración total en segundos
    volume: 50                     // Volumen (0-100)
};

// Variables de control del reproductor
let shuffleMode = false;           // Modo aleatorio activado/desactivado
let repeatMode = false;            // Modo repetición activado/desactivado

/**
 * Se ejecuta cuando el DOM está completamente cargado
 * Inicializa la aplicación
 */
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentTrack();             // Cargar la canción actual guardada
    updatePlayerUI();               // Actualizar interfaz del reproductor
    setupEventListeners();          // Configurar listeners de eventos
});

/**
 * Carga la canción actual desde el servidor
 * Realiza una petición GET a api/get-current-track.php
 */
async function loadCurrentTrack() {
    try {
        // Realizar petición para obtener la canción actual
        const response = await fetch('api/get-current-track.php');
        const data = await response.json();
        // Si la respuesta es exitosa, actualizar currentTrack
        if (data.success) {
            currentTrack = data.track;
            updatePlayerUI();
        }
    } catch (error) {
        console.error('Error cargando track actual:', error);
    }
}

/**
 * Actualiza la interfaz del reproductor con la información actual
 * Actualiza títulos, imágenes, controles y barras de progreso
 */
function updatePlayerUI() {
    // Obtener elementos del DOM
    const trackInfo = document.getElementById('current-track-info');
    const trackImage = document.getElementById('current-track-image');
    const playPauseBtn = document.getElementById('play-pause-btn');
    const progressFill = document.getElementById('progress-fill');
    const currentTime = document.getElementById('current-time');
    const totalTime = document.getElementById('total-time');
    const volumeSlider = document.getElementById('volume-slider');
    
    // Actualizar información de la canción
    if (trackInfo) {
        trackInfo.textContent = `${currentTrack.title} - ${currentTrack.artist}`;
    }
    
    // Actualizar imagen de la canción
    if (trackImage) {
        trackImage.src = currentTrack.image;
    }
    
    // Actualizar icono del botón play/pause
    if (playPauseBtn) {
        playPauseBtn.className = currentTrack.isPlaying 
            ? 'fa-solid fa-circle-pause main-play-btn'
            : 'fa-solid fa-circle-play main-play-btn';
    }
    
    // Actualizar barra de progreso
    if (progressFill) {
        progressFill.style.width = `${currentTrack.progress}%`;
    }
    
    // Actualizar tiempo actual de reproducción
    if (currentTime) {
        currentTime.textContent = formatTime((currentTrack.progress / 100) * currentTrack.duration);
    }
    
    // Actualizar tiempo total
    if (totalTime) {
        totalTime.textContent = formatTime(currentTrack.duration);
    }
    
    // Actualizar slider de volumen
    if (volumeSlider) {
        volumeSlider.value = currentTrack.volume;
    }
}

/**
 * Convierte segundos a formato de tiempo mm:ss
 * @param {number} seconds Segundos a convertir
 * @return {string} Tiempo en formato mm:ss
 */
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

/**
 * Configura los listeners de eventos para el reproductor
 * Actualiza el progreso cada segundo si la canción está reproduciéndose
 */
function setupEventListeners() {
    setInterval(() => {
        if (currentTrack.isPlaying && currentTrack.duration > 0) {
            // Incrementar el progreso en función de la duración
            currentTrack.progress = Math.min(currentTrack.progress + (100 / currentTrack.duration), 100);
            updatePlayerUI();
            
            // Si la canción terminó, pasar a la siguiente
            if (currentTrack.progress >= 100) {
                nextTrack();
            }
        }
    }, 1000); // Actualizar cada segundo
}

/**
 * Selecciona un elemento (artista, álbum, etc.) para visualizar o reproducir
 * @param {number} id ID del elemento
 * @param {string} type Tipo del elemento (artist, album, etc.)
 */
async function selectItem(id, type) {
    try {
        // Realizar petición para obtener información del elemento
        const response = await fetch(`api/get-item.php?id=${id}&type=${type}`);
        const data = await response.json();
        
        if (data.success) {
            console.log('Item seleccionado:', data.item);
            
            // Si es un álbum y tiene canciones, reproducir la primera canción
            if (type === 'album' && data.item.songs) {
                if (data.item.songs.length > 0) {
                    playTrack(data.item.songs[0]);
                }
            }
        }
    } catch (error) {
        console.error('Error seleccionando item:', error);
    }
}

/**
 * Reproduce una canción específica
 * @param {Object} track Objeto con información de la canción (debe tener propiedad 'id')
 */
async function playTrack(track) {
    try {
        // Realizar petición POST para reproducir la canción
        const response = await fetch('api/play-track.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ trackId: track.id })
        });
        
        const data = await response.json();
        // Si la petición es exitosa, actualizar estado
        if (data.success) {
            currentTrack = data.track;
            currentTrack.isPlaying = true;
            updatePlayerUI();
        }
    } catch (error) {
        console.error('Error reproduciendo track:', error);
    }
}

/**
 * Alterna entre reproducción y pausa
 */
async function togglePlayPause() {
    try {
        // Realizar petición POST para toggle play/pause
        const response = await fetch('api/toggle-play.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                isPlaying: !currentTrack.isPlaying 
            })
        });
        
        const data = await response.json();
        // Si la petición es exitosa, actualizar estado
        if (data.success) {
            currentTrack.isPlaying = data.isPlaying;
            updatePlayerUI();
        }
    } catch (error) {
        console.error('Error toggle play/pause:', error);
    }
}

/**
 * Reproduce la siguiente canción
 */
async function nextTrack() {
    try {
        // Realizar petición POST para obtener la siguiente canción
        const response = await fetch('api/next-track.php', {
            method: 'POST'
        });
        
        const data = await response.json();
        // Si la petición es exitosa, actualizar canción y reproducir
        if (data.success) {
            currentTrack = data.track;
            currentTrack.isPlaying = true;
            updatePlayerUI();
        }
    } catch (error) {
        console.error('Error siguiente track:', error);
    }
}

/**
 * Reproduce la canción anterior
 */
async function previousTrack() {
    try {
        // Realizar petición POST para obtener la canción anterior
        const response = await fetch('api/previous-track.php', {
            method: 'POST'
        });
        
        const data = await response.json();
        // Si la petición es exitosa, actualizar canción y reproducir
        if (data.success) {
            currentTrack = data.track;
            currentTrack.isPlaying = true;
            updatePlayerUI();
        }
    } catch (error) {
        console.error('Error track anterior:', error);
    }
}

/**
 * Activa/desactiva el modo aleatorio (shuffle)
 */
function toggleShuffle() {
    // Alternar el estado del modo shuffle
    shuffleMode = !shuffleMode;
    // Obtener el botón de shuffle
    const btn = document.getElementById('shuffle-btn');
    // Cambiar color del botón según el estado
    if (btn) {
        btn.style.color = shuffleMode ? 'var(--color-verde-spotify)' : 'var(--color-texto-gris)';
    }
}

/**
 * Activa/desactiva el modo repetición
 */
function toggleRepeat() {
    // Alternar el estado del modo repetición
    repeatMode = !repeatMode;
    // Obtener el botón de repetición
    const btn = document.getElementById('repeat-btn');
    // Cambiar color del botón según el estado
    if (btn) {
        btn.style.color = repeatMode ? 'var(--color-verde-spotify)' : 'var(--color-texto-gris)';
    }
}

/**
 * Establece el volumen del reproductor
 * @param {number} value Valor del volumen (0-100)
 */
async function setVolume(value) {
    // Actualizar el volumen actual
    currentTrack.volume = value;
    // Obtener el icono de volumen
    const icon = document.getElementById('volume-icon');
    
    // Cambiar icono según el nivel de volumen
    if (value == 0) {
        icon.className = 'fa-solid fa-volume-x';
    } else if (value < 50) {
        icon.className = 'fa-solid fa-volume-low';
    } else {
        icon.className = 'fa-solid fa-volume-high';
    }
    
    // Enviar el volumen al servidor
    try {
        await fetch('api/set-volume.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ volume: value })
        });
    } catch (error) {
        console.error('Error estableciendo volumen:', error);
    }
}

/**
 * Permite al usuario hacer clic en la barra de progreso para cambiar la posición
 * @param {Event} event Evento del click
 */
function seek(event) {
    // Obtener la barra de progreso
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const x = event.clientX - rect.left;
    // Calcular el porcentaje de la barra clickeado
    const percentage = (x / rect.width) * 100;
    
    // Actualizar el progreso (entre 0 y 100)
    currentTrack.progress = Math.max(0, Math.min(100, percentage));
    updatePlayerUI();
    
    // Enviar la nueva posición al servidor
    fetch('api/seek-track.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ progress: currentTrack.progress })
    });
}

// Exportar funciones al objeto window para que sean accesibles globalmente
window.togglePlayPause = togglePlayPause;
window.nextTrack = nextTrack;
window.previousTrack = previousTrack;
window.toggleShuffle = toggleShuffle;
window.toggleRepeat = toggleRepeat;
window.setVolume = setVolume;
window.selectItem = selectItem;
window.seek = seek;

