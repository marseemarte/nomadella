<?php
include 'conexion.php';

$term = isset($_GET['term']) ? $conn->real_escape_string($_GET['term']) : '';

$resultado = $conn->query("SELECT id_usuario, nombre FROM usuarios WHERE nombre LIKE '%$term%' LIMIT 10");

$sugerencias = [];
while ($row = $resultado->fetch_assoc()) {
    $sugerencias[] = [
        'id' => $row['id_usuario'],
        'label' => $row['nombre']
    ];
}

header('Content-Type: application/json');
echo json_encode($sugerencias);
?>
