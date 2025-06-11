<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT * FROM ordenes WHERE id_usuario = ? ORDER BY fecha_orden DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$ordenes = [];

while ($orden = $res->fetch_assoc()) {
    $id_orden = $orden['id_orden'];

    // Traer ítems de esta orden
    $sql_items = "SELECT oi.*, pt.nombre FROM orden_items oi 
                  JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto 
                  WHERE oi.id_orden = ? AND oi.tipo_producto = 'paquete'";
    $stmt_items = $conexion->prepare($sql_items);
    $stmt_items->bind_param("i", $id_orden);
    $stmt_items->execute();
    $res_items = $stmt_items->get_result();

    $items = [];
    while ($item = $res_items->fetch_assoc()) {
        $items[] = $item;
    }

    $orden['items'] = $items;
    $ordenes[] = $orden;
}

echo json_encode($ordenes);
?>
