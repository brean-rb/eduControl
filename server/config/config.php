<?php
/**
 * =========================
 *  config.php (Configuración)
 * =========================
 * 
 * Archivo de configuración del sistema.
 * Gestiona:
 * - Conexión a base de datos
 * - Configuración de entorno
 * - Variables globales
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_guardias_asistencias');
define('DB_USER', 'root');
define('DB_PASS', '');

// Crear conexión mysqli
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

// Verificar conexión inicial
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión al servidor MySQL: ' . mysqli_connect_error()
    ]);
    exit;
}

// Seleccionar la base de datos
if (!mysqli_select_db($conn, DB_NAME)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al seleccionar la base de datos: ' . mysqli_error($conn)
    ]);
    exit;
}

// Establecer charset
if (!mysqli_set_charset($conn, "utf8")) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al establecer el charset: ' . mysqli_error($conn)
    ]);
    exit;
}