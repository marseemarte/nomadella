<?php
include 'conexion.php';
include 'verificar_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_destino'])) {
    $id_destino = intval($_POST['id_destino']);

    // activar el destino
    $conn->query("UPDATE destinos SET estado = 'activo' WHERE id_destino = $id_destino");

    // Registrar en bitácora si se desea
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Activar destino',
            "Destino #$id_destino activado."
        );
    }

    header("Location: destinos.php?activado=1");
    exit;
}
?>
