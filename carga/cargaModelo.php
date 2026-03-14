<?php
require_once('../mjolnir/conexion/conectar.php');
require_once('../mjolnir/conexion/gestor_consultas.php');

class CargaModelo
{

    public static function actualizarRuta($id,$ruta)
    {

        $sql = "UPDATE permiso SET comprobante = :ruta WHERE id = :id";

        $stmt = obtenerConexion()->prepare($sql);

        $stmt->bindParam(':ruta',$ruta);
        $stmt->bindParam(':id',$id);

        if($stmt->execute()){

            return [
                'success'=>true,
                'message'=>'Ruta guardada correctamente'
            ];

        }

        return [
            'success'=>false,
            'message'=>'Error al guardar ruta'
        ];

    }

}
?>