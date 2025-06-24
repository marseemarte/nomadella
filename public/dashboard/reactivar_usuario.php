<?php
include 'conexion.php';
include 'verificar_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("UPDATE usuarios SET estado = 'activo' WHERE id_usuario = $id");
    // Registrar en bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Reactivar usuario',
            "Usuario reactivado: ID $id"
        );
    }
    echo "ok";
}
?>