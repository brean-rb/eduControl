<?php
/**
 * =========================
 *  asistencia.php (Control de Asistencia)
 * =========================
 * 
 * Endpoint de control de asistencia.
 * Gestiona:
 * - Registro de asistencia
 * - Consulta de asistencia
 * - Estadísticas de asistencia
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

header('Content-Type: application/json');

// Validar token JWT
$auth = new Authentication();
$error = $auth->validaToken();
if ($error) {
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$decodedToken = $auth->getDecodedToken();

// Obtener datos del token
$rol = $decodedToken->rol;

// Verificar si el usuario tiene rol de administrador
if ($rol !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

try {
    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $tipoConsulta = $_POST['tipo_consulta'] ?? '';
        $tipoFecha = $_POST['tipo_fecha'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $mes = $_POST['mes'] ?? '';
        $documento = $_POST['documento'] ?? '';
        
        $sql = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                FROM registro_jornada a 
                JOIN docent d ON a.documento = d.document";
        
        $conditions = [];
        
        if ($tipoConsulta === 'docente' && !empty($documento)) {
            $documento = mysqli_real_escape_string($conn, $documento);
            $conditions[] = "a.documento = '$documento'";
        }
        
        if ($tipoFecha === 'dia' && !empty($fecha)) {
            $fecha = mysqli_real_escape_string($conn, $fecha);
            $conditions[] = "DATE(a.fecha) = '$fecha'";
        } elseif ($tipoFecha === 'mes' && !empty($mes)) {
            $mes = mysqli_real_escape_string($conn, $mes);
            $conditions[] = "DATE_FORMAT(a.fecha, '%Y-%m') = '$mes'";
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY a.fecha DESC, a.hora_entrada DESC";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception('Error al consultar asistencias: ' . mysqli_error($conn));
        }
        
        $asistencias = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $asistencias[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'asistencias' => $asistencias
        ]);
    } else {
        throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}