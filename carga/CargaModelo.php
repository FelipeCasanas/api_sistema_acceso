<?php
require_once('../mjolnir/conexion/conectar.php');

class CargaModelo
{
    // 🔹 GUARDAR

    public static function guardarComprobante($ruta, $id)
    {
        $conexion = obtenerConexion();

        $sql = "UPDATE permiso SET comprobante = :ruta WHERE id = :id";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":ruta", $ruta, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function guardarImagen($ruta, $id)
    {
        $conexion = obtenerConexion();

        $sql = "UPDATE usuario SET enlace_imagen = :ruta WHERE id = :id";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":ruta", $ruta, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    // 🔹 OBTENER

    public static function obtenerComprobante($id)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT comprobante FROM permiso WHERE id = :id LIMIT 1";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['comprobante'] : null;
    }

    public static function obtenerImagen($id)
    {
        $conexion = obtenerConexion();

        $sql = "SELECT enlace_imagen FROM usuario WHERE id = :id LIMIT 1";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['enlace_imagen'] : null;
    }
}