<?php
require_once('../mjolnir/conexion/conectar.php');
require_once('../mjolnir/conexion/gestor_consultas.php');

class AmbienteModelo {
    
    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta)
    {

        if (empty($medio_busqueda)) {
            return [
                'success' => false,
                'message' => 'No se definio argumento de busqueda'
            ];
        }

        if (filter_var($coincidencia_exacta, FILTER_VALIDATE_BOOLEAN) === true) {
            list($sql, $params) = construirQuery('ambiente', [], 'SELECT', [$medio_busqueda => $dato_busqueda]);

        } else {
            list($sql, $params) = construirQuery('ambiente', [], 'SELECT', [$medio_busqueda => ['LIKE', "%$dato_busqueda%"]]);
        }

        
        $stmt = ejecutarQuery($sql, $params);

        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            unset($fila['contrasena']);
            $resultados[] = $fila;
        }

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

    public static function crear($datos)
    {

        $registro = [
            'id_creador'      => $datos['id_creador'],
            'bloque'  => $datos['bloque'],
            'sitio'  => $datos['sitio'],
            'activo'          => '1'
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
            'bloque',
            'sitio',
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
}