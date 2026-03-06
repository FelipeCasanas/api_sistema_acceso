<?php
require_once 'RegistroModelo.php';

class RegistroControlador {

    public static function obtener($medio_busqueda, $dato_busqueda) {
        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        return RegistroModelo::obtener($medio_busqueda, $dato_busqueda);
    }

    public static function obtenerTodos() {
        return RegistroModelo::obtenerTodos();
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
