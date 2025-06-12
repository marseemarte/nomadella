<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  echo "No autorizado";
  exit;
}

$id = $_SESSION['usuario_id'];
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$email = $_POST['email'] ?? '';

if ($nombre && $apellido && $email) {
  $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ? WHERE id_usuario = ?";
  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("sssi", $nombre, $apellido, $email, $id);
  if ($stmt->execute()) {
    echo "Datos actualizados correctamente.";
  } else {
    echo "Error al actualizar.";
  }
} else {
  echo "Faltan datos.";
}
?>