<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_usuario = $_SESSION['usuario_id'];
$id_item = intval($_POST['id_item']);

// Verificar que el ítem pertenezca al usuario
$res = $conexion->query("SELECT ci.id_item FROM carrito_items ci INNER JOIN carritos c ON ci.id_carrito = c.id_carrito WHERE ci.id_item=$id_item AND c.id_usuario=$id_usuario AND c.estado='activo'");
if ($res->num_rows) {
    $conexion->query("DELETE FROM carrito_items WHERE id_item=$id_item");
    echo 'ok';
} else {
    echo 'error';
}
?>
