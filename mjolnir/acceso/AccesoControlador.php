<?php
require_once('AccesoModelo.php');

class AccesoControlador {

    public static function obtener() {
        return AccesoModelo::obtener();
    }

    public static function registrar($datos) {
        if (empty($datos['android_id']) || empty($datos['medio_acceso'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Faltan datos obligatorios para registrar el acceso'];
        }

        return AccesoModelo::registrar($datos);
    }
}
