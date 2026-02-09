<?php
require_once('ActualizacionAmbienteModelo.php');

class ActualizacionAmbienteControlador {

    public static function registrar($datos) {
        $campos = ['id_usuario', 'id_ambiente', 'descripcion'];
        foreach ($campos as $campo) {
            if (empty($datos[$campo])) {
                http_response_code(400);
                return ['success' => false, 'message' => "Falta el campo: $campo"];
            }
        }

        return ActualizacionAmbienteModelo::registrar($datos);
    }

    public static function obtener($filtros = []) {
        return ActualizacionAmbienteModelo::obtener($filtros);
    }
}
