<?php
require_once('AuthModelo.php');

class AuthControlador {

    public static function login($datos) {
        if (empty($datos['correo']) || empty($datos['contrasena'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Faltan campos obligatorios'];
        }

        return AuthModelo::login($datos['correo'], $datos['contrasena']);
    }

    public static function verificarSesion($datos) {
        $token = $datos['session_token'] ?? session_id();
        return AuthModelo::verificarSesion($token);
    }

    public static function logout() {
        return AuthModelo::logout();
    }
}
