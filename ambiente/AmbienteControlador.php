<?php
require_once('AmbienteModelo.php');

class AmbienteControlador {

    public static function obtener($filtros = [])
    {
        return AmbienteModelo::obtener($filtros);
    }
    
    public static function crear($datos)
    {
        if (empty($datos['id_creador']) || !isset($datos['bloque']) || !isset($datos['sitio'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Faltan datos obligatorios'];
        }

        return AmbienteModelo::crear($datos);
    }

    public static function modificar($datos)
    {
        if (empty($datos['id'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el ID del ambiente'];
        }

        return AmbienteModelo::modificar($datos);
    }

    public static function eliminar($id)
    {
        if (!$id) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el ID del ambiente'];
        }

        return AmbienteModelo::eliminar($id);
    }
}
