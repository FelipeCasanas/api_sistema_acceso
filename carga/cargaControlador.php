<?php
require_once('CargaModelo.php');

class CargaControlador
{

    public static function subir($datos, $archivo)
    {
        // Extensiones permitidas para imágenes
        $extensiones_imagen = ['jpg', 'jpeg', 'png'];

        // MIME permitidos para imágenes
        $mime_imagen = ['image/jpeg', 'image/png'];

        // Extensiones peligrosas
        $extensiones_peligrosas = ['php', 'exe', 'js', 'sh', 'bat', 'cmd', 'msi'];

        // Tamaño máximo (5MB)
        $max_size = 5 * 1024 * 1024;

        // Validar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Error al subir el archivo'
            ];
        }

        // Validar tamaño
        if ($archivo['size'] > $max_size) {
            return [
                'success' => false,
                'message' => 'El archivo supera el tamaño permitido (5MB)'
            ];
        }

        // Obtener extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (empty($extension)) {
            return [
                'success' => false,
                'message' => 'El archivo no tiene extensión válida'
            ];
        }

        // Bloquear extensiones peligrosas
        if (in_array($extension, $extensiones_peligrosas)) {
            return [
                'success' => false,
                'message' => 'Extensión no permitida por seguridad'
            ];
        }

        // Obtener MIME real
        $mime = mime_content_type($archivo['tmp_name']);

        // Validar tipo enviado
        if (!isset($datos['tipo'])) {
            return [
                'success' => false,
                'message' => 'No se especificó el tipo de archivo'
            ];
        }

        $tipo = $datos['tipo'];

        // Generar nombre único
        $uuid = self::uuid();
        $nombre = $uuid . '.' . $extension;

        if ($tipo === 'usuario') {

            // Validar extensión
            if (!in_array($extension, $extensiones_imagen)) {
                return [
                    'success' => false,
                    'message' => 'Solo se permiten imágenes para usuario'
                ];
            }

            // Validar MIME REAL
            if (!in_array($mime, $mime_imagen)) {
                return [
                    'success' => false,
                    'message' => 'El archivo no es una imagen válida'
                ];
            }

            $carpeta = 'imagen/';
            $destino = __DIR__ . '/imagen/' . $nombre;

        } elseif ($tipo === 'comprobante') {

            $carpeta = 'comprobante/';
            $destino = __DIR__ . '/comprobante/' . $nombre;

        } else {
            return [
                'success' => false,
                'message' => 'Tipo de archivo no válido'
            ];
        }

        // Crear carpeta si no existe
        if (!is_dir(__DIR__ . '/' . $carpeta)) {
            mkdir(__DIR__ . '/' . $carpeta, 0755, true);
        }

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $destino)) {

            $ruta = $carpeta . $nombre;

            self::guardarRuta($datos['id'], $ruta, $tipo);

            return [
                'success' => true,
                'message' => 'Archivo subido correctamente',
                'data' => [
                    'ruta' => $ruta
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al mover el archivo'
        ];
    }

    // 🔥 CAMBIO: ahora recibe $tipo
    public static function guardarRuta($id, $ruta, $tipo)
    {
        return CargaModelo::actualizarRuta($id, $ruta, $tipo);
    }

    private static function uuid()
    {
        return bin2hex(random_bytes(16));
    }

}
?>