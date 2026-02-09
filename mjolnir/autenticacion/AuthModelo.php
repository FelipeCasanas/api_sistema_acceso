<?php
require_once('../../mjolnir/conexion/conectar.php');
require_once('../../mjolnir/conexion/gestor_consultas.php');

class AuthModelo {

    public static function login($correo, $contrasena) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['correo' => $correo]);
        $stmt = ejecutarQuery($sql, $parametros);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || !password_verify($contrasena, $usuario['contrasena'])) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Correo o contraseña incorrectos'];
        }

        if (!$usuario['activo']) {
            http_response_code(403);
            return ['success' => false, 'message' => 'Usuario inactivo'];
        }

        // Crear sesión
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol'] ?? null
        ];

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => $_SESSION['usuario'],
            'session_token' => session_id()
        ];
    }


    public static function verificarSesion($token) {
        if (session_id() !== $token) session_id($token);
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['usuario'])) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Sesión no iniciada'];
        }

        return [
            'success' => true,
            'message' => 'Sesión activa',
            'data' => $_SESSION['usuario']
        ];
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();

        return ['success' => true, 'message' => 'Sesión cerrada correctamente'];
    }
}
