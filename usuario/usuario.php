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
        switch (true) {
            case isset($_GET['total']):
                echo json_encode(UsuarioControlador::obtenerTotal());
                break;

            case isset($_GET['id']):
                echo json_encode(UsuarioControlador::obtener('id', $_GET['id'], true));
                break;

            case isset($_GET['identificacion']):
            echo json_encode(UsuarioControlador::obtener('identificacion', $_GET['identificacion'], $_GET['coincidencia_exacta'] ?? true));
            break;

            case isset($_GET['cargo']):
                echo json_encode(UsuarioControlador::obtener('cargo', $_GET['cargo'], $_GET['coincidencia_exacta'] ?? true));
                break;

            case isset($_GET['nombre']):
                echo json_encode(UsuarioControlador::obtener('nombre', $_GET['nombre'], $_GET['coincidencia_exacta'] ?? true));
                break;

            case isset($_GET['todos']) && $_GET['todos'] === 'true':
                echo json_encode(UsuarioControlador::obtenerTodos());
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'No se recibió el medio de busqueda']);
        }
        break;

    case 'POST':
        if (!empty($datos['masivo']) && $datos['masivo'] === true) {
            echo json_encode(
                UsuarioControlador::cargaMasiva($datos['usuarios'] ?? [])
            );
        } else {
            echo json_encode(UsuarioControlador::crear($datos));
        }
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
