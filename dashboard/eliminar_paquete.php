<?php
include 'conexion.php'; // debe definir $pdo
include 'verificar_admin.php'; // debe verificar si el usuario es admin

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Elimina asociaciones primero (si no tienes ON DELETE CASCADE)
    $pdo->prepare("DELETE FROM paquete_alojamientos WHERE id_paquete = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM paquete_vuelos WHERE id_paquete = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM paquete_autos WHERE id_paquete = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM paquete_servicios WHERE id_paquete = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM paquete_etiquetas WHERE id_paquete = ?")->execute([$id]);

    // Ahora elimina el paquete
    $pdo->prepare("DELETE FROM paquetes_turisticos WHERE id_paquete = ?")->execute([$id]);
}

if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Eliminar paquete',
        "Paquete #$id eliminado"
    );
}
?>