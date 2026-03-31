<?php
require_once('../mjolnir/seguridad.php');
Seguridad::proteger();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once('CargaControlador.php');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {

    $tipo = $_POST['tipo'] ?? null;
    $id = $_POST['id'] ?? null;
    $archivo = $_FILES['archivo'] ?? null;

    echo json_encode(
        CargaControlador::subir($tipo, $id, $archivo)
    );

} elseif ($metodo === 'GET') {

    $tipo = $_GET['tipo'] ?? null;
    $id = $_GET['id'] ?? null;

    echo json_encode(
        CargaControlador::obtener($tipo, $id)
    );

} else {
    echo json_encode(["error" => "Método no permitido"]);
}