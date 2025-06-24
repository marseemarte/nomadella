<?php
include 'conexion.php';

$id_orden = 1; // Cambia por un ID que exista

$res = $conn->query("SELECT o.*, u.nombre, u.apellido FROM ordenes o JOIN usuarios u ON u.id_usuario = o.id_usuario WHERE o.id_orden = $id_orden");
if ($res && $res->num_rows > 0) {
    $reserva = $res->fetch_assoc();
    echo "Reserva encontrada: " . $reserva['nombre'] . " " . $reserva['apellido'];
} else {
    echo "No se encontró la reserva";
}
?>