<?php
/**
 * config.php
 * Archivo de configuración principal de la aplicación
 * Define constantes globales y funciones de utilidad para manejo de datos
 */

// URL base de la aplicación
define('BASE_URL', 'http://localhost');
// Directorio donde se almacenan los archivos JSON de datos
define('DATA_DIR', __DIR__ . '/data/');

/**
 * Carga datos de un archivo JSON
 * @param string $filename Nombre del archivo a cargar
 * @return array Datos decodificados del archivo o array vacío si no existe
 */
function loadData($filename) {
    $filepath = DATA_DIR . $filename;
    // Verificar si el archivo existe
    if (file_exists($filepath)) {
        // Leer el contenido del archivo
        $content = file_get_contents($filepath);
        // Decodificar JSON a array asociativo
        return json_decode($content, true);
    }
    return [];
}

/**
 * Guarda datos en un archivo JSON
 * @param string $filename Nombre del archivo a guardar
 * @param array $data Datos a guardar
 */
function saveData($filename, $data) {
    $filepath = DATA_DIR . $filename;
    // Crear el directorio si no existe
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0777, true);
    }
    // Guardar el array como JSON con formato bonito
    file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Sanitiza datos para evitar inyecciones XSS
 * @param string $data Datos a sanitizar
 * @return string Datos sanitizados
 */
function sanitize($data) {
    // Eliminar etiquetas HTML/PHP, espacios en blanco y convertir caracteres especiales
    return htmlspecialchars(strip_tags(trim($data)));
}
?>

