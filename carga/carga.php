<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once('CargaControlador.php');

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'POST':

        if(isset($_FILES['archivo'])){
            echo json_encode(CargaControlador::subir($_FILES['archivo']));
        } else {
            echo json_encode([
                'success'=>false,
                'message'=>'No se recibió archivo'
            ]);
        }

    break;

    case 'PUT':

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        if(isset($datos['id']) && isset($datos['ruta'])){
            echo json_encode(
                CargaControlador::guardarRuta(
                    $datos['id'],
                    $datos['ruta']
                )
            );
        } else {
            echo json_encode([
                'success'=>false,
                'message'=>'Datos incompletos'
            ]);
        }

    break;

}
?>