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

require_once('AccesoControlador.php');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':

        // Si NO viene ningún parámetro, retornar error
        if (
            !isset($_GET['id_usuario']) &&
            !isset($_GET['android_id']) &&
            !isset($_GET['medio_acceso'])
        ) {
            echo json_encode([
                'success' => false,
                'message' => 'Debe enviar al menos un parámetro'
            ]);
            return;
        }

        $data = [];

        if (isset($_GET['id_usuario'])) {
            $data['id_usuario'] = $_GET['id_usuario'];
        }

        if (isset($_GET['android_id'])) {
            $data['android_id'] = $_GET['android_id'];
        }

        if (isset($_GET['medio_acceso'])) {
            $data['medio_acceso'] = $_GET['medio_acceso'];
        }

        echo json_encode(AccesoControlador::obtener($data));
        break;

    case 'POST':
        echo json_encode(AccesoControlador::registrar($data));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
