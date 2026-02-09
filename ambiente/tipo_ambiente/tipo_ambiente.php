<?php
header('Content-Type: application/json');
require_once ('TipoAmbienteControlador.php');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':
        echo json_encode(TipoAmbienteControlador::obtener());
        break;

    case 'POST':
        echo json_encode(TipoAmbienteControlador::crear($data));
        break;

    case 'PUT':
        echo json_encode(TipoAmbienteControlador::modificar($data));
        break;

    case 'DELETE':
        echo json_encode(TipoAmbienteControlador::eliminar($data['id'] ?? null));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
