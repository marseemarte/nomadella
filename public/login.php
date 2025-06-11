<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($email) || empty($clave)) {
        echo "Faltan datos";
        exit;
    }

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if ($usuario['contraseña'] === $clave || password_verify($clave, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['rol'] = $usuario['rol'];
            echo "ok";
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
}
?>