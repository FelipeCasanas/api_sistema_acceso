<?php
require_once('../mjolnir/conexion/conectar.php');
require_once('../mjolnir/conexion/gestor_consultas.php');

class RegistroModelo
{

    public static function obtener($medio_busqueda, $dato_busqueda)
    {
        list($sql, $params) = construirQuery('registro', [], 'SELECT', [$medio_busqueda => $dato_busqueda]);
        $sql .= " ORDER BY fecha_registro DESC";

        $stmt = ejecutarQuery($sql, $params);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    public static function obtenerUltimo($idUsuario, $idAmbiente)
    {
        $sql = "SELECT tipo_registro
            FROM registro
            WHERE id_usuario = :id_usuario
            AND id_ambiente = :id_ambiente
            ORDER BY id DESC
            LIMIT 1";

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_ambiente', $idAmbiente, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado : null;
    }

    public static function registrar($datos)
    {

        if (!isset($datos['id_usuario']) || !isset($datos['id_ambiente'])) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Faltan campos: id_usuario o id_ambiente'
            ];
        }

        $idUsuario = $datos['id_usuario'];
        $idAmbiente = $datos['id_ambiente'];

        $ultimoRegistro = self::obtenerUltimo($idUsuario, $idAmbiente);

        if ($ultimoRegistro !== null && strtoupper($ultimoRegistro['tipo_registro']) === 'ENTRADA') {
            $tipoRegistro = 'SALIDA';
        } else {
            $tipoRegistro = 'ENTRADA';
        }

        $sql = "INSERT INTO registro (id_usuario, id_ambiente, tipo_registro)
            VALUES (:id_usuario, :id_ambiente, :tipo_registro)";

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_ambiente', $idAmbiente, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_registro', $tipoRegistro, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => $tipoRegistro
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al registrar'
            ];
        }
    }
}
