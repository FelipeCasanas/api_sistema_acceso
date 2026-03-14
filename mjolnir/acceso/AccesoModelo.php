<?php
require_once('../../mjolnir/conexion/conectar.php');

class AccesoModelo {

    public static function obtener() {

        $conexion = obtenerConexion();

        $sql = "SELECT * FROM acceso ORDER BY fecha_acceso DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();

        $accesos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'Accesos obtenidos correctamente',
            'data' => $accesos
        ];
    }
    
    public static function registrar($datos) {

        $conexion = obtenerConexion();

        $sql = "INSERT INTO acceso (android_id, medio_acceso)
                VALUES (:android_id, :medio_acceso)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':android_id', $datos['android_id']);
        $stmt->bindValue(':medio_acceso', $datos['medio_acceso']);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Acceso registrado exitosamente'
            ];
        } else {
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Error al registrar el acceso'
            ];
        }
    }
}