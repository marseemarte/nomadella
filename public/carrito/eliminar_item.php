<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "No logueado";
    exit;
}

$id_item = $_POST['id_item'] ?? null;

if (!$id_item) {
    echo "ID inválido";
    exit;
}

$sql = "DELETE FROM carrito_items WHERE id_item = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_item);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "ok";
} else {
    echo "Error al eliminar";
}
?>
