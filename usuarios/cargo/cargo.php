<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once 'CargoControlador.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':
        echo json_encode(CargoControlador::obtener());
        break;

    case 'POST':
        echo json_encode(CargoControlador::crear($data));
        break;

    case 'PUT':
        echo json_encode(CargoControlador::modificar($data));
        break;

    case 'DELETE':
        echo json_encode(CargoControlador::eliminar($data['id'] ?? null));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
