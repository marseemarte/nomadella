<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_usuario = $_SESSION['usuario_id'];
$res = $conexion->query("SELECT id_carrito FROM carritos WHERE id_usuario=$id_usuario AND estado='activo' ORDER BY id_carrito DESC LIMIT 1");
if (!$row = $res->fetch_assoc()) {
    echo json_encode([]);
    exit;
}
$id_carrito = $row['id_carrito'];

$items = [];
$q = $conexion->query("SELECT * FROM carrito_items WHERE id_carrito=$id_carrito");
while($item = $q->fetch_assoc()) {
    // Solo para paquetes turísticos
    if ($item['tipo_producto'] === 'paquete_turistico') {
        $p = $conexion->query("SELECT nombre FROM paquetes_turisticos WHERE id_paquete={$item['id_producto']}")->fetch_assoc();
        $item['nombre'] = $p ? $p['nombre'] : 'Paquete';
    }
    $items[] = $item;
}
echo json_encode($items);
?>
