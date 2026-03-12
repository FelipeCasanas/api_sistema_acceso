<?php
require_once('ImagenModelo.php');

class ImagenControlador {

    public static function obtener($tipo, $id) {

        if (!$tipo || !$id) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        return ImagenModelo::obtener($tipo, $id);
    }

    public static function subirImagen($tipo, $id, $archivo) {

        if (!$tipo || !$id) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Datos incompletos'
            ];
        }

        if (!$archivo) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'No se recibió ninguna imagen'
            ];
        }

        return ImagenModelo::subirImagen($tipo, $id, $archivo);
    }
}