<?php
require_once ('../../mjolnir/conexion/conectar.php');
require_once ('../../mjolnir/conexion/gestor_consultas.php');

class CargoModelo {

    public static function crear($nombre) {
        list($sql, $params) = construirQuery('cargo', [], 'SELECT', ['nombre' => $nombre, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);
        if ($stmt->fetch()) {
            http_response_code(409);
            return ['success' => false, 'message' => 'Ya existe un cargo activo con ese nombre'];
        }

        $datos = [
            'nombre' => $nombre,
            'activo' => '1'
        ];

        list($sql, $params) = construirQuery('cargo', $datos, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        if ($stmt) {
            return ['success' => true, 'message' => 'Cargo creado exitosamente'];
        } else {
            http_response_code(500);
            return ['success' => false, 'message' => 'Error al insertar el nuevo cargo'];
        }
    }

    public static function modificar($id, $nombre) {
        list($sql, $params) = construirQuery('cargo', [], 'SELECT', ['id' => $id, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);
        if (!$stmt->fetch()) {
            http_response_code(404);
            return ['success' => false, 'message' => 'El cargo no existe o está inactivo'];
        }

        list($sql, $params) = construirQuery('cargo', ['nombre' => $nombre], 'UPDATE', ['id' => $id]);
        $resultado = ejecutarQuery($sql, $params);

        if ($resultado) {
            return ['success' => true, 'message' => 'Cargo actualizado correctamente'];
        } else {
            http_response_code(500);
            return ['success' => false, 'message' => 'No se pudo actualizar el cargo'];
        }
    }

    public static function eliminar($id) {
        list($sql, $params) = construirQuery('cargo', [], 'SELECT', ['id' => $id, 'activo' => '1']);
        $stmt = ejecutarQuery($sql, $params);
        if (!$stmt->fetch()) {
            http_response_code(404);
            return ['success' => false, 'message' => 'El cargo no existe o ya está eliminado'];
        }

        list($sql, $params) = construirQuery('cargo', ['activo' => '0'], 'UPDATE', ['id' => $id]);
        $resultado = ejecutarQuery($sql, $params);

        if ($resultado) {
            return ['success' => true, 'message' => 'Cargo eliminado correctamente'];
        } else {
            http_response_code(500);
            return ['success' => false, 'message' => 'No se pudo eliminar el cargo'];
        }
    }

    public static function obtener() {
        list($sql, $parametros) = construirQuery('cargo', [], 'SELECT', ['activo' => '1']);
        $sql .= "";

        $stmt = ejecutarQuery($sql, $parametros);
        $cargos = [];

        while ($cargo = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cargos[] = $cargo;
        }

        return [
            'success' => true,
            'message' => 'Cargos obtenidos correctamente',
            'data' => $cargos
        ];
    }
}
