<?php
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

    if ($method === 'POST') {
        $documento = mysqli_real_escape_string($conn, $_POST['documento']);
        $tipo = $_POST['tipo'];
        $motivo = mysqli_real_escape_string($conn, $_POST['motivo']);
        $registrado_por = $decodedToken->id;
        $justificada = isset($_POST['justificada']) ? 1 : 0;

        if ($tipo === 'dia') {
            $fecha = $_POST['fecha'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];

            $sql = "INSERT INTO ausencias (
                        documento, 
                        fecha_inicio, 
                        fecha_fin,
                        hora_inicio, 
                        hora_fin, 
                        motivo,
                        jornada_completa,
                        justificada,
                        registrado_por
                    ) VALUES (
                        '$documento', 
                        '$fecha', 
                        '$fecha', 
                        '$hora_inicio', 
                        '$hora_fin', 
                        '$motivo', 
                        0,
                        $justificada,
                        '$registrado_por'
                    )";

        } else {
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];

            $sql = "INSERT INTO ausencias (
                        documento, 
                        fecha_inicio, 
                        fecha_fin,
                        motivo,
                        jornada_completa,
                        justificada,
                        registrado_por
                    ) VALUES (
                        '$documento', 
                        '$fecha_inicio', 
                        '$fecha_fin', 
                        '$motivo', 
                        1,
                        $justificada,
                        '$registrado_por'
                    )";
        }

        if (!mysqli_query($conn, $sql)) {
            throw new Exception('Error al registrar la ausencia: ' . mysqli_error($conn));
        }

        echo json_encode([
            'success' => true,
            'message' => 'Ausencia registrada correctamente'
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