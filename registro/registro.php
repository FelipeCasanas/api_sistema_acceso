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

require_once('RegistroControlador.php');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {

    case 'GET':
        switch (true) {
            case isset($_GET['id']):
                echo json_encode(RegistroControlador::obtener('id', $_GET['id']));
                break;

            case isset($_GET['id_usuario']):
                echo json_encode(RegistroControlador::obtener('id_usuario', $_GET['id_usuario']));
                break;

            case isset($_GET['id_ambiente']):
                echo json_encode(RegistroControlador::obtener('id_ambiente', $_GET['id_ambiente']));
                break;

            case isset($_GET['tipo_registro']):
                echo json_encode(RegistroControlador::obtener('tipo_registro', $_GET['tipo_registro']));
                break;

            case isset($_GET['todos']) && $_GET['todos'] === 'true':
                echo json_encode(RegistroControlador::obtenerTodos());
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'No se recibió el medio de busqueda']);
        }
        break;

    case 'POST':
        echo json_encode(RegistroControlador::registrar($data));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
