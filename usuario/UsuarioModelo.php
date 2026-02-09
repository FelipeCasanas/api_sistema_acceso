<?php
require_once ('../../mjolnir/conexion/conectar.php');
require_once ('../../mjolnir/conexion/gestor_consultas.php');

class UsuarioModelo {

    public static function crear($datos) {
        $identificacion = $datos['identificacion'];

        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['identificacion' => $identificacion]);
        $stmt = ejecutarQuery($sql, $parametros);
        $usuarios = procesarResultado($stmt, 'SELECT');

        if (!empty($usuarios)) {
            http_response_code(409);
            return ['success' => false, 'message' => 'Ya existe un usuario con esa identificación'];
        }

        list($sql, $parametros) = construirQuery('usuario', $datos, 'INSERT');
        $respuesta = ejecutarQuery($sql, $parametros);

        if (!$respuesta) {
            http_response_code(500);
            return ['success' => false, 'message' => 'Error al crear el usuario'];
        }

        return ['success' => true, 'message' => 'Usuario creado exitosamente'];
    }

    public static function modificar($id, $datos) {
        $camposValidos = [
            'id',
            'tipo_identificacion',
            'identificacion',
            'rol',
            'nombre',
            'apellido',
            'correo',
            'celular',
            'contrasena'
        ];

        $datosFiltrados = [];

        foreach ($camposValidos as $campo) {
            if (!empty($datos[$campo])) {
                $datosFiltrados[$campo] = $datos[$campo];
            }
        }

        if (empty($datosFiltrados)) {
            http_response_code(400);
            return ['success' => false, 'message' => 'No se enviaron datos para actualizar'];
        }

        // Verifica existencia
        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);
        $usuarios = procesarResultado($stmt, 'SELECT');

        if (empty($usuarios)) {
            http_response_code(404);
            return ['success' => false, 'message' => 'El usuario no existe'];
        }

        // Actualizar
        list($sql, $parametros) = construirQuery('usuario', $datosFiltrados, 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);

        return [
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0 ? 'Usuario actualizado correctamente' : 'No se realizaron cambios en el usuario'
        ];
    }

    public static function eliminar($id) {
        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);
        $usuarios = procesarResultado($stmt, 'SELECT');

        if (empty($usuarios)) {
            http_response_code(404);
            return ['success' => false, 'message' => 'El usuario no existe'];
        }

        list($sql, $parametros) = construirQuery('usuario', ['activo' => '0'], 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);

        return [
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0 ? 'Usuario eliminado correctamente' : 'No se realizaron cambios'
        ];
    }

    public static function obtenerTodos() {
        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['activo' => '1']);
        $stmt = ejecutarQuery($sql, $parametros);

        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            unset($row['contrasena']);
            $usuarios[] = $row;
        }

        return [
            'success' => true,
            'message' => 'Usuarios activos obtenidos correctamente',
            'data' => $usuarios
        ];
    }

    public static function obtenerUno($id) {
        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        unset($usuario['contrasena']);

        return [
            'success' => true,
            'message' => 'Información del usuario obtenida',
            'data' => $usuario
        ];
    }
}
