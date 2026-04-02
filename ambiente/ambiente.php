<?php
require_once('../mjolnir/seguridad.php');
Seguridad::verificarSesion();

header('Content-Type: application/json');

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://54.156.114.70:63001');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once('AmbienteControlador.php');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':
        switch (true) {
            case isset($_GET['total']):
                echo json_encode(AmbienteControlador::obtenerTotal());
                break;

            case isset($_GET['id']):
                echo json_encode(AmbienteControlador::obtener('id', $_GET['id'], true));
                break;

            case isset($_GET['bloque']):
                echo json_encode(AmbienteControlador::obtener('bloque', $_GET['bloque'], $_GET['coincidencia_exacta'] ?? true));
                break;

            case isset($_GET['sitio']):
                echo json_encode(AmbienteControlador::obtener('sitio', $_GET['sitio'], $_GET['coincidencia_exacta'] ?? true));
                break;

            case isset($_GET['todos']) && $_GET['todos'] === 'true':
                echo json_encode(AmbienteControlador::obtenerTodos());
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'No se recibió el medio de busqueda']);
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