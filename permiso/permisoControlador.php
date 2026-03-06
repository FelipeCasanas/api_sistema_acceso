<?php
require_once('PermisoModelo.php');

class PermisoControlador {

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta) {
        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        return PermisoModelo::obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta);
    }

    public static function obtenerTodos() {
        return PermisoModelo::obtenerTodos();
    }
    
    public static function crear($datos) {
        if (empty($datos['id_usuario']) && empty($datos['tipo_permiso'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el campo "id_usuario" o "tipo_permiso"'];
        }

        return PermisoModelo::crear($datos);
    }

    public static function modificar($id, $datos) {
        if (empty($datos['id'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del usuario'];
        }

        if (!empty($datos['estado'])) {
            return json_encode(['success' => false, 'message' => 'No se puede modificar el estado del permiso']);
        }

        return PermisoModelo::modificar($id, $datos);
    }
}