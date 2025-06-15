<?php
include 'conexion.php';
include 'verificar_admin.php';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $email = $conn->real_escape_string($_POST['email']);
    // Contraseña por defecto '1234' si no se envía ninguna contraseña
    $contraseña_raw = isset($_POST['contraseña']) && !empty($_POST['contraseña']) ? $_POST['contraseña'] : '1234';
    $contraseña = password_hash($contraseña_raw, PASSWORD_DEFAULT);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $fecha_registro = date('Y-m-d H:i:s');
    $rol = 2; // rol por defecto
    $estado = $conn->real_escape_string($_POST['estado']);

    $conn->query("INSERT INTO usuarios (nombre, apellido, email, contraseña, telefono, fecha_registro, rol, estado) 
                  VALUES ( '$nombre', '$apellido', '$email', '$contraseña', '$telefono', '$fecha_registro', $rol, '$estado')");

    header("Location: clientes.php?ok=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Empleado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
    <style>
        .main-content {
            margin-left: 260px;
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
            background: #FFF6F8;
        }
        .card-empleado {
            background: #fff;
            border: 1px solid #6CE0B6;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 rgba(117, 13, 55, 0.08);
            padding: 32px 28px 24px 28px;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .card-empleado:before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, #6CE0B6 0%, #5CC7ED 100%);
            opacity: 0.18;
            z-index: 0;
        }
        .form-label {
            color: #750D37;
            font-weight: 500;
        }
        .btn-success {
            background: #3AB789 !important;
            border: none;
            font-weight: bold;
            color: #fff !important;
            letter-spacing: 1px;
        }
        .btn-secondary {
            background: #5CC7ED !important;
            border: none;
            color: #1A001C !important;
            font-weight: bold;
        }
        .icon-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6CE0B6 60%, #5CC7ED 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            font-size: 2.2rem;
            color: #fff;
            box-shadow: 0 2px 8px #6CE0B633;
        }
        .breadcrumb-item a {
            color: #750D37;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="empleados.php">Empleados</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Empleado</li>
            </ol>
        </nav>
        <div class="card-empleado mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-person-badge"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Registro de Empleado</h2>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="text" name="contraseña" class="form-control" placeholder="Contraseña por defecto: 1234" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" maxlength="30">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de Registro</label>
                    <input type="date" name="fecha_registro" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="">Seleccione estado</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="empleados.php" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
