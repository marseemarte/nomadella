<?php
include 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $acepta = $_POST['acepta_terminos'] ?? null;
    $rol = 3; // Cliente
    $estado = 'activo';

    if (empty($nombre) || empty($apellido) || empty($email) || empty($clave) || !$acepta) {
        $mensaje = "Debe completar todos los campos y aceptar los términos.";
    } else {
        $consulta = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $consulta->bind_param("s", $email);
        $consulta->execute();
        $consulta->store_result();

        if ($consulta->num_rows > 0) {
            $mensaje = "El correo ya está registrado";
        } else {
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, apellido, email, contraseña, rol, estado, telefono) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssis", $nombre, $apellido, $email, $clave_hash, $rol, $estado);

            if ($stmt->execute()) {
                header("Location: login.php?msg=ok");
                exit;
            } else {
                $mensaje = "Error al registrar usuario: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Nomadella</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .form-box {max-width:400px;margin:60px auto;padding:32px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;}
        .form-box h2 {color:#b84e6f;}
        .form-box input[type="text"],
        .form-box input[type="email"],
        .form-box input[type="password"] {
            width:100%;
            margin-bottom:16px;
            padding:10px;
            border:1px solid #ccc;
            border-radius:5px;
        }
        .form-box button {
            width:100%;
            background:#b84e6f;
            color:#fff;
            padding:10px;
            border:none;
            border-radius:5px;
            font-size:1.1em;
        }
        .form-box .mensaje {
            color:#b84e6f;
            margin-bottom:10px;
        }
        .form-box a {
            color:#b84e6f;
            text-decoration:none;
        }
        .form-box .checkbox {
            margin-bottom:16px;
            font-size:0.9em;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Registro</h2>
        <?php if($mensaje): ?><div class="mensaje"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="acepta_terminos" required>
                    Acepto los <a href="terminos.php" target="_blank">Términos y Condiciones</a> y la <a href="privacidad.php" target="_blank">Política de Privacidad</a>.
                </label>
            </div>

            <button type="submit">Registrarme</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
