<?php
require_once('../mjolnir/conexion/conectar.php');
require_once('../mjolnir/conexion/gestor_consultas.php');

class CargaModelo
{

    public static function actualizarRuta($id, $ruta, $tipo)
    {

        $conexion = obtenerConexion();

        if ($tipo === 'usuario') {

            $sql = "UPDATE usuario SET enlace_imagen = :ruta WHERE id = :id";

        } elseif ($tipo === 'comprobante') {

            $sql = "UPDATE permiso SET comprobante = :ruta WHERE id = :id";

        } else {
            return [
                'success' => false,
                'message' => 'Tipo no válido para guardar ruta'
            ];
        }

        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':ruta', $ruta);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Ruta guardada correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al guardar ruta'
        ];
    }
}
?>