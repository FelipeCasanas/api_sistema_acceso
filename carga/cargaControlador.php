<?php
require_once('CargaModelo.php');

class CargaControlador
{
    public static function subir($tipo, $id, $archivo)
    {
        // Validar archivo
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ["error" => "Archivo no válido"];
        }

        // Configuración por tipo
        if ($tipo === "comprobante") {

            $carpeta = "uploads/comprobante/";
            $extensiones = ["jpg", "jpeg", "png", "pdf", "doc", "docx"];
            $mime_permitidos = [
                "image/jpeg",
                "image/png",
                "application/pdf",
                "application/msword",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            ];

        } elseif ($tipo === "imagen") {

            $carpeta = "uploads/imagen/";
            $extensiones = ["jpg", "jpeg", "png"];
            $mime_permitidos = ["image/jpeg", "image/png"];

        } else {
            return ["error" => "Tipo no permitido"];
        }

        // Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensiones)) {
            return ["error" => "Extensión no permitida"];
        }

        // Validar MIME real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_real = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_real, $mime_permitidos)) {
            return ["error" => "Tipo MIME no permitido"];
        }

        // Crear carpeta si no existe
        if (!is_dir($carpeta)) {
            if (!mkdir($carpeta, 0777, true)) {
                return ["error" => "No se pudo crear la carpeta"];
            }
        }

        // Generar UUID
        try {
            $uuid = bin2hex(random_bytes(16));
        } catch (Exception $e) {
            return ["error" => "Error generando nombre único"];
        }

        $nombre_final = $uuid . "." . $extension;
        $ruta = $carpeta . $nombre_final;

        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
            return ["error" => "Error al guardar archivo"];
        }

        // Guardar en BD (SIN dinámico)
        if ($tipo === "comprobante") {
            $filas = CargaModelo::guardarComprobante($ruta, $id);
        } else {
            $filas = CargaModelo::guardarImagen($ruta, $id);
        }

        // Validar resultado real
        if ($filas > 0) {
            return [
                "success" => true,
                "ruta" => $ruta
            ];
        } else {
            return [
                "error" => "No se actualizó la BD",
                "detalle" => "Verifica si el ID existe o si el valor ya era igual"
            ];
        }
    }

    public static function obtener($tipo, $id)
    {
        if ($tipo === "comprobante") {
            $ruta = CargaModelo::obtenerComprobante($id);
        } elseif ($tipo === "imagen") {
            $ruta = CargaModelo::obtenerImagen($id);
        } else {
            return ["error" => "Tipo no válido"];
        }

        if ($ruta) {
            return [
                "success" => true,
                "ruta" => $ruta
            ];
        } else {
            return ["error" => "No encontrado"];
        }
    }
}