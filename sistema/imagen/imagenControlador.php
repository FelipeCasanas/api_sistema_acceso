<?php
require_once('ImagenModelo.php');

class ImagenControlador {
    public static function obtener($id_usuario) {
        if (!$id_usuario) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'No se recibió el id del usuario'
            ];
        }

        return ImagenModelo::obtener($id_usuario);
    }

    public static function subirImagen($id_usuario, $archivo) {
        if (!$id_usuario) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el id del usuario'];
        }

        if (!$archivo) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió ninguna imagen'];
        }

        return ImagenModelo::subirImagen($id_usuario, $archivo);
    }
}