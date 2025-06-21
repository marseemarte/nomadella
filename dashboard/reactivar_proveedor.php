<?php
include 'conexion.php';
include 'verificar_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("UPDATE proveedores SET estado = 'activo' WHERE id_proveedor = $id");
    // Registrar en bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Reactivar proveedor',
            "Proveedor reactivado: ID $id"
        );
    }
    echo "ok";
}
?>