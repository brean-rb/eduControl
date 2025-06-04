<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Authentication.php';
use Config\Authentication;

header('Content-Type: application/json');

/**
 * =========================
 *  horario_profesor.php (Horario de Profesor)
 * =========================
 * 
 * Endpoint de gestión de horarios de profesores.
 * Gestiona:
 * - Consulta de horarios
 * - Actualización de horarios
 * - Gestión de asignaturas
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */

// Validar token JWT
$auth = new Authentication();
$error = $auth->validaToken();
if ($error) {
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$decodedToken = $auth->getDecodedToken();

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y validar parámetros
$documento = isset($_POST['documento']) ? $_POST['documento'] : '';
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';

if (empty($documento) || empty($fecha)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

try {
    // Compatibilidad con ambos esquemas de base de datos
    if (isset($conn)) {
        // Esquema nuevo (MySQLi, tabla horarios)
        $dia_semana = date('w', strtotime($fecha));
        $query = "SELECT h.hora_inicio, h.hora_fin, h.asignatura, h.grupo, h.aula 
                    FROM horarios h 
                    WHERE h.documento = '$documento' AND h.dia_semana = '$dia_semana' 
                    ORDER BY h.hora_inicio";
        $result = mysqli_query($conn, $query);
        $horario = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $horario[] = $row;
        }
    } else if (isset($conexion)) {
        // Esquema antiguo (MySQL, tabla horari_grup)
        $dia_mapping = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V'];
        $dia_numero = date('N', strtotime($fecha));
        $dia_letra = $dia_mapping[$dia_numero];
        $sql = "SELECT h.hora_desde AS hora_inicio, h.hora_fins AS hora_fin, h.grup AS grupo, 
                        COALESCE(c.nom_val, h.contingut) as asignatura, h.aula
                FROM horari_grup h
                LEFT JOIN continguts c ON h.contingut = c.codi AND h.ensenyament = c.ensenyament 
                WHERE h.docent = '" . mysqli_real_escape_string($conexion, $documento) . "' 
                AND h.dia_setmana = '" . mysqli_real_escape_string($conexion, $dia_letra) . "'
                ORDER BY h.hora_desde ASC";
        $resultado = mysqli_query($conexion, $sql);
        $horario = [];
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $horario[] = $row;
            }
        } else {
            throw new Exception(mysqli_error($conexion));
        }
    } else {
        throw new Exception('No se encontró conexión a la base de datos.');
    }

    echo json_encode([
        'success' => true,
        'horario' => $horario
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar el horario: ' . $e->getMessage()
    ]);
}