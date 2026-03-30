<?php
require_once('AmbienteModelo.php');
require_once('../mjolnir/seguridad.php');

class AmbienteControlador {

    public static function obtenerTotal() {
        return AmbienteModelo::obtenerTotal();
    }
    
    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta)
    {
        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        $dato_busqueda = Seguridad::limpiarTexto($dato_busqueda);

        return AmbienteModelo::obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta);
    }

    public static function obtenerTodos() {
        return AmbienteModelo::obtenerTodos();
    }
    
    public static function crear($datos)
    {
        if (empty($datos['id_creador']) || !isset($datos['bloque']) || !isset($datos['sitio'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Faltan datos obligatorios'];
        }

        // Sanitizar
        foreach ($datos as $key => $value) {
            $datos[$key] = Seguridad::limpiarTexto($value);
        }

        return AmbienteModelo::crear($datos);
    }

    public static function modificar($datos)
    {
        if (empty($datos['id'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Falta el ID del ambiente'];
        }

        foreach ($datos as $key => $value) {
            $datos[$key] = Seguridad::limpiarTexto($value);
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