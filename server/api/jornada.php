<?php
/**
 * =========================
 *  jornada.php (Control de Jornada)
 * =========================
 * 
 * Endpoint de control de jornada laboral.
 * Gestiona:
 * - Inicio de jornada
 * - Fin de jornada
 * - Estado de jornada
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

// Instanciar la clase Authentication
$auth = new Authentication();

// Validar el token JWT
$error = $auth->validaToken();
if ($error) {
    echo json_encode([
        'success' => false,
        'message' => $error
    ]);
    exit;
}

// Obtener datos del token
$decodedToken = $auth->getDecodedToken();
$dni = mysqli_real_escape_string($conn, $decodedToken->id);

$fecha = date('Y-m-d');
$hora = date('H:i:s');

$method = $_SERVER['REQUEST_METHOD'];

// Asegurar que siempre devolvemos JSON
header('Content-Type: application/json');

try {
    if ($method === 'POST') {
        // Iniciar jornada
        $sqlCheck = "SELECT id FROM registro_jornada 
                        WHERE documento = '$dni' 
                        AND fecha = '$fecha' 
                        AND hora_salida = '00:00:00' 
                        LIMIT 1";
        $resCheck = mysqli_query($conn, $sqlCheck);
        
        if (!$resCheck) {
            throw new Exception('Error al verificar jornada: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($resCheck) > 0) {
            throw new Exception('Ya tienes una jornada iniciada hoy');
        }

        $sqlInsert = "INSERT INTO registro_jornada (documento, fecha, hora_entrada, hora_salida) 
                        VALUES ('$dni', '$fecha', '$hora', '00:00:00')";
        
        if (!mysqli_query($conn, $sqlInsert)) {
            throw new Exception('Error al iniciar la jornada: ' . mysqli_error($conn));
        }

        echo json_encode([
            'success' => true,
            'message' => 'Jornada iniciada correctamente'
        ]);

    } elseif ($method === 'PUT') {
        // Finalizar jornada
        $sqlUpdate = "UPDATE registro_jornada 
                        SET hora_salida = '$hora' 
                        WHERE documento = '$dni' 
                        AND fecha = '$fecha' 
                        AND hora_salida = '00:00:00' 
                        ORDER BY id DESC 
                        LIMIT 1";
        
        if (!mysqli_query($conn, $sqlUpdate)) {
            throw new Exception('Error al finalizar la jornada: ' . mysqli_error($conn));
        }

        if (mysqli_affected_rows($conn) === 0) {
            throw new Exception('No hay jornada abierta para finalizar');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Jornada finalizada correctamente'
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