<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "
SELECT ci.id_item, pt.nombre, ci.cantidad, ci.precio_unitario, ci.subtotal
FROM carritos c
JOIN carrito_items ci ON c.id_carrito = ci.id_carrito
JOIN paquetes_turisticos pt ON pt.id_paquete = ci.id_producto
WHERE c.id_usuario = ? AND c.estado = 'activo'
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
?>
