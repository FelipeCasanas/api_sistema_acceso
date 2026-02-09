<?php
require_once ('../../mjolnir/conexion/conectar.php');
require_once ('../../mjolnir/conexion/gestor_consultas.php');

class TipoRegistroModelo {

    public static function crear($nombre) {
        // Validar que no exista
        list($sql, $params) = construirQuery('tipo_registro', [], 'SELECT', ['nombre' => $nombre, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);

        if ($stmt->fetch()) {
            http_response_code(409);
            return ['success' => false, 'message' => 'Ya existe un tipo de registro con ese nombre'];
        }

        $datos = ['nombre' => $nombre, 'activo' => '1'];
        list($sql, $params) = construirQuery('tipo_registro', $datos, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Tipo de registro creado exitosamente']
            : ['success' => false, 'message' => 'Error al crear tipo de registro'];
    }

    public static function modificar($id, $nombre) {
        list($sql, $params) = construirQuery('tipo_registro', [], 'SELECT', ['id' => $id, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);

        if (!$stmt->fetch()) {
            http_response_code(404);
            return ['success' => false, 'message' => 'No se encontró el tipo de registro'];
        }

        list($sql, $params) = construirQuery('tipo_registro', ['nombre' => $nombre], 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Tipo de registro actualizado']
            : ['success' => false, 'message' => 'Error al actualizar'];
    }

    public static function eliminar($id) {
        list($sql, $params) = construirQuery('tipo_registro', ['activo' => '0'], 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Tipo de registro eliminado (inactivo)']
            : ['success' => false, 'message' => 'Error al eliminar'];
    }

    public static function obtener() {
        list($sql, $params) = construirQuery('tipo_registro', [], 'SELECT', ['activo' => '1']);
        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Tipos de registro obtenidos correctamente',
            'data' => $resultados
        ];
    }
}
