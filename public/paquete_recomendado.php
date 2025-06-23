<?php
include 'conexion.php';

$tipo = $_GET['tipo'] ?? '';
$tipo = $conexion->real_escape_string($tipo);

$sql = "SELECT id_paquete, nombre 
        FROM paquetes_turisticos 
        WHERE tipo_paquete = '$tipo' AND activo = 1 
        LIMIT 1";
$result = $conexion->query($sql);

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(null);
}
