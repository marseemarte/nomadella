<?php

session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo 0;
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$res = $conexion->query("SELECT id_carrito FROM carritos WHERE id_usuario=$id_usuario AND estado='activo' ORDER BY id_carrito DESC LIMIT 1");
if (!$row = $res->fetch_assoc()) {
    echo 0;
    exit;
}
$id_carrito = $row['id_carrito'];

$q = $conexion->query("SELECT SUM(cantidad) as total FROM carrito_items WHERE id_carrito=$id_carrito");
$data = $q->fetch_assoc();
echo intval($data['total']);
?>