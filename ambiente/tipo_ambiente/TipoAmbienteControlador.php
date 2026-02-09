<?php
require_once ('TipoAmbienteModelo.php');

class TipoAmbienteControlador {

    public static function crear($datos) {
        if (empty($datos['nombre'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'El nombre es obligatorio'];
        }

        return TipoAmbienteModelo::crear($datos['nombre']);
    }

    public static function modificar($datos) {
        if (empty($datos['id']) || empty($datos['nombre'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Se requiere el ID y el nuevo nombre'];
        }

        return TipoAmbienteModelo::modificar($datos['id'], $datos['nombre']);
    }

    public static function eliminar($id) {
        if (!$id) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el ID'];
        }

        return TipoAmbienteModelo::eliminar($id);
    }

    public static function obtener() {
        return TipoAmbienteModelo::obtener();
    }
}
