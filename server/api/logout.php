<?php
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
$dni = $decodedToken->id;

// Registrar el cierre de sesión
$log = date('Y-m-d H:i:s') . " - " . $dni . " cerró sesión\n";
file_put_contents(__DIR__ . '/../registro_sesion.txt', $log, FILE_APPEND);

echo json_encode(['mensaje' => 'Logout correcto']);