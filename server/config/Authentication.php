<?php

namespace Config;

/**
 * =========================
 *  Authentication.php (Autenticación)
 * =========================
 * 
 * Clase de autenticación del sistema.
 * Gestiona:
 * - Validación de tokens JWT
 * - Generación de tokens
 * - Control de sesiones
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */

// Incluir el autoload de Composer para cargar las dependencias
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Authentication {
    private $decodedToken = null;
    private $jwtKey = "123.IESjc.zzz"; // Clave secreta para firmar los tokens

    public function getDecodedToken() {
        return $this->decodedToken;
    }

    /**
     * Valida que en la cabecera HTTP "Authorization" haya un token válido
     */
    public function validaToken() {
        $error = '';
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            $error = 'No se ha iniciado sesión en la aplicación';
        } else {
            $authorization = $headers['Authorization'];
            $parts = explode(' ', $authorization);
            $auth = $parts[1] ?? '';

            try {
                $decoded = JWT::decode($auth, new Key($this->jwtKey, 'HS256'));
                $this->decodedToken = $decoded;
            } catch (Exception $e) {
                $error = 'Token inválido o sesión caducada';
            }
        }

        return $error;
    }

    /**
     * Genera un token para la autenticación del usuario en la aplicación
     */
    public function generaToken($usuario, $rol = 'profe') {
        $tiempo = time();
        $token = JWT::encode(
            [
                'exp' => $tiempo + 3600, // Caduca en 1 hora
                'id' => $usuario,
                'rol' => $rol,
            ],
            $this->jwtKey,
            'HS256'
        );

        return $token;
    }
}
