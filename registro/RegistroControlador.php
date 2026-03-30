<?php
require_once 'RegistroModelo.php';
require_once('../mjolnir/seguridad.php');

class RegistroControlador {

    public static function obtener($medio_busqueda, $dato_busqueda) {
        if (!$dato_busqueda) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se recibió el medio de busqueda'];
        }

        // Sanitizar búsqueda
        $dato_busqueda = Seguridad::limpiarTexto($dato_busqueda);

        return RegistroModelo::obtener($medio_busqueda, $dato_busqueda);
    }

    public static function obtenerTodos() {
        return RegistroModelo::obtenerTodos();
    }
    
    public static function registrar($datos) {

        // Sanitizar
        $idUsuario = Seguridad::limpiarTexto($datos['id_usuario'] ?? '');
        $idAmbiente = Seguridad::limpiarTexto($datos['id_ambiente'] ?? '');

        // Validar vacíos
        if (empty($idUsuario) || empty($idAmbiente)) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Faltan campos: id_usuario o id_ambiente'
            ];
        }

        // Validar que sean números
        if (!Seguridad::soloNumeros($idUsuario) || !Seguridad::soloNumeros($idAmbiente)) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Los IDs deben ser numéricos'
            ];
        }

        // Usar datos limpios
        $datos['id_usuario'] = $idUsuario;
        $datos['id_ambiente'] = $idAmbiente;

        return RegistroModelo::registrar($datos);
    }
}