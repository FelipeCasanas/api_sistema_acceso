<?php
require_once ('../../mjolnir/conexion/conectar.php');
require_once ('../../mjolnir/conexion/gestor_consultas.php');

class AmbienteModelo {

    public static function crear($datos) {
        $registro = [
            'id_creador'   => $datos['id_creador'],
            'id_responsable' => $datos['id_responsable'],
            'activo'       => '1',
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];

        list($sql, $params) = construirQuery('ambiente', $registro, 'INSERT');
        $resultado = ejecutarQuery($sql, $params);

        return $resultado
            ? ['success' => true, 'message' => 'Ambiente creado exitosamente']
            : ['success' => false, 'message' => 'Error al crear ambiente'];
    }

    public static function modificar($datos) {
        $id = $datos['id'];
        unset($datos['id']);

        list($sql, $params) = construirQuery('ambiente', $datos, 'UPDATE', ['id' => $id]);
        $resultado = ejecutarQuery($sql, $params);

        return $resultado
            ? ['success' => true, 'message' => 'Ambiente actualizado correctamente']
            : ['success' => false, 'message' => 'Error al actualizar'];
    }

    public static function eliminar($id) {
        list($sql, $params) = construirQuery('ambiente', ['activo' => '0'], 'UPDATE', ['id' => $id]);
        $resultado = ejecutarQuery($sql, $params);

        return $resultado
            ? ['success' => true, 'message' => 'Ambiente eliminado (inactivo)']
            : ['success' => false, 'message' => 'Error al eliminar'];
    }

    public static function obtener($filtros = []) {
        $condiciones = ['activo' => '1'];

        if (!empty($filtros['id'])) {
            $condiciones['id'] = $filtros['id'];
        }

        list($sql, $params) = construirQuery('ambiente', [], 'SELECT', $condiciones);
        $sql .= " ORDER BY fecha_creacion DESC";

        $resultado = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = json_decode($fila, true);
        }

        return [
            'success' => true,
            'message' => 'Ambientes obtenidos correctamente',
            'data' => $resultados
        ];
    }
}
