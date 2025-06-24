<?php
include 'conexion.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE paquetes_turisticos SET activo = 1 WHERE id_paquete = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora($conn, $_SESSION['id_usuario'], 'Reactivar paquete', "Paquete #$id reactivado");
    }

    echo "OK";
}
?>
