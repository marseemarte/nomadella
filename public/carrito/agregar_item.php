<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_usuario = $_SESSION['usuario_id'];
$tipo_producto = $_POST['tipo_producto'];
$id_producto = intval($_POST['id_producto']);
$cantidad = intval($_POST['cantidad']);

if ($cantidad < 1) exit('Cantidad inválida');

// Buscar o crear carrito activo
$res = $conexion->query("SELECT id_carrito FROM carritos WHERE id_usuario=$id_usuario AND estado='activo' ORDER BY id_carrito DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $id_carrito = $row['id_carrito'];
} else {
    $conexion->query("INSERT INTO carritos (id_usuario, estado) VALUES ($id_usuario, 'activo')");
    $id_carrito = $conexion->insert_id;
}

// Solo para paquetes turísticos
if ($tipo_producto === 'paquete_turistico') {
    $prod = $conexion->query("SELECT precio_base, cupo_disponible FROM paquetes_turisticos WHERE id_paquete=$id_producto AND activo=1")->fetch_assoc();
    if (!$prod) exit('Paquete no encontrado');
    if ($cantidad > $prod['cupo_disponible']) exit('No hay suficiente cupo');
    $precio = $prod['precio_base'];
    $subtotal = $precio * $cantidad;
} else {
    exit('Tipo de producto no soportado');
}

// Ver si ya está en el carrito
$res = $conexion->query("SELECT id_item, cantidad FROM carrito_items WHERE id_carrito=$id_carrito AND tipo_producto='$tipo_producto' AND id_producto=$id_producto");
if ($row = $res->fetch_assoc()) {
    $nueva_cantidad = $row['cantidad'] + $cantidad;
    $nuevo_subtotal = $precio * $nueva_cantidad;
    $conexion->query("UPDATE carrito_items SET cantidad=$nueva_cantidad, subtotal=$nuevo_subtotal WHERE id_item={$row['id_item']}");
} else {
    $conexion->query("INSERT INTO carrito_items (id_carrito, tipo_producto, id_producto, cantidad, precio_unitario, subtotal) VALUES ($id_carrito, '$tipo_producto', $id_producto, $cantidad, $precio, $subtotal)");
}

echo 'ok';
?>