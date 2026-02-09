<?php
header('Content-Type: application/json');
require_once('AccesoControlador.php');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':
        echo json_encode(AccesoControlador::obtener());
        break;

    case 'POST':
        echo json_encode(AccesoControlador::registrar($data));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
