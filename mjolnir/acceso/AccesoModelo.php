<?php
require_once('../../mjolnir/conexion/conectar.php');
require_once('../../mjolnir/conexion/gestor_consultas.php');

class AccesoModelo {

    public static function registrar($datos) {
        $acceso = [
            'id_usuario' => $datos['id_usuario'],
            'ip' => $datos['ip'],
            'medio_acceso' => $datos['medio_acceso'],
            'fecha_acceso' => date('Y-m-d H:i:s')
        ];

        list($sql, $params) = construirQuery('acceso', $acceso, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        if ($stmt) {
            return ['success' => true, 'message' => 'Acceso registrado exitosamente'];
        } else {
            http_response_code(500);
            return ['success' => false, 'message' => 'Error al registrar el acceso'];
        }
    }

    public static function obtener() {
        list($sql, $params) = construirQuery('acceso', [], 'SELECT', []);
        $sql .= " ORDER BY fecha_acceso DESC";

        $stmt = ejecutarQuery($sql, $params);
        $accesos = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $accesos[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Accesos obtenidos correctamente',
            'data' => $accesos
        ];
    }
}
