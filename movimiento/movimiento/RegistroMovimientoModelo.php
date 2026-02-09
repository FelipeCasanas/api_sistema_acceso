<?php
require_once ('../../mjolnir/conexion/conectar.php');
require_once ('../../mjolnir/conexion/gestor_consultas.php');

class RegistroRegistroModelo {

    public static function registrar($datos) {
        $registro = [
            'registrado_por'   => $datos['registrado_por'],
            'id_elemento'      => $datos['id_elemento'],
            'tipo_registro'  => $datos['tipo_registro'],
            'fecha'            => date('Y-m-d H:i:s'),
            'descripcion'      => $datos['descripcion'],
            'metadatos'        => json_encode($datos['metadatos'] ?? [])
        ];

        list($sql, $params) = construirQuery('registro_registro', $registro, 'INSERT');
        $stmt = ejecutarQuery($sql, $params);

        return $stmt
            ? ['success' => true, 'message' => 'Registro registrado exitosamente']
            : ['success' => false, 'message' => 'Error al registrar el registro'];
    }

    public static function obtener($filtros = []) {
        list($sql, $params) = construirQuery('registro_registro', [], 'SELECT', $filtros);
        $sql .= " ORDER BY fecha DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fila['metadatos'] = json_decode($fila['metadatos'], true); // decodificar JSON
            $resultados[] = $fila;
        }

        return [
            'success' => true,
            'message' => 'Registros de registro obtenidos correctamente',
            'data' => $resultados
        ];
    }
}
