<?php
require_once('../mjolnir/seguridad.php');
Seguridad::proteger();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once('PermisoControlador.php');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {

    case 'GET':
        switch (true) {
            case isset($_GET['total']):
                echo json_encode(PermisoControlador::obtenerTotal());
                break;

            case isset($_GET['id']):
                echo json_encode(PermisoControlador::obtener('id', $_GET['id'], true));
                break;

            case isset($_GET['id_usuario']):
                echo json_encode(PermisoControlador::obtener('id_usuario', $_GET['id_usuario'], true));
                break;

            case isset($_GET['tipo_permiso']):
                echo json_encode(PermisoControlador::obtener('tipo_permiso', $_GET['tipo_permiso'], true));
                break;

            case isset($_GET['estado']):
                echo json_encode(PermisoControlador::obtener('estado', $_GET['estado'], true));
                break;

            case isset($_GET['todos']) && $_GET['todos'] === 'true':
                echo json_encode(PermisoControlador::obtenerTodos());
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'No se recibió el medio de busqueda']);
        }
        break;

    case 'POST':
        echo json_encode(PermisoControlador::crear($data));
        break;

    case 'PUT':
        echo json_encode(PermisoControlador::modificar($data));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}