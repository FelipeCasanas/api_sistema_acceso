<?php
header('Content-Type: application/json');
require_once 'RegistroControlador.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode(RegistroControlador::obtener('id', $_GET['id']));
        } else if(isset($_GET['id_usuario'])) {
            echo json_encode(RegistroControlador::obtener('id_usuario', $_GET['id_usuario']));
        } else if(isset($_GET['id_ambiente'])) {
            echo json_encode(RegistroControlador::obtener('id_ambiente', $_GET['id_ambiente']));
        } else if(isset($_GET['tipo_registro'])) {
            echo json_encode(RegistroControlador::obtener('tipo_registro', $_GET['tipo_registro']));
        } else {
            return json_encode(['success' => false, 'message' => 'No se recibió el medio de busqueda']);
        }
        break;
        
    case 'POST':
        echo json_encode(RegistroControlador::registrar($data));
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
