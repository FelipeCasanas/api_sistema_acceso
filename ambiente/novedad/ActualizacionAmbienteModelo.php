<?php
require_once('../../mjolnir/conexion/conectar.php');
require_once('../../mjolnir/conexion/gestor_consultas.php');

class ActualizacionAmbienteModelo {

    public static function registrar($datos) {
        $registro = [
            'id_usuario'      => $datos['id_usuario'],
            'id_ambiente'     => $datos['id_ambiente'],
            'descripcion'     => $datos['descripcion'],
            'fecha_creacion'  => date('Y-m-d H:i:s')
        ];

        list($sql, $params) = construirQuery('actualizacion_ambiente', $registro, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Novedad registrada exitosamente']
            : ['success' => false, 'message' => 'Error al registrar la novedad'];
    }

    public static function obtener($filtros = []) {
        list($sql, $params) = construirQuery('actualizacion_ambiente', [], 'SELECT', $filtros);
        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Novedades obtenidas correctamente',
            'data' => $resultados
        ];
    }
}
