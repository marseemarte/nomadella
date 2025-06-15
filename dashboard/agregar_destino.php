<?php
include 'conexion.php';
include 'verificar_admin.php';
$destino = trim($_POST['destino']);
if ($destino) {
    $conn->query("INSERT IGNORE INTO destinos (destino) VALUES ('$destino')");
    $res = $conn->query("SELECT id_destino, destino FROM destinos WHERE destino='$destino' LIMIT 1");
    $row = $res->fetch_assoc();
    echo json_encode($row);
}
if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Agregar destino',
        "Destino '$destino' agregado"
    );
}
?>