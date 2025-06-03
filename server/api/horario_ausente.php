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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$response = ['success' => false, 'message' => '', 'horario' => []];

try {
    $documento = $_POST['documento'] ?? null;
    $fecha = $_POST['fecha'] ?? null;

    if (!$documento || !$fecha) {
        throw new Exception('Faltan parámetros');
    }

    // Calcular día de la semana en letra
    $dia_mapping = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V'];
    $dia_numero = date('N', strtotime($fecha));
    $dia_letra = $dia_mapping[$dia_numero] ?? '';

    // Consulta con JOIN a ausencias y filtrado correcto
    $sql = "SELECT h.hora_desde, h.hora_fins, h.grup, h.aula, COALESCE(c.nom_val, h.contingut) as asignatura,
                   rg.docente_guardia,
                   a.jornada_completa,
                   a.hora_inicio as ausencia_inicio,
                   a.hora_fin as ausencia_fin
            FROM horari_grup h
            LEFT JOIN continguts c ON h.contingut = c.codi AND h.ensenyament = c.ensenyament
            INNER JOIN ausencias a ON a.documento = '$documento'
                AND '$fecha' BETWEEN a.fecha_inicio AND a.fecha_fin
            LEFT JOIN registro_guardias rg ON rg.fecha = '$fecha'
                AND rg.hora_inicio = h.hora_desde
                AND rg.hora_fin = h.hora_fins
                AND rg.docente_ausente = '$documento'
                AND rg.grupo = h.grup
            WHERE h.docent = '$documento'
            AND h.dia_setmana = '$dia_letra'
            AND (
                a.jornada_completa = 1
                OR
                (a.jornada_completa = 0 AND h.hora_desde >= a.hora_inicio AND h.hora_fins <= a.hora_fin)
            )
            ORDER BY h.hora_desde ASC";

    $resultado = mysqli_query($conn, $sql);

    if ($resultado) {
        $horario = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $horario[] = [
                'hora_inicio' => $row['hora_desde'],
                'hora_fin' => $row['hora_fins'],
                'grupo' => $row['grup'],
                'asignatura' => $row['asignatura'],
                'aula' => $row['aula'],
                'reservada' => !is_null($row['docente_guardia'])
            ];
        }
        $response['success'] = true;
        $response['horario'] = $horario;
    } else {
        throw new Exception(mysqli_error($conn));
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();