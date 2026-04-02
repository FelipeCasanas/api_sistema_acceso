<?php
require_once('../conexion/conectar.php');
require_once('../seguridad.php');

class AuthModelo {

    public static function verificarSesion() {

        Seguridad::iniciarSesionSegura();

        Seguridad::controlarExpiracion();

        if (
            empty($_SESSION['usuario']) ||
            !isset($_SESSION['autenticado']) ||
            $_SESSION['autenticado'] !== true
        ) {
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

    public static function login($identificacion, $android_id, $nombre_dispositivo) {

        Seguridad::iniciarSesionSegura();

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

        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'identificacion' => $usuario['identificacion'],
            'cargo' => $usuario['cargo']
        ];

        $_SESSION['autenticado'] = true;
        $_SESSION['LAST_ACTIVITY'] = time();

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => $_SESSION['usuario']
        ];
    }

    public static function logout() {

        Seguridad::iniciarSesionSegura();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        return [
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ];
    }
}