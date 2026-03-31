<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost'); // ajusta a tu dominio
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once('../seguridad.php');
require_once('AuthControlador.php');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

Seguridad::iniciarSesion();

$metodo = $_SERVER['REQUEST_METHOD'];
$datos = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($metodo) {
    case 'GET':
        echo json_encode(AuthControlador::verificarSesion());
        break;

    case 'POST':
        echo json_encode(AuthControlador::login($datos));
        break;

    case 'DELETE':
        echo json_encode(AuthControlador::logout());
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}