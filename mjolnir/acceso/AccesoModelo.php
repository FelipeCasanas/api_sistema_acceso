<?php
require_once('../conexion/conectar.php');

class AccesoModelo
{

    public static function obtener($data)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT * FROM acceso 
            WHERE id_usuario = :id_usuario 
            AND android_id = :android_id 
            ORDER BY fecha_acceso DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':id_usuario', $data['id_usuario']);
        $stmt->bindValue(':android_id', $data['android_id']);
        $stmt->execute();

        $accesos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($accesos)) {
            return [
                'success' => false,
                'message' => 'No se encontraron accesos',
                'data' => null
            ];
        }

        return [
            'success' => true,
            'message' => 'Accesos obtenidos correctamente',
            'data' => $accesos
        ];
    }

    public static function registrar($datos)
    {
        try {
            $conexion = obtenerConexion();

            // Validar si ya existe el acceso para ese usuario y dispositivo
            $sql = "SELECT 1 FROM acceso 
                WHERE id_usuario = :id_usuario 
                AND android_id = :android_id 
                LIMIT 1";

            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id_usuario', $datos['id_usuario']);
            $stmt->bindValue(':android_id', $datos['android_id']);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'El usuario ya ha registrado un acceso con este dispositivo'
                ];
            }

            // Insertar acceso
            $sql = "INSERT INTO acceso (id_usuario, android_id, medio_acceso)
                VALUES (:id_usuario, :android_id, :medio_acceso)";

            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id_usuario', $datos['id_usuario']);
            $stmt->bindValue(':android_id', $datos['android_id']);
            $stmt->bindValue(':medio_acceso', $datos['medio_acceso']);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Acceso registrado exitosamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al registrar el acceso'
            ];

        } catch (Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
}