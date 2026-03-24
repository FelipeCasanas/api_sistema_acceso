<?php
date_default_timezone_set('America/Bogota');

function obtenerConexion() {
    $host = '127.0.0.1';
    $dbname = 'u267461442_etherium';
    $usuario = 'u267461442_etherium_root';
    $contrasena = '300Saldo300';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch de datos como array asociativo
        PDO::ATTR_EMULATE_PREPARES => false, // Usar prepared statements reales del servidor
    ];

    try {
        $conexion = new PDO($dsn, $usuario, $contrasena, $opciones);
        $conexion->query("SET time_zone = '-05:00'"); // Establecer zona horaria
        return $conexion;
    } catch (PDOException $e) {
        // Aquí puedes loguear el error o mostrar mensaje personalizado
        die("Error de conexión: " . $e->getMessage());
    }
}
?>