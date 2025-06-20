<?php
include 'conexion.php';
include 'verificar_admin.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM proveedores WHERE id_proveedor = $id LIMIT 1";
    $resultado = $conn->query($sql);
    $sugerencias = [];
    if ($row = $resultado->fetch_assoc()) {
        $sugerencias[] = $row;
    }
} else {
    $term = isset($_GET['term']) ? $conn->real_escape_string($_GET['term']) : '';
    $sql = "SELECT id_proveedor, nombre, tipo FROM proveedores 
            WHERE nombre LIKE '%$term%' 
            ORDER BY nombre ASC LIMIT 10";
    $resultado = $conn->query($sql);
    $sugerencias = [];
    while ($row = $resultado->fetch_assoc()) {
        $sugerencias[] = [
            'id' => $row['id_proveedor'],
            'label' => $row['nombre'] . " (" . ucfirst($row['tipo']) . ")"
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($sugerencias);
?>
