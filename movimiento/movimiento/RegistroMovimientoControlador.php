<?php
require_once 'RegistroRegistroModelo.php';

class RegistroRegistroControlador {

    public static function registrar($datos) {
        $campos = ['registrado_por', 'id_elemento', 'tipo_registro', 'descripcion'];
        foreach ($campos as $campo) {
            if (empty($datos[$campo])) {
                http_response_code(400);
                return ['success' => false, 'message' => "Falta el campo: $campo"];
            }
        }

        return RegistroRegistroModelo::registrar($datos);
    }

    public static function obtener($filtros = []) {
        return RegistroRegistroModelo::obtener($filtros);
    }
}
