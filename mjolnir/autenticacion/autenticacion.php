<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once('AuthControlador.php');

session_start();
$metodo = $_SERVER['REQUEST_METHOD'];
$datos = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($metodo) {
    case 'POST':
        echo json_encode(AuthControlador::login($datos));
        break;

    case 'GET':
        echo json_encode(AuthControlador::verificarSesion($datos));
        break;

    case 'DELETE':
        echo json_encode(AuthControlador::logout());
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
