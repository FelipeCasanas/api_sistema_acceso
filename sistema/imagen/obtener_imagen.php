<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/conexion/conectar.php');

// Verificar que la variable de sesión 'id_inmueble' esté definida
if (isset($_SESSION['id_inmueble'])) {
    // Construir la consulta para obtener solo una imagen (la primera imagen del inmueble)
    list($sql, $parametros) = construirQuery('imagen', [], 'SELECT', ['id_inmueble' => $_SESSION['id_inmueble']]);

    // Limitar la consulta para obtener solo la primera imagen
    $sql .= " LIMIT 1"; // Esto limita la consulta a solo una fila
    $respuesta = ejecutarQuery($sql, $parametros);

    // Obtener la imagen (solo la primera)
    $imagen = $respuesta->fetch(PDO::FETCH_ASSOC);

    // Verificar si se obtuvo la imagen
    if ($imagen) {
        $imagen_url = $imagen['url_img'];
    } else {
        $imagen_url = null; // Si no se obtiene imagen, asignar null
    }
} else {
    $imagen_url = null;
}

?>
