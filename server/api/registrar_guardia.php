<?php
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

$response = ['success' => false, 'message' => ''];

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Escapar todos los valores recibidos
    $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
    $hora_inicio = mysqli_real_escape_string($conn, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($conn, $_POST['hora_fin']);
    $grupo = mysqli_real_escape_string($conn, $_POST['grupo']);
    $aula = mysqli_real_escape_string($conn, $_POST['aula']);
    $docente_ausente = mysqli_real_escape_string($conn, $_POST['docente_ausente']);
    $docente_guardia = mysqli_real_escape_string($conn, $decodedToken->id);
    $contenido = mysqli_real_escape_string($conn, $_POST['contenido']);
    $dia_semana = ['', 'L', 'M', 'X', 'J', 'V'][date('N', strtotime($fecha))];
    
    // Generar horario_grupo
    $horario_grupo = $fecha . '_' . $hora_inicio . '_' . $grupo;

    // Verificar si la guardia ya está reservada
    $sql_check = "SELECT id FROM registro_guardias 
                  WHERE fecha = '$fecha' 
                  AND hora_inicio = '$hora_inicio' 
                  AND docente_ausente = '$docente_ausente'
                  AND grupo = '$grupo'";

    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $response['message'] = 'Esta guardia ya ha sido reservada';
        echo json_encode($response);
        exit();
    }

    $sql = "INSERT INTO registro_guardias (
                horario_grupo, fecha, docente_ausente, docente_guardia, 
                aula, grupo, contenido, dia_semana, hora_inicio, hora_fin
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssss', 
        $horario_grupo, 
        $fecha, 
        $docente_ausente, 
        $docente_guardia, 
        $aula, 
        $grupo, 
        $contenido, 
        $dia_semana, 
        $hora_inicio, 
        $hora_fin
    );

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Guardia registrada correctamente';
    } else {
        throw new Exception(mysqli_error($conn));
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();