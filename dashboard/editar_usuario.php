<?php
include 'conexion.php';
include 'verificar_admin.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario = null;
if ($id) {
    $res = $conn->query("SELECT * FROM usuarios WHERE id_usuario = $id");
    $usuario = $res->fetch_assoc();
}
if (!$usuario) {
    echo "<div class='alert alert-danger'>Usuario no encontrado.</div>";
    exit;
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $activo = isset($_POST['estado']) ? 1 : 0;

    $conn->query("UPDATE usuarios SET nombre='$nombre', email='$email', telefono='$telefono', estado=$activo WHERE id_usuario=$id");

    // Registrar en bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Editar usuario',
            "Usuario editado: ID $id, nombre: $nombre, email: $email, estado: $activo"
        );
    }

    header("Location: clientes.php?ok=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
    <style>
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; background: #FFF6F8; }
        .card-editar { background: #fff; border: 1px solid #6CE0B6; border-radius: 18px; box-shadow: 0 4px 24px 0 rgba(117, 13, 55, 0.08); padding: 32px 28px 24px 28px; max-width: 600px; margin: 0 auto; position: relative; overflow: hidden; }
        .form-label { color: #750D37; font-weight: 500; }
        .btn-success { background: #3AB789 !important; border: none; font-weight: bold; color: #fff !important; letter-spacing: 1px; }
        .btn-secondary { background: #5CC7ED !important; border: none; color: #1A001C !important; font-weight: bold; }
        .icon-circle { width: 60px; height: 60px; background: linear-gradient(135deg, #6CE0B6 60%, #5CC7ED 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px auto; font-size: 2.2rem; color: #fff; box-shadow: 0 2px 8px #6CE0B633; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="clientes.php">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Usuario</li>
            </ol>
        </nav>
        <div class="card-editar mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-person-badge"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Editar Usuario</h2>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required maxlength="100" value="<?= htmlspecialchars($usuario['nombre']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required maxlength="100" value="<?= htmlspecialchars($usuario['email']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" maxlength="30" value="<?= htmlspecialchars($usuario['telefono']) ?>">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="activo" class="form-check-input" id="activo" <?= $usuario['activo'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Cuenta activa</label>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="clientes.php" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>