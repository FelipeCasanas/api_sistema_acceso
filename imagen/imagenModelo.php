<?php

class ImagenModelo {

    private static function obtenerConfiguracion($tipo) {

        $config = [
            'usuario' => [
                'tabla' => 'usuario',
                'campo' => 'enlace_imagen',
                'prefijo' => 'usuario'
            ],
            'permiso' => [
                'tabla' => 'permiso',
                'campo' => 'comprobante',
                'prefijo' => 'permiso'
            ]
        ];

        return $config[$tipo] ?? null;
    }

    public static function obtener($tipo, $id) {

        $config = self::obtenerConfiguracion($tipo);

        if (!$config) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Tipo de imagen no válido'
            ];
        }

        list($sql, $parametros) = construirQuery(
            $config['tabla'],
            [],
            'SELECT',
            ['id' => $id]
        );

        $stmt = ejecutarQuery($sql, $parametros);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            http_response_code(404);
            return [
                'success' => false,
                'message' => 'Registro no encontrado'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'url' => $registro[$config['campo']] ?? null
            ]
        ];
    }

    public static function subirImagen($tipo, $id, $file) {

        $config = self::obtenerConfiguracion($tipo);

        if (!$config) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Tipo de imagen no válido'
            ];
        }

        if ($file['error'] !== 0) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Error al cargar la imagen'
            ];
        }

        $tmp = $file['tmp_name'];
        $nombre = $file['name'];
        $size = $file['size'];

        $mime = mime_content_type($tmp);
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        $tiposPermitidos = ['image/pdf', 'image/png'];
        $extensionesPermitidas = ['jpg', 'pdf', 'png'];
        $maxTamano = 2 * 1024 * 1024;

        if (!in_array($mime, $tiposPermitidos) || !in_array($ext, $extensionesPermitidas)) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'Formato de imagen no permitido'
            ];
        }

        if ($size > $maxTamano) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => 'La imagen supera el tamaño máximo'
            ];
        }

        $nuevoNombre = $config['prefijo'] . "_" . $id . "_" . uniqid() . "." . $ext;

        $rutaFisica = "imagenes_guardadas/" . $nuevoNombre;
        $urlPublica = "../../../api/imagenes_guardadas/" . $nuevoNombre;

        if (!move_uploaded_file($tmp, $rutaFisica)) {
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Error al guardar la imagen'
            ];
        }

        list($sql, $parametros) = construirQuery(
            $config['tabla'],
            [$config['campo'] => $urlPublica],
            'UPDATE',
            ['id' => $id]
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