<?php
require_once('UsuarioModelo.php');

class UsuarioControlador {

    public static function obtenerTotal() {
        return UsuarioModelo::obtenerTotal();
    }

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta) {
        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        return UsuarioModelo::obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta);
    }
    
    public static function obtenerTodos() {
        return UsuarioModelo::obtenerTodos();
    }
    
    public static function crear($datos) {
        if (empty($datos['identificacion'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el campo "identificacion"'];
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
}