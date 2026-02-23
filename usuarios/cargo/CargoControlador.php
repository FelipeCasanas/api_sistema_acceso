<?php
require_once 'CargoModelo.php';

class CargoControlador {

    public static function crear($datos) {
        if (empty($datos['nombre'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'El nombre del cargo es obligatorio'];
        }

        return CargoModelo::crear($datos['nombre']);
    }

    public static function modificar($datos) {
        if (empty($datos['id']) || empty($datos['nombre'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Se requiere el ID y el nuevo nombre del cargo'];
        }

        return CargoModelo::modificar($datos['id'], $datos['nombre']);
    }

    public static function eliminar($id) {
        if (!$id) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el ID del cargo a eliminar'];
        }

        return CargoModelo::eliminar($id);
    }

    public static function obtener() {
        return CargoModelo::obtener();
    }
}
