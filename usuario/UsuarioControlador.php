<?php
require_once('UsuarioModelo.php');
require_once('../mjolnir/seguridad.php');

class UsuarioControlador {

    public static function obtenerTotal() {
        return UsuarioModelo::obtenerTotal();
    }

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta) {

        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        $dato_busqueda = Seguridad::limpiarTexto($dato_busqueda);

        return UsuarioModelo::obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta);
    }
    
    public static function obtenerTodos() {
        return UsuarioModelo::obtenerTodos();
    }
    
    public static function crear($datos) {

        $validacion = Seguridad::validarUsuario($datos);

        if (!$validacion['valido']) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => $validacion['mensaje']
            ];
        }

        return UsuarioModelo::crear($validacion['datos']);
    }

    public static function cargaMasiva($datos)
    {
        if (empty($datos) || !is_array($datos)) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Se esperaba un arreglo de usuarios'
            ];
        }

        return UsuarioModelo::cargaMasiva($datos);
    }

    public static function modificar($datos) {

        if (empty($datos['id'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del usuario'];
        }

        $validacion = Seguridad::validarUsuario($datos);

        if (!$validacion['valido']) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => $validacion['mensaje']
            ];
        }

        return UsuarioModelo::modificar($datos['id'], $validacion['datos']);
    }

    public static function eliminar($id_usuario) {
        if (!$id_usuario) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del usuario'];
        }

        return UsuarioModelo::eliminar($id_usuario);
    }
}