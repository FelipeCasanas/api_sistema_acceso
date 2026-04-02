<?php
require_once('PermisoModelo.php');

class PermisoControlador {

    public static function obtenerTotal() {
        return PermisoModelo::obtenerTotal();
    }
    
    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta) {

        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        $dato_busqueda = Seguridad::limpiarTexto($dato_busqueda);

        return PermisoModelo::obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta);
    }

    public static function obtenerTodos() {
        return PermisoModelo::obtenerTodos();
    }
    
    public static function crear($datos) {

        $camposRequeridos = [
            'id_usuario',
            'tipo_permiso',
            'descripcion',
            'comprobante',
            'estado'
        ];

        foreach ($camposRequeridos as $campo) {
            if (empty($datos[$campo])) {
                http_response_code(400);
                return [
                    'success' => false,
                    'message' => "Falta el campo requerido: $campo"
                ];
            }
        }

        // Sanitizar
        foreach ($datos as $key => $value) {
            $datos[$key] = Seguridad::limpiarTexto($value);
        }

        return PermisoModelo::crear($datos);
    }

    public static function modificar($datos) {

        if (empty($datos['id'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el ID del permiso'];
        }

        if (!isset($datos['estado'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se envió el estado'];
        }

        $datos['estado'] = Seguridad::limpiarTexto($datos['estado']);

        return PermisoModelo::modificar($datos);
    }
}