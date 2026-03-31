<?php
header('Content-Type: application/json');

class Seguridad
{
    public static function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'httponly' => true,
                'secure' => false,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            session_unset();
            session_destroy();
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function proteger()
    {
        self::iniciarSesion();

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
    }

    public static function crearSesion($usuario)
    {
        self::iniciarSesion();
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario'] = $usuario['nombre'] ?? '';
    }

    public static function cerrarSesion()
    {
        self::iniciarSesion();
        session_unset();
        session_destroy();
    }

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
}
?>