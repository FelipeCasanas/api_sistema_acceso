<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/conexion/conectar.php');

// Clase utilitaria para el manejo de imágenes
class ImageUploader {
    private $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    private $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    private $maxTamano = 2 * 1024 * 1024; // 2MB
    private $minDimension = 100;
    private $rutaGuardadoBase = "../imagenes_guardadas/"; // Ruta física para guardar
    private $urlBasePublica = "../../api/imagenes_guardadas/"; // URL pública para acceder
    private $idInmueble;

    public function __construct($idInmueble) {
        $this->idInmueble = $idInmueble;
    }

    public function uploadAndProcess($file) {
        if ($file['error'] !== 0) {
            return 'Error al cargar la imagen.';
        }

        $nombreTmp = $file['tmp_name'];
        $nombreOriginal = $file['name'];
        $tamanio = $file['size'];
        $tipoMime = mime_content_type($nombreTmp);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if (!$this->isValidType($tipoMime)) {
            return 'Tipo de archivo de imagen no permitido.';
        }
        if (!$this->isValidExtension($extension)) {
            return 'Extensión de imagen no permitida.';
        }
        if (!$this->isValidSize($tamanio)) {
            return 'La imagen supera el tamaño máximo permitido (2MB).';
        }
        if (!$this->isValidDimensions($nombreTmp)) {
            return 'El archivo no es una imagen válida o sus dimensiones son incorrectas.';
        }

        $nuevoNombre = "inmueble_" . $this->idInmueble . "_" . uniqid() . "." . $extension;
        $rutaGuardadoCompleta = $this->rutaGuardadoBase . $nuevoNombre;
        $urlImagenPublica = $this->urlBasePublica . $nuevoNombre;

        if (!move_uploaded_file($nombreTmp, $rutaGuardadoCompleta)) {
            return 'Error al mover la imagen al servidor.';
        }

        if (!$this->saveToDatabase($urlImagenPublica)) { // Guardamos la URL pública
            return 'Error al guardar la información de la imagen en la base de datos.';
        }

        return null; // Indica éxito
    }

    private function isValidType($tipoMime) {
        return in_array($tipoMime, $this->tiposPermitidos);
    }

    private function isValidExtension($extension) {
        return in_array($extension, $this->extensionesPermitidas);
    }

    private function isValidSize($tamanio) {
        return $tamanio <= $this->maxTamano;
    }

    private function isValidDimensions($nombreTmp) {
        if (($dimensiones = @getimagesize($nombreTmp)) === false) {
            return false;
        }
        return $dimensiones[0] >= $this->minDimension && $dimensiones[1] >= $this->minDimension;
    }

    private function saveToDatabase($urlImagenPublica) {
        try {
            list($sql, $parametros) = construirQuery('imagen', ['id_inmueble' => $this->idInmueble, 'url_img' => $urlImagenPublica], 'INSERT');
            return ejecutarQuery($sql, $parametros);
        } catch (Exception $e) {
            return false;
        }
    }
}

if (isset($_FILES['imagen'])) {
    $idInmueble = $_SESSION['id_inmueble'];
    $imageUploader = new ImageUploader($idInmueble);
    $imagenes = is_array($_FILES['imagen']['error']) ? $_FILES['imagen'] : ['error' => [$_FILES['imagen']['error']], 'tmp_name' => [$_FILES['imagen']['tmp_name']], 'name' => [$_FILES['imagen']['name']], 'size' => [$_FILES['imagen']['size']]];
    $errores = [];

    foreach ($imagenes['error'] as $key => $error) {
        $file = [
            'error' => $error,
            'tmp_name' => $imagenes['tmp_name'][$key],
            'name' => $imagenes['name'][$key],
            'size' => $imagenes['size'][$key],
        ];
        $errorUpload = $imageUploader->uploadAndProcess($file);
        if ($errorUpload) {
            $errores[] = $errorUpload;
        }
    }

    if (empty($errores)) {
        $_SESSION['codigo_de_error'] = 'Todas las imágenes han sido subidas correctamente.';
        header('Location: ../../../interfaz/pagina/exito.php');
    } else {
        $_SESSION['codigo_de_error'] = implode("<br>", $errores);
        header('Location: ../../../interfaz/pagina/error.php');
    }
    exit;
} else {
    $_SESSION['codigo_de_error'] = 'No se recibió ninguna imagen o hubo un error en la carga.';
    header('Location: ../../../interfaz/pagina/error.php');
    exit;
}
?>