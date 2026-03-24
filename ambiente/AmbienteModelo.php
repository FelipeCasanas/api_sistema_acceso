<?php
require_once('../mjolnir/conexion/conectar.php');

class AmbienteModelo
{

    public static function obtenerTotal()
    {
        $sql = "SELECT COUNT(*) AS total FROM ambiente WHERE activo = :activo";

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->bindValue(':activo', 1, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'Total de ambientes activos obtenido correctamente',
            'data' => $resultado['total']
        ];
    }

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta)
    {
        if (empty($medio_busqueda)) {
            return [
                'success' => false,
                'message' => 'No se definio argumento de busqueda'
            ];
        }

        $conexion = obtenerConexion();

        if (filter_var($coincidencia_exacta, FILTER_VALIDATE_BOOLEAN)) {

            $sql = "SELECT 
                        a.id,
                        a.id_creador,
                        u.nombre AS nombre_creador,
                        a.bloque,
                        a.sitio,
                        a.activo,
                        a.fecha_creacion
                    FROM ambiente a
                    JOIN usuario u ON a.id_creador = u.id
                    WHERE a.$medio_busqueda = :dato";

            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':dato', $dato_busqueda);

        } else {

            $sql = "SELECT 
                        a.id,
                        a.id_creador,
                        u.nombre AS nombre_creador,
                        a.bloque,
                        a.sitio,
                        a.activo,
                        a.fecha_creacion
                    FROM ambiente a
                    JOIN usuario u ON a.id_creador = u.id
                    WHERE a.$medio_busqueda LIKE :dato";

            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':dato', "%$dato_busqueda%");
        }

        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultados)) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'ambiente no encontrado'
            ];
        }

        return [
            'success' => true,
            'message' => 'Información obtenida correctamente',
            'data' => $resultados
        ];
    }

    public static function obtenerTodos()
    {
        $conexion = obtenerConexion();

        $sql = "SELECT 
                    a.id,
                    a.id_creador,
                    u.nombre AS nombre_creador,
                    a.bloque,
                    a.sitio,
                    a.activo,
                    a.fecha_creacion
                FROM ambiente a
                JOIN usuario u ON a.id_creador = u.id
                WHERE a.activo = :activo";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':activo', 1, PDO::PARAM_INT);
        $stmt->execute();

        $ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'ambientes activos obtenidos correctamente',
            'data' => $ambientes
        ];
    }

    public static function crear($datos)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT id 
        FROM ambiente 
        WHERE bloque = :bloque 
        AND sitio = :sitio 
        LIMIT 1";

        $stmtConsulta = $conexion->prepare($sql);
        $stmtConsulta->bindParam(':bloque', $datos['bloque']);
        $stmtConsulta->bindParam(':sitio', $datos['sitio']);
        $stmtConsulta->execute();

        if ($stmtConsulta->fetch()) {
            return [
                'success' => false,
                'message' => 'Ya existe este ambiente'
            ];
        }

        $sql = "INSERT INTO ambiente (id_creador, bloque, sitio, activo)
                VALUES (:id_creador, :bloque, :sitio, 1)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':id_creador', $datos['id_creador']);
        $stmt->bindValue(':bloque', $datos['bloque']);
        $stmt->bindValue(':sitio', $datos['sitio']);

        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => 'Error al crear ambiente'
            ];
        }

        return [
            'success' => true,
            'message' => 'Ambiente creado exitosamente'
        ];
    }

    public static function modificar($datos)
    {
        $camposValidos = [
            'id_creador',
            'bloque',
            'sitio'
        ];

        $datosFiltrados = [];
        $id = $datos['id'];

        foreach ($camposValidos as $campo) {
            if (!empty($datos[$campo])) {
                $datosFiltrados[$campo] = $datos[$campo];
            }
        }

        if (empty($datosFiltrados)) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'No se enviaron datos para actualizar'
            ];
        }

        $conexion = obtenerConexion();

        $set = [];
        foreach ($datosFiltrados as $campo => $valor) {
            $set[] = "$campo = :$campo";
        }

        $sql = "UPDATE ambiente SET " . implode(", ", $set) . " WHERE id = :id";
        $stmt = $conexion->prepare($sql);

        foreach ($datosFiltrados as $campo => $valor) {
            $stmt->bindValue(":$campo", $valor);
        }

        $stmt->bindValue(':id', $id);

        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => 'Error al actualizar'
            ];
        }

        return [
            'success' => true,
            'message' => 'Ambiente actualizado correctamente'
        ];
    }

    public static function eliminar($id)
    {
        $conexion = obtenerConexion();

        $sql = "UPDATE ambiente SET activo = 0 WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':id', $id);

        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => 'Error al eliminar'
            ];
        }

        return [
            'success' => true,
            'message' => 'Ambiente eliminado'
        ];
    }
}