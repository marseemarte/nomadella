<?php
include 'conexion.php';
include 'verificar_admin.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM proveedores WHERE id_proveedor = $id");
}

if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Eliminar proveedor',
        "proveedor', #$id eliminado"
    );
}

header("Location: proveedores.php");
exit;
?>