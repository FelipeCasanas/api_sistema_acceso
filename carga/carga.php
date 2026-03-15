<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once('CargaControlador.php');

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'POST':
        if(isset($_POST['id']) && isset($_FILES['archivo'])){
            echo json_encode(CargaControlador::subir($_POST, $_FILES['archivo']));
        } else {
            echo json_encode([
                'success'=>false,
                'message'=>'No se recibió archivo o id'
            ]);
        }

    break;

    default:
        echo json_encode([
            'success'=>false,
            'message'=>'Método no permitido'
        ]);
        break;
}
?>