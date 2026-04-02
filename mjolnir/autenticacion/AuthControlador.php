<?php
require_once('AuthModelo.php');
require_once('../seguridad.php');

class AuthControlador {

    public static function verificarSesion($datos) {
        return AuthModelo::verificarSesion();
    }

    public static function login($datos) {

        if (empty($datos['identificacion'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el campo obligatorio "identificacion"'];
        }

        $identificacion = Seguridad::limpiarTexto($datos['identificacion']);

        if (!Seguridad::soloNumeros($identificacion)) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Identificación inválida'];
        }

        $android_id = isset($datos['android_id']) 
            ? Seguridad::limpiarTexto($datos['android_id']) 
            : null;

        $nombre_dispositivo = isset($datos['nombre_dispositivo']) 
            ? Seguridad::limpiarTexto($datos['nombre_dispositivo']) 
            : null;

        return AuthModelo::login($identificacion, $android_id, $nombre_dispositivo);
    }

    public static function logout() {
        return AuthModelo::logout();
    }
}