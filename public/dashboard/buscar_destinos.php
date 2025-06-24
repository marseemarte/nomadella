<?php
include 'conexion.php';
include 'verificar_admin.php';
$term = isset($_GET['term']) ? $conn->real_escape_string($_GET['term']) : '';
$res = $conn->query("SELECT id_destino, destino FROM destinos WHERE destino LIKE '%$term%' ORDER BY destino LIMIT 10");
$sugerencias = [];
while ($row = $res->fetch_assoc()) {
    $sugerencias[] = $row;
}
header('Content-Type: application/json');
echo json_encode($sugerencias);
?>