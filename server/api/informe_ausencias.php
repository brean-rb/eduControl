<?php
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
$rol = $decodedToken->rol;
if ($rol !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        // Verificar que se recibieron los datos necesarios
        if (!isset($_POST['tipo_informe'])) {
            throw new Exception('Falta el tipo de informe');
        }

        $tipo_informe = $_POST['tipo_informe'];
        
        $sql = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                FROM ausencias a 
                LEFT JOIN docent d ON a.documento = d.document 
                WHERE 1=1";

        switch ($tipo_informe) {
            case 'docente':
                if (!empty($_POST['documento'])) {
                    $documento = mysqli_real_escape_string($conn, $_POST['documento']);
                    $sql .= " AND a.documento = '$documento'";
                }
                break;

            case 'dia':
                if (!isset($_POST['fecha'])) {
                    throw new Exception('Falta la fecha');
                }
                $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
                $sql .= " AND DATE(a.fecha_inicio) = '$fecha'";
                break;

            case 'semana':
                if (!isset($_POST['fecha'])) {
                    throw new Exception('Falta la fecha');
                }
                $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
                $sql .= " AND YEARWEEK(a.fecha_inicio, 1) = YEARWEEK('$fecha', 1)";
                break;

            case 'mes':
                if (!isset($_POST['fecha'])) {
                    throw new Exception('Falta la fecha');
                }
                $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
                $sql .= " AND MONTH(a.fecha_inicio) = MONTH('$fecha') 
                        AND YEAR(a.fecha_inicio) = YEAR('$fecha')";
                break;

            case 'trimestre':
                if (!isset($_POST['fecha'])) {
                    throw new Exception('Falta la fecha');
                }
                $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
                $sql .= " AND QUARTER(a.fecha_inicio) = QUARTER('$fecha')
                        AND YEAR(a.fecha_inicio) = YEAR('$fecha')";
                break;

            case 'curso':
                if (!isset($_POST['fecha'])) {
                    throw new Exception('Falta la fecha');
                }
                $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
                $sql .= " AND YEAR(a.fecha_inicio) = YEAR('$fecha')";
                break;

            default:
                throw new Exception('Tipo de informe no válido');
        }

        $sql .= " ORDER BY a.fecha_inicio DESC";

        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception('Error al consultar ausencias: ' . mysqli_error($conn));
        }

        $ausencias = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ausencias[] = [
                'nombre' => $row['nombre'],
                'fecha_inicio' => date('d/m/Y', strtotime($row['fecha_inicio'])),
                'fecha_fin' => date('d/m/Y', strtotime($row['fecha_fin'])),
                'motivo' => $row['motivo'],
                'justificada' => $row['justificada'] == 1
            ];
        }
        
        echo json_encode([
            'success' => true,
            'ausencias' => $ausencias
        ]);
    } else {
        throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}