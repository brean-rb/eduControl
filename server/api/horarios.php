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

try {
    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Obtener DNI del profesor del token
        $dni = mysqli_real_escape_string($conn, $decodedToken->id);

        $sql = "SELECT 
                    hg.dia_setmana,
                    hg.hora_desde,
                    hg.hora_fins,
                    c.nom_val AS asignatura,
                    hg.grup AS grupo,         
                    hg.aula AS aula
                FROM horari_grup hg
                LEFT JOIN continguts c ON c.codi = hg.contingut
                WHERE hg.docent = '$dni'
                ORDER BY FIELD(hg.dia_setmana, 'L', 'M', 'X', 'J', 'V'), hg.hora_desde";

    } elseif ($method === 'POST') {
        // Obtener DNI y fecha del profesor
        $dni = mysqli_real_escape_string($conn, $_POST['documento']);
        $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);

        // Convertir fecha a día de la semana
        $diaSemana = date('N', strtotime($fecha));
        $diasMap = [
            1 => 'L', // Lunes
            2 => 'M', // Martes
            3 => 'X', // Miércoles
            4 => 'J', // Jueves
            5 => 'V'  // Viernes
        ];
        $diaSetmana = $diasMap[$diaSemana] ?? '';

        $sql = "SELECT 
                    hg.dia_setmana,
                    hg.hora_desde,
                    hg.hora_fins,
                    c.nom_val AS asignatura,
                    hg.grup AS grupo,         
                    hg.aula AS aula
                FROM horari_grup hg
                LEFT JOIN continguts c ON c.codi = hg.contingut
                WHERE hg.docent = '$dni'
                AND hg.dia_setmana = '$diaSetmana'
                ORDER BY hg.hora_desde";
    } else {
        throw new Exception('Método no permitido');
    }

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conn));
    }

    $horarios = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $horarios[] = $row;
    }

    echo json_encode([
        'success' => true,
        'horarios' => $horarios
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}