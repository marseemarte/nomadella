<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "No logueado";
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Buscar carrito activo
$sql = "SELECT * FROM carritos WHERE id_usuario = ? AND estado = 'activo'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No hay carrito activo";
    exit;
}

$carrito = $res->fetch_assoc();
$id_carrito = $carrito['id_carrito'];

// Obtener items
$sql_items = "SELECT * FROM carrito_items WHERE id_carrito = ?";
$stmt_items = $conexion->prepare($sql_items);
$stmt_items->bind_param("i", $id_carrito);
$stmt_items->execute();
$items_res = $stmt_items->get_result();

$total = 0;
$items = [];
while ($item = $items_res->fetch_assoc()) {
    $total += $item['subtotal'];
    $items[] = $item;
}

// Crear orden
$sql_orden = "INSERT INTO ordenes (id_usuario, total, estado, medio_pago, datos_facturacion) VALUES (?, ?, 'pendiente', 'no especificado', 'no especificado')";
$stmt_orden = $conexion->prepare($sql_orden);
$stmt_orden->bind_param("id", $id_usuario, $total);
$stmt_orden->execute();
$id_orden = $stmt_orden->insert_id;

// Insertar ítems en la orden
foreach ($items as $item) {
    $sql_item = "INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad, precio_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_item = $conexion->prepare($sql_item);
    $stmt_item->bind_param(
        "isiiid",
        $id_orden,
        $item['tipo_producto'],
        $item['id_producto'],
        $item['cantidad'],
        $item['precio_unitario'],
        $item['subtotal']
    );
    $stmt_item->execute();
}

// Marcar carrito como procesado
$update = $conexion->prepare("UPDATE carritos SET estado = 'procesado' WHERE id_carrito = ?");
$update->bind_param("i", $id_carrito);
$update->execute();

echo "Orden creada correctamente";
?>
