<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once('CargaControlador.php');

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'POST':

        // Validar datos obligatorios
        if (
            isset($_POST['id']) &&
            isset($_POST['tipo']) &&
            isset($_FILES['archivo'])
        ) {

            echo json_encode(
                CargaControlador::subir($_POST, $_FILES['archivo'])
            );

        } else {

            echo json_encode([
                'success' => false,
                'message' => 'Faltan datos obligatorios (id, tipo o archivo)'
            ]);
        }

    break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    break;
}