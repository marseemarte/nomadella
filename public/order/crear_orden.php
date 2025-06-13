<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_usuario = $_SESSION['usuario_id'];
$res = $conexion->query("SELECT id_carrito FROM carritos WHERE id_usuario=$id_usuario AND estado='activo' ORDER BY id_carrito DESC LIMIT 1");
if (!$row = $res->fetch_assoc()) exit('No hay carrito activo');
$id_carrito = $row['id_carrito'];

// Obtener items
$items = [];
$total = 0;
$q = $conexion->query("SELECT * FROM carrito_items WHERE id_carrito=$id_carrito");
while($item = $q->fetch_assoc()) {
    $items[] = $item;
    $total += $item['subtotal'];
}

// Simular pago (puedes agregar integración real aquí)
$medio_pago = 'Tarjeta de Crédito';
$datos_facturacion = 'Datos de ejemplo';

// Crear orden
$conexion->query("INSERT INTO ordenes (id_usuario, total, estado, medio_pago, datos_facturacion) VALUES ($id_usuario, $total, 'Confirmada', '$medio_pago', '$datos_facturacion')");
$id_orden = $conexion->insert_id;

// Pasar items a la orden
foreach($items as $item) {
    $conexion->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad, precio_unitario, subtotal) VALUES ($id_orden, '{$item['tipo_producto']}', {$item['id_producto']}, {$item['cantidad']}, {$item['precio_unitario']}, {$item['subtotal']})");
    // Descontar cupo si es paquete
    if ($item['tipo_producto'] === 'paquete_turistico') {
        $conexion->query("UPDATE paquetes_turisticos SET cupo_disponible = cupo_disponible - {$item['cantidad']} WHERE id_paquete = {$item['id_producto']}");
    }
}

// Vaciar carrito
$conexion->query("DELETE FROM carrito_items WHERE id_carrito=$id_carrito");

// Opcional: cerrar carrito
$conexion->query("UPDATE carritos SET estado='cerrado' WHERE id_carrito=$id_carrito");

echo 'Orden creada correctamente';
?>
