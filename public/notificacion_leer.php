<?php
include 'conexion.php';
$id = intval($_GET['id']);
$conexion->query("UPDATE notificaciones SET leido=1 WHERE id=$id");
?>