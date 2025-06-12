<?php
session_start();
include 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($email) || empty($clave)) {
        $mensaje = "Faltan datos";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($clave, $usuario['contraseña'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['rol'] = $usuario['rol'];
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Contraseña incorrecta";
            }
        } else {
            $mensaje = "Usuario no encontrado";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Nomadella</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .form-box {max-width:400px;margin:60px auto;padding:32px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;}
        .form-box h2 {color:#b84e6f;}
        .form-box input {width:100%;margin-bottom:16px;padding:10px;border:1px solid #ccc;border-radius:5px;}
        .form-box button {width:100%;background:#b84e6f;color:#fff;padding:10px;border:none;border-radius:5px;font-size:1.1em;}
        .form-box .mensaje {color:#b84e6f;margin-bottom:10px;}
        .form-box a {color:#b84e6f;text-decoration:none;}
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Iniciar sesión</h2>
        <?php if($mensaje): ?><div class="mensaje"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>