<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once 'UsuarioControlador.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];
$datos = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($metodo) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode(UsuarioControlador::obtenerUno($_GET['id']));
        } else {
            echo json_encode(UsuarioControlador::obtenerTodos());
        }
        break;

    case 'POST':
        echo json_encode(UsuarioControlador::crear($datos));
        break;

    case 'PUT':
        echo json_encode(UsuarioControlador::modificar($datos));
        break;

    case 'DELETE':
        echo json_encode(UsuarioControlador::eliminar($datos['id'] ?? null));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
