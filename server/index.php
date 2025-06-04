<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Asegurar que siempre devolvemos JSON
header('Content-Type: application/json');

try {
    // Obtener la ruta solicitada
    $ruta = $_GET['ruta'] ?? '';

    // Router simple
    switch($ruta) {
        case 'login':
            require_once __DIR__ . '/api/login.php';
            break;
            
        case 'logout':
            require_once __DIR__ . '/api/logout.php';
            break;
            
        case 'horarios':
            require_once __DIR__ . '/api/horarios.php';
            break;
            
        case 'jornada':
            require_once __DIR__ . '/api/jornada.php';
            break;
            
        case 'asistencia':
        case 'asistencia/consultar':
        case 'asistencia/registrar':
            require_once __DIR__ . '/api/asistencia.php';
            break;
            
        case 'guardias':
        case 'consultar_guardias':
            require_once __DIR__ . '/api/consultar_guardias.php';
            break;
            
        case 'ausencias':
            require_once __DIR__ . '/api/ausencias.php';
            break;
            
        case 'informe_ausencias':
            require_once __DIR__ . '/api/informe_ausencias.php';
            break;
            
        case 'docentes':
            require_once __DIR__ . '/api/docentes.php';
            break;
            
        case 'horario_ausente':
            require_once __DIR__ . '/api/horario_ausente.php';
            break;
            
        case 'registrar_guardia':
            require_once __DIR__ . '/api/registrar_guardia.php';
            break;
            
        case 'obtener_guardias_realizadas':
            require_once __DIR__ . '/api/obtener_guardias_realizadas.php';
            break;
            
        default:
            throw new Exception('Ruta no encontrada');
    }
} catch (Exception $e) {
    error_log("Error en index.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 