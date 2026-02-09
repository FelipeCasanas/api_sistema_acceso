<?php
require_once ('../../mjolnir/conexion/conectar.php');
require_once ('../../mjolnir/conexion/gestor_consultas.php');

class TipoAmbienteModelo {

    public static function crear($nombre) {
        list($sql, $params) = construirQuery('tipo_ambiente', [], 'SELECT', ['nombre' => $nombre, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);

        if ($stmt->fetch()) {
            http_response_code(409);
            return ['success' => false, 'message' => 'Ya existe un tipo de ambiente con ese nombre'];
        }

        $datos = ['nombre' => $nombre, 'activo' => '1'];
        list($sql, $params) = construirQuery('tipo_ambiente', $datos, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Tipo de ambiente creado exitosamente']
            : ['success' => false, 'message' => 'Error al crear tipo de ambiente'];
    }

    public static function modificar($id, $nombre) {
        list($sql, $params) = construirQuery('tipo_ambiente', [], 'SELECT', ['id' => $id, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);

        if (!$stmt->fetch()) {
            http_response_code(404);
            return ['success' => false, 'message' => 'No se encontró el tipo de ambiente'];
        }

        list($sql, $params) = construirQuery('tipo_ambiente', ['nombre' => $nombre], 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Tipo de ambiente actualizado']
            : ['success' => false, 'message' => 'Error al actualizar'];
    }

    public static function eliminar($id) {
        list($sql, $params) = construirQuery('tipo_ambiente', ['activo' => '0'], 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Tipo de ambiente eliminado (inactivo)']
            : ['success' => false, 'message' => 'Error al eliminar'];
    }

    public static function obtener() {
        list($sql, $params) = construirQuery('tipo_ambiente', [], 'SELECT', ['activo' => '1']);
        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Tipos de ambiente obtenidos correctamente',
            'data' => $resultados
        ];
    }
}
