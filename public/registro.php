<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $rol = $_POST['rol'] ?? 3;
    $estado = 'activo';

    if (empty($nombre) || empty($apellido) || empty($email) || empty($clave)) {
        echo "Faltan datos obligatorios";
        exit;
    }

    $consulta = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $consulta->bind_param("s", $email);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows > 0) {
        echo "El correo ya está registrado";
        exit;
    }

    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, apellido, email, contraseña, rol, estado) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssis", $nombre, $apellido, $email, $clave_hash, $rol, $estado);

    if ($stmt->execute()) {
        echo "Usuario registrado correctamente";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
    }
}
?>