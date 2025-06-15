<?php
include 'conexion.php';
include 'verificar_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("UPDATE usuarios SET estado = 'inactivo' WHERE id_usuario = $id");
    // Opcional: auditar en bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Desactivar usuario',
            "Usuario desactivado: ID $id"
        );
    }
    echo "ok";
}
?>