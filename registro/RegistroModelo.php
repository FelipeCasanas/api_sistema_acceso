<?php
require_once ('../mjolnir/conexion/conectar.php');
require_once ('../mjolnir/conexion/gestor_consultas.php');

class RegistroModelo {

    public static function obtener($medio_busqueda, $dato_busqueda) {
        list($sql, $params) = construirQuery('registro', [], 'SELECT', [$medio_busqueda => $dato_busqueda]);
        $sql .= " ORDER BY fecha_registro DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Registros de registro obtenidos correctamente',
            'data' => $resultados
        ];
    }

    public static function obtenerTodos()
    {
        list($sql, $parametros) = construirQuery('registro', [], 'SELECT', []);
        $stmt = ejecutarQuery($sql, $parametros);

        $registros = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            unset($row['contrasena']);
            $registros[] = $row;
        }

        return [
            'success' => true,
            'message' => 'registros activos obtenidos correctamente',
            'data' => $registros
        ];
    }

    public static function registrar($datos) {
        $registro = [
            'id_usuario'   => $datos['registrado_por'],
            'id_ambiente'      => $datos['id_elemento'],
            'tipo_registro'  => $datos['tipo_registro']
        ];

        list($sql, $params) = construirQuery('registro', $registro, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Registro registrado exitosamente']
            : ['success' => false, 'message' => 'Error al registrar el registro'];
    }
}
