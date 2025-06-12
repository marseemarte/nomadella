<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode([]);
  exit;
}

$id_usuario = $_SESSION['usuario_id'];
$sql = "SELECT id_orden, fecha_orden, total, estado FROM ordenes WHERE id_usuario = ? ORDER BY fecha_orden DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$ordenes = [];
while ($row = $res->fetch_assoc()) {
  $ordenes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($ordenes);
?>