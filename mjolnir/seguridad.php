<?php
class Seguridad
{
    public static function limpiarTexto($texto)
    {
        $texto = trim($texto);
        $texto = strip_tags($texto);
        $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
        return $texto;
    }

    public static function normalizarEspacios($texto)
    {
        return preg_replace('/\s+/', ' ', $texto);
    }

    public static function soloLetras($texto)
    {
        return preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $texto);
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
        return strlen($texto) == $longitud;
    }

    public static function validarUsuario($datos)
    {
        // Sanitizar
        $nombre = self::normalizarEspacios(self::limpiarTexto($datos['nombre'] ?? ''));
        $correo = trim(self::limpiarTexto($datos['correo'] ?? ''));
        $identificacion = self::limpiarTexto($datos['identificacion'] ?? '');
        $celular = self::limpiarTexto($datos['celular'] ?? '');
        $tipoIdentificacion = strtoupper(self::limpiarTexto($datos['tipo_identificacion'] ?? ''));
        $cargo = strtoupper(self::limpiarTexto($datos['cargo'] ?? ''));

        // Validar vacíos
        if (
            empty($nombre) ||
            empty($correo) ||
            empty($identificacion) ||
            empty($celular) ||
            empty($tipoIdentificacion)
        ) {
            return ['valido' => false, 'mensaje' => 'Todos los campos son obligatorios'];
        }

        // Nombre
        if (!self::soloLetras($nombre)) {
            return ['valido' => false, 'mensaje' => 'El nombre solo puede contener letras y espacios'];
        }

        // Identificación
        if (!self::soloNumeros($identificacion)) {
            return ['valido' => false, 'mensaje' => 'La identificación solo debe contener números'];
        }

        // Celular (Colombia)
        if (!self::soloNumeros($celular) || !self::longitudExacta($celular, 10)) {
            return ['valido' => false, 'mensaje' => 'El celular debe tener 10 dígitos'];
        }

        // Correo
        if (!self::validarEmail($correo)) {
            return ['valido' => false, 'mensaje' => 'Correo inválido'];
        }

        // Tipo identificación
        $tiposPermitidos = ['CC', 'TI', 'CE', 'PASAPORTE'];

        if (!in_array($tipoIdentificacion, $tiposPermitidos)) {
            return ['valido' => false, 'mensaje' => 'Tipo de identificación inválido'];
        }

        // Cargo
        $cargosPermitidos = ['ADMINISTRADOR', 'INSTRUCTOR', 'APRENDIZ', 'VISITANTE'];

        // if (!in_array($cargo, $cargosPermitidos)) {
        //     return ['valido' => false, 'mensaje' => 'Cargo inválido'];
        // }

        // Retornar datos limpios
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