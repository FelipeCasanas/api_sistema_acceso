<?php

require_once('conectar.php');

function recibirDatos($requeridos) {
    $datos = [];

    foreach ($requeridos as $campo) {
        $valor = $_POST[$campo] ?? null;

        if (!$valor) {
            throw new Exception("Falta el campo obligatorio: $campo");
        }

        switch ($campo) {
            case 'correo':
                $valor = filter_var($valor, FILTER_SANITIZE_EMAIL);
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Correo inválido: $valor");
                }
                break;

            case 'celular':
            case 'telefono':
                $valor = preg_replace('/[^0-9]/', '', $valor);
                if (strlen($valor) < 7) {
                    throw new Exception("Teléfono inválido: $valor");
                }
                break;

            default:
                $valor = htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
                break;
        }

        $datos[$campo] = $valor;
    }

    return $datos;
}


function construirQuery($tabla, $datos = [], $accion = 'INSERT', $condiciones = []) {
    $sql = "";
    $parametros = [];

    if ($accion === 'INSERT') {
        $campos = implode(", ", array_keys($datos));
        $placeholders = implode(", ", array_fill(0, count($datos), '?'));
        $sql = "INSERT INTO $tabla ($campos) VALUES ($placeholders)";
        $parametros = array_values($datos);
    } else if ($accion === 'UPDATE') {
        $set = implode(", ", array_map(fn($campo) => "$campo = ?", array_keys($datos)));
        $sql = "UPDATE $tabla SET $set";

        if (!empty($condiciones)) {
            $where = implode(" AND ", array_map(fn($campo) => "$campo = ?", array_keys($condiciones)));
            $sql .= " WHERE $where";
            $parametros = array_merge(array_values($datos), array_values($condiciones));
        } else {
            throw new Exception("Se requiere condición para UPDATE");
        }
    } else if ($accion === 'DELETE') {
        $sql = "DELETE FROM $tabla";

        if (!empty($condiciones)) {
            $where = implode(" AND ", array_map(fn($campo) => "$campo = ?", array_keys($condiciones)));
            $sql .= " WHERE $where";
            $parametros = array_values($condiciones);
        } else {
            throw new Exception("Se requiere condición para DELETE");
        }
    } else if ($accion === 'SELECT') {
        $sql = "SELECT * FROM $tabla";

        if (!empty($condiciones)) {

            $wherePartes = [];
            $parametros = [];

            foreach ($condiciones as $campo => $valor) {

                if (is_array($valor)) {
                    $operador = strtoupper($valor[0]);
                    $wherePartes[] = "$campo $operador ?";
                    $parametros[] = $valor[1];
                } else {
                    $wherePartes[] = "$campo = ?";
                    $parametros[] = $valor;
                }
            }

            $where = implode(" AND ", $wherePartes);
            $sql .= " WHERE $where";
        }
    } else {
        throw new Exception("Acción no soportada: $accion");
    }

    return [$sql, $parametros];
}

function ejecutarQuery($sql, $parametros) {
    $conexion = obtenerConexion();
    $stmt = $conexion->prepare($sql);
    $resultado = $stmt->execute($parametros);

    if (!$resultado) {
        throw new Exception("Error en la consulta");
    }

    return $stmt;
}

function procesarResultado($stmt, $tipo = 'INSERT') {
    if ($tipo === 'INSERT') {
        return $stmt->connection->lastInsertId();
    } else if ($tipo === 'SELECT') {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if ($tipo === 'UPDATE' || $tipo === 'DELETE') {
        return $stmt->rowCount();
    }
}

function construirQueryBusqueda($tabla, $termino_busqueda, $campos) {
    $sql = "SELECT * FROM $tabla WHERE ";
    $condiciones = [];
    $parametros = [];

    foreach ($campos as $campo) {
        $condiciones[] = "$campo LIKE ?";
        $parametros[] = "%$termino_busqueda%";
    }

    $sql .= implode(" OR ", $condiciones);

    return [$sql, $parametros];
}


?>