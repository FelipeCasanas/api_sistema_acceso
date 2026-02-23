<?php
require_once('UsuarioModelo.php');

class UsuarioControlador {

    public static function crear($datos) {
        if (empty($datos['celular'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el campo "celular"'];
        }

        return UsuarioModelo::crear($datos);
    }

    public static function modificar($datos) {
        if (empty($datos['id'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del usuario'];
        }

        return UsuarioModelo::modificar($datos['id'], $datos);
    }

    public static function eliminar($id_usuario) {
        if (!$id_usuario) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del usuario'];
        }

        return UsuarioModelo::eliminar($id_usuario);
    }

    public static function obtenerTodos() {
        return UsuarioModelo::obtenerTodos();
    }

    public static function obtenerUno($id_usuario) {
        if (!$id_usuario) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del usuario'];
        }

        return UsuarioModelo::obtenerUno($id_usuario);
    }
}
