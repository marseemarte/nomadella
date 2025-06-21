<?php
include 'conexion.php';
session_start(); // por si no está

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $activo = 0;

    $stmt = $conn->prepare("UPDATE paquetes_turisticos SET activo = ? WHERE id_paquete = ?");
    $stmt->bind_param("ii", $activo, $id);
    $stmt->execute();

    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $conn, // tu variable se llama $conn, no $pdo
            $_SESSION['id_usuario'],
            'Desactivar paquete',
            "Paquete #$id desactivado"
        );
    }

    echo "OK";
}
?>
