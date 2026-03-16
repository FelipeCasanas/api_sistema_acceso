<?php
require_once('../mjolnir/conexion/conectar.php');

class PermisoModelo
{

    public static function obtenerTotal()
    {
        $sql = "SELECT COUNT(*) AS total FROM permiso";

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'Total de permisos obtenido correctamente',
            'data' => $resultado['total']
        ];
    }

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta)
    {
        $conexion = obtenerConexion();

        if (filter_var($coincidencia_exacta, FILTER_VALIDATE_BOOLEAN)) {

            $sql = "SELECT 
                        p.id,
                        u.nombre AS id_usuario,
                        p.tipo_permiso,
                        p.descripcion,
                        p.comprobante,
                        p.estado,
                        p.fecha_estado
                    FROM permiso p
                    JOIN usuario u ON p.id_usuario = u.id
                    WHERE p.$medio_busqueda = :dato";

            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':dato', $dato_busqueda);

        } else {

            $sql = "SELECT 
                        p.id,
                        u.nombre AS id_usuario,
                        p.tipo_permiso,
                        p.descripcion,
                        p.comprobante,
                        p.estado,
                        p.fecha_estado
                    FROM permiso p
                    JOIN usuario u ON p.id_usuario = u.id
                    WHERE p.$medio_busqueda LIKE :dato";

            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':dato', "%$dato_busqueda%");
        }

        $stmt->execute();
        $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($permisos)) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'permiso no encontrado'
            ];
        }

        return [
            'success' => true,
            'message' => 'Información del permiso obtenida',
            'data' => $permisos
        ];
    }

    public static function obtenerTodos()
    {
        $conexion = obtenerConexion();

        $sql = "SELECT 
                    p.id,
                    u.nombre AS id_usuario,
                    p.tipo_permiso,
                    p.descripcion,
                    p.comprobante,
                    p.estado,
                    p.fecha_estado
                FROM permiso p
                JOIN usuario u ON p.id_usuario = u.id";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();

        $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'permisos activos obtenidos correctamente',
            'data' => $permisos
        ];
    }

    public static function crear($datos)
    {
        $camposRequeridos = [
            'id_usuario',
            'tipo_permiso',
            'descripcion',
            'comprobante',
            'estado'
        ];

        foreach ($camposRequeridos as $campo) {
            if (empty($datos[$campo])) {
                http_response_code(400);
                return [
                    'success' => false,
                    'message' => "Falta el campo requerido: $campo"
                ];
            }
        }

        $conexion = obtenerConexion();

        if (!empty($datos['id'])) {

            $sql = "SELECT id FROM permiso WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id', $datos['id']);
            $stmt->execute();

            if ($stmt->fetch()) {
                http_response_code(409);
                return [
                    'success' => false,
                    'message' => 'Ya existe un permiso con esa identificacion'
                ];
            }
        }

        $sql = "INSERT INTO permiso 
                (id_usuario, tipo_permiso, descripcion, comprobante, estado) 
                VALUES 
                (:id_usuario, :tipo_permiso, :descripcion, :comprobante, :estado)";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(':id_usuario', $datos['id_usuario']);
        $stmt->bindValue(':tipo_permiso', $datos['tipo_permiso']);
        $stmt->bindValue(':descripcion', $datos['descripcion']);
        $stmt->bindValue(':comprobante', $datos['comprobante']);
        $stmt->bindValue(':estado', $datos['estado']);

        if (!$stmt->execute()) {
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Error al crear el permiso'
            ];
        }

        return [
            'success' => true,
            'message' => 'permiso creado exitosamente'
        ];
    }

    public static function modificar($datos)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT id FROM permiso WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':id', $datos['id']);
        $stmt->execute();

        if (!$stmt->fetch()) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'El permiso no existe'
            ];
        }

        $sql = "UPDATE permiso SET estado = :estado WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':estado', $datos['estado']);
        $stmt->bindValue(':id', $datos['id']);
        $stmt->execute();

        return [
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0
                ? 'permiso actualizado correctamente'
                : 'No se realizaron cambios en el permiso'
        ];
    }
}