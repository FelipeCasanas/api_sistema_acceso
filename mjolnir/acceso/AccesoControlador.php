<?php
require_once('AccesoModelo.php');

class AccesoControlador {

    public static function obtener($data) {
        if(empty($data['id_usuario']) || empty($data['android_id']) || empty($data['medio_acceso'])) {
            http_response_code(400);
             return ['success' => false, 'message' => 'Faltan datos obligatorios para obtener los accesos'];
        }

        return AccesoModelo::obtener($data);
    }

    public static function registrar($datos) {
        if (empty($datos['id_usuario']) || empty($datos['android_id']) || empty($datos['medio_acceso'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Faltan datos obligatorios para registrar el acceso'];
        }

        return AccesoModelo::registrar($datos);
    }
}
