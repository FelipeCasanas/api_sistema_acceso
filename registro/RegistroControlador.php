<?php
require_once 'RegistroModelo.php';

class RegistroControlador {

    public static function obtener($filtros = []) {
        return RegistroModelo::obtener($filtros);
    }

    public static function registrar($datos) {
        $campos = ['registrado_por', 'id_elemento', 'tipo_registro', 'descripcion'];
        foreach ($campos as $campo) {
            if (empty($datos[$campo])) {
                http_response_code(400);
                return ['success' => false, 'message' => "Falta el campo: $campo"];
            }
        }

        return RegistroModelo::registrar($datos);
    }
}
