<?php
require_once('CargaModelo.php');

class CargaControlador
{

    public static function subir($datos, $archivo)
    {

        $extensiones_imagen = ['jpg','jpeg','png'];
        $extensiones_archivo = ['pdf','doc','docx'];

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if(!in_array($extension, array_merge($extensiones_imagen,$extensiones_archivo))){
            return [
                'success'=>false,
                'message'=>'Tipo de archivo no permitido'
            ];
        }

        $uuid = self::uuid();

        $nombre = $uuid.'.'.$extension;

        if(in_array($extension,$extensiones_imagen)){
            $ruta = 'imagen/'.$nombre;
            $destino = __DIR__.'/imagen/'.$nombre;
        }else{
            $ruta = 'comprobante/'.$nombre;
            $destino = __DIR__.'/comprobante/'.$nombre;
        }

        if(move_uploaded_file($archivo['tmp_name'],$destino)){

            CargaControlador::guardarRuta($datos['id'], $ruta);

            return [
                'success'=>true,
                'message'=>'Archivo subido',
                'data'=>[
                    'ruta'=>$ruta
                ]
            ];

        }

        return [
            'success'=>false,
            'message'=>'Error al subir archivo'
        ];
    }

    public static function guardarRuta($id,$ruta)
    {
        return CargaModelo::actualizarRuta($id,$ruta);
    }

    private static function uuid()
    {
        return bin2hex(random_bytes(16));
    }

}
?>