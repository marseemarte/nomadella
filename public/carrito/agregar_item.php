<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "No logueado";
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_producto = $_POST['id_paquete'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;
$precio_unitario = $_POST['precio'] ?? 0;

if (!$id_producto || $cantidad <= 0) {
    echo "Datos inválidos";
    exit;
}

$sql_carrito = "SELECT id_carrito FROM carritos WHERE id_usuario = ? AND estado = 'activo'";
$stmt = $conexion->prepare($sql_carrito);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $crear = $conexion->prepare("INSERT INTO carritos (id_usuario, estado) VALUES (?, 'activo')");
    $crear->bind_param("i", $id_usuario);
    $crear->execute();
    $id_carrito = $crear->insert_id;
} else {
    $fila = $result->fetch_assoc();
    $id_carrito = $fila['id_carrito'];
}

$check = $conexion->prepare("SELECT id_item, cantidad FROM carrito_items WHERE id_carrito = ? AND tipo_producto = 'paquete_turistico' AND id_producto = ?");
$check->bind_param("ii", $id_carrito, $id_producto);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $item = $res->fetch_assoc();
    $nueva_cantidad = $item['cantidad'] + $cantidad;
    $nuevo_subtotal = $nueva_cantidad * $precio_unitario;

    $update = $conexion->prepare("UPDATE carrito_items SET cantidad = ?, subtotal = ? WHERE id_item = ?");
    $update->bind_param("idi", $nueva_cantidad, $nuevo_subtotal, $item['id_item']);
    $update->execute();
} else {
    $subtotal = $cantidad * $precio_unitario;
    $insert = $conexion->prepare("INSERT INTO carrito_items (id_carrito, tipo_producto, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, 'paquete_turistico', ?, ?, ?, ?)");
    $insert->bind_param("iiidd", $id_carrito, $id_producto, $cantidad, $precio_unitario, $subtotal);
    $insert->execute();
}

echo "ok";
?>