<?php
// Desactivar la salida de errores de PHP
error_reporting(0);
ini_set('display_errors', 0);

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Authentication.php';

use Config\Authentication;

$auth = new Authentication();
$error = $auth->validaToken();
if ($error) {
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$decodedToken = $auth->getDecodedToken();

// Asegurar que siempre devolvemos JSON
header('Content-Type: application/json');

try {

    // Obtener el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Consultar profesores ausentes
        $fecha_actual = date('Y-m-d');
        $sql = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                FROM ausencias a 
                JOIN docent d ON a.documento = d.document 
                WHERE '$fecha_actual' BETWEEN a.fecha_inicio AND a.fecha_fin";
                
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception('Error al consultar ausencias: ' . mysqli_error($conn));
        }
        
        $profesores_ausentes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $profesores_ausentes[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'profesores_ausentes' => $profesores_ausentes
        ]);
    } 
    else if ($method === 'POST') {
        if (isset($_POST['documento'])) {
            // Consultar horario del profesor
            $documento = mysqli_real_escape_string($conn, $_POST['documento']);
            $fecha_actual = date('Y-m-d');
            
            // Primero obtenemos los datos de la ausencia
            $sql_ausencia = "SELECT hora_inicio, hora_fin 
                            FROM ausencias 
                            WHERE documento = '$documento' 
                            AND '$fecha_actual' BETWEEN fecha_inicio AND fecha_fin";
            
            $result_ausencia = mysqli_query($conn, $sql_ausencia);
            
            if (!$result_ausencia) {
                throw new Exception('Error al consultar ausencia: ' . mysqli_error($conn));
            }
            
            $ausencia = mysqli_fetch_assoc($result_ausencia);
            
            if (!$ausencia) {
                throw new Exception('No se encontró la ausencia para este profesor');
            }
            
            // Obtener el día de la semana en letra (L, M, X, J, V)
            $dias_letras = ['', 'L', 'M', 'X', 'J', 'V', 'S'];
            $dia_semana = date('N', strtotime($fecha_actual)); // 1 (lunes) a 7 (domingo)
            $dia_letra = $dias_letras[$dia_semana];

            $sql = "SELECT h.hora_desde, h.hora_fins, h.grup, h.aula, c.nom_cas as asignatura,
                CASE WHEN EXISTS (
                    SELECT 1 FROM registro_guardias rg 
                    WHERE rg.fecha = '$fecha_actual' 
                    AND rg.hora_inicio = h.hora_desde 
                    AND rg.hora_fin = h.hora_fins 
                    AND rg.grupo = h.grup
                    AND rg.docente_ausente = '$documento'
                ) THEN 1 ELSE 0 END as guardia_reservada
                FROM horari_grup h
                LEFT JOIN continguts c ON h.contingut = c.codi
                WHERE h.docent = '$documento'
                AND h.dia_setmana = '$dia_letra'
                AND h.hora_desde >= '{$ausencia['hora_inicio']}'
                AND h.hora_fins <= '{$ausencia['hora_fin']}'
                ORDER BY h.hora_desde, h.grup";
                    
            $result = mysqli_query($conn, $sql);
            
            if (!$result) {
                throw new Exception('Error al consultar horario: ' . mysqli_error($conn));
            }
            
            $horarios = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $horarios[] = $row;
            }
            // Depuración: volcar el resultado en el log de errores
            // error_log('HORARIOS GUARDIAS: ' . print_r($horarios, true));
            
            echo json_encode([
                'success' => true,
                'horarios' => $horarios
            ]);
        } 
        else if (isset($_POST['docente_ausente'])) {
            // Registrar guardia
            $docente_ausente = mysqli_real_escape_string($conn, $_POST['docente_ausente']);
            $docente_guardia = mysqli_real_escape_string($conn, $_POST['docente_guardia']);
            $fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
            $hora_inicio = mysqli_real_escape_string($conn, $_POST['hora_inicio']);
            $hora_fin = mysqli_real_escape_string($conn, $_POST['hora_fin']);
            $grupo = mysqli_real_escape_string($conn, $_POST['grupo']);
            $aula = mysqli_real_escape_string($conn, $_POST['aula']);
            $contenido = mysqli_real_escape_string($conn, $_POST['contenido']);
            
            $sql = "INSERT INTO registro_guardias (docente_ausente, docente_guardia, fecha, hora_inicio, hora_fin, grupo, aula, contenido) 
                    VALUES ('$docente_ausente', '$docente_guardia', '$fecha', '$hora_inicio', '$hora_fin', '$grupo', '$aula', '$contenido')";
                    
            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Error al registrar la guardia: ' . mysqli_error($conn));
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Guardia registrada correctamente'
            ]);
        }
    } else {
        throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}