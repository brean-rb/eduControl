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
$dni_profesor = $decodedToken->id;

// Log para depuración
error_log("DNI del profesor: " . $dni_profesor);

$response = ['success' => false, 'message' => '', 'guardias' => []];

try {
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    $hora = $_GET['hora'] ?? null;

    // Log para depuración
    error_log("Fecha de búsqueda: " . $fecha);
    error_log("Hora de búsqueda: " . $hora);

    // Primero verificar si el profesor existe
    $check_profesor = mysqli_query($conn, "SELECT document FROM docent WHERE document = '$dni_profesor'");
    if (mysqli_num_rows($check_profesor) === 0) {
        throw new Exception("El profesor con DNI $dni_profesor no existe en la base de datos");
    }

    $sql = "SELECT 
                g.id,
                g.fecha,
                TIME_FORMAT(g.hora_inicio, '%H:%i') as hora,
                TIME_FORMAT(g.hora_fin, '%H:%i') as hora_fin,
                g.docente_ausente,
                CONCAT(d1.nom, ' ', d1.cognom1, ' ', d1.cognom2) as nombre_ausente,
                g.docente_guardia,
                CONCAT(d2.nom, ' ', d2.cognom1, ' ', d2.cognom2) as nombre_guardia,
                g.grupo,
                g.aula,
                g.contenido as asignatura,
                g.horario_grupo,
                g.dia_semana
            FROM registro_guardias g
            LEFT JOIN docent d1 ON g.docente_ausente = d1.document
            LEFT JOIN docent d2 ON g.docente_guardia = d2.document
            WHERE g.docente_guardia = '$dni_profesor'";
    
    if ($fecha) {
        $sql .= " AND DATE(g.fecha) = '$fecha'";
    }
    
    if ($hora) {
        $sql .= " AND TIME(g.hora_inicio) = '$hora'";
    }

    $sql .= " ORDER BY g.fecha DESC, g.hora_inicio ASC";

    // Log para depuración
    error_log("SQL Query: " . $sql);

    $resultado = mysqli_query($conn, $sql);
    if (!$resultado) {
        throw new Exception("Error en la consulta: " . mysqli_error($conn));
    }

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $response['guardias'][] = [
                'id_guardia' => $row['id'],
                'fecha' => $row['fecha'],
                'hora' => $row['hora'],
                'hora_fin' => $row['hora_fin'],
                'profesor_ausente' => $row['nombre_ausente'] ?: $row['docente_ausente'],
                'profesor_guardia' => $row['nombre_guardia'] ?: $row['docente_guardia'],
                'asignatura' => $row['asignatura'],
                'grupo' => $row['grupo'],
                'aula' => $row['aula'],
                'horario_grupo' => $row['horario_grupo'],
                'dia_semana' => $row['dia_semana']
            ];
        }
        $response['success'] = true;
    }

    // Log para depuración
    error_log("Número de guardias encontradas: " . count($response['guardias']));

} catch (Exception $e) {
    error_log("Error en obtener_guardias_realizadas.php: " . $e->getMessage());
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();