<?php
require_once('../conexion/conectar.php');
require_once('../seguridad.php');

class AuthModelo
{

    public static function verificarSesion()
    {

        Seguridad::iniciarSesion();

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

    public static function login($identificacion, $android_id, $nombre_dispositivo)
    {

        Seguridad::iniciarSesion();

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
                'message' => 'Identificación incorrecta'
            ];
        }

        if (!$usuario['activo']) {
            http_response_code(403);
            return [
                'success' => false,
                'message' => 'Usuario inactivo'
            ];
        }

        if ($android_id) {

            if (empty($usuario['android_id'])) {

                $sql_update = "UPDATE usuario SET android_id = :android_id WHERE id = :id";
                $stmt_update = $conexion->prepare($sql_update);
                $stmt_update->bindValue(':android_id', $android_id);
                $stmt_update->bindValue(':id', $usuario['id']);
                $stmt_update->execute();

            } else {

                if ($usuario['android_id'] !== $android_id) {
                    http_response_code(403);
                    return [
                        'success' => false,
                        'message' => 'Acceso denegado. Esta cuenta está vinculada a otro dispositivo.'
                    ];
                }
            }
        }

        Seguridad::crearSesion($usuario);

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'usuario' => $_SESSION['usuario'],
                'cargo' => $usuario['cargo']
            ],
            'session_token' => session_id()
        ];
    }

    public static function logout()
    {

        Seguridad::cerrarSesion();

        return [
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ];
    }
}