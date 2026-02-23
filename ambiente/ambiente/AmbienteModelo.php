<?php
require_once('../../mjolnir/conexion/conectar.php');
require_once('../../mjolnir/conexion/gestor_consultas.php');

class AmbienteModelo
{

    public static function crear($datos)
    {

        $registro = [
            'id_creador'      => $datos['id_creador'],
            'id_responsable'  => $datos['id_responsable'],
            'activo'          => '1',
            'fecha_creacion'  => date('Y-m-d H:i:s')
        ];

        list($sql, $params) = construirQuery('ambiente', $registro, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Ambiente creado exitosamente']
            : ['success' => false, 'message' => 'Error al crear ambiente'];
    }


    public static function modificar($datos)
    {

        $camposValidos = [
            'id_creador',
            'id_responsable'
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
            return ['success' => false, 'message' => 'No se enviaron datos para actualizar'];
        }

        list($sql, $params) = construirQuery('ambiente', $datosFiltrados, 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Ambiente actualizado correctamente']
            : ['success' => false, 'message' => 'Error al actualizar'];
    }


    public static function eliminar($id)
    {

        list($sql, $params) = construirQuery('ambiente', ['activo' => '0'], 'UPDATE', ['id' => $id]);
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Ambiente eliminado']
            : ['success' => false, 'message' => 'Error al eliminar'];
    }


    public static function obtener($filtros = [])
    {

        $condiciones = ['activo' => '1'];

        if (!empty($filtros['id'])) {
            $condiciones['id'] = $filtros['id'];
        }

        list($sql, $params) = construirQuery('ambiente', [], 'SELECT', $condiciones);
        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Ambientes obtenidos correctamente',
            'data' => $resultados
        ];
    }
}
