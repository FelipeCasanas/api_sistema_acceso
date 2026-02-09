<?php
function obtenerConexion() {
    $host = 'localhost';
    $dbname = 'sistema_acceso';
    $usuario = 'root';
    $contrasena = '';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch de datos como array asociativo
        PDO::ATTR_EMULATE_PREPARES => false, // Usar prepared statements reales del servidor
    ];

    try {
        $conexion = new PDO($dsn, $usuario, $contrasena, $opciones);
        return $conexion;
    } catch (PDOException $e) {
        // Aquí puedes loguear el error o mostrar mensaje personalizado
        die("Error de conexión: " . $e->getMessage());
    }
}
?>