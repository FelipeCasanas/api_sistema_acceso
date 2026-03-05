<?php
require_once('../mjolnir/conexion/conectar.php');
require_once('../mjolnir/conexion/gestor_consultas.php');

class PermisoModelo
{

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta)
    {
        if (filter_var($coincidencia_exacta, FILTER_VALIDATE_BOOLEAN) === true) {
            list($sql, $parametros) = construirQuery(
                'permiso',
                [],
                'SELECT',
                [$medio_busqueda => $dato_busqueda]
            );

        } else {
            list($sql, $parametros) = construirQuery(
                'permiso',
                [],
                'SELECT',
                [$medio_busqueda => ['LIKE', "%$dato_busqueda%"]]
            );
        }

        $stmt = ejecutarQuery($sql, $parametros);
        $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($permisos)) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'permiso no encontrado'
            ];
        }

        // Eliminar contraseña de cada resultado
        foreach ($permisos as &$permiso) {
            unset($permiso['contrasena']);
        }

        return [
            'success' => true,
            'message' => 'Información del permiso obtenida',
            'data' => $permisos
        ];
    }

    public static function crear($datos)
    {
        $camposRequeridos = [
            'id_permiso',
            'tipo_permiso',
            'descripcion',
            'comprobante',
            'estado'
        ];

        foreach ($camposRequeridos as $campo) {
            if (empty($datos[$campo])) {
                http_response_code(400);
                return ['success' => false, 'message' => "Falta el campo requerido: $campo"];
            }
        }

        $condicion = $datos['id'];

        list($sql, $parametros) = construirQuery('permiso', [], 'SELECT', ['id' => $condicion]);
        $stmt = ejecutarQuery($sql, $parametros);
        $permisos = procesarResultado($stmt, 'SELECT');

        if (!empty($permisos)) {
            http_response_code(409);
            return ['success' => false, 'message' => 'Ya existe un permiso con esa identificacion'];
        }

        list($sql, $parametros) = construirQuery('permiso', $datos, 'INSERT');
        $respuesta = ejecutarQuery($sql, $parametros);

        if (!$respuesta) {
            http_response_code(500);
            return ['success' => false, 'message' => 'Error al crear el permiso'];
        }

        return ['success' => true, 'message' => 'permiso creado exitosamente'];
    }

    public static function modificar($id, $datos)
    {
        // Verifica existencia
        list($sql, $parametros) = construirQuery('permiso', [], 'SELECT', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);
        $permisos = procesarResultado($stmt, 'SELECT');

        if (empty($permisos)) {
            http_response_code(404);
            return ['success' => false, 'message' => 'El permiso no existe'];
        }

        // Actualizar
        list($sql, $parametros) = construirQuery('permiso', $$datos, 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $parametros);

        return [
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0 ? 'permiso actualizado correctamente' : 'No se realizaron cambios en el permiso'
        ];
    }
}