<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/conexion/conectar.php');

list($sql, $parametros) = construirQuery('imagen', [], 'SELECT', ['id_inmueble' => $_SESSION['id_inmueble']]);
$respuesta = ejecutarQuery($sql, $parametros);
$imagenes = $respuesta->fetchAll(PDO::FETCH_ASSOC);

?>