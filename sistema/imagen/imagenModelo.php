<?php

class ImagenModelo {
    public static function obtener($id_usuario) {
            // Buscar usuario
            list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['id' => $id_usuario]);
            $stmt = ejecutarQuery($sql, $parametros);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                http_response_code(404);
                return [
                    'success' => false,
                    'message' => 'El usuario no existe'
                ];
            }

            // Verificar si tiene imagen
            if (empty($usuario['enlace_imagen'])) {
                return [
                    'success' => true,
                    'message' => 'El usuario no tiene imagen asignada',
                    'data' => [
                        'url' => null
                    ]
                ];
            }

            return [
                'success' => true,
                'message' => 'Imagen del usuario obtenida correctamente',
                'data' => [
                    'url' => $usuario['enlace_imagen']
                ]
            ];
    }

    public static function subirImagen($id_usuario, $file) {

        // 1. verificar existencia del usuario
        list($sql, $parametros) = construirQuery('usuario', [], 'SELECT', ['id' => $id_usuario]);
        $stmt = ejecutarQuery($sql, $parametros);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            return ['success' => false, 'message' => 'El usuario no existe'];
        }

        // 2. validar archivo
        if ($file['error'] !== 0) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Error al cargar la imagen'];
        }

        $tmp = $file['tmp_name'];
        $nombre = $file['name'];
        $size = $file['size'];
        $mime = mime_content_type($tmp);
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        $tiposPermitidos = ['image/jpeg', 'image/png'];
        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        $maxTamano = 2 * 1024 * 1024; // 2MB
        $minDimension = 100;

        if (!in_array($mime, $tiposPermitidos)) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Tipo de imagen no permitido'];
        }

        if (!in_array($ext, $extensionesPermitidas)) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Extensión de imagen no permitida'];
        }

        if ($size > $maxTamano) {
            http_response_code(400);
            return ['success' => false, 'message' => 'La imagen supera el tamaño máximo de 2MB'];
        }

        if (($dim = @getimagesize($tmp)) === false || $dim[0] < $minDimension || $dim[1] < $minDimension) {
            http_response_code(400);
            return ['success' => false, 'message' => 'La imagen no es válida o es demasiado pequeña'];
        }

        // 3. guardar archivo
        $nuevoNombre = "usuario_" . $id_usuario . "_" . uniqid() . "." . $ext;
        $rutaFisica = "imagenes_guardadas/" . $nuevoNombre;
        $urlPublica = "../../../api_sistema_acceso/imagenes_guardadas/" . $nuevoNombre;

        if (!move_uploaded_file($tmp, $rutaFisica)) {
            http_response_code(500);
            return ['success' => false, 'message' => 'Error al guardar la imagen en el servidor'];
        }

        // 4. actualizar usuario (sobrescribe la imagen anterior)
        list($sql, $parametros) = construirQuery(
            'usuario',
            ['enlace_imagen' => $urlPublica],
            'UPDATE',
            ['id' => $id_usuario]
        );

        ejecutarQuery($sql, $parametros);

        return [
            'success' => true,
            'message' => 'Imagen subida correctamente',
            'data' => [
                'url' => $urlPublica
            ]
        ];
    }
}