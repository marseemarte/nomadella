<?php
session_start();
include 'conexion.php';
include 'verificar_admin.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $res = $conn->query("SELECT * FROM proveedores WHERE id_proveedor = $id");
        if ($res->num_rows === 0) {
            die("Proveedor no encontrado.");
        }

        $conn->query("UPDATE `proveedores` SET `estado`='inactivo' WHERE id_proveedor = $id");

        if (isset($_SESSION['id_usuario'])) {
            registrar_bitacora(
                $pdo,
                $_SESSION['id_usuario'],
                'Desactivar proveedor',
                "Proveedor #$id eliminado"
            );
        }

        header("Location: proveedores.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    die("No se recibió ningún ID");
}