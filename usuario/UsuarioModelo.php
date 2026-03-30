<?php
require_once('../mjolnir/conexion/conectar.php');
require_once('../mjolnir/seguridad.php');

class UsuarioModelo
{

    public static function obtenerTotal()
    {
        $sql = "SELECT COUNT(*) AS total FROM usuario WHERE activo = :activo";

        $consulta = obtenerConexion()->prepare($sql);
        $consulta->bindValue(':activo', 1, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'Total de usuarios activos obtenido correctamente',
            'data' => $resultado['total']
        ];
    }

    public static function obtener($medio_busqueda, $dato_busqueda, $coincidencia_exacta)
    {
        $camposPermitidos = ['id', 'identificacion', 'cargo', 'nombre'];

        if (!in_array($medio_busqueda, $camposPermitidos)) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Campo de búsqueda inválido'
            ];
        }

        if (filter_var($coincidencia_exacta, FILTER_VALIDATE_BOOLEAN)) {
            $sql = "SELECT * FROM usuario WHERE $medio_busqueda = :dato";
            $consulta = obtenerConexion()->prepare($sql);
            $consulta->bindValue(':dato', $dato_busqueda);
        } else {
            $sql = "SELECT * FROM usuario WHERE $medio_busqueda LIKE :dato";
            $consulta = obtenerConexion()->prepare($sql);
            $consulta->bindValue(':dato', "%$dato_busqueda%");
        }

        $consulta->execute();
        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if (empty($usuarios)) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        return [
            'success' => true,
            'message' => 'Información del usuario obtenida',
            'data' => $usuarios
        ];
    }

    public static function obtenerTodos()
    {
        $sql = "SELECT * FROM usuario WHERE activo = :activo";
        $consulta = obtenerConexion()->prepare($sql);
        $consulta->bindValue(':activo', 1, PDO::PARAM_INT);
        $consulta->execute();

        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => 'Usuarios activos obtenidos correctamente',
            'data' => $usuarios
        ];
    }

    public static function crear($datos)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT id FROM usuario WHERE identificacion = :identificacion AND activo = :estado";
        $consulta = $conexion->prepare($sql);
        $consulta->bindValue(':identificacion', $datos['identificacion']);
        $consulta->bindValue(':estado', 1, PDO::PARAM_INT);
        $consulta->execute();

        if ($consulta->fetch()) {
            http_response_code(409);
            return [
                'success' => false,
                'message' => 'Ya existe un usuario con esa identificacion'
            ];
        }

        $sql = "INSERT INTO usuario 
            (tipo_identificacion, identificacion, cargo, nombre, correo, celular, activo) 
            VALUES 
            (:tipo_identificacion, :identificacion, :cargo, :nombre, :correo, :celular, 1)";

        $consulta = $conexion->prepare($sql);

        $consulta->bindValue(':tipo_identificacion', $datos['tipo_identificacion']);
        $consulta->bindValue(':identificacion', $datos['identificacion']);
        $consulta->bindValue(':cargo', $datos['cargo']);
        $consulta->bindValue(':nombre', $datos['nombre']);
        $consulta->bindValue(':correo', $datos['correo']);
        $consulta->bindValue(':celular', $datos['celular']);

        if (!$consulta->execute()) {
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Error al crear el usuario'
            ];
        }

        $id = $conexion->lastInsertId();

        return [
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => [
                'id' => $id
            ]
        ];
    }

    public static function cargaMasiva($usuarios)
    {
        $conexion = obtenerConexion();

        $total = count($usuarios);
        $insertados = 0;
        $duplicados = 0;
        $errores = [];

        try {
            $conexion->beginTransaction();

            $identificaciones = array_column($usuarios, 'identificacion');

            if (!empty($identificaciones)) {
                $placeholders = implode(',', array_fill(0, count($identificaciones), '?'));
                $sql = "SELECT identificacion FROM usuario WHERE identificacion IN ($placeholders) AND activo = 1";
                $consulta = $conexion->prepare($sql);
                $consulta->execute($identificaciones);

                $existentes = $consulta->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $existentes = [];
            }

            $sqlInsert = "INSERT INTO usuario 
                (tipo_identificacion, identificacion, cargo, nombre, correo, celular, activo) 
                VALUES 
                (:tipo_identificacion, :identificacion, :cargo, :nombre, :correo, :celular, 1)";

            $stmtInsert = $conexion->prepare($sqlInsert);

            foreach ($usuarios as $index => $usuario) {

                $validacion = Seguridad::validarUsuario([
                    'nombre' => $usuario['nombre'] ?? '',
                    'correo' => $usuario['correo'] ?? '',
                    'identificacion' => $usuario['identificacion'] ?? '',
                    'celular' => $usuario['telefono'] ?? '',
                    'tipo_identificacion' => $usuario['tipo_identificacion'] ?? '',
                    'cargo' => $usuario['cargo'] ?? ''
                ]);

                if (!$validacion['valido']) {
                    $errores[] = "Fila $index: " . $validacion['mensaje'];
                    continue;
                }

                $usuario = $validacion['datos'];

                if (in_array($usuario['identificacion'], $existentes)) {
                    $duplicados++;
                    continue;
                }

                try {
                    $stmtInsert->execute([
                        ':tipo_identificacion' => $usuario['tipo_identificacion'],
                        ':identificacion' => $usuario['identificacion'],
                        ':cargo' => $usuario['cargo'],
                        ':nombre' => $usuario['nombre'],
                        ':correo' => $usuario['correo'],
                        ':celular' => $usuario['celular']
                    ]);

                    $insertados++;

                } catch (Exception $e) {
                    $errores[] = "Fila $index: error al insertar";
                }
            }

            $conexion->commit();

            return [
                'success' => true,
                'message' => 'Carga masiva procesada',
                'data' => [
                    'total' => $total,
                    'insertados' => $insertados,
                    'duplicados' => $duplicados,
                    'fallidos' => count($errores),
                    'errores' => $errores
                ]
            ];

        } catch (Exception $e) {
            $conexion->rollBack();

            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Error en la carga masiva',
                'error' => $e->getMessage()
            ];
        }
    }

    public static function modificar($id, $datos)
    {
        $sql = "SELECT id FROM usuario WHERE id = :id";
        $consulta = obtenerConexion()->prepare($sql);
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        if (!$consulta->fetch()) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'El usuario no existe'
            ];
        }

        $set = [];
        foreach ($datos as $campo => $valor) {
            $set[] = "$campo = :$campo";
        }

        $sql = "UPDATE usuario SET " . implode(", ", $set) . " WHERE id = :id";
        $consulta = obtenerConexion()->prepare($sql);

        foreach ($datos as $campo => $valor) {
            $consulta->bindValue(":$campo", $valor);
        }

        $consulta->bindValue(':id', $id);
        $consulta->execute();

        return [
            'success' => $consulta->rowCount() > 0,
            'message' => $consulta->rowCount() > 0
                ? 'Usuario actualizado correctamente'
                : 'No se realizaron cambios en el usuario'
        ];
    }

    public static function eliminar($id)
    {
        $sql = "SELECT id FROM usuario WHERE id = :id";
        $consulta = obtenerConexion()->prepare($sql);
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        if (!$consulta->fetch()) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'El usuario no existe'
            ];
        }

        $sql = "UPDATE usuario SET activo = 0 WHERE id = :id";
        $consulta = obtenerConexion()->prepare($sql);
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        return [
            'success' => $consulta->rowCount() > 0,
            'message' => $consulta->rowCount() > 0
                ? 'Usuario eliminado correctamente'
                : 'No se realizaron cambios'
        ];
    }
}