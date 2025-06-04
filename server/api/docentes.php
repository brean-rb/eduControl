<?php
/**
 * =========================
 *  docentes.php (Gestión de Docentes)
 * =========================
 * 
 * Endpoint de gestión de docentes.
 * Gestiona:
 * - Listado de docentes
 * - Información de docentes
 * - Roles de docentes
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */

// Desactivar la salida de errores de PHP
error_reporting(0);
ini_set('display_errors', 0);

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Authentication.php';

use Config\Authentication;

// Asegurar que siempre devolvemos JSON
header('Content-Type: application/json');

// Validar token JWT
$auth = new Authentication();
$error = $auth->validaToken();
if ($error) {
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$decodedToken = $auth->getDecodedToken();
$rol = $decodedToken->rol;
if ($rol !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Solo permitir GET para listar docentes
    if ($method !== 'GET') {
        throw new Exception('Método no permitido');
    }

    // Consultar todos los docentes
    $sql = "SELECT document, CONCAT(nom, ' ', cognom1, ' ', cognom2) as nombre 
            FROM docent 
            ORDER BY nom, cognom1, cognom2";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Error al consultar docentes: ' . mysqli_error($conn));
    }
    
    $docentes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $docentes[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'docentes' => $docentes
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}