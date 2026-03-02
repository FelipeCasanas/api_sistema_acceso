<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'ImagenControlador.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];
$datos = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($metodo) {

    case 'GET':
            echo json_encode(ImagenControlador::obtener($_GET['id']));
        break;

    case 'POST':
            echo json_encode(
                ImagenControlador::subirImagen(
                    $_POST['id'] ?? null,
                    $_FILES['imagen'] ?? null
                )
            );
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}