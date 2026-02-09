<?php
require_once 'TipoRegistroModelo.php';

class TipoRegistroControlador {

    public static function crear($datos) {
        if (empty($datos['nombre'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'El nombre es obligatorio'];
        }

        return TipoRegistroModelo::crear($datos['nombre']);
    }

    public static function modificar($datos) {
        if (empty($datos['id']) || empty($datos['nombre'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Se requiere el ID y el nuevo nombre'];
        }

        return TipoRegistroModelo::modificar($datos['id'], $datos['nombre']);
    }

    public static function eliminar($id) {
        if (!$id) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el ID'];
        }

        return TipoRegistroModelo::eliminar($id);
    }

    public static function obtener() {
        return TipoRegistroModelo::obtener();
    }
}
