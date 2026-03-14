<?php
require_once('../../mjolnir/conexion/conectar.php');

class AuthModelo {

    public static function verificarSesion($token) {

        if (session_id() !== $token) {
            session_id($token);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            http_response_code(401);
            return [
                'success' => false,
                'message' => 'Sesión no iniciada'
            ];
        }

        return [
            'success' => true,
            'message' => 'Sesión activa',
            'data' => $_SESSION['usuario']
        ];
    }

    public static function login($identificacion) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $conexion = obtenerConexion();

        $sql = "SELECT * FROM usuario WHERE identificacion = :identificacion LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':identificacion', $identificacion);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(401);
            return [
                'success' => false,
                'message' => 'identificacion incorrecta'
            ];
        }

        if (!$usuario['activo']) {
            http_response_code(403);
            return [
                'success' => false,
                'message' => 'Usuario inactivo'
            ];
        }

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'identificacion' => $usuario['identificacion'],
            'cargo' => $usuario['cargo']
        ];

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => $_SESSION['usuario'],
            'session_token' => session_id()
        ];
    }

    public static function logout() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        return [
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ];
    }
}