<?php
/**
 * =========================
 *  login.php (Autenticación)
 * =========================
 * 
 * Endpoint de autenticación del sistema.
 * Gestiona:
 * - Validación de credenciales
 * - Generación de token JWT
 * - Respuesta de autenticación
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

try {
    // Verificar que la petición es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener y decodificar el JSON del body
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON');
    }

    $dni = $input['dni'] ?? '';
    $password = $input['password'] ?? '';

    if (!$dni || !$password) {
        throw new Exception('DNI y contraseña requeridos');
    }

    $dni = mysqli_real_escape_string($conn, trim($dni));
    $password = mysqli_real_escape_string($conn, trim($password));

    // Consulta igual que el login clásico
    $sql = "SELECT u.*, d.nom, d.cognom1, d.cognom2 
            FROM usuarios u 
            LEFT JOIN docent d ON u.documento = d.document 
            WHERE u.documento = '$dni'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conn));
    }

    $auth = new Authentication();

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $token = $auth->generaToken($row['documento'], $row['rol']);

            // Registrar el inicio de sesión
            $log = date('Y-m-d H:i:s') . " - " . $row['documento'] . " inició sesión\n";
            $file_path = __DIR__ . '/../registro_sesion.txt';
            
            // Log para depuración
            error_log("Ruta del archivo en login: " . $file_path);
            error_log("__DIR__ en login: " . __DIR__);
            
            file_put_contents($file_path, $log, FILE_APPEND);

            echo json_encode([
                'success' => true,
                'mensaje' => 'Login correcto',
                'token' => $token,
                'rol' => $row['rol'],
                'nombre_completo' => trim($row['nom'] . ' ' . $row['cognom1'] . ' ' . $row['cognom2'])
            ]);
            exit;
        } else {
            throw new Exception('Contraseña incorrecta');
        }
    } else {
        throw new Exception('Usuario no encontrado');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}