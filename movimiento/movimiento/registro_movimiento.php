<?php
header('Content-Type: application/json');
require_once 'RegistroRegistroControlador.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'POST':
        echo json_encode(RegistroRegistroControlador::registrar($data));
        break;

    case 'GET':
        echo json_encode(RegistroRegistroControlador::obtener($_GET));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
