<?php
class Seguridad
{
    public static function limpiarTexto($texto)
    {
        $texto = trim($texto);
        $texto = strip_tags($texto);
        return $texto;
    }

    public static function normalizarEspacios($texto)
    {
        return preg_replace('/\s+/', ' ', $texto);
    }

    public static function soloLetras($texto)
    {
        return preg_match('/^[\p{L}\s]+$/u', $texto);
    }

    public static function soloNumeros($texto)
    {
        return preg_match('/^[0-9]+$/', $texto);
    }

    public static function validarEmail($correo)
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

    public static function longitudExacta($texto, $longitud)
    {
        return mb_strlen($texto, 'UTF-8') == $longitud;
    }

    public static function validarUsuario($datos)
    {
        $nombre = self::normalizarEspacios(self::limpiarTexto($datos['nombre'] ?? ''));
        $correo = trim(self::limpiarTexto($datos['correo'] ?? ''));
        $identificacion = self::limpiarTexto($datos['identificacion'] ?? '');
        $celular = self::limpiarTexto($datos['celular'] ?? '');
        $tipoIdentificacion = strtoupper(self::limpiarTexto($datos['tipo_identificacion'] ?? ''));
        $cargo = self::limpiarTexto($datos['cargo'] ?? '');

        if (
            empty($nombre) ||
            empty($correo) ||
            empty($identificacion) ||
            empty($celular) ||
            empty($tipoIdentificacion)
        ) {
            return ['valido' => false, 'mensaje' => 'Todos los campos son obligatorios'];
        }

        if (!self::soloLetras($nombre)) {
            return ['valido' => false, 'mensaje' => 'El nombre solo puede contener letras y espacios'];
        }

        if (!self::soloNumeros($identificacion)) {
            return ['valido' => false, 'mensaje' => 'La identificación solo debe contener números'];
        }

        if (!self::soloNumeros($celular) || !self::longitudExacta($celular, 10)) {
            return ['valido' => false, 'mensaje' => 'El celular debe tener 10 dígitos'];
        }

        if (!self::validarEmail($correo)) {
            return ['valido' => false, 'mensaje' => 'Correo inválido'];
        }

        $tiposPermitidos = ['CC', 'TI', 'CE', 'PASAPORTE'];

        if (!in_array($tipoIdentificacion, $tiposPermitidos)) {
            return ['valido' => false, 'mensaje' => 'Tipo de identificación inválido'];
        }

        return [
            'valido' => true,
            'datos' => [
                'nombre' => $nombre,
                'correo' => $correo,
                'identificacion' => $identificacion,
                'celular' => $celular,
                'tipo_identificacion' => $tipoIdentificacion,
                'cargo' => $cargo
            ]
        ];
    }

    public static function iniciarSesionSegura()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public static function crearSesionUsuario($usuario)
    {
        self::iniciarSesionSegura();
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario;
        $_SESSION['autenticado'] = true;
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function verificarSesion()
    {
        self::iniciarSesionSegura();

        if (
            !isset($_SESSION['autenticado']) ||
            $_SESSION['autenticado'] !== true
        ) {
            http_response_code(401);
            header("Location: http://192.168.18.6/etherium/login.html");
            exit;
        }

        self::controlarExpiracion();
    }

    public static function controlarExpiracion()
    {
        $tiempoMaximo = 1800;

        if (
            isset($_SESSION['LAST_ACTIVITY']) &&
            (time() - $_SESSION['LAST_ACTIVITY']) > $tiempoMaximo
        ) {
            session_unset();
            session_destroy();
            http_response_code(401);
            echo json_encode(['error' => 'Sesión expirada']);
            exit;
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function cerrarSesion()
    {
        self::iniciarSesionSegura();
        session_unset();
        session_destroy();
    }
}
?>