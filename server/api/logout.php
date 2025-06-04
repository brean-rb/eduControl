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

try {
    $auth = new Authentication();
    $error = $auth->validaToken();
    if ($error) {
        throw new Exception($error);
    }

    $decodedToken = $auth->getDecodedToken();
    $dni = $decodedToken->id;

    // Registrar el cierre de sesión - EXACTAMENTE igual que en login
    $log = date('Y-m-d H:i:s') . " - " . $dni . " cerró sesión\n";
    $file_path = __DIR__ . '/../registro_sesion.txt';
    file_put_contents($file_path, $log, FILE_APPEND);

    echo json_encode(['success' => true, 'mensaje' => 'Logout correcto']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}