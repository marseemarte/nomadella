<?php
include 'conexion.php';
session_start();

$id_notif = intval($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? 'aceptar'; // 'aceptar' o 'rechazar'

if (!$id_notif || !isset($_SESSION['usuario_id'])) exit('error');

// Obtener id_orden relacionado a la notificación (asumiendo que tienes un campo id_orden en notificaciones o puedes obtenerlo)
$res = $conexion->query("SELECT id_orden FROM notificaciones WHERE id=$id_notif");
$row = $res ? $res->fetch_assoc() : null;
$id_orden = $row ? intval($row['id_orden']) : 0;

// Marcar la notificación como leída
$conexion->query("UPDATE notificaciones SET leido=1 WHERE id=$id_notif");

// Registrar en bitácora y actualizar estado de la orden según la acción
if ($accion === 'aceptar') {
    // Registrar aceptación en bitácora
    $stmt = $conexion->prepare("INSERT INTO bitacora (id_usuario, accion, descripcion, fecha_hora) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('iss', $_SESSION['usuario_id'], $accion, "El usuario aceptó el cambio de fecha en la reserva #$id_orden");
    $stmt->execute();
    // Puedes dejar la reserva como está o actualizar su estado si lo deseas
} else {
    // Cancelar la reserva
    if ($id_orden) {
        $conexion->query("UPDATE ordenes SET estado='cancelada' WHERE id_orden=$id_orden");
    }
    // Registrar rechazo en bitácora
    $stmt = $conexion->prepare("INSERT INTO bitacora (id_usuario, accion, descripcion, fecha_hora) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('iss', $_SESSION['usuario_id'], $accion, "El usuario rechazó el cambio de fecha y la reserva #$id_orden fue cancelada");
    $stmt->execute();
}

echo 'ok';
?>