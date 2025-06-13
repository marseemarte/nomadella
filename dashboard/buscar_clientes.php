<?php
include 'conexion.php';

$term = isset($_GET['term']) ? $conn->real_escape_string($_GET['term']) : '';

$resultado = $conn->query("SELECT id_usuario, nombre, email, telefono FROM usuarios WHERE nombre LIKE '%$term%' OR email LIKE '%$term%' OR telefono LIKE '%$term%' LIMIT 10");

$sugerencias = [];
while ($row = $resultado->fetch_assoc()) {
    $sugerencias[] = [
        'id' => $row['id_usuario'],
        'label' => $row['nombre'] . ' - ' . $row['email'] . ' - ' . $row['telefono']
    ];
}

header('Content-Type: application/json');
echo json_encode($sugerencias);
?>
