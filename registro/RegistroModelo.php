<?php
require_once('../mjolnir/conexion/conectar.php');

class RegistroModelo
{

    public static function obtener($medio_busqueda, $dato_busqueda)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT 
            r.id,
            r.id_usuario,
            r.id_ambiente,
            r.tipo_registro,
            r.fecha_registro,
            u.nombre AS nombre_usuario,
            a.sitio AS sitio_ambiente,
            a.bloque AS bloque_ambiente
        FROM registro r
        JOIN usuario u ON r.id_usuario = u.id
        JOIN ambiente a ON r.id_ambiente = a.id
        WHERE r.$medio_busqueda = :dato_busqueda
        ORDER BY r.fecha_registro DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':dato_busqueda', $dato_busqueda);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'Registros de registro obtenidos correctamente',
            'data' => $resultados
        ];
    }

    public static function obtenerTodos()
    {
        $conexion = obtenerConexion();

        $sql = "SELECT 
            r.id,
            r.id_usuario,
            r.id_ambiente,
            r.tipo_registro,
            r.fecha_registro,
            u.nombre AS nombre_usuario,
            a.sitio AS sitio_ambiente,
            a.bloque AS bloque_ambiente
        FROM registro r
        JOIN usuario u ON r.id_usuario = u.id
        JOIN ambiente a ON r.id_ambiente = a.id
        ORDER BY r.fecha_registro DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute();

        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'registros activos obtenidos correctamente',
            'data' => $registros
        ];
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

        $conexion = obtenerConexion();

        // 1. Validar los 5 minutos directamente en SQL
        $sqlValidacion = "SELECT fecha_registro
                      FROM registro
                      WHERE id_usuario = :id_usuario
                      AND id_ambiente = :id_ambiente
                      AND tipo_registro = 'ENTRADA'
                      ORDER BY fecha_registro DESC
                      LIMIT 1";

        $stmt = $conexion->prepare($sqlValidacion);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_ambiente', $idAmbiente, PDO::PARAM_INT);
        $stmt->execute();

        $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ultimo) {
            $ultimaFecha = new DateTime($ultimo['fecha_registro']);
            $ahora = new DateTime();

            $diferencia = $ahora->getTimestamp() - $ultimaFecha->getTimestamp();

            if ($diferencia < 300) {
                $segundosRestantes = 300 - $diferencia;

                return [
                    'success' => false,
                    'message' => 'Debe esperar ' . $segundosRestantes . ' segundos antes de volver a escanear'
                ];
            }
        }

        // 2. Obtener último tipo_registro
        $sqlTipo = "SELECT tipo_registro
                FROM registro
                WHERE id_usuario = :id_usuario
                AND id_ambiente = :id_ambiente
                ORDER BY id DESC
                LIMIT 1";

        $stmtTipo = $conexion->prepare($sqlTipo);
        $stmtTipo->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmtTipo->bindParam(':id_ambiente', $idAmbiente, PDO::PARAM_INT);
        $stmtTipo->execute();

        $ultimoTipo = $stmtTipo->fetch(PDO::FETCH_ASSOC);

        // 3. Determinar entrada o salida
        if ($ultimoTipo && strtoupper($ultimoTipo['tipo_registro']) === 'ENTRADA') {
            $tipoRegistro = 'SALIDA';
        } else {
            $tipoRegistro = 'ENTRADA';
        }

        // 4. Insertar
        $sqlInsert = "INSERT INTO registro (id_usuario, id_ambiente, tipo_registro)
                  VALUES (:id_usuario, :id_ambiente, :tipo_registro)";

        $stmtInsert = $conexion->prepare($sqlInsert);
        $stmtInsert->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmtInsert->bindParam(':id_ambiente', $idAmbiente, PDO::PARAM_INT);
        $stmtInsert->bindParam(':tipo_registro', $tipoRegistro, PDO::PARAM_STR);

        if ($stmtInsert->execute()) {
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