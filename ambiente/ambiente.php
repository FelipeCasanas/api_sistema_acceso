<?php
header('Content-Type: application/json');
require_once('AmbienteControlador.php');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            echo json_encode(AmbienteControlador::obtener('id', $_GET['id'], true));
        } else if(isset($_GET['bloque'])) {
            echo json_encode(AmbienteControlador::obtener('bloque', $_GET['bloque'], $_GET['coincidencia_exacta'] ?? true));
        } else if(isset($_GET['sitio'])) {
            echo json_encode(AmbienteControlador::obtener('sitio', $_GET['sitio'], $_GET['coincidencia_exacta'] ?? true));
        } else {
            return json_encode(['success' => false, 'message' => 'No se recibió el medio de busqueda']);
        }
        break;
        
    case 'POST':
        echo json_encode(AmbienteControlador::crear($data));
        break;

    case 'PUT':
        echo json_encode(AmbienteControlador::modificar($data));
        break;

    case 'DELETE':
        echo json_encode(AmbienteControlador::eliminar($data['id'] ?? null));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
