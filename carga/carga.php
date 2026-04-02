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

require_once('CargaControlador.php');

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