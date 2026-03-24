<?php
require_once('AuthModelo.php');

class AuthControlador {

    public static function verificarSesion($datos) {
        $token = $datos['session_token'] ?? session_id();
        return AuthModelo::verificarSesion($token);
    }

    public static function login($datos) {
        if (empty($datos['identificacion'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el campo obligatorio "identificacion"'];
        }

        // 👇 Capturamos los datos del dispositivo (si no vienen, se ponen como null)
        $android_id = $datos['android_id'] ?? null;
        $nombre_dispositivo = $datos['nombre_dispositivo'] ?? null;

        // Le pasamos los 3 datos al modelo
        return AuthModelo::login($datos['identificacion'], $android_id, $nombre_dispositivo);
    }

    public static function logout() {
        return AuthModelo::logout();
    }
}
?>